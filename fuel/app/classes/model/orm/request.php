<?php
/**
 * The Request Model.
 *
 * Connects to request table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_Request extends Orm\Model
{
    protected static $_table_name = 'requests';
    protected static $_primary_key = array('request_id');
    protected static $_properties = array(
        'request_id',
        'sender_id',
        'event_id'
    );

    protected static $_belongs_to = array(
        'sender' => array(
            'key_from'       => 'sender_id',
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