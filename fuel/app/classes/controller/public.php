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
        $user_group = $user_group[0][1];


        // Determine current users group
        if ($user_group == 0)
        {
            // Current user is guest, load guest menu
            $this->template->navbar = View::forge("navbar/guest");
        }
        else
        {
            // Current user is blocked, user, power user or admin, check if he has messages
            $query = Model_Orm_Invite::query()->where('recipient_id', $user_id);
            $invites_count = $query->count();

            // check if have demote alerts
            $query = Model_Orm_Alert::query()
                ->where('recipient_id', $user_id)
                ->and_where_open()
                     ->where('type', 'demote')
                ->and_where_close();
            $demote_count = $query->count();

            // check if have promote alerts
            $query = Model_Orm_Alert::query()
                ->where('recipient_id', $user_id)
                ->and_where_open()
                     ->where('type', 'promote')
                ->and_where_close();
            $promote_count = $query->count();
            if ($user_group == 1)
            {
                $this->template->navbar = View::forge("navbar/user");
            }
            elseif ($user_group == -1)
            {
                $this->template->navbar = View::forge("navbar/blocked");
            }
            elseif ($user_group == 10)
            {
                $this->template->navbar = View::forge("navbar/power");
            }
            elseif ($user_group == 100)
            {
                $this->template->navbar = View::forge("navbar/admin");
            }

            // if messages found, show count
            if ( ! is_null($invites_count) and $invites_count != 0)
            {
                $this->template->navbar->set('invites_count', $invites_count);
            }
            // if demote messages found, show them
            if ( ! is_null($demote_count) and $demote_count != 0)
            {
                $this->template->navbar->set('demote_count', $demote_count);
            }
            // if promote messages found, show them
            if ( ! is_null($promote_count) and $promote_count != 0)
            {
                $this->template->navbar->set('promote_count', $promote_count);
            }

            // get users username
            $query = Model_Orm_User::query()->where('user_id', $user_id);
            $username = $query->get_one()->username;
            $this->template->navbar->set('username', $username);
        }
    }
}

?>
