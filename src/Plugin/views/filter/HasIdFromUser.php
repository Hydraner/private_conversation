<?php

namespace Drupal\private_conversation\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filters all messages that don't belong to a folter of the logged in user
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("has_id_from_user")
 */
class HasIdFromUser extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $field = "$this->tableAlias.$this->realField";

    $uid = \Drupal::currentUser()->id();
    $this->query->addWhere(
      $this->options['group'],
      db_or()
        ->condition($field, NULL, "IS NULL")
        ->condition(
          db_and()
            ->condition($field, NULL, 'IS NOT NULL')
            ->condition('user_id', $uid, '!=')
        )
    );
  }

}
