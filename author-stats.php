<?php
/*
Plugin Name: Author Stats Widget
Plugin URI: https://imtiazrayhan.com/
Description: Displays the number of posts published by site users in a dashboard widget.
Version: 1.0
Author: Imtiaz Rayhan
Author URI: https://imtiazrayhan.com/
License: GPL2
*/

// Add the dashboard widget
function author_stats_widget() {
	wp_add_dashboard_widget(
		"author_stats_widget", 
		"Authors Publishing Stats", 
		"display_author_stats_widget"
	);
}
add_action("wp_dashboard_setup", "author_stats_widget");

// Display the widget content
function display_author_stats_widget() {
	$users = get_users( array( 'role__in' => array( 'author',  ) ) );
	$post_counts = [];
	$current_month = date("n");
	$current_year = date("Y");
	for ($i = 2;$i >= 0;$i--) {
		$month = $current_month - $i;
		$year = $current_year;
		if ($month < 1) {
			$month = 12 + $month;
			$year = $current_year - 1;
		}
		$month_name = date("F", mktime(0, 0, 0, $month, 1, $year));
		foreach ($users as $user) {
			$args = ["author" => $user->ID, "post_type" => "post", "post_status" => "publish", "posts_per_page" => - 1, "monthnum" => $month, "year" => $year, ];
			$query = new WP_Query($args);
			$count = $query->found_posts;
			if (isset($post_counts[$user->display_name][$month_name])) {
				$post_counts[$user->display_name][$month_name] += $count;
			}
			else {
				$post_counts[$user->display_name][$month_name] = $count;
			}
		}
	}
	echo "<style>";
	echo "table.asw-table {border-collapse: collapse; width: 100%;}";
	echo "table.asw-table th, table.asw-table td {padding: 8px; text-align: left; border: 1px solid #ddd;}";
	echo "table.asw-table th {background-color: #f2f2f2;}";
	echo "</style>";
	echo '<table class="asw-table">';
	echo "<tr><th>Author</th><th>" . date("F", mktime(0, 0, 0, $current_month - 2, 1, $current_year)) . "</th><th>" . date("F", mktime(0, 0, 0, $current_month - 1, 1, $current_year)) . "</th><th>" . date("F", mktime(0, 0, 0, $current_month, 1, $current_year)) . "</th></tr>";
	foreach ($post_counts as $name => $counts) {
		echo "<tr><td>" . $name . "</td><td>";
		if (isset($counts[date("F", mktime(0, 0, 0, $current_month - 2, 1, $current_year))])) {
			$month_count = $counts[date("F", mktime(0, 0, 0, $current_month - 2, 1, $current_year))];
			if ($month_count > 0) {
				echo "<a href='" . admin_url("edit.php?author=" . $users[array_search($name, array_column($users, 'display_name'))]->ID . "&m=" . $year . sprintf("%02d", $month - 2)) . "'>" . $month_count . "</a>";
			} else {
				echo "0";
			}
		} else {
			echo "0";
		}
		echo "</td><td>";
		if (isset($counts[date("F", mktime(0, 0, 0, $current_month - 1, 1, $current_year))])) {
			$month_count = $counts[date("F", mktime(0, 0, 0, $current_month - 1, 1, $current_year))];
			if ($month_count > 0) {
				echo "<a href='" . admin_url("edit.php?author=" . $users[array_search($name, array_column($users, 'display_name'))]->ID . "&m=" . $year . sprintf("%02d", $month - 1)) . "'>" . $month_count . "</a>";
			} else {
				echo "0";
			}
		} else {
			echo "0";
		}
		echo "</td><td>";
		if (isset($counts[date("F", mktime(0, 0, 0, $current_month, 1, $current_year))])) {
			$month_count = $counts[date("F", mktime(0, 0, 0, $current_month, 1, $current_year))];
			if ($month_count > 0) {
				echo "<a href='" . admin_url("edit.php?author=" . $users[array_search($name, array_column($users, 'display_name'))]->ID . "&m=" . $year . sprintf("%02d", $month)) . "'>" . $month_count . "</a>";
			} else {
				echo "0";
			}
		} else {
			echo "0";
		}
		echo "</td></tr>";
	}
	
	echo "</table>";
}

