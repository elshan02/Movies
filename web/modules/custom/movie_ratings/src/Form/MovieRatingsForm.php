<?php

namespace Drupal\movie_ratings\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
// Option 2
use Drupal\node\Entity\Node;
// Option 3
use Drupal\Core\Database\Database;

/**
 * Create a form to accept ratings
 */


class MovieRatingsForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'rating_form';
    }

    /**
     * {@inheritdoc}
     */

// Option 1 - General Form and not tied to a node/movie

    // public function buildForm(array $form, FormStateInterface $form_state) {
    //     // Rating options: 1 to 5 stars
    //     $form['rating'] = [
    //         '#type' => 'radios',
    //         '#title' => $this->t('Rate this movie'),
    //         '#options' => [
    //             1 => $this->t('1 star'),
    //             2 => $this->t('2 stars'),
    //             3 => $this->t('3 stars'),
    //             4 => $this->t('4 stars'),
    //             5 => $this->t('5 stars'),
    //         ],
    //         '#required' => TRUE,
    //     ];

    //     $form['submit'] = [
    //         '#type' => 'submit',
    //         '#value' => $this->t('Submit'),
    //     ];
    //     return $form;
    // }
    

    // public function submitForm(array &$form, FormStateInterface $form_state) {
    //     $rating = $form_state->getValue('rating');
    //     \Drupal::messenger()->addMessage($this->t('Thank you for your Rating!'));
    // }

// Option 2 - Rating Form tied to a node/movie

//     public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
//         if (empty($nid)) {
//             \Drupal::messenger()->addError($this->t('No valid node ID provided.'));
//             return [];
//         }
    
//         $node = Node::load($nid);
//         if (!$node) {
//             \Drupal::messenger()->addError($this->t('Node not found.'));
//             return [];
//         }

//         if ($nid && $node->getType() == 'movie') {
//         $form['rating'] = [
//             '#type' => 'radios',
//             '#title' => $this->t('Rate this movie'),
//             '#options' => [
//                 1 => $this->t('1 star'),
//                 2 => $this->t('2 stars'),
//                 3 => $this->t('3 stars'),
//                 4 => $this->t('4 stars'),
//                 5 => $this->t('5 stars'),
//             ],
//             '#required' => TRUE,
//         ];
    
//         $form['nid'] = [
//             '#type' => 'hidden',
//             '#value' => $nid,
//         ];
    
//         // Submit button to submit the form
//         $form['submit'] = [
//             '#type' => 'submit',
//             '#value' => $this->t('Submit'),
//         ];
//         return $form;
//     }
// }
    

//     public function submitForm(array &$form, FormStateInterface $form_state) {
//         $rating = $form_state->getValue('rating');

//         \Drupal::messenger()->addMessage($this->t('Thank you for your Rating!'));
//     }

 // Option 3 - Rating form tied to a node/movie and saves to the database

 public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
        $node = Node::load($nid);
        if ($nid && $node->getType() == 'movie'){
            $form['rating'] = [
                '#type' => 'radios',
                '#title' => $this->t('&star; Rate'),
                '#options' => [
                    1 => $this->t('&star;'),
                    2 => $this->t('&star; &star;'),
                    3 => $this->t('&star; &star; &star;'),
                    4 => $this->t('&star; &star; &star; &star;'),
                    5 => $this->t('&star; &star; &star; &star; &star;'),
                ],
                '#required' => TRUE,
            ];

            $form['nid'] = [
                '#type' => 'hidden',
                '#value' => $nid,
            ];

            $form['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t('Submit')
            ];
        }
        return $form;
 }

 public function submitForm(array &$form, FormStateInterface $form_state) {
    $rating = $form_state->getValue('rating');
    $nid = $form_state->getValue('nid');
    $timestamp = time();

    // Starting pushing submission into our database

    $connection = Database::getConnection();
    $connection->insert('movie_ratings')->fields([
        'movie_nid' => $nid,
        'rating' => $rating,
        'timestamp' => $timestamp,
    ])
    ->execute();

    \Drupal::messenger()->addMessage($this->t('Thank you for your rating!'));
 }
    
    // public function build() {
    //     $route_match = \Drupal::routeMatch();
    //     $nid = $route_match->getParameter('nid'); // Get nid directly
    
    //     if (!$nid) {
    //         \Drupal::messenger()->addError($this->t('No valid node ID found in the route.'));
    //         return [];
    //     }
    
    //     $node = Node::load($nid);
    //     if ($node) {
    //         \Drupal::messenger()->addStatus($this->t('Node found: @id', ['@id' => $node->id()]));
    //     } else {
    //         \Drupal::messenger()->addError($this->t('No node found.'));
    //         return [];
    //     }
    
    //     if ($node instanceof \Drupal\node\NodeInterface && $node->getType() == 'movie') {
    //         return \Drupal::formBuilder()->getForm('Drupal\movie_ratings\Form\MovieRatingsForm', $nid);
    //     }
    
    //     return [];
    // }
    
    
    
}
