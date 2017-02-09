<?php

namespace Drupal\private_conversation\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\String;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides folder overview form on a per user basis.
 */
class OverviewFolder extends FormBase {

  /**
   * Folder entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $folderManager;

  /**
   * Constructs a \Drupal\forum\Form\OverviewForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->folderManager = $entity_type_manager->getStorage('private_conversation_folder');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'folder_overview';
  }

  /**
   * Build custom tabledrag table.
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Load the users folders.
    $folder_ids = $this->folderManager->getQuery()
      ->condition('user_id', \Drupal::currentUser()->id())
      ->sort('weight')
      ->execute();

    $entities = $this->folderManager->loadMultiple($folder_ids);

    $form['folder_list'] = [
      '#type' => 'table',
      '#header' => [t('Label'), t('Weight'), t('Operations')],
      '#empty' => t('There are no folders yet. Add a folder.', [
        '@add-url' => Url::fromRoute('entity.private_conversation_folder.add_form'),
      ]),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'folder-list-order-weight',
        ],
      ],
    ];

    foreach ($entities as $id => $entity) {
      // TableDrag: Mark the table row as draggable.
      $form['folder_list'][$id]['#attributes']['class'][] = 'draggable';
      // TableDrag: Sort the table row according to its existing/configured weight.
      $form['folder_list'][$id]['#weight'] = $entity->get('weight')->value;

      $form['folder_list'][$id]['label'] = [
        '#plain_text' => $entity->label(),
      ];

      // TableDrag: Weight column element.
      $form['folder_list'][$id]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => $entity->label()]),
        '#title_display' => 'invisible',
        '#default_value' => $entity->get('weight')->value,
        // Classify the weight element for #tabledrag.
        '#attributes' => ['class' => ['folder-list-order-weight']],
      ];

      // Operations (dropbutton) column.
      $form['folder_list'][$id]['operations'] = [
        '#type' => 'operations',
        '#links' => []
      ];
      $form['folder_list'][$id]['operations']['#links']['edit'] = [
        'title' => t('Edit'),
        'url' => Url::fromRoute('entity.private_conversation_folder.edit_form', ['private_conversation_folder' => $id]),
      ];
      $form['folder_list'][$id]['operations']['#links']['delete'] = [
        'title' => t('Delete'),
        'url' => Url::fromRoute('entity.private_conversation_folder.delete_form', ['private_conversation_folder' => $id]),
      ];
    }
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save changes'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues()['folder_list'] as $folder_id => $weight) {
      $folder = $this->folderManager->load($folder_id);
      $folder->setWeight($weight['weight']);
      $folder->save();
    }
  }
}
