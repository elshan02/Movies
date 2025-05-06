<?php

namespace Drupal\movie_ratings\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\node\Entity\Node;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides a Movie Ratings Block.
 *
 * @Block(
 *   id = "movie_rating_block",
 *   admin_label = @Translation("Movie Rating Block"),
 * )
 */

class MovieRatingsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

  
  // Option 3 - Rating form tied to a node/movie and saves to the database

  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');

    if ($node instanceof \Drupal\node\NodeInterface && $node->getType() == 'movie') {
      $nid = $node->id();

      $connection = Database::getConnection();
      $query = $connection->select('movie_ratings', 'r')
      ->fields('r', ['rating'])
      ->condition('movie_nid', $nid)
      ->execute();

      // Global variables
      $ratings = $query->fetchAll();
      $total_number = count($ratings);    
      $sum = 0;
      $average = 0;

      foreach ($ratings as $rating) {
        $sum += $rating->rating;
      }

      if ($total_number > 0) {         // Calculating the average
        $average = round($sum / $total_number, 1);
      }
      
      return // Output of Average
      [  
        '#type' => 'container',
        '#attributes' => ['class' => ['movie-rating-average']],
        'average' => [

        '#markup' => $this->t('Average Rating: @avg / 5', ['@avg' => $average]),
        ],
        '#cache' => [
          'tags' => $node->getCacheTags(),
        ],
      ];
    }
    return [];
  }

  public function getCacheTags() {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface && $node->getType() == 'movie') {
      return ['movie_rating:' . $node->id()];
    }
    return parent::getCacheTags();
  }

  public function getCacheContexts() {
    return ['route'];
  }
}