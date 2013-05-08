<?php
/**
 * The Alert Model.
 *
 * Connects to alerts table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_Alert extends Orm\Model
{
    protected static $_table_name = 'alerts';
    protected static $_primary_key = array('alert_id');
    protected static $_properties = array(
        'alert_id',
        'recipient_id',
        'type',
        'message'
    );

    protected static $_belongs_to = array(
        'author' => array(
            'key_from'       => 'recipient_id',
            'model_to'       => 'Model_Orm_User',
            'key_to'         => 'user_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        )
    );
}