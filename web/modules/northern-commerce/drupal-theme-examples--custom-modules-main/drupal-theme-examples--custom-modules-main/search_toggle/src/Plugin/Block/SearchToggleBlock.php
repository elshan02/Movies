<?php

namespace Drupal\search_toggle\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a Search Toggle Block.
 *
 * @Block(
 *   id = "search_toggle_block",
 *   admin_label = @Translation("Search Toggle"),
 * )
 */
class SearchToggleBlock extends BlockBase  {
  /**
   * Build the content for mymodule block.
   */
  public function build() {
    return [
      '#theme' => 'search_toggle',
    ];
  }
}
