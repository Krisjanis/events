<?php
/**
 * The Public Controller.
 *
 * Public controller runs before every other controller and determines what
 * group the current user belongs to and sets it menu options in navbar
 *
 * @package  app
 * @extends  Template
 */
class Controller_Public extends Controller_Template
{

    /**
     * Function which initializes before any other function and displays
     * user groups menu
     */
    public function before()
    {
        parent::before();

        $auth = Auth::instance();
        $user_id = $auth->get_user_id();
        $user_id = $user_id[1];
        $user_group = $auth->get_groups();


        // Determine current users group
        if ($user_group[0][1] == 0)
        {
            // Current user is guest, load guest menu
            $this->template->navbar = View::forge("navbar/guest");
        }
        elseif ($user_group[0][1] == 1 || $user_group[0][1] == 10)
        {
            // Current user is user or power user, check if he has messages
            $query = Model_Orm_Invite::query()->where('recipient_id', $user_id);
            $invites_count = $query->count();

            $this->template->navbar = View::forge("navbar/user");
            // if messages found, show count
            if ( ! is_null($invites_count) and $invites_count != 0)
            {
                $this->template->navbar->set('invites_count', $invites_count);
            }

            // get users username
            $query = Model_Orm_User::query()->where('user_id', $user_id);
            $username = $query->get_one()->username;
            $this->template->navbar->set('username', $username);
        }
        elseif ($user_group[0][1] == 100)
        {
             // Current user is moderator, load moderator menu
            $query = Model_Orm_Invite::query()->where('recipient_id', $user_id);
            $invites_count = $query->count();

            $this->template->navbar = View::forge("navbar/admin");
            // if messages found, show count
            if ( ! is_null($invites_count) and $invites_count != 0)
            {
                $this->template->navbar->set('invites_count', $invites_count);
            }

            // get users username
            $query = Model_Orm_User::query()->where('user_id', $user_id);
            $username = $query->get_one()->username;
            $this->template->navbar->set('username', $username);
        }
        elseif ($user_group[0][1] == -1)
        {
            // Current user is blocked, load bloked users menu
            $this->template->navbar = View::forge("navbar/guest");
        }
    }
}

?>
