<?php

namespace Drupal\private_conversation\Plugin\views\field;

use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\comment\CommentManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * A handler to show an indicator if a comment is new.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("comment_is_new")
 */
class CommentIsNew extends FieldPluginBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The comment manager service.
   *
   * @var \Drupal\comment\CommentManagerInterface
   */
  protected $commentManager;

  /**
   * Constructs a PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user.
   *   The current user.
   * @param \Drupal\comment\CommentManagerInterface $comment_manager.
   *   The comment manager service.
   * @param \Drupal\Core\Database\Connection $connection
   *   The current database connection.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountInterface $current_user,
    CommentManagerInterface $comment_manager,
    Connection $connection
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentUser = $current_user;
    $this->commentManager = $comment_manager;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('comment.manager'),
      $container->get('database'),
      $container->get('history.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
//    $topic = $values->_entity;
//    $history = $this->lastVisit($topic->id(), $this->currentUser);
//    // @todo: Implement history. @see https://www.drupal.org/node/2081585
//    $is_new = $this->commentManager->getCountNewComments($topic, 'private_conversation_thread', $history);
    $debug = 1;
//
//
//    // A forum is new if the topic is new, or if there are new comments since
//    // the user's last visit.
//    $topic->new = 0;
//    $topic->last_comment_timestamp = $values->comment_entity_statistics_comment_count;
//    $topic->new_replies = $this->commentManager->getCountNewComments($topic, 'comment_forum', $history);
//    if ($this->currentUser->isAuthenticated()) {
//      $topic->new = $topic->new_replies || ($topic->last_comment_timestamp > $history);
//    }

    return [
    ];
  }

  /**
   * Gets the last time the user viewed a node.
   *
   * @param int $nid
   *   The node ID.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Account to fetch last time for.
   *
   * @return int
   *   The timestamp when the user last viewed this node, if the user has
   *   previously viewed the node; otherwise HISTORY_READ_LIMIT.
   */
//  protected function lastVisit($nid, AccountInterface $account) {
//    if (empty($this->history[$nid])) {
//      $result = $this->connection->select('history', 'h')
//        ->fields('h', array('nid', 'timestamp'))
//        ->condition('uid', $account->id())
//        ->execute();
//      foreach ($result as $t) {
//        $this->history[$t->nid] = $t->timestamp > HISTORY_READ_LIMIT ? $t->timestamp : HISTORY_READ_LIMIT;
//      }
//    }
//    return isset($this->history[$nid]) ? $this->history[$nid] : HISTORY_READ_LIMIT;
//  }

}
