<?php
/**
 * Implements hook_views_data_alter().
 */
function views_addfeildplugin_views_data_alter(array &$data) {
  $data['node']['parent_taxonomy'] = array(
    'title' => t('parenttaxonomy'),
    'field' => array(
      'title' => t('Parent Taxonomy'),
      'help' => t('To add parent taxnomy'),
      'id' => 'parent_taxonomy',
    ),
  );
}