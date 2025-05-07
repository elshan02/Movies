<?php
namespace Drupal\movie_autofill\Service;

use GuzzleHttp\ClientInterface;
use Dotenv\Dotenv;

class TmdbService {

    protected $httpClient;
    protected $apiKey;

    public function __construct(ClientInterface $http_client) {
        $this->httpClient = $http_client;

        // Load envoriment variables from .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../../../');
        $dotenv->load();

        // Fetch api keys from envoriment variables
        $this->apiKey = $_ENV['TMDB_API_KEY'];
    }

    public function getPosterUrl($title) {
        // Makes a GET request to TMDb API to search for the movie by title
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/search/movie', [
            'query' => [
                'api_key' => $this->apiKey,
                'query' => $title,
                ],
            ]);

        // Decodes the JSON response into an array
        $data = json_decode($response->getBody(), true);
        if (!empty($data['results'][0]['poster_path'])) {
            return 'https://image.tmdb.org/t/p/original' . $data['results'][0]['poster_path'];
        }
        return NULL;
    }

   public function getDescription($title) {
    // Makes a GET request to TMDb API to search for the movie by title
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/search/movie', [
          'query' => [
              'api_key' => $this->apiKey,
              'query' => $title,
            ],
        ]);
         // Decodes the JSON response into an array
        $data = json_decode($response->getBody(), true);

        // Checks if the first movie result has an 'overview' field (description)
        if (!empty($data['results']) && !empty($data['results'][0]['overview'])) {
            return $data['results'][0]['overview'];     // If so return it
        }

        // If no, return NULL
        return NULL;
   }

   public function getGenreIds($title) {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/search/movie', [
        'query' => [
            'api_key' => $this->apiKey,
            'query' => $title,
        ], 
    ]);
    
    $data = json_decode($response->getBody(), true);

    if (!empty($data['results']) && !empty($data['results'][0]['genre_ids'])) {
        return $data['results'][0]['genre_ids'];     // If so return it
    }

    return [];
   }
}