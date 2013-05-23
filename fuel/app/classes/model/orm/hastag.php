<?php
/**
 * The Tag - Event relation Model.
 *
 * Connects to has_tag table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_HasTag extends Orm\Model
{
    protected static $_table_name = 'has_tag';
    protected static $_primary_key = array('tag_id', 'event_id');
    protected static $_properties = array(
        'tag_id',
        'event_id'
    );

    protected static $_belongs_to = array(
        'tag' => array(
            'key_from'       => 'tag_id',
            'model_to'       => 'Model_Orm_Tag',
            'key_to'         => 'tag_id',
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