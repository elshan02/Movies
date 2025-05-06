<?php

namespace Drupal\movie_autofill\Service;

use GuzzleHttp\ClientInterface;

class YouTubeService {
  protected $httpClient;
  protected $apiKey;

  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
    $this->apiKey = 'AIzaSyD3YMAL_JNVmKvSvydi5y6i7i9NwqlKXqg';
  }

  /**
   * Fetch the YouTube trailer video URL for the movie title.
   */
  public function getTrailerUrl($title) {
    $query = urlencode($title . ' official movie trailer');
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=5&q={$query}&key={$this->apiKey}&type=video";

    try {
      $response = $this->httpClient->get($url);
      $data = json_decode($response->getBody(), true);

      if (!empty($data['items'])) {
        foreach ($data['items'] as $item) {
          $videoTitle = strtolower($item['snippet']['title']);
          if (strpos($videoTitle, 'trailer') !== false && $this->titleMatches($videoTitle, $title)) {
            return 'https://www.youtube.com/watch?v=' . $item['id']['videoId'];
          }
        }

        // Fallback: any video with "trailer"
        foreach ($data['items'] as $item) {
          if (strpos(strtolower($item['snippet']['title']), 'trailer') !== false) {
            return 'https://www.youtube.com/watch?v=' . $item['id']['videoId'];
          }
        }

        // Final fallback
        return 'https://www.youtube.com/watch?v=' . $data['items'][0]['id']['videoId'];
      }
    } catch (\Exception $e) {
      \Drupal::logger('movie_autofill')->error($e->getMessage());
    }

    return null;
  }

  /**
   * Check if the movie title matches enough words in the video title.
   */
  protected function titleMatches($videoTitle, $movieTitle) {
    $videoWords = explode(' ', preg_replace('/[^a-zA-Z0-9 ]/', '', strtolower($videoTitle)));
    $movieWords = explode(' ', preg_replace('/[^a-zA-Z0-9 ]/', '', strtolower($movieTitle)));
    $matches = array_intersect($videoWords, $movieWords);
    return count($matches) >= 2;
  }
}
