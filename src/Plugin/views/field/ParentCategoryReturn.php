<?php
/**
 * @file
 * Contains \Drupal\views_addfeildplugin\Plugin\views\field\ParentCategoryReturn.
 */

namespace Drupal\views_addfeildplugin\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\node\Entity\Node;
use Drupal\Core\Config\Entity;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Field;
use Symfony\Component\HttpFoundation;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("parent_taxonomy")
 */
class ParentCategoryReturn extends FieldPluginBase {
  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['node_type'] = array('default' => 'article');

    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $types = NodeType::loadMultiple();
    $options = [];
    foreach ($types as $key => $type) {
      $options[$key] = $type->label();
    }

    $form['node_type'] = array(
      '#title' => $this->t('Which node type should be flagged?'),
      '#type' => 'select',
      '#default_value' => $this->options['node_type'],
      '#options' => $options,
    );

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $values->_entity;
    if ($node->bundle() == $this->options['node_type']) {
      return $this->categoryParent($node->id());
    }
    else {
      return $this->t('Hey, I\'m something else.');
    }
  }


  /**
   * @param $nodeid
   * @return \Drupal\Core\GeneratedLink
   */
  public function categoryParent($nodeid) {
    $nodes = Node::load($nodeid);
    $term = array();
    $tid = array();
    if(empty($nodes->field_category->getValue())){
      return NULL;
    }
    else {
      foreach ($nodes->field_category->getValue() as $cat) {
        if (!empty(token_taxonomy_term_load_all_parents($cat['target_id']))) {
          $parent_term = token_taxonomy_term_load_all_parents($cat['target_id']);
          $term = array_shift(array_values($parent_term));
          $tid = array_shift(array_keys($parent_term));
        }
      }
    }
    $url = Url::fromUri('internal:/taxonomy/term/' . $tid);
    $link = Link::fromTextAndUrl($term, $url)->toString();
    return $link;
  }
}
