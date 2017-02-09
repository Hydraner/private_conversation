<?php

namespace Drupal\private_conversation\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Folder entity.
 *
 * @ingroup private_conversation
 *
 * @ContentEntityType(
 *   id = "private_conversation_folder",
 *   label = @Translation("Folder"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\private_conversation\Entity\PrivateConversationFolderViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\private_conversation\Form\PrivateConversationFolderForm",
 *       "add" = "Drupal\private_conversation\Form\PrivateConversationFolderForm",
 *       "edit" = "Drupal\private_conversation\Form\PrivateConversationFolderForm",
 *       "delete" = "Drupal\private_conversation\Form\PrivateConversationFolderDeleteForm",
 *     },
 *     "access" = "Drupal\private_conversation\PrivateConversationFolderAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\private_conversation\PrivateConversationFolderHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "private_conversation_folder",
 *   admin_permission = "administer folder entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "conversations" = "conversations",
 *   },
 *   links = {
 *     "canonical" = "/conversations/folder/{private_conversation_folder}",
 *     "add-form" = "/conversations/folder/add",
 *     "edit-form" = "/conversations/folder/{private_conversation_folder}/edit",
 *     "delete-form" = "/conversations/folder/{private_conversation_folder}/delete",
 *   },
 *   field_ui_base_route = "private_conversation_folder.settings"
 * )
 */
class PrivateConversationFolder extends ContentEntityBase implements PrivateConversationFolderInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->get('weight')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->set('weight', $weight);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Folder entity.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default');

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Folder.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['weight'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Weight'))
      ->setDescription(t('The weight of this term in relation to other terms.'))
      ->setDefaultValue(0);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
