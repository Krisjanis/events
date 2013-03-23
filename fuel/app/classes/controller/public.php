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
class Controller_Public extends Controller_Template {

    public function before() {
        parent::before();
        //echo "<br /><br /><br />";

        $auth = Auth::instance();
        $user_id = $auth->get_user_id();
        // Determine current users group
        if ($user_id[1] === 0) {
            // Current user is guest, load guest menu
            $this->template->navbar = View::forge("navbar/guest");
        }
        elseif ($user_id[1] === 1 || $user_id[1] === 10) {
            // Current user is user or power user, load user menu
            $this->template->navbar = View::forge("navbar/user");
        }
        elseif ($user_id[1] === 50) {
            // Current user is moderator, load moderator menu
            $this->template->navbar = View::forge("navbar/guest");
        }
        elseif ($user_id[1] === 50) {
            // Current user is blocked, load bloked users menu
            $this->template->navbar = View::forge("navbar/guest");
        }
    }
}

?>
