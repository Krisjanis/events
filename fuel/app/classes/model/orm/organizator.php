<?php
/**
 * The Organizator Model.
 *
 * Connects to organizators table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_Organizator extends Orm\Model
{
    protected static $_table_name = 'organizators';
    protected static $_primary_key = array('organizator_id');
    protected static $_properties = array(
        'organizator_id',
        'event_id',
        'user_id',
        'is_author'
    );

    protected static $_belongs_to = array(
        'event' => array(
            'key_from'       => 'event_id',
            'model_to'       => 'Model_Orm_Event',
            'key_to'         => 'event_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'user' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_Orm_User',
            'key_to'         => 'user_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        )
    );
}