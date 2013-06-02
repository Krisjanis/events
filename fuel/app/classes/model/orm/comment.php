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

    /**
     * Gets comment object for given events given attribute
     *
     * @param string $event_id is id of event whichs comments are needed
     * @param string $atr is events attribute whichs comments are needed
     * @return Model_Orm_Comment object
     */
    public static function get_comment_by_event_and_attribute($event_id, $atr)
    {
        $query = Model_Orm_Comment::query()
            ->order_by('comment_id', 'desc')
            ->where('event_id', $event_id)
            ->and_where_open()
                ->where('attribute', $atr)
            ->and_where_close();
        return $query->get();
    }


    /**
     * Gets comment object form given ID
     *
     * @param integer $comment_id is ID of comment to be edited
     * @return Model_Orm_Comment object
     */
    public static function get_comment($comment_id)
    {
        $query = Model_Orm_Comment::query()
            ->where('comment_id', $comment_id);
        return $query->get_one();
    }

    /**
     * Gets all comment objects for given event
     *
     * @param string $event_id is id of event whichs comments are needed
     * @return Model_Orm_Comment object
     */
    public static function get_comment_by_event($event_id)
    {
        $query = Model_Orm_Comment::query()->where('event_id', $event_id);
        return $query->get();
    }

    /**
     * Gets all comment objects published by given user
     *
     * @param integer $user_id is ID of user whichs comments ar needed
     * @return Model_Orm_Comment object
     */
    public static function get_comment_by_user($user_id)
    {
        $query = Model_Orm_Comment::query()->where('author_id', $user_id);
        return $query->get();
    }

    public static function get_comment_by_string($string)
    {
        $comment_obj = DB::query("SELECT * FROM `comments` WHERE `message` LIKE '%$string%'")
            ->as_object('Model_Orm_Comment')
            ->execute();
        return $comment_obj;
    }

    /**
     * Gets recenlty added comment objects
     *
     * @return Model_Orm_Comment object
     */
    public static function get_recenty_comments()
    {
        $query = Model_Orm_Comment::query()
            ->order_by('created_at', 'desc')
            ->limit(20);
        return $query->get();
    }

    /**
     * Gets recently eddited comment objects
     *
     * @return Model_Orm_Comment object
     */
    public static function get_recently_edited_comments()
    {
        $query = Model_Orm_Comment::query()
            ->order_by('edited_at', 'desc')
            ->limit(20);
        return $query->get();
    }
}