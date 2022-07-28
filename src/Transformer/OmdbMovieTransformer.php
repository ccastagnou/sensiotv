<?php

namespace App\Transformer;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class OmdbMovieTransformer implements DataTransformerInterface
{
    public function transform($data): Movie
    {
        $genres = explode(', ', $data['Genre']);
        $movie = (new Movie())
            ->setTitle($data['Title'])
            ->setPoster($data['Poster'])
            ->setCountry($data['Country'])
            ->setReleasedAt(new \DateTimeImmutable($data['Released']))
            ->setOmdbId($data['imdbID'])
            ->setRated($data['Rated'])
            ->setPrice(5.0)
        ;

        foreach ($genres as $genre) {
            $genreEnt = (new Genre())
                ->setName($genre)
                ->setPoster($data['Poster'])
            ;
            $movie->addGenre($genreEnt);
        }

        return $movie;
    }

    public function reverseTransform(mixed $value)
    {
        // TODO: Implement reverseTransform() method.
    }
}