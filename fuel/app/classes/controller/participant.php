<?php
/**
 * The Tag Controller.
 *
 * Has function for comment creating, editing, deleting
 *
 * @package  app
 * @extends  Public
 */
class Controller_Participant  extends Controller_Public
{
    /**
     * Add given organizator to given event
     */
    public function action_add_organizator()
    {
        // check if user has access to adding organizator
        if (Auth::has_access('participant.add_organizator'))
        {
            // check if user has access to adding orginazator
            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];
            $query = Model_Orm_Participant::query()
                ->where('event_id', Input::post('event_id'))
                ->and_where_open()
                     ->where('user_id', $user_id)
                ->and_where_close();
            $has_access = $query->get_one();

            if ( ! is_null($has_access) and $has_access->role != 0)
            {
                if (Input::method() == 'POST')
                {
                    if (Input::post('organizator'))
                    {
                        // check if user exists
                        $query = Model_Orm_User::query()->where('username', Input::post('organizator'));
                        $user_obj = $query->get_one();

                        if ( ! is_null($user_obj))
                        {
                            // check if it isn't already an organizator for this event
                            $query = Model_Orm_Participant::query()
                                ->where('event_id', Input::post('event_id'))
                                ->and_where_open()
                                    ->where('user_id', $user_obj->user_id)
                                ->and_where_close();
                            $organizator_obj = $query->get_one();

                            if (is_null($organizator_obj))
                            {
                                // create new invite for given user
                                $invite = array (
                                    'sender_id'     => $user_id,
                                    'recipient_id'  => $user_obj->user_id,
                                    'event_id'    => Input::post('event_id')
                                );

                                $new_invite = Model_Orm_Invite::forge($invite);

                                if ($new_invite and $new_invite->save())
                                {
                                    // new invite created
                                    Session::set_flash('success', 'Lietotājam '.$user_obj->username.' veiksmīgi nosūtīts uzaicinājums.');
                                    Response::redirect('event/view/'.Input::post('event_id'));
                                }
                            }
                            else
                            {
                                // user is already organizator for this event
                                $error[] = 'Piedod, bet šis lietotājs jau ir organizators šim pasākumam.';
                                Session::set_flash('errors', $error);
                            }
                        }
                        else
                        {
                            // user doesn't exists
                            $error[] = 'Piedod, bet šis lietotājs neekistē.';
                            Session::set_flash('errors', $error);
                        }
                    }
                    else
                    {
                        // no username specified
                        $error[] = 'Norādi lietotājvārdu, kuru vēlies pievienot.';
                        Session::set_flash('errors', $error);
                    }
                }
                Response::redirect('event/view/'.Input::post('event_id'));
            }
            else
            {
                // user doesn't have access to adding organizator
                $error[] = 'Piedod, bet tev nav pieejas pievienot organizatoru šim pasākumam.';
                Session::set_flash('errors', $error);
                Response::redirect('event/view/'.Input::post('event_id'));
            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Accepts current users invite to become organizer in given event
     *
     * @param string $event_id is ID of event to which current user has invite
     */
    public function action_accept_invite($event_id = null)
    {
        // chekc if user has access to accepting invite
        if (Auth::has_access('participant.accept_invite'))
        {
            is_null($event_id) and Response::redirect('/');

            // check if current user has invite for given event
            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];

            $query = Model_Orm_Invite::query()
                ->where('event_id', $event_id)
                ->and_where_open()
                    ->where('recipient_id', $user_id)
                ->and_where_close();
            $invite_obj = $query->get_one();

            // if invite found, make user organizator for this event
            if ( ! empty($invite_obj))
            {
                // give this user organizer status
                $organizator = array(
                    'event_id'  => $event_id,
                    'user_id'   => $user_id,
                    'role' => 1
                );
                $new_organizator = Model_Orm_Participant::forge($organizator);

                if ($new_organizator and $new_organizator->save())
                {
                    // users is now organizer, delete accepted invite
                    $query->delete();

                    Session::set_flash('success', 'Apsveicu, tu esi veiksmīgi pievienots kā organizators.');
                    Response::redirect('event/view/'.$event_id);
                }
            }
            else
            {
                // user has no invites
                $error[] = 'Piedod, bet tev nav neviena uzaicinājuma.';
                Session::set_flash('errors', $error);
                Response::redirect('user/view');
            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Delete give organizator form given event
     *
     * @param integer $event_id ID of event form which is needed to delete given user
     * @param integer $user_id ID of user needed to be deleted
     */
    public function action_delete($event_id = null, $user_id = null)
    {
        // check if user has access to deleting organizator
        if (Auth::has_access('participant.delete'))
        {
            is_null($event_id) and Response::redirect('/');
            is_null($user_id) and Response::redirect('/');

            // if user has access to delete user, delete it
            $cur_user_id = Auth::instance()->get_user_id();
            $cur_user_id = $cur_user_id[1];

            $query = Model_Orm_Participant::query()
                ->where('event_id', $event_id)
                ->and_where_open()
                     ->where('user_id', $cur_user_id)
                ->and_where_close();
            $has_access = $query->get_one();

            if ( ! is_null($has_access) and $has_access->role == 10)
            {
                $organizator_model = Model_Orm_Participant::query()
                    ->where('event_id', $event_id)
                    ->and_where_open()
                        ->where('user_id', $user_id)
                    ->and_where_close();
                $organizator = $organizator_model->get_one();

                if ($organizator->role == 10)
                {
                    // can't delete events author
                    $error[] = 'Nedrīkst dzēst pasākuma autoru!';
                    Session::set_flash('errors', $error);
                    Response::redirect('event/view/'.$event_id);
                }
                else
                {
                    // isn't events author, delte it
                    $organizator->delete();

                    Session::set_flash('success', 'Lietotājs veiksmīgi izdzēsts kā organizators.');
                    Response::redirect('event/view/'.$event_id);
                }
            }
            else
            {
                $error[] = 'Tev nav pieejas dzēst šo lietotāju kā organizatoru.';
                Session::set_flash('errors', $error);
                Response::redirect('event/view/'.$event_id);
            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Sends a request to given event to become an organizer
     *
     * @param string $event_id is ID of event to which user is requesting to be an organizer
     */
    public function action_request($event_id = null)
    {
        // check if user has access to make a request for organizator
        if (Auth::has_access('participant.request'))
        {
            is_null($event_id) and Response::redirect('/');

            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];

            // check if request isn't already sent
            $query = Model_Orm_Request::query()
                ->where('sender_id', $user_id)
                ->and_where_open()
                    ->where('event_id', $event_id)
                ->and_where_close();
            $existing_request = $query->get_one();
            if ( empty($existing_request))
            {
                // check if user already isn't an organizer for this event
                $query = Model_Orm_Participant::query()->where('user_id', $user_id);
                $participant = $query->get_one();

                if ( ! is_null($participant))
                {
                    // user is participant, check if not organizer or author
                    if ($participant->role != 1 or $participant->role != 10)
                    {
                        // user not organizer or author, send request
                        $request = array (
                            'sender_id'   => $user_id,
                            'event_id'    => $event_id
                        );

                        $new_request = Model_Orm_Request::forge($request);
                        $new_request->save();

                        Session::set_flash('success', 'Organizatora pieprasījums veiskmīgi nosūtīts.');
                        Response::redirect('event/view/'.$event_id);
                    }
                    else
                    {
                        // user already organizer for this event
                        $error[] = 'Tu jau esi organizētājs šajā pasākumā.';
                        Session::set_flash('errors', $error);
                        Response::redirect('event/view/'.$event_id);
                    }
                }
                else
                {
                    // user isn't organizator for this event
                    $request = array (
                        'sender_id'   => $user_id,
                        'event_id'    => $event_id
                    );

                    $new_request = Model_Orm_Request::forge($request);
                    $new_request->save();

                    Session::set_flash('success', 'Organizatora pieprasījums veiskmīgi nosūtīts.');
                    Response::redirect('event/view/'.$event_id);
                }
            }
            else
            {
                // request already sent
                $error[] = 'Tu jau esi nosūtījis pieprasījumu šim pasākumam.';
                Session::set_flash('errors', $error);
                Response::redirect('event/view/'.$event_id);
            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Decline given request to become participabnt
     *
     * @param string $event_id is ID of event to which the request is adresed
     * @param integer $user_id is ID of user which send the request
     */
    public function action_decline_request($event_id = null, $user_id = null)
    {
        // check if user has access to declining request
        if (Auth::has_access('participant.decline_request'))
        {
            is_null($event_id) and Response::redirect('/');
            is_null($user_id) and Response::redirect('/');

            // check if current user has organizator acces for given event
            $cur_user_id = Auth::instance()->get_user_id();
            $cur_user_id = $cur_user_id[1];

            $query = Model_Orm_Participant::query()
                ->where('event_id', $event_id)
                ->and_where_open()
                    ->where('user_id', $cur_user_id)
                ->and_where_close();
            $has_access = $query->get_one();

            if ( ! empty($has_access) and $has_access->role != 0)
            {
                // user has organizator access, delete given request
                $query = Model_Orm_Request::query()
                    ->where('event_id', $event_id)
                    ->and_where_open()
                        ->where('sender_id', $user_id)
                    ->and_where_close();
                $request_obj = $query->get_one();
                $request_obj->delete();

                Session::set_flash('success', 'Organizatora pieprasījums atteikts.');
                Response::redirect('event/view/'.$event_id);
            }
            else
            {
                // user doesn't have organizator access
                $error[] = 'Piedod, bet tev organizatora pieeja šim pasākumam.';
                Session::set_flash('errors', $error);
                Response::redirect('event/view/'.$event_id);
            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Accept request to become participant
     *
     * @param string $event_id is ID of event to which event request is adressed
     * @param integer $user_id is ID of user which sent the request
     */
    public function action_accept_request($event_id = null, $user_id = null)
    {
        // check if user has access to accepting request
        if (Auth::has_access('participant.accpet_request'))
        {
            is_null($event_id) and Response::redirect('/');
            is_null($user_id) and Response::redirect('/');

            // check if current user has organizator access for given event
            $cur_user_id = Auth::instance()->get_user_id();
            $cur_user_id = $cur_user_id[1];

            $query = Model_Orm_Participant::query()
                ->where('event_id', $event_id)
                ->and_where_open()
                    ->where('user_id', $cur_user_id)
                ->and_where_close();
            $has_access = $query->get_one();

            if ( ! empty($has_access) and $has_access->role != 0)
            {
                // user has organizator access, add participant and delete given request
                $participant = array(
                    'event_id'  => $event_id,
                    'user_id'   => $user_id,
                    'role'      => 0
                );
                $new_participant = Model_Orm_Participant::forge($participant);
                $new_participant->save();

                $query = Model_Orm_Request::query()
                    ->where('event_id', $event_id)
                    ->and_where_open()
                        ->where('sender_id', $user_id)
                    ->and_where_close();
                $request_obj = $query->get_one();
                $request_obj->delete();

                Session::set_flash('success', 'Lietotājs veiksmīgi pievienots kā organizators.');
                Response::redirect('event/view/'.$event_id);
            }
            else
            {
                // user doesn't have organizator access
                $error[] = 'Piedod, bet tev organizatora pieeja šim pasākumam.';
                Session::set_flash('errors', $error);
                Response::redirect('event/view/'.$event_id);
            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Sends an invite email to user outside of system
     *
     * @param string $event_id is ID of event to which event request is adressed
     */
    public function action_email($event_id = null)
    {
        // chech if user has access to invited organizers via email
        if (Auth::has_access('participant.email'))
        {
            is_null($event_id) and Response::redirect('/');

            $query = Model_Orm_Event::query()->where('event_id', $event_id);
            $event_obj = $query->get_one();
            if (empty($event_obj))
            {
                // no event with such event id
                $error[] = 'Piedod, bet pasākums ar šādu ID neeksistē.';
                Session::set_flash('errors', $error);
                Response::redirect('/');
            }

            // check if user has organizator access
            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];

            $query = Model_Orm_Participant::query()
                ->where('event_id', $event_id)
                ->and_where_open()
                    ->where('user_id', $user_id)
                ->and_where_close();
            $has_access = $query->get_one();

            if ( ! empty($has_access) and $has_access->role != 0)
            {
                if (Input::method() == 'POST')
                {
                    // form submited, validate it
                    $is_error = false;

                    if (Input::post('email'))
                    {
                        // Email set, check if its valid email format
                        if (filter_var(Input::post('email'), FILTER_VALIDATE_EMAIL))
                        {
                                $query = Model_Orm_User::query()->where('email', Input::post('email'));
                                $exist_email = $query->get_one();

                            if ( ! empty($exist_email))
                            {
                                // Email allready is used
                                $is_error = true;
                                $errors[] = 'E-pasts jau tiek izmantot sistēmā!';
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

                    if (Input::post('message'))
                    {
                        // message set, check if not too long
                        if (strlen(Input::post('message')) > 500)
                        {
                            // message to long
                            $is_error = true;
                            $errors[] = 'Ziņa ir pārak gara!';
                        }
                    }
                    else
                    {
                        // message wasn't set
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadiet ziņu!';
                    }

                    if ( ! $is_error)
                    {
                        // invite form valid, use swift mailer to send invite via email to recipent
                        require_once 'lib/swift_required.php';

                        // use gmail smtp server for email sending
                        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
                          ->setUsername('notikumiem')
                          ->setPassword('notikumiempassword');

                        $mailer = Swift_Mailer::newInstance($transport);

                        // create access key, for user to become organizator after registration
                        $salt = mt_rand(0, mt_getrandmax());
                        $access_key = md5($event_id.$salt);

                        // save invite
                        $invite = array (
                            'sender_id'     => $user_id,
                            'event_id'      => $event_id,
                            'email'         => Input::post('email'),
                            'message'       => Input::post('message'),
                            'access_key'    => $access_key
                        );

                        $new_invite = Model_Orm_Invite::forge($invite);
                        $new_invite->save();

                        $event_title = $event_obj->title;
                        $subject = "kļūsti par organizatoru pasākumā $event_title";

                        $body = "Sveiks,\n\nTu esi uzaicināts kļūt par organizatoru pasākumā $event_title\n\n"
                               .Input::post('message')
                               ."\n\nnotikumiem.lv ir pasākumu organizēšanas vietne, kura palīdzēs tev parocīgāk noorganizēt jebkāka veida pasākumu. Spied zemāk redzamajā saitē un reģistrējies\n\n"
                               .Uri::create('user/create/'.$access_key);

                        $message = Swift_Message::newInstance('Test Subject')
                          ->setFrom(array('notikumiem@gmail.com' => 'notikumiem.lv'))
                          ->setTo(array(Input::post('email')))
                          ->setSubject($subject)
                          ->setBody($body);

                        $mailer->send($message);

                        Session::set_flash('success', 'Lietotājam veiksmīgi nosūtīts uzaicinājums uz '.Input::post('email'));
                        Response::redirect('event/view/'.$event_id);
                    }
                    else
                    {
                        // Some error in validation, render form with errors
                        Session::set_flash('errors', $errors);
                        $this->template->page_title = 'Nosūti uzaicinājumu';
                        $this->template->content = View::forge('participant/send');
                        $this->template->content->set('event_id', $event_id);
                    }
                }
                else
                {
                    // no form submited, redner form view
                    $this->template->page_title = 'Nosūti uzaicinājumu';
                    $this->template->content = View::forge('participant/send');
                    $this->template->content->set('event_id', $event_id);

                }
            }
            else
            {
                // user doesn't have organizator access
                $error[] = 'Piedod, bet tev organizatora pieeja šim pasākumam.';
                Session::set_flash('errors', $error);
                Response::redirect('event/view/'.$event_id);
            }
        }
        else
        {
            Response::redirect('/');
        }
    }
}

?>
