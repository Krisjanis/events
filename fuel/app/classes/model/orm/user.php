<?php

class Model_Orm_User extends Orm\Model
{
    protected static $_table_name = 'users';
    protected static $_primary_key = array('user_id');
    protected static $_properties = array(
        'user_id',
        'username',
        'name',
        'surname',
        'password',
        'email',
        'last_login',
        'login_hash',
        'profile_fields',
        'created_at',
        'group'
    );

    public static function validate($factory) {
        $val = Validation::forge($factory);
        $val->add_field('username', 'Lietotājvārds', 'required|min_length[3]|max_length[50]');
        $val->add_field('name', 'Lietotāja vārds', 'required|min_length[3]|max_length[50]');
        $val->add_field('surname', 'Lietotāja uzvārds', 'required|min_length[3]|max_length[50]');
        $val->add_field('email', 'E-pasts', 'required|valid_email');
        $val->add_field('password', 'Parole', 'required|min_length[6]');
        $val->add_field('password_rep', 'Parole atkārtoti', 'required|min_length[6]|match_field[password]');
        return $val;
    }
}