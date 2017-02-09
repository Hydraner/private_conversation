<?php

/**
 * @file
 * Provide views data for the private_conversation module.
 */

/**
 * Implements hook_views_data_alter().
 */
function private_conversation_views_data_alter(array &$data) {
  $debug = 1;
  // Add custom filter.
  $data['private_conversation_folder']['has_id_from_user'] = [
    'title' => t('Has id from user'),
    'filter' => [
      'title' => t('Has id from user'),
      'help' => t('The folder id is not null and belongs to the logged in user.'),
      'field' => 'id',
      'id' => 'has_id_from_user'
    ],
  ];

  // Add custom argument.
  $data['private_conversation_folder']['id']['argument'] = [
    'id' => 'folder_id',
    'numeric' => TRUE,
  ];

  // Add bulk operations for groups.
  $data['groups']['group_bulk_form'] = [
    'title' => t('Bulk update'),
    'help' => t('Text, todo: better description.'),
    'field' => [
      'id' => 'group_bulk_form',
    ],
  ];

  // Add comment is new field to comments.
  // @todo: Make this work.
  $data['comment_field_data']['comment_is_new'] = [
    'title' => t('Comment is new'),
    'field' => [
      'title' => t('Comment is new'),
      'help' => t('Indicates whether a comment is new.'),
      'id' => 'comment_is_new',
    ],
  ];

}
