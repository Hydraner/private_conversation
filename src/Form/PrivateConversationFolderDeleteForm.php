<?php

namespace Drupal\private_conversation\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting Folder entities.
 *
 * @ingroup private_conversation
 * @todo: Add message for user where the message will go.
 */
class PrivateConversationFolderDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.private_conversation_folder.overview_form');
  }

  /**
   * {@inheritdoc}
   */
  protected function getRedirectUrl() {
    return $this->getCancelUrl();
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('The conversations of this folder will be transferred back to the inbox. <br /> This action cannot be undone.');
  }

}
