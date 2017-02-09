<?php

namespace Drupal\private_conversation\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Folder edit forms.
 *
 * @ingroup private_conversation
 */
class PrivateConversationFolderForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\private_conversation\Entity\PrivateConversationFolder */
    $form = parent::buildForm($form, $form_state);
    $form['user_id']['#access'] = false;
    $form['conversations']['#access'] = false;
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Folder.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Folder.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.private_conversation_folder.overview_form', ['private_conversation_folder' => $entity->id()]);
  }

}
