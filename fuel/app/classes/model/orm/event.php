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
        'assistants'
    );
}