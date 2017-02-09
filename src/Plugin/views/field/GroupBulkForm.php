<?php

namespace Drupal\private_conversation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\group\Entity\Group;
use Drupal\system\Plugin\views\field\BulkForm;

/**
 * Defines a group operations bulk form element.
 *
 * @ViewsField("group_bulk_form")
 */
class GroupBulkForm extends BulkForm {

  /**
   * {@inheritdoc}
   *
   * Provide a more useful title to improve the accessibility.
   */
  public function viewsForm(&$form, FormStateInterface $form_state) {
    parent::viewsForm($form, $form_state);

    if (!empty($this->view->result)) {
      foreach ($this->view->result as $row_index => $result) {
        $account = $result->_entity;
        if ($account instanceof Group) {
          $form[$this->options['id']][$row_index]['#title'] = $this->t('Update the user %name', array('%name' => $account->label()));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function emptySelectedMessage() {
    return $this->t('No conversations selected.');
  }

}
