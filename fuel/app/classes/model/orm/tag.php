<?php
/**
 * The Tag Model.
 *
 * Connects to tags table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_Tag extends Orm\Model
{
    protected static $_table_name = 'tags';
    protected static $_primary_key = array('tag_id');
    protected static $_properties = array(
        'tag_id',
        'author_id',
        'title',
        'event_count'
    );

    protected static $_belongs_to = array(
        'author' => array(
            'key_from'       => 'author_id',
            'model_to'       => 'Model_Orm_User',
            'key_to'         => 'user_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        )
    );

    protected static $_has_many = array(
        'events' => array(
            'key_from'       => 'tag_id',
            'model_to'       => 'Model_Orm_hasTag',
            'key_to'         => 'tag_id',
            'cascade_save'   => true,
            'cascade_delete' => false
        )
    );
}