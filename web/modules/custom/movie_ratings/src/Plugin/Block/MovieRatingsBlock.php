<?php

namespace Drupal\movie_ratings\Plugin\Block;

use Drupal\Core\Block\BlockBase;

// Option 3
use Drupal\Core\Database\Database;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

      $form = \Drupal::formBuilder()->getForm('Drupal\movie_ratings\Form\MovieRatingsForm',$nid);
      
      return // Output of Average
      [  
        'rating' => [
          '#markup' => $this->t('Movie Rating: @avg', ['@avg' => $average]), 
        ],
        'form' => $form,
      ];
    }
    return [];
  }
}