<?php

namespace Drupal\private_conversation\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Folder entities.
 */
class PrivateConversationFolderViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['private_conversation_folder']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Folder'),
      'help' => $this->t('The Folder ID.'),
    );

    return $data;
  }

}
