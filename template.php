<?php
/**
 * @file
 * Preprocess and Process Functions.
 */

/**
 * Override or insert variables into the html template.
 */
function loop_preprocess_html(&$variables) {
}

/**
 * Override or insert variables into the page template.
 */
function loop_preprocess_page(&$variables) {
  // Prepare system search block for page.tpl.
  $variables['search'] = module_invoke('search', 'block_view', 'form');

  if ( (arg(0) == 'search') && (!isset($variables['page']['no_result'])) ) {
    // No search results, change title.
    $variables['title'] = t('Ask question');
  }

  // Load LOOP primary menu.
  if (module_exists('loop_navigation')) {
    $variables['loop_primary_menu'] = module_invoke('menu', 'block_view', 'menu-loop-primary-menu');
  }

  // Check if we are using a panel page to define layout.
  $variables['no_panel'] = FALSE;
  $panel = panels_get_current_page_display();

  if(empty($panel)) {
    $variables['no_panel'] = TRUE;
  }
}


/**
 * Override or insert variables into the region template.
 */
function loop_preprocess_region(&$variables) {
}

/**
 * Override or insert variables into the node template.
 */
function loop_preprocess_node(&$variables) {
}

/**
 * Override or insert variables into the field template.
 */
function loop_preprocess_field(&$variables) {
}

/**
 * Override or insert variables into the block template.
 */
function loop_preprocess_block(&$variables) {
}

/**
 * Override or insert variables into the panel pane template.
 */
function loop_preprocess_panels_pane(&$variables) {
}

/**
 * Implements template_preprocess_search_results().
 */
function loop_apachesolr_search_page_alter(&$build, $search_page) {
  if (!isset($build['search_results']['#results'])) {
    // No hits. Send formular to template.
    module_load_include('inc', 'node', 'node.pages');
    $node = new stdClass();
    $node->type = 'post';

    // Add no search results message.
    $build['no_result'] = array(
      '#prefix' => '<div class="loop-no-search-results">',
      '#suffix' => '</div>',
      '#markup' => t('No search results found!'),
    );

    // Add the post.
    $node->field_description['und'][0]['value'] = arg(2);
    $form = drupal_get_form('node_form', $node);
    $build['form'] = $form;

    // Remove suggestions and other related information.
    unset($build['search_form']);
    unset($build['suggestions']);
    unset($build['search_results']);
  }
}

// Main menu
function loop_menu_tree__menu_block__1($variables) {
  return $variables['tree'];
}

// Secondary menu
function loop_menu_tree__menu_block__2($variables) {
  return $variables['tree'];
}

/**
 * Implements theme_menu_link().
 */
function loop_menu_link($variables) {
  $element = $variables['element'];

  // Dropdown menu.
  if ($element['#theme'][0] == 'menu_link__menu_block__1') {
    return _loop_menu_styling($variables, 'nav-dropdown--link', 'nav-dropdown--header', 'nav-dropdown--item');
  }

  // Main menu.
  if ($element['#theme'][0]  == 'menu_link__menu_block__2') {
    return _loop_menu_styling($variables, 'nav--link');
  }
}

/**
 * Helper function for menu blocks
 */
function _loop_menu_styling($variables, $class, $nolink_class = FALSE, $below_class = FALSE, $icon = FALSE, $span = FALSE) {
  $element = $variables['element'];

  $sub_menu = '';

  // Check if <nolink> is present (used for parent menu items).
  if ($element['#href'] == '<nolink>') {
    // Add header class to parent item.
    $element['#localized_options']['attributes']['class'][] = $nolink_class;
    // <img src="/profiles/loopdk/themes/loop/images/nav-arrow-down-icon.png" class="nav-dropdown--icon">

    if (isset($element['#below'])) {
      // Add a wrapper class.
      if (isset($below_class)) {
        $sub_menu = '<div class="' . $below_class . '">' . drupal_render($element['#below']) . '</div>';
      }
      else {
        $sub_menu = drupal_render($element['#below']);
      }
    }
  }
  else {
    // Add default class to a tag.
    $element['#localized_options']['attributes']['class'][] = $class;
  }

  // Make sure text string is treated as html by l function.
  $element['#localized_options']['html'] = true;

  $output = l($element['#title'], $element['#href'], $element['#localized_options']);

  return (($span)?'<span>':'') . $output . (($span)?'</span>':'') . $sub_menu . "\n";
}


/**
 * Returns HTML for a wrapper for a menu sub-tree.
 *
 * Cleans up markup for main menu.
 */

/**
 * Returns HTML for a menu link and submenu.
 *
 * Cleans up markup for main menu.
 * And insert icons in front of spcific menu items.
 */

/**
 * Returns HTML for a wrapper for a menu sub-tree.
 *
 * Cleans up markup for Loop primary menu.
 */
function loop_menu_tree__menu_loop_primary_menu($variables) {
  return $variables['tree'];
}

/**
 * Returns HTML for a menu link and submenu.
 *
 * Cleans up markup for Loop primary menu.
 */
function loop_menu_link__menu_loop_primary_menu($variables) {
  $element = $variables['element'];
  $element['#attributes']['class'][] = 'nav-mobile--link';
  $element['#localized_options']['attributes']['class'] = $element['#attributes']['class'];
  // Make sure text string is treated as html by l function.
  $element['#localized_options']['html'] = TRUE;

  $output_title = '<span class="nav-mobile--text">' . $element['#title'] . '</span>';

  $output = l($output_title, $element['#href'], $element['#localized_options']);
  return $output;
}

/**
 * Returns HTML for a fieldset form element and its children.
 *
 * Changes the class added to fieldsets, so it differs from the wrapper added inside.
 */
function loop_fieldset($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id'));
  _form_set_class($element, array('field-group-fieldset'));

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    // Always wrap fieldset legends in a SPAN for CSS positioning.
    $output .= '<legend><span class="fieldset-legend">' . $element['#title'] . '</span></legend>';
  }
  $output .= '<div class="fieldset-wrapper">';
  if (!empty($element['#description'])) {
    $output .= '<div class="fieldset-description">' . $element['#description'] . '</div>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= '</div>';
  $output .= "</fieldset>\n";
  return $output;
}
