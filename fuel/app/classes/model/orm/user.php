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

    protected static $_has_many = array(
        'organizators' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_Orm_Organizator',
            'key_to'         => 'user_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'senders' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_Orm_Invite',
            'key_to'         => 'sender_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'recipients' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_Orm_Invite',
            'key_to'         => 'recipient_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'authors' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_Orm_Comment',
            'key_to'         => 'author_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        )
    );
}