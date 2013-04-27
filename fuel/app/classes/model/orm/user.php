<?php
/**
 * The User Model.
 *
 * Connects to users table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_User extends Orm\Model
{
    protected static $_table_name = 'users';
    protected static $_primary_key = array('user_id');
    protected static $_properties = array(
        'user_id',
        'id',
        'username',
        'name',
        'surname',
        'password',
        'email',
        'last_login',
        'login_hash',
        'profile_fields',
        'created_at',
        'group'
    );
}