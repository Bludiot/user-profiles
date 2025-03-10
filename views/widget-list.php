<?php
/**
 * Users list widget
 *
 * @package    User Profiles
 * @subpackage Views
 * @category   Frontend
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	sidebar_users_list
};

echo sidebar_users_list();
