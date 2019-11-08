<?php

namespace Drupal\poll;

use Drupal\views\EntityViewsData;

/**
 * Render controller for polls.
 */
class PollViewData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['poll_field_data']['votes'] = array(
      'title' => 'Total votes',
      'help' => 'Displays the total number of votes.',
      'real field' => 'id',
      'field' => array(
        'id' => 'poll_totalvotes',
      ),
    );

    $data['poll_field_data']['status_with_runtime'] = array(
      'title' => 'Active with runtime',
      'help' => 'Displays the status with runtime.',
      'real field' => 'id',
      'field' => array(
        'id' => 'poll_status',
      ),
    );

    return $data;
  }

}
