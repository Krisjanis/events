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
                $verification_key = md5(mt_rand(0, mt_getrandmax()));
                $id = Auth::instance()->create_user(
                    Input::post('username'),
                    Input::post('password'),
                    Input::post('email'),
                    1,
                    array(
                        'verified'            => false,
                        'verification_key'    => $verification_key
                    )
                );

                // Save name and surname for just created user
                $user = Model_Orm_User::find($id);
                $user->set(array(
                    'name'  => Input::post('name'),
                    'surname' => Input::post('surname')
                ));
                $user->save();
                //$id =1;

                Session::set_flash('success', 'Jūs esat veiksmīgi reģistrējies, atliek vienīgi apstiprināt kontu no e-pasta.');
                // User ir registered, send him vertification email;
                $this->action_send_verification_email($id, Input::post('email'), $verification_key);
                Response::redirect('user/login');
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
     * Sends a vertification email to given user with vertification key
     *
     * @param int $id is ID of user to be vertified
     * @param string $mail_address is e-mail where to send the vertification
     * @param int $key is vertification key
     */
    public function action_send_verification_email($id, $mail_address, $key)
    {
        $email = Email::forge();
        $email->from('notikumiem@gmail.com', 'Pasakumu organizēšanas sistēma');

        $email->to($mail_address);
        $email->subject('Reģistrācijas apstiprināšana vietnē notikumiem.lv');

        $mail_text = "Paldies, ka reģistrējāties notikumiem.lv
        Lai apstiprinātu reģistrēšanos, lūdzu nospiediet uz šīs saites: " .
        Uri::create('user/verify/' . $id . '/' . $key . '/');
        $email->body($mail_text);
        try {
            $email->send();
        }
        catch(\EmailSendingFailedException $e)
        {
            // Could not send the email
            $error[] = 'Kaut kas nogāja greizi, neizdevās nosūtīt epastu!';
            Session::set_flash('errors', $error);
        }
        catch(\EmailValidationFailedException $e)
        {
            // Email failed validation.
            $error[] = 'Kaut kas nogāja greizi, epasts neizturēja validāciju!';
            Session::set_flash('errors', $error);
        }
    }

    /**
     * Vertifies given user if he has the correct vertification key
     *
     * @param int $id is ID of user whos account needs to be vertified
     * @param string $key is ertification key
     */
    public function action_verify($id, $key) {
        $auth = Auth::instance();

        // Force login with given user
        if ($auth->force_login($id)) {
            // Logged in, check if vertification key valid
            if ($auth->get_profile_fields('verification_key', null) != $key) {
                // Key doesn't match, logout
                $auth->logout();
                $error[] = 'Vertificēšanas atslēga nepareiza!';
                Session::set_flash('errors', $error);
            }
            else {
                $auth->update_user(array('verified' => true, 'verification_key' => null));
                Session::set_flash('success', 'Vertificēšana norita veiksmīgi, pieslēdzaties sistēmai!');
            }
        }
        else {
            // Could not log in
            $error[] = 'Kaut kas nogāja greizi, nevarēja pieslēgties!';
            Session::set_flash('errror', $error);
        }
        Response::redirect('user/login');
    }

    /**
     * Logs in the given user if fields are correct
     */
    public function action_login() {
        if (Input::method() == 'POST') {
            $auth = Auth::instance();

            if ($auth->login()) {
                if ($auth->get_profile_fields('verified', false) == false) {
                    $errors[] = 'Jūs vēl neesat apstiprinājis kontu ar e-pastu!';
                    Session::set_flash('errors', $errors);
                }
                else {
                    // credentials ok, go right in
                    Response::redirect('/') and die();
                }
            } else {
            // Oops, no soup for you. try to login again. Set some values to
            // repopulate the username field and give some error text back to the view
            $errors[] = 'Lietotājvārds un vai parole nepareiza';
            Session::set_flash('errors', $errors);
            }
        }

        // Show the login form
        $this->template->content = View::forge('user/login');
    }
}

?>
