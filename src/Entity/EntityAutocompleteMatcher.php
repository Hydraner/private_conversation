<?php

namespace Drupal\private_conversation\Entity;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Entity\EntityAutocompleteMatcher as SourceEntityAutocompleteMatcher;

/**
 * Matcher class to get autocompletion results for entity reference.
 *
 * Adds new #selection_setting to hide the entity_id of matching entities. This
 * might be useful, if you don't want the enduser to see the entity_id. Caution,
 * this setting requires you, to load the entity yourself!
 */
class EntityAutocompleteMatcher extends SourceEntityAutocompleteMatcher {

  /**
   * {@inheritdoc}
   */
  public function getMatches($target_type, $selection_handler, $selection_settings, $string = '') {
    $matches = array();

    $options = array(
      'target_type' => $target_type,
      'handler' => $selection_handler,
      'handler_settings' => $selection_settings,
    );
    $handler = $this->selectionManager->getInstance($options);

    if (isset($string)) {
      // Get an array of matching entities.
      $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
      $entity_labels = $handler->getReferenceableEntities($string, $match_operator, 10);

      // Loop through the entities and convert them into autocomplete output.
      foreach ($entity_labels as $values) {
        foreach ($values as $entity_id => $label) {
          $key = "$label ($entity_id)";
          // Hide entity id if configured.
          if (isset($selection_settings['hide_entity_id'])) {
            $key = "$label";
          }
          // Strip things like starting/trailing white spaces, line breaks and
          // tags.
          $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));
          // Names containing commas or quotes must be wrapped in quotes.
          $key = Tags::encode($key);
          $matches[] = array('value' => $key, 'label' => $label);
        }
      }
    }

    return $matches;
  }

}
