<?php
/**
 * Frontend sidebar content
 *
 * @package    User Profiles
 * @subpackage Views
 * @category   Frontend
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	plugin,
	active_widgets
};

// Get enabled widgets array, sorted.
$active = active_widgets();
$sort   = plugin()->sb_widgets_sort();
if ( ! empty( $sort ) ) {
	$order = explode( ',', $sort );
	$list  = array_replace( array_flip( $order ), $active );
} else {
	$list = $active;
}

// Get widget templates for each in sorted order.
foreach ( $list as $widget => $name ) {
	if ( ! array_key_exists( $widget, $active ) ) {
		continue;
	}
	include( $this->phpPath() . '/views/widget-' . $widget . '.php' );
}
