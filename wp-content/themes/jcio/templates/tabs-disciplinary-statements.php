<?php

$basePage = get_page_by_path('disciplinary-statements');
$children = get_children(array(
  'post_parent' => $basePage->ID,
  'post_type' => 'page',
  'post_status' => 'publish',
));

$sql = sprintf("SELECT YEAR(post_date) AS year
                FROM %s
                WHERE post_type = 'disciplinary_stmnt'
                AND post_status = 'publish'
                GROUP BY YEAR(post_date)
                ORDER BY post_date DESC", $wpdb->posts);
$years = $wpdb->get_results($sql);

function ds_year_url($year, $basePage) {
  $baseUrl = get_the_permalink($basePage);
  return sprintf('%s%d/', $baseUrl, $year);
}

function ds_is_current_tab($tab) {
  if ($tab['type'] == 'page') {
    return (get_the_ID() == $tab['page_id']);
  } else {
    return (isset($_GET['ds_year']) && $_GET['ds_year'] == $tab['year']);
  }
}

$tabs = array();
foreach ($years as $year) {
  $tabs[] = array(
    'text' => $year->year,
    'url' => ds_year_url($year->year, $basePage),
    'type' => 'year',
    'year' => $year->year,
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
