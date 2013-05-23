<?php
/**
 * The Event Model.
 *
 * Connects to events table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_Event extends Orm\Model
{
    protected static $_table_name = 'events';
    protected static $_primary_key = array('event_id');
    protected static $_properties = array(
        'event_id',
        'type',
        'title',
        'description',
        'location',
        'date',
        'participants_min',
        'participants_max',
        'entry_fee',
        'takeaway',
        'dress_code',
        'assistants',
        'created_at'
    );

    protected static $_has_many = array(
        'organizators' => array(
            'key_from'       => 'event_id',
            'model_to'       => 'Model_Orm_Organizator',
            'key_to'         => 'event_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'invites' => array(
            'key_from'       => 'event_id',
            'model_to'       => 'Model_Orm_Invite',
            'key_to'         => 'event_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'comments' => array(
            'key_from'       => 'event_id',
            'model_to'       => 'Model_Orm_Comment',
            'key_to'         => 'event_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        ),
        'tags' => array(
            'key_from'       => 'event_id',
            'model_to'       => 'Model_Orm_hasTag',
            'key_to'         => 'event_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        )
    );
}