<?php
/**
 * The Comment Model.
 *
 * Connects to comments table in database
 *
 * @package  app
 * @extends  Orm\Model
 */
class Model_Orm_Comment extends Orm\Model
{
    protected static $_table_name = 'comments';
    protected static $_primary_key = array('comment_id');
    protected static $_properties = array(
        'comment_id',
        'author_id',
        'event_id',
        'attribute',
        'message',
        'created_at',
        'edited_at'
    );

    protected static $_belongs_to = array(
        'author' => array(
            'key_from'       => 'author_id',
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