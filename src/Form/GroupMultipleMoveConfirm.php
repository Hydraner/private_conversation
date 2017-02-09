<?php

namespace Drupal\private_conversation\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a confirmation form for moving conversations to folders.
 */
class GroupMultipleMoveConfirm extends ConfirmFormBase {

  /**
   * Stores the tempstore factory.
   *
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The query object.
   *
   * @var \Drupal\Core\Entity\Query\QueryInterface
   */
  protected $entityQuery;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The source folder entity.
   *
   * @var \Drupal\private_conversation\Entity\PrivateConversationFolderInterface
   */
  protected $sourceFolder;

  /**
   * A list of conversation entities.
   *
   * @var array
   */
  protected $conversations;

  /**
   * Constructs a new GroupMultipleMoveConfirm.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   *   The factory for the temp store object.
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   *   The temp store factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The query object that can query the given entity type.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(
    PrivateTempStoreFactory $temp_store_factory,
    EntityTypeManagerInterface $entity_type_manager,
    QueryFactory $entity_query,
    AccountInterface $current_user
  ) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityQuery = $entity_query;
    $this->currentUser = $current_user;

    $store = $temp_store_factory->get('move_conversation_to_folder')
      ->get($this->currentUser()->id());
    $this->sourceFolder = $store['source_folder'];
    $this->conversations = $store['conversations'];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('entity_type.manager'),
      $container->get('entity.query'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'group_multiple_move_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Move the selected conversation(s) to the following folder.');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.private_conversation_folder.overview_form');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Move conversation(s)');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Cancel early if no conversations exists.
    if (!$this->conversations) {
      return $this->redirect($this->getCancelUrl()->getRouteName());
    }

    $form['conversations'] = [
      '#prefix' => '<ul>',
      '#suffix' => '</ul>',
      '#tree' => TRUE
    ];

    // Fetch all folder's, related to the user.
    $query = $this->entityQuery->get('private_conversation_folder');
    $query->condition('user_id', $this->currentUser->id());
    // Don't fetch the source folder.
    $source_folder_id = isset($this->sourceFolder) ? $this->sourceFolder->id() : 0;
    $query->condition('id', $source_folder_id, '!=');
    $query->sort('weight');
    $folder_ids = $query->execute();

    $folders = $this->entityTypeManager
      ->getStorage('private_conversation_folder')
      ->loadMultiple($folder_ids);

    $options = $source_folder_id != 0 ? [NULL => $this->t('Inbox')] : [];
    foreach ($folders as $folder) {
      $options[$folder->id()] = $folder->label();
    }

    $form['target_folder'] = [
      '#type' => 'select',
      '#title' => $this->t('Folder'),
      '#options' => $options,
    ];

    foreach ($this->conversations as $conversation) {
      $form['conversations'][$conversation->id()] = [
        '#type' => 'hidden',
        '#value' => $conversation->id(),
        '#prefix' => '<li>',
        '#suffix' => $conversation->label() . "</li>\n",
      ];
    }

    $form = parent::buildForm($form, $form_state);
    // Hide the default description.
    unset($form['description']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('confirm')) {
      // Load the selected folder.
      $target_folder_id = $form_state->getValue('target_folder');

      $this->removeRelations();
      $this->clearStoreFactory();

      // If target folder is NULL, we don't need to append a new relation.
      if ($target_folder_id == NULL) {
        return;
      }

      // Load the folder we want the conversations append to.
      $target_folder = $this->entityTypeManager
        ->getStorage('private_conversation_folder')
        ->load($target_folder_id);
      $this->appendRelations($target_folder);

      // Save the target folder.
      $target_folder->save();
    }
  }

  /**
   * Adds conversations to a folder.
   *
   * @param $folder
   *   A folder entity.
   */
  private function appendRelations($folder) {
    foreach ($this->conversations as $id => $conversation_id) {
      $folder->conversations[] = $conversation_id;
    }
  }

  /**
   * Check the conversations and remove existing relations to folders.
   */
  private function removeRelations() {
    if (!empty($this->sourceFolder)) {
      $field = $this->sourceFolder->get('conversations');
      foreach ($this->conversations as $id => $conversation_id) {
        foreach ($field as $key => $item) {
          // Remove the conversation.
          if ($item->target_id == $conversation_id->id()) {
            unset($field[$key]);
          }
        }
        // Save source folder.
        $this->sourceFolder->save();
      }
    }
  }

  /**
   * Clear the tempStoreFactory key we use to store the conversations in.
   */
  private function clearStoreFactory() {
    // Clear out the conversations from the temp store.
    $this->tempStoreFactory
      ->get('move_conversation_to_folder')
      ->delete($this->currentUser->id());
  }

}
