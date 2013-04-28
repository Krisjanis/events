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
}