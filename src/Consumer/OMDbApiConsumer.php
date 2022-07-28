<?php

namespace App\Consumer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OMDbApiConsumer
{
    public const MODE_ID = 'i';
    public const MODE_TITLE = 't';

    public function __construct(
        private HttpClientInterface $omdbClient
    ) {}

    public function consume(string $type, string $value) : array
    {
        if (!\in_array($type, [self::MODE_ID, self::MODE_TITLE])) {
            throw new \InvalidArgumentException(sprintf("Invalid mode provided for consumer : %s, or %s allowed, %s given",
                self::MODE_ID, self::MODE_TITLE, $type));
        }

        $data = $this->omdbClient
            ->request(Request::METHOD_GET, '', ['query' => [$type => $value]])
            ->toArray();

        if (array_key_exists('Response', $data) && $data['Response'] === 'False') {
            throw new NotFoundHttpException();
        }

        return $data;
    }
}