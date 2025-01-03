<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TMDbService {
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
        $this->apiKey = '330af9a5d59c143f0d73f2768355a50b';
    }

    public function getPopularMovies(int $page = 1): array {
        $response = $this->client->request('GET', 'https://api.themoviedb.org/3/movie/popular', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
                'page' => $page,
            ],
        ]);

        return $response->toArray();
    }
}
