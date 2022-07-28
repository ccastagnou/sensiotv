<?php

namespace App\Command;

use App\Consumer\OMDbApiConsumer;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use App\Transformer\OmdbMovieTransformer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:movie:find',
    description: 'Find a movie by title or OMDb ID',
)]
class MovieFindCommand extends Command
{
    public function __construct(
        private MovieRepository $movieRepository,
        private OMDbApiConsumer $consumer,
        private OmdbMovieTransformer $transformer,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('value', InputArgument::OPTIONAL, 'The movie you are searching for.')
            ->addArgument('type', InputArgument::OPTIONAL, 'The type of search (title or id).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!$value = $input->getArgument('value')) {
            $value = $io->ask('What is the title or id of the movie you\'researching for?');
        }

        $type = strtolower($input->getArgument('type'));
        while (!in_array($type, ['title', 'id'])) {
            $type = strtolower($io->ask('What type of data are your searching on? (id or title)'));
        }

        $io->title('Your search :');
        $io->text(sprintf("Searching for a movie with %s %s", $type, $value));

        $io->section('Searching the database.');
        $property = $type === 'title' ? 'title' : 'imdbId';

        if (!$movie = $this->movieRepository->findOneBy([$property => $value])) {
            $io->note('Not found in database.');
            $io->section('Searching on OMDb API.');

            $method= 'getMovieBy' . ucfirst($type);
            $movie = $this->transformer->transform(
                $this->consumer->$method($value)
            );
            if (!$movie) {
                $io->error('No movie find on OMDb either!');
                return Command::FAILURE;
            }
            $io->note('Movie found on OMDb! Saving in database.');
            $this->movieRepository->add($movie, true);
        }

        $io->section('Result :');
        $io->table(['id', 'imdbId', 'Title', 'Rated'],[
            [$movie->getId(), $movie->getOmdbId(), $movie->getTitle(), $movie->getRated()],
        ]);

        $io->success('Movie successfully found and imported!');

        return Command::SUCCESS;
    }
}
