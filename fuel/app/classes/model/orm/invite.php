<?php
/**
 * The Invite Model.
 *
 * Connects to invites table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_Invite extends Orm\Model
{
    protected static $_table_name = 'invites';
    protected static $_primary_key = array('invite_id');
    protected static $_properties = array(
        'invite_id',
        'sender_id',
        'recipient_id',
        'event_id',
        'email',
        'message'
    );

    protected static $_belongs_to = array(
        'sender' => array(
            'key_from'       => 'sender_id',
            'model_to'       => 'Model_Orm_User',
            'key_to'         => 'user_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'recipient' => array(
            'key_from'       => 'recipient_id',
            'model_to'       => 'Model_Orm_User',
            'key_to'         => 'user_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'event' => array(
            'key_from'       => 'event_id',
            'model_to'       => 'Model_Orm_Event',
            'key_to'         => 'event_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        )
    );
}