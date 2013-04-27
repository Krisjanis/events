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
}