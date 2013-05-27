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

            if ( ! is_null($has_access))
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

            if ( ! is_null($has_access))
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
        // check if user has access to deleting organizator
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
     * Decline given request to become organizator
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
            $organizator_obj = $query->get_one();

            if ( ! empty($organizator_obj))
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
     * Accept request to become organizer
     *
     * @param string $event_id is ID of event to which event request is adressed
     * @param integer $user_id is ID of user which sent the request
     */
    public function action_accept_request($event_id = null, $user_id = null)
    {
        // chech if user has access to accepting request
        if (Auth::has_access('participant.accpet_request'))
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
            $organizator_obj = $query->get_one();

            if ( ! empty($organizator_obj))
            {
                // user has organizator access, add organizer and delete given request
                $organizator = array(
                    'event_id'  => $event_id,
                    'user_id'   => $user_id,
                    'role'      => 0
                );
                $new_organizator = Model_Orm_Participant::forge($organizator);
                $new_organizator->save();

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
}

?>
