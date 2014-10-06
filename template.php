<?php
/**
 * @file
 * template.php
 */

/**
 * Implements hook_preprocess_page().
 *
 * @see page.tpl.php
 */
function nimbus_preprocess_page(&$variables) {
  // Hook into color.module.
  if (module_exists('color')) {
    _color_page_alter($variables);
  }
  // Always print the site name and slogan, but if they are toggled off, we'll
  // just hide them visually.
  $variables['hide_site_name']   = theme_get_setting('toggle_name') ? FALSE : TRUE;
  $variables['hide_site_slogan'] = theme_get_setting('toggle_slogan') ? FALSE : TRUE;
  if ($variables['hide_site_name']) {
    // If toggle_name is FALSE, the site_name will be empty, so we rebuild it.
    $variables['site_name'] = filter_xss_admin(variable_get('site_name', 'Drupal'));
  }
  if ($variables['hide_site_slogan']) {
    // If toggle_site_slogan is FALSE, the site_slogan will be empty, so we rebuild it.
    $variables['site_slogan'] = filter_xss_admin(variable_get('site_slogan', ''));
  }
  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut']) && $variables['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render elements.
    $variables['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $variables['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $variables['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;
  }
  
  $form = drupal_get_form('search_form');
  $search_box = drupal_render($form);
  $variables['nimbus_search_box'] = $search_box;
}

/**
 * Implements hook_preprocess_node().
 *
 * @see node.tpl.php
 */
function nimbus_preprocess_node(&$variables) {
  $node = $variables['node'];
  
  $about_me = '';
  if ($node->uid) {
    $account = user_load($node->uid);
    $about_me = $account->field_profile_about_me['und'][0]['safe_value'];
  }
  
  // Display post information only on certain node types.
  if (variable_get('node_submitted_' . $node->type, TRUE)) {
    $variables['display_submitted'] = TRUE;
    $variables['submitted'] = t('Posted by !username on !datetime', array('!username' => $variables['name'], '!datetime' => $variables['date']));
    $variables['user_picture'] = theme_get_setting('toggle_node_user_picture') ? theme('user_picture', array('account' => $node)) : '';
    $variables['author'] = t('Posted by !username', array('!username' => $variables['name']));
    $variables['about_me'] = $about_me;
  }
  else {
    $variables['display_submitted'] = FALSE;
    $variables['submitted'] = '';
    $variables['user_picture'] = '';
  }
}

/**
 * Implements hook_preprocess_region().
 */
function nimbus_preprocess_region(&$variables) {
  
  switch ($variables['region']) {
    // @todo is this actually used properly?
    case 'footer_firstcolumn':
      $variables['classes_array'][] = 'col-md-3';
      break;
    
    case 'footer_secondcolumn':
      $variables['classes_array'][] = 'col-md-3';
      break;
    
    case 'footer_thirdcolumn':
      $variables['classes_array'][] = 'col-md-3';
      break;
    
    case 'footer_fourthcolumn':
      $variables['classes_array'][] = 'col-md-3';
      break;
    
    case 'triptych_first':
      $variables['classes_array'][] = 'col-md-4';
      break;
    
    case 'triptych_middle':
      $variables['classes_array'][] = 'col-md-4';
      break;
    
    case 'triptych_last':
      $variables['classes_array'][] = 'col-md-4';
      break;
  }
}

/**
 * Impelements hook_field().
 * 
 * @param type $variables
 */
function nimbus_field__field_tags(&$variables) {
  $output = '';
  
  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<div class="field-label"' . $variables['title_attributes'] . '>' . $variables['label'] . ':&nbsp;</div>';
  }
  
  // Render the items.
  foreach ($variables['items'] as $delta => $item) {
    $output .= '<span><i class="glyphicon glyphicon-tag"> </i> ' . drupal_render($item) . '</span>';
  }
  
  // Render the top-level DIV.
  $output = '<span class="' . $variables['classes'] . '"' . $variables['attributes'] . '>' . $output . '</span>';
  
  return $output;
}

/**
 * Implements hook_preprocess_comment().
 *
 * @see comment.tpl.php
 */
function nimbus_preprocess_comment(&$variables) {
  $variables['submitted'] = t('Posted by !username on !datetime', array('!username' => $variables['author'], '!datetime' => $variables['created']));
}

/**
 * Bootstrap theme wrapper function for the primary menu links.
 */
function nimbus_menu_tree__primary(&$variables) {
  return '<ul id="primary-menu" class="menu nav navbar-nav pull-right">' . $variables['tree'] . '</ul>';
}
