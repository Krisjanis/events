<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.5
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(

	/**
	 * DB connection, leave null to use default
	 */
	'db_connection' => null,

	/**
	 * DB table name for the user table
	 */
	'table_name' => 'users',

	/**
	 * Choose which columns are selected, must include: username, password, email, last_login,
	 * login_hash, group & profile_fields
	 */
	'table_columns' => array('*'),

	/**
	 * This will allow you to use the group & acl driver for non-logged in users
	 */
	'guest_login' => true,

	/**
	 * Groups as id => array(name => <string>, roles => <array>)
	 */
	'groups' => array(
		 -1   => array('name' => 'Bloķēts', 'roles' => array('blocked')),
		 0    => array('name' => 'Viesis', 'roles' => array()),
		 1    => array('name' => 'Lietotājs', 'roles' => array('user')),
		 10   => array('name' => 'Prasmīgs lietotājs', 'roles' => array('user', 'power user')),
		 100   => array('name' => 'Operātors', 'roles' => array('user', 'moderator')),
		 
	),

	/**
	 * Roles as name => array(location => rights)
	 */
	'roles' => array(
		/**
		 * Examples
		 * ---
		 *
		 * Regular example with role "user" given create & read rights on "comments":
		 *   'user'  => array('comments' => array('create', 'read')),
		 * And similar additional rights for moderators:
		 *   'moderator'  => array('comments' => array('update', 'delete')),
		 *
		 * Wildcard # role (auto assigned to all groups):
		 *   '#'  => array('website' => array('read'))
		 *
		 * Global disallow by assigning false to a role:
		 *   'banned' => false,
		 *
		 * Global allow by assigning true to a role (use with care!):
		 *   'super' => true,
		 */
		 'user'  => array(
			'tag' => array('edit'),
			'user' => array('edit'),
			'event' => array('create', 'edit_attribute', 'change_to_public'),
			'comment' => array('create', 'delete'),
			'participant' => array('add_organizator', 'accept_invite', 'delete', 'request', 'decline_request', 'accpet_request', 'email')
		 ),
		 'power user' =>array(
			'tag' => array('create')
		 ),
		 'moderator'  => array(
			'admin' => array('event', 'block_event', 'delete_event', 'user', 'unblock_user', 'block_user', 'power_user', 'demote_power_user', 'delete_user', 'demote_admin', 'block_user', 'comment', 'block_comment', 'delete_comment', 'tag', 'tag_create', 'demote_tag', 'delete_tag')
         )
	),

	/**
	 * Salt for the login hash
	 */
	'login_hash_salt' => 'event_salt',

	/**
	 * $_POST key for login username
	 */
	'username_post_key' => 'username',

	/**
	 * $_POST key for login password
	 */
	'password_post_key' => 'password',
);
