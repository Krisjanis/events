<?php
/**
 * The User Controller.
 *
 * Has function for account creating, account delete, user login, user logout
 *
 * @package  app
 * @extends  Public
 */
class Controller_User extends Controller_Public
{
    /**
     * View users onw profile or some other user profile
     *
     * @param integer $user_id is ID of user which profile is needed to be viewed
     */
    public function action_view($user_id = null)
    {
        $onwer_access = false;
        $auth = Auth::instance();
        $user_group_id = $auth->get_groups();

        // if no user id is given, view users own profile, if quest, register
        if (is_null($user_id))
        {
            if ($user_group_id[0][1] == 0)
            {
                // if guest, show register form
                $error[] = 'Piedod, bet kaut kāda iemesla dēļ mums tevi bija jāatslēdz no profila, mēģini vēlreiz!';
                Session::set_flash('errors', $error);

                Response::redirect('user/login');
            }
            else
            {
                // registerred user, view users own profile
                $user_id = Auth::instance()->get_user_id();
                $user_id = $user_id[1];
                $onwer_access = true;
            }
        }

        // find needed user
        $exist_username = Model_Orm_User::find($user_id);

        // if no user found, view user own profile, or register if guest
        if (empty($exist_username))
        {
            if ($user_group_id[0][1] == 0)
            {
                // if guest, show register form
                $error[] = 'Piedod, bet neatradām šādu lietotāju!';
                Session::set_flash('errors', $error);

                Response::redirect('user/create');
            }
            else
            {
                // user not found
                $error[] = 'Piedod, bet neatradām šādu lietotāju!';
                Session::set_flash('errors', $error);

                Response::redirect('/');
            }
        }

        // user profile found, show it
        $user = array(
            'id'        => $exist_username->user_id,
            'username'  => $exist_username->username,
            'email'     => $exist_username->email,
            'name'      => $exist_username->name,
            'surname'   => $exist_username->surname
        );

        // check if user is author in any events
        $query = Model_Orm_Participant::query()
            ->where('user_id', $user_id)
            ->and_where_open()
                ->where('role', 10)
            ->and_where_close();
        $author_obj = $query->get();

        $event_author = array();
        $i = 0;
        foreach ($author_obj as $author)
        {
            $query = Model_Orm_Event::query()->where('event_id', $author->event_id);
            $event_obj = $query->get_one();
            $event_author[$i]['title'] = $event_obj->title;
            $event_author[$i]['id'] = $event_obj->event_id;
            $i++;
        }

        // check if user is organinzator in any events
        $query = Model_Orm_Participant::query()
            ->where('user_id', $user_id)
            ->and_where_open()
                ->where('role', 1)
            ->and_where_close();
        $organizators_obj = $query->get();

        $event_organizator = array();
        $i = 0;
        foreach ($organizators_obj as $organizator)
        {
            $query = Model_Orm_Event::query()->where('event_id', $organizator->event_id);
            $event_obj = $query->get_one();
            $event_organizator[$i]['title'] = $event_obj->title;
            $event_organizator[$i]['id'] = $event_obj->event_id;
            $i++;
        }

        // check if user has any messages
        $query = Model_Orm_Invite::query()->where('recipient_id', $user_id);
        $invites_obj = $query->get();

        // save them in array
        $invites = array();
        $i = 0;
        foreach ($invites_obj as $invite)
        {
            // get sender username
            $query = Model_Orm_User::query()->where('user_id', $invite->sender_id);
            $sender_obj = $query->get_one();
            $invites[$i]['sender_id'] = $invite->sender_id;
            $invites[$i]['sender_username'] = $sender_obj->username;

            // get events title
            $query = Model_Orm_Event::query()->where('event_id', $invite->event_id);
            $event_obj = $query->get_one();
            $invites[$i]['event_id'] = $invite->event_id;
            $invites[$i]['event_title'] = $event_obj->title;

            $i++;
        }

        // check if user has aby alert messages
        $query = Model_Orm_Alert::query()->where('recipient_id', $user_id);
        $alerts_obj = $query->get();

        // save them in array
        $alerts = array();
        $i = 0;
        foreach ($alerts_obj as $alert)
        {
            $alerts[$i]['id'] = $alert->alert_id;
            $alerts[$i]['type'] = $alert->type;
            $alerts[$i]['message'] = $alert->message;
            $i++;
        }

        $this->template->page_title = $exist_username->username.' profils';
        $this->template->content = View::forge('user/view');
        $this->template->content->set('user', $user);
        $this->template->content->set('onwer_access', $onwer_access);
        empty($event_author) or $this->template->content->set('event_author', $event_author);
        empty($event_organizator) or $this->template->content->set('event_organizator', $event_organizator);
        empty($invites) or $this->template->content->set('invites', $invites);
        empty($alerts) or $this->template->content->set('alerts', $alerts);
    }

    /**
     * Validates register form and creates new user
     */
    public function action_create()
    {
        if (Input::method() == 'POST')
        {
            // Registeration form submited, validate form
            $is_error = false;
            $errors = array();

            // Check if username set
            if (Input::post('username'))
            {
                // Username set, check if username already exists
                $query = Model_Orm_User::query()->where('username', Input::post('username'));
                $user_obj = $query->get_one();

                if ( ! empty($user_obj))
                {
                    $is_error = true;
                    $errors[] = 'Lietotājvārds jau eksistē ! ';
                }
            }
            else
            {
                // Username wans't set
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet lietotājvārdu!';
            }

            // Check if email set
            if (Input::post('email'))
            {
                // Email set, check if its valid email format
                if (filter_var(Input::post('email'), FILTER_VALIDATE_EMAIL))
                {
                    // Email set, check if email exists
                    $exist_email = Model_Orm_User::find('all', array(
                        'where' => array(
                            array('email', Input::post('email'))
                        ),
                    ));

                    if ( ! empty($exist_email))
                    {
                        // Email allready is used
                        $is_error = true;
                        $errors[] = 'E-pasts jau eksistē!';
                    }
                }
                else
                {
                    // Email isn't valid email format
                    $is_error = true;
                    $errors[] = 'E-pastam jābūt derīgai e-pasta adresei!';
                }

            }
            else
            {
                // Email wans't set
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet e-pastu!';
            }

            // Check if passwords set
            if (Input::post('password') and Input::post('password_rep'))
            {
                // Check if password match
                if (Input::post("password") != Input::post("password_rep"))
                {
                    $errors[] = 'Paroles nesakrīt!';
                    $is_error = true;
                }
                // Check if password is longer than 6 simbols
                elseif (strlen(Input::post("password")) < 6)
                {
                    $errors[] = 'Parolei jābūt garākai par 6 simboliem!';
                    $is_error = true;
                }
            }
            else
            {
                // Password wans't set
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet paroli abos laukos!';
            }

            // If form valid create user
            if ( ! $is_error)
            {
                $id = Auth::instance()->create_user(
                    Input::post('username'),
                    Input::post('password'),
                    Input::post('email'),
                    1
                );

                // Save id in 'id' field for fuelphp update bug
                $user = Model_Orm_User::find($id);
                $user->set(array(
                    'id'        => $id
                ));
                $user->save();

                // login with user
                Auth::instance()->force_login($id);

                Session::set_flash('success', 'Jūs esat veiksmīgi reģistrējies!');
                Response::redirect('/');
            }
            else
            {
                // Some error in validation, render registeration form with errors
                Session::set_flash('errors', $errors);
                $this->template->page_title = 'Reģistrējies';
                $this->template->content = View::forge('user/create');
            }
        }
        else
        {
            // No form submited
            // Generate form view
            $this->template->page_title = "Reģistrējies";
            $this->template->content = View::forge("user/create");
        }
    }

    /**
     * Logs in the given user if fields are correct
     */
    public function action_login()
    {
        // only show log in form if user is quest
        $auth = Auth::instance();
        $user_group = $auth->get_groups();

        if ($user_group[0][1] == 0)
        {
            if (Input::method() == 'POST')
            {
                $auth = Auth::instance();

                if ($auth->login($_POST['email'], $_POST['password']))
                {
                    // credentials ok, go right in
                    Response::redirect('/');
                }
                else
                {
                    // Oops, no soup for you. try to login again. Set some values to
                    // repopulate the username field and give some error text back to the view
                    $errors[] = 'Lietotājvārds un vai parole nepareiza';
                    Session::set_flash('errors', $errors);
                }
            }
        }
        else
        {
            // already logged in, redirect to home page
            Response::redirect('/');
        }

        // Show the login form
        $this->template->page_title = "Pieslēdzies";
        $this->template->content = View::forge('user/login');
    }

    /**
     * Logs out current user
     */
    public function action_logout()
    {
        $auth = Auth::instance();
        $auth->logout();
        Response::redirect('/');
    }

    /**
     * Deletes current user
     */
    public function action_delete()
    {
        // check if user is guest
        $auth = Auth::instance();
        $user_group_id = $auth->get_groups();

        if ($user_group_id[0][1] != 0)
        {
            // user not guest, delete this profile
            $username = $auth->get_screen_name();

            if ($auth->delete_user($username))
            {
                //user deleted
                Session::set_flash('success', 'Jūs veiksmīgi izdzēsāt savu profilu!');
                Response::redirect('/');

            }
            else
            {
                // something wen't wrong
                $errors[] = 'Lietotājvārds un vai parole nepareiza';
                Session::set_flash('errors', $errors);
                Response::redirect('/');
            }
        }
    }

    /**
     * Change users name, surname and email or set name and surname if not set
     */
    public function action_edit()
    {
        $auth = Auth::instance();
        $user_id = Auth::instance()->get_user_id();
        $user_id = $user_id[1];
        $user = Model_Orm_User::find($user_id);

        if (Input::method() == 'POST')
        {
            // Edit form submited, validate form
            $is_error = false;
            $errors = array();
            $name_fields = array();

            $name_set = false;
            // Check if name set
            if ( ! is_numeric(Input::post('name')))
            {
                // check if name field not left blank
                if (Input::post('name') != '')
                {
                    $name_set = true;
                    $name_fields['name'] = Input::post('name');
                }
            }
            else
            {
                $is_error = true;
                $errors[] = 'Vārds nevar būt skaitlis!';
            }

            // Check if surname set
            if ( ! is_numeric(Input::post('surname')))
            {
                // check if surname field not left blank
                if (Input::post('surname') != '')
                {
                    // check if name set
                    if ($name_set)
                    {
                        $name_fields['surname'] = Input::post('surname');
                    }
                    else
                    {
                        // name not set, must set name and surname
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadi arī vārdu!';
                    }
                }
                else
                {
                    // check if name set
                    if ($name_set)
                    {
                        // name set, must set surname too
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadi arī uzvārdu!';
                    }
                    else
                    {
                        $name_fields['surname'] = Input::post('surname');
                    }
                }
            }
            else
            {
                $is_error = true;
                $errors[] = 'Uzvārds nevar būt skaitlis!';
            }

            // Check if email set
            if (Input::post('email') and Input::post('email') != '')
            {
                // Email set, check if its valid email format
                if (filter_var(Input::post('email'), FILTER_VALIDATE_EMAIL))
                {
                    // Email set, check if email changed
                    if ($user->email != Input::post('email'))
                    {
                        // email has changed, check if its already used
                        $exist_email = Model_Orm_User::find('all', array(
                            'where' => array(
                                array('email', Input::post('email'))
                            ),
                        ));

                        if ( ! empty($exist_email))
                        {
                            // Email allready is used
                            $is_error = true;
                            $errors[] = 'E-pasts jau eksistē!';
                        }
                        else
                        {
                            $user_fields['email'] = Input::post('email');
                        }
                    }
                }
                else
                {
                    // Email isn't valid email format
                    $is_error = true;
                    $errors[] = 'E-pastam jābūt derīgai e-pasta adresei!';
                }

            }

            // If form valid create user
            if ( ! $is_error)
            {
                // if email has changed, change it
                if (isset($user_fields))
                {
                    // get users username
                    $username = $auth->get_screen_name();

                    $auth->update_user($user_fields, $username);
                }

                // if name or surname set
                if (isset($name_fields['name']) or isset($name_fields['surname']))
                {
                    // Save name and surname for just created user
                    $user->set($name_fields);
                    $user->save();
                }

                Session::set_flash('success', 'Jūs veiksmīgi labojāt savu profilu!');
                Response::redirect('/');
            }
            else
            {
                // Some error in validation, render registeration form with errors
                Session::set_flash('errors', $errors);
                $this->template->page_title = 'Izlabo savu profilu';
                $this->template->content = View::forge('user/edit');
            }
        }
        else
        {
            // No form submited
            // Generate form view with existing values
            $auth = Auth::instance();
            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];
            $user = Model_Orm_User::find($user_id);

            $_POST['name'] = $user->name;
            $_POST['surname'] = $user->surname;
            $_POST['email'] = $user->email;
            $this->template->page_title = 'Izlabo savu profilu';
            $this->template->content = View::forge('user/edit');
        }
    }

    public function action_change_password()
    {
        if (Input::method() == 'POST')
        {
            // Edit form submited, validate form
            $is_error = false;
            $errors = array();
            $name_fields = array();

            // Check if old passwords set
            if ( ! Input::post('password_old'))
            {
                // Password wans't set
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet savu tagadēja paroli!';
            }

            // Check if passwords set
            if (Input::post('password') and Input::post('password_rep'))
            {
                // Check if password match
                if (Input::post("password") != Input::post("password_rep"))
                {
                    $errors[] = 'Paroles nesakrīt!';
                    $is_error = true;
                }
                // Check if password is longer than 6 simbols
                elseif (strlen(Input::post("password")) < 6)
                {
                    $errors[] = 'Parolei jābūt garākai par 6 simboliem!';
                    $is_error = true;
                }
            }
            else
            {
                // Password wans't set
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet paroli abos laukos!';
            }

            // if new password valid
            if ( ! $is_error)
            {
                // try to change password
                $auth = Auth::instance();
                $username = $auth->get_screen_name();
                if ($auth->change_password(Input::post('password_old'), Input::post("password"), $username))
                {
                    // password changed
                    Session::set_flash('success', 'Parole vieksmīgi nomainīta!');
                    Response::redirect('/');
                }
                else
                {
                    // old password inccorect
                    $errors[] = 'Pašreizējā parole nepareiza!';
                    Session::set_flash('errors', $errors);
                    $this->template->page_title = 'Nomaini paroli';
                    $this->template->content = View::forge('user/change_password');
                }
            }
            else
            {
                // new passoword was invalid
                Session::set_flash('errors', $errors);
                $this->template->page_title = 'Nomaini paroli';
                $this->template->content = View::forge('user/change_password');
            }
        }
        else
        {
            // No form submited
            // Generate form view°
            $this->template->page_title = 'Nomaini paroli';
            $this->template->content = View::forge('user/change_password');
        }
    }

    /**
     * Dismises user alert
     *
     * @param int $alert_id is id of alert which user wished to dismiss
     */
    public function action_dismiss_alert($alert_id = null)
    {
        // if no alert if given, redirect back
        is_null($alert_id) and Response::redirect('user/view');

        // get alert
        $query = Model_Orm_Alert::query()->where('alert_id', $alert_id);
        $alert_obj = $query->get_one();

        if ( ! empty($alert_obj))
            {
            // check if user is alert recipient
            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];
            if ($alert_obj->recipient_id == $user_id)
            {
                // user is alerts recipent, delete alert
                $alert_obj->delete();
                Response::redirect('user/view');
            }
            else
            {
                // user is not alerts recipient
                $error[] = 'Nevar paslēpt cita lietotāja paziņojumus!';
                Session::set_flash('errors', $error);
                Response::redirect('user/view');
            }
        }
        else
        {
            // alert not found
            $error[] = 'Šis paziņojums vairs neeksistē!';
            Session::set_flash('errors', $error);
            Response::redirect('user/view');
        }
    }
}

?>
