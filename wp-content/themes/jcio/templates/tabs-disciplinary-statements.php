<?php

use Roots\Sage\Content\CPT\DisciplinaryStatement;

$basePage = DisciplinaryStatement::getBasePage();
$children = get_children(array(
  'post_parent' => $basePage->ID,
  'post_type' => 'page',
  'post_status' => 'publish',
));

$years = DisciplinaryStatement::getArchiveYears();

function ds_is_current_tab($tab) {
  switch ($tab['type']) {
    case 'page':
      $is = ( get_the_ID() == $tab['page_id'] );
      break;

    case 'year':
      $is = ( get_query_var('disciplinary_stmnt_year') == $tab['year'] );
      break;

    default:
      $is = false;
      break;
  }
  return $is;
}

$tabs = array();
foreach ($years as $year) {
  $tabs[] = array(
    'text' => $year,
    'url' => DisciplinaryStatement::getYearPageUrl($year),
    'type' => 'year',
    'year' => $year,
  );
}
foreach ($children as $child) {
  $tabs[] = array(
    'text' => get_the_title($child),
    'url' => get_the_permalink($child),
    'type' => 'page',
    'page_id' => $child->ID,
  );
}

?>
<ul id="tabs">
  <?php foreach ($tabs as $tab): ?>
    <li<?php if (ds_is_current_tab($tab)) echo ' class="on"'; ?>>
      <a href="<?php echo esc_url($tab['url']); ?>"><?php echo wptexturize($tab['text']); ?></a>
    </li>
  <?php endforeach; ?>
</ul>
