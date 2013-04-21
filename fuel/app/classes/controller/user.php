<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.5
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

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
     * The basic welcome message
     */
    public function action_index()
    {
        //
    }

    /**
     * Validates register form and creates new user
     */
    public function action_create()
    {
        if (Input::method() == 'POST') {
            // Registeration form submited, validate form
            $is_error = false;
            $errors = array();

            // Check if username set
            if (Input::post('username')) {
                // Username set, check if username already exists
                $exist_username = Model_Orm_User::find('all', array(
                    'where' => array(
                        array('username', Input::post('username'))
                    ),
                ));
                if (!empty($exist_username)) {
                    $is_error = true;
                    $errors[] = 'Lietotājvārds jau eksistē!';
                }
            }
            else {
                // Username wans't set
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet lietotājvārdu!';
            }

            // Check if name set
            if (!Input::post('name')) {
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet savu vārdu!';
            }

            // Check if surname set
            if (!Input::post('surname')) {
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet savu uzvārdu!';
            }

            // Check if email set
            if (Input::post('email')) {
                // Email set, check if its valid email format
                if (filter_var(Input::post('email'), FILTER_VALIDATE_EMAIL)) {
                    // Email set, check if email exists
                    $exist_email = Model_Orm_User::find('all', array(
                        'where' => array(
                            array('email', Input::post('email'))
                        ),
                    ));
                    if (!empty($exist_email)) {
                        // Email allready is used
                        $is_error = true;
                        $errors[] = 'E-pasts jau eksistē!';
                    }
                }
                else {
                    // Email isn't valid email format
                    $is_error = true;
                    $errors[] = 'E-pastam jābūt derīgai e-pasta adresei!';
                }

            }
            else {
                // Email wans't set
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet e-pastu!';
            }

            // Check if passwords set
            if (Input::post('password') && Input::post('password_rep')) {
                // Check if password match
                if (Input::post("password") != Input::post("password_rep")) {
                    $errors[] = 'Paroles nesakrīt!';
                    $is_error = true;
                }
                // Check if password is longer than 6 simbols
                elseif (strlen(Input::post("password")) < 6) {
                    $errors[] = 'Parolei jābūt garākai par 6 simboliem!';
                    $is_error = true;
                }
            }
            else {
                // Password wans't set
                $is_error = true;
                $errors[] = 'Lūdzu ievadiet paroli abos laukos!';
            }

            // If form valid create user
            if (!$is_error) {
                $id = Auth::instance()->create_user(
                    Input::post('username'),
                    Input::post('password'),
                    Input::post('email'),
                    1
                );

                // Save name and surname for just created user
                $user = Model_Orm_User::find($id);
                $user->set(array(
                    'id'         => $id,
                    'name'       => Input::post('name'),
                    'surname'    => Input::post('surname')
                ));
                $user->save();

                // login with user
                Auth::instance()->force_login($id);

                Session::set_flash('success', 'Jūs esat veiksmīgi reģistrējies!');
                Response::redirect('/');
            }
            else {
                // Some error in validation, render registeration form with errors
                Session::set_flash('errors', $errors);
                $this->template->page_title = 'Reģistrējies';
                $this->template->content = View::forge('user/create');
            }
        }
        else {
            // No form submited
            // Generate form view
            $this->template->page_title = "Reģistrējies";
            $this->template->content = View::forge("user/create");
        }
    }

    /**
     * Logs in the given user if fields are correct
     */
    public function action_login() {
        if (Input::method() == 'POST') {
            $auth = Auth::instance();

            if ($auth->login($_POST['email'], $_POST['password'])) {
                // credentials ok, go right in
                Response::redirect('event/create') and die();
            }
            else {
            // Oops, no soup for you. try to login again. Set some values to
            // repopulate the username field and give some error text back to the view
            $errors[] = 'Lietotājvārds un vai parole nepareiza';
            Session::set_flash('errors', $errors);
            }
        }

        // Show the login form
        $this->template->page_title = "Pieslēdzies";
        $this->template->content = View::forge('user/login');
    }

    public function action_logout() {
        $auth = Auth::instance();
        $auth->logout();
        Response::redirect("/");
    }
}

?>
