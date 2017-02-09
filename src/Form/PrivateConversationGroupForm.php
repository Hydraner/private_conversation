<?php

namespace Drupal\private_conversation\Form;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Form\FormStateInterface;
use Drupal\group\Entity\Form\GroupForm;

/**
 * Form controller for the group edit forms.
 *
 * @ingroup group
 */
class PrivateConversationGroupForm extends GroupForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    if ($this->entity->bundle() == 'private_conversation') {
      $form['label']['widget'][0]['value']['#title'] = $this->t('Subject');

      $recipients = '';
      if (!$this->entity->isNew()) {
        // Create list of members.
        $members = $this->entity->getMembers();
        foreach ($members as $delta => $member_relation) {
          $user = $member_relation->getGroupContent()->getEntity();
          if ($user->id() != \Drupal::currentUser()->id()) {
            $recipients .= $user->getUsername() . ', ';
          }
        }
        $recipients = rtrim($recipients, ", ");
      }

      $form['recipients'] = [
        '#title' => 'Recipients',
        '#type' => 'entity_autocomplete',
        '#target_type' => 'user',
        '#selection_settings' => [
          'include_anonymous' => FALSE,
          'hide_entity_id' => TRUE
        ],
        '#value' => $recipients,
        '#validate_reference' => FALSE,
        '#element_validate' => [],
      ];

      // Only show the message field when creating a new conversation.
      if ($this->entity->isNew()) {
        $form['message'] = [
          '#title' => 'Message',
          '#type' => 'text_format',
        ];
      }

      // Hide comment settings.
      $form['private_conversation_thread']['#access'] = FALSE;

      // Hide path settings.
      $form['path']['#access'] = FALSE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // Check if we need to create a comment.
    $save_comment = FALSE;
    if ($this->entity->bundle() == 'private_conversation' && $this->entity->isNew()) {
      $save_comment = TRUE;
    }

    // We call the parent function first so the entity is saved. We can then
    // read out its ID and redirect to the canonical route.
    $return = parent::save($form, $form_state);

    if ($this->entity->bundle() == 'private_conversation') {
      $group = $this->entity;

      // Get recipients.
      $names = explode(',', $form_state->getUserInput()['recipients']);
      $names = array_map('trim', $names);

      // Get current memberships.
      $recipients = [];
      foreach ($group->getMembers() as $delta => $member_relation) {
        $user = $member_relation->getGroupContent()->getEntity();
        if ($user->id() != \Drupal::currentUser()->id()) {
          $recipients[] = $user->getUsername();
        }
      }

      // Diff to see what we need to change.
      $diff = array_merge(array_diff($recipients, $names), array_diff($names, $recipients));
      foreach ($diff as $key => $name) {
        $user = user_load_by_name($name);
        if ($user && $user->id() != 0) {
          // If the user is already a member, we can assume it has to be
          // removed, otherwise we add a new Membership.
          if (!$group->getMember($user)) {
            $group->addMember($user);
          }
          else {
            $membership = $group->getMember($user);
            entity_delete_multiple($membership->getGroupContent()
              ->getEntityTypeId(), [$membership->getGroupContent()->id()]);
          }
        }
      }

      // Create a comment.
      if ($save_comment) {
        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $comment = Comment::create(array(
          'comment_type' => 'private_conversation_thread',
          'langcode' => $language,
          'entity_id' => $group->id(),
          // Target entity_type-
          'entity_type' => 'group',
          'uid' => \Drupal::currentUser()->id(),
          'subject' => $form_state->getValue('label'),
          'status' => 1,
          'pid' => 0,
          'field_name' => 'private_conversation_thread',
          'comment_body' => [
            'summary' => '',
            'value' => '<p>' . $form_state->getValue('message') . '</p>',
            'format' => 'basic_html',
          ],
          // 'field_msg_read' is a custom field that shows that a message was read or not.
          'field_msg_read' => [
            'value' => 0,
          ],

        ));

        // Save the comment.
        $comment->save();
      }

      // Generate path alias with UUID.
      $uuid = $group->get('uuid')->value;
      $group->set('path', "/conversations/$uuid");
      $group->save();
    }

    return $return;
  }

}
