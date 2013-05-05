<?php
/**
 * The Admin Controller.
 *
 * Event controller validates event form and creates new event, sends invitations
 * to become oganizators to user already in the system and user outside system
 * with email
 *
 * @package  app
 * @extends  Public
 */
class Controller_Event extends Controller_Public
{
    /**
     * Displays event with given link title
     *
     * @param string $event_id unique string of needed event
     */
    public function action_view($event_id = null)
    {
        // if no link title given redirect to home page
        is_null($event_id) and Response::redirect('/');

        // id of current user and chrck if has author or organizator access
        $user_id = Auth::instance()->get_user_id();
        $user_id = $user_id[1];
        $organizator_access = false;
        $author_access = false;

        // get event
        $query = Model_Orm_Event::query()->where('event_id', $event_id);
        $event_obj = $query->get_one();

        // if no user found redirect to home page
        is_null($event_obj) and Response::redirect('/');

        // save mandetory model atributes in an array
        $event = array(
            'id'         => $event_obj->event_id,
            'type'       => $event_obj->type,
            'title'      => $event_obj->title,
            'desc'       => $event_obj->description,
            'location'   => $event_obj->location,
            'date'       => $event_obj->date,
        );

        // check for additional model attributes
        is_null($event_obj->participants_min) or $event['part_min'] = $event_obj->participants_min;
        is_null($event_obj->participants_max) or $event['part_max'] = $event_obj->participants_max;
        is_null($event_obj->entry_fee) or $event['entry_fee'] = $event_obj->entry_fee;
        is_null($event_obj->takeaway) or $event['takeaway'] = $event_obj->takeaway;
        is_null($event_obj->dress_code) or $event['dress_code'] = $event_obj->dress_code;
        is_null($event_obj->assistants) or $event['assistants'] = $event_obj->assistants;

        // get events author
        $organizators = array();
        $query = Model_Orm_Organizator::query()
            ->where('event_id', $event_obj->event_id)
            ->and_where_open()
                ->where('is_author', 1)
            ->and_where_close();
        $author_id = $query->get_one()->user_id;

        $query = Model_Orm_User::query()->where('user_id', $author_id);
        $author = $query->get_one();

        $organizators['author'] = array(
            'id'        => $author->user_id,
            'username'  => $author->username,
        );

        if ($author->user_id == $user_id)
        {
            $author_access = true;
            $organizator_access = true;
        }
        // get organizators
        $query = Model_Orm_Organizator::query()
            ->where('event_id', $event_obj->event_id)
            ->and_where_open()
                ->where('is_author', 0)
            ->and_where_close();
        $organizators_obj = $query->get();

        $organizators['organizators'] = array();
        $i = 0;

        // get username for each organizator
        foreach($organizators_obj as $o)
        {
            $query = Model_Orm_User::query()->where('user_id', $o->user_id);
            $organizator = $query->get_one();
            $organizators['organizators'][$i]['id'] = $organizator->user_id;
            $organizators['organizators'][$i]['username'] = $organizator->username;

            // check if current user is an organizator for current event
            $organizator->user_id == $user_id and $organizator_access = true;
            $i++;
        }

        // get events comments
        $comments = array();

        // get whole event comments
        $event_comment_obj = Request::forge('comment/view/w/'.$event_obj->event_id)->execute();
        foreach ($event_comment_obj->response->body as $comment)
        {
            $comments['event'][] = $comment;
        }

        // get location comments
        $location_comment_obj = Request::forge('comment/view/l/'.$event_obj->event_id)->execute();
        foreach ($location_comment_obj->response->body as $comment)
        {
            $comments['location'][] = $comment;
        }

        // get date comments
        $date_comment_obj = Request::forge('comment/view/d/'.$event_obj->event_id)->execute();
        foreach ($date_comment_obj->response->body as $comment)
        {
            $comments['date'][] = $comment;
        }

        // get participiants comments if event has this field
        if ( ! is_null($event_obj->participants_min) or ! is_null($event_obj->participants_max))
        {
            $participiants_comment_obj = Request::forge('comment/view/p/'.$event_obj->event_id)->execute();
            foreach ($participiants_comment_obj->response->body as $comment)
            {
                $comments['participiants'][] = $comment;
            }
        }

        // get entry fee comments if event has this field
        if ( ! is_null($event_obj->entry_fee))
        {
            $entry_fee_comment_obj = Request::forge('comment/view/f/'.$event_obj->event_id)->execute();
            foreach ($entry_fee_comment_obj->response->body as $comment)
            {
                $comments['entry_fee'][] = $comment;
            }
        }

        // get takeaway comments if event has this field
        if ( ! is_null($event_obj->takeaway))
        {
            $takeaway_comment_obj = Request::forge('comment/view/t/'.$event_obj->event_id)->execute();
            foreach ($takeaway_comment_obj->response->body as $comment)
            {
                $comments['takeaway'][] = $comment;
            }
        }

        // get dress code comments if event has this field
        if ( ! is_null($event_obj->dress_code))
        {
            $dress_code_comment_obj = Request::forge('comment/view/dc/'.$event_obj->event_id)->execute();
            foreach ($dress_code_comment_obj->response->body as $comment)
            {
                $comments['dress_code'][] = $comment;
            }
        }

        // get assistants comments if event has this field
        if ( ! is_null($event_obj->assistants))
        {
            $assistans_comment_obj = Request::forge('comment/view/a/'.$event_obj->event_id)->execute();
            foreach ($assistans_comment_obj->response->body as $comment)
            {
                $comments['assistants'][] = $comment;
            }
        }

        $this->template->page_title = $event['title'];
        $this->template->content = View::forge('event/view');
        $this->template->content->set('event', $event);
        $this->template->content->set('organizators', $organizators);
        $this->template->content->set('organizator_access', $organizator_access);
        $this->template->content->set('author_access', $author_access);
        $this->template->content->set('comments', $comments);
        $this->template->content->set('user_id', $user_id);
    }

    /**
     * Validates creation form and creates new event
     */
    public function action_create()
    {
        // check if user has accesss to create new event
        if (Auth::has_access('event.create'))
        {
            if (Input::method() == 'POST')
            {
                // form submited, validate it
                $is_error = false;
                $errors = array();
                $event = array();

                // check type set
                if (Input::post('type') == 'private')
                {
                    $event['type'] = 'private';
                }
                else
                {
                    $event['type'] = 'public';
                }

                // check if title set
                if ( ! Input::post('title'))
                {
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma nosaukumu!';
                }
                else
                {
                    $event['title'] = Input::post('title');
                }

                // check if link title is set
                if (Input::post('link_title'))
                {
                    if ( ! preg_match('/^[a-zA-Z0-9]+$/', Input::post('link_title')) == 1)
                    {
                        // string contained illegal characters
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadiet pasākuma saites nosaukumu, tas drīkst saturēt tikai burtus bez mīkstinājuma zīmēm un ciparus!';
                    }
                    else
                    {
                        // check if link doesn't already exist in system
                        $query = Model_Orm_Event::query()->where('event_id', Input::post('link_title'));
                        $event_obj = $query->get_one();

                        if (empty($event_obj))
                        {
                            $event['event_id'] = Input::post('link_title');
                        }
                        else
                        {
                            $is_error = true;
                            $errors[] = 'Saites nosaukums jau aizņemts, izvēlies citu!';
                        }
                    }
                }
                else
                {
                    // link title not set
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma saites nosaukumu!';
                }

                // check if dexcription set
                if ( ! Input::post('desc'))
                {
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma aprakstu!';
                }
                else
                {
                    $event['description'] = Input::post('desc');
                }

                // check if location set
                if ( ! Input::post('location'))
                {
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma atrašanās vietu!';
                }
                else
                {
                    $event['location'] = Input::post('location');
                }

                // check if date set
                if ( ! Input::post('date'))
                {
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma norises datumu!';
                }
                else
                {
                    $event['date'] = Input::post('date');
                }

                $part_min_valid = false;
                // check if participants min set
                if (Input::post('part_min'))
                {
                    // min participants set, check if its numeric
                    if (is_numeric(Input::post('part_min')))
                    {
                        // min participants is numeric, check if its not real number
                        if (strpos(Input::post('part_min'), '.') !== false)
                        {
                            $is_error = true;
                            $errors[] = 'Minimālajam dalībnieku skaitam jābūt veselam skaitlim!';
                        }
                        else
                        {
                            // min participants is integer, check if positive
                            if (Input::post('part_min') <= 0)
                            {
                                $is_error = true;
                                $errors[] = 'Minimālajam dalībnieku skaitam jābūt pozitīvam skaitlim!';
                            }
                            else
                            {
                                $event['participants_min'] = Input::post('part_min');
                                $part_min_valid = true;
                            }
                        }
                    }
                    else
                    {
                        // min participants wasn't numeric
                        $is_error = true;
                        $errors[] = 'Minimālajam dalībnieku skaitam jābūt skaitlim!';
                    }
                }

                // check if participants max set
                if (Input::post('part_max'))
                {
                    // max participants set, check if its numeric
                    if (is_numeric(Input::post('part_max')))
                    {
                        // max participants is numeric, check if its not real number
                        if (strpos(Input::post('part_max'), '.') !== false)
                        {
                            $is_error = true;
                            $errors[] = 'Maksimālajam dalībnieku skaitam jābūt veselam skaitlim!';
                        }
                        else
                        {
                            // min participants is integer, check if positive
                            if (Input::post('part_max') > 0)
                            {
                                // min participants is positive integer
                                // check if max bigger than min participants if both set
                                if ($part_min_valid and Input::post('part_max') >= Input::post('part_min'))
                                {
                                    // values are correct, check if not equal
                                    if (Input::post('part_max') == Input::post('part_min'))
                                    {
                                        $is_error = true;
                                        $errors[] = 'Maksimālā un minimālā dalībnieku skaita vērtības sakrīt!';
                                    }
                                    else
                                    {
                                        $event['participants_max'] = Input::post('part_max');
                                    }
                                }
                                else
                                {
                                    $is_error = true;
                                    $errors[] = 'Minimālajam dalībnieku ir jābūt mazākam par maksimālo dalībnieku skaitu!';
                                }
                            }
                            else
                            {
                                $is_error = true;
                                $errors[] = 'Minimālajam dalībnieku skaitam jābūt pozitīvam skaitlim!';
                            }
                        }
                    }
                    else
                    {
                        // max participants wasn't numeric
                        $is_error = true;
                        $errors[] = 'Maksimālajam dalībnieku skaitam jābūt skaitlim!';
                    }
                }

                // check if entry fee set
                if (Input::post('entry_fee'))
                {
                    // entry fee set, check if its positive number
                    if ( ! is_numeric(Input::post('entry_fee')) and Input::post('entry_fee') <= 0)
                    {
                        $is_error = true;
                        $errors[] = 'Ieejas maksai jābūt pozitīvam skaitlim!';
                    }
                    else
                    {
                        $event['entry_fee'] = Input::post('entry_fee');
                    }
                }

                // check if takeway items set
                Input::post('takeaway') and $event['takeaway'] = Input::post('takeaway');

                // check if dress code set
                Input::post('dress_code') and $event['dress_code'] = Input::post('dress_code');

                // check if assistants set
                Input::post('assistants') and $event['assistants'] = Input::post('assistants');

                if ( ! $is_error)
                {
                    // form valid, try to save it
                    $new_event = Model_Orm_Event::forge($event);

                    if ($new_event and $new_event->save())
                    {
                        // save event organizator
                        $user_id = Auth::instance()->get_user_id();
                        $organizator = array(
                            'event_id'  => $new_event->event_id,
                            'user_id'   => $user_id[1],
                            'is_author' => 1
                        );
                        $new_organizator = Model_Orm_Organizator::forge($organizator);

                        if ($new_organizator and $new_organizator->save())
                        {
                            // organizator successfully added
                            Session::set_flash('success', 'Pasākums "'.$new_event->title.'" veiksmīgi izveidots.');
                            Response::redirect('event/view/'.$event['event_id']);
                        }
                        else
                        {
                            // something wen't wrong with adding organizator
                            Session::set_flash('errors', 'Kaut kas nogāja greizi ar pasākuma autoru, mēģini vēlreiz!');
                            $this->template->page_title = 'Izveido pasākumu!';
                            $this->template->content = View::forge('event/create');
                            $this->template->content->form_title = 'Izveido jaunu pasākumu!';
                        }
                    }
                    else
                    {
                        Session::set_flash('errors', 'Piedod, bet kaut kas nogāja greizi, mēģini vēlreiz!');
                        $this->template->page_title = 'Izveido pasākumu!';
                        $this->template->content = View::forge('event/create');
                        $this->template->content->form_title = 'Izveido jaunu pasākumu!';
                    }
                }
                else
                {
                    // some error in validation, render registeration form with errors
                    Session::set_flash('errors', $errors);
                    $this->template->page_title = 'Izveido pasākumu!';
                    $this->template->content = View::forge('event/create');
                    $this->template->content->form_title = 'Izveido jaunu pasākumu!';
                }
            }
            else
            {
                // No form submited, render creation form
                $this->template->page_title = 'Izveido pasākumu!';
                $this->template->content = View::forge('event/create');
                $this->template->content->form_title = 'Izveido jaunu pasākumu!';
            }
        }
        else
        {
            // user doesn't have access to creeating new event
            Response::redirect('/');
        }
    }

    /**
     * Add given organizator to given event
     */
    public function action_add_organizator()
    {
        // check if user has access to adding organizator
        if (Auth::has_access('event.add_organizator'))
        {
            // check if user has access to adding orginazator
            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];
            $query = Model_Orm_Organizator::query()
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
                            $query = Model_Orm_Organizator::query()
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

    public function action_accept_invite($event_id = null)
    {
        // chekc if user has access to accepting invite
        if (Auth::has_access('event.accept_invite'))
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
                    'is_author' => 0
                );
                $new_organizator = Model_Orm_Organizator::forge($organizator);

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
     * @param string $event_id title link of event to which needs to be redirected
     * @param integer $event_id ID of event form which is needed to delete given user
     * @param integer $user_id ID of user needed to be deleted
     */
    public function action_delete_organizator($event_id = null, $user_id = null)
    {
        // check if user has access to deleting organizator
        if (Auth::has_access('event.delete_organizator'))
        {
            // if user has access to delete user, delete it
            $cur_user_id = Auth::instance()->get_user_id();
            $query = Model_Orm_Organizator::query()
                ->where('event_id', Input::post('event_id'))
                     ->and_where_open()
                     ->where('user_id', $cur_user_id[1])
                ->and_where_close();
            $has_access = $query->get_one();

            if ( ! is_null($has_access))
            {
                $organizator_model = Model_Orm_Organizator::query()
                    ->where('event_id', $event_id)
                    ->and_where_open()
                        ->where('user_id', $user_id)
                    ->and_where_close();
                $organizator = $organizator_model->get_one();
                if ($organizator->is_author == 1)
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
     * Edit atributes of given event
     *
     * @param integer $event_id is link to were redirect
     * @param integer $event_id is ID of event needed to be edited
     */
    public function action_edit_attribute($event_id = null, $event_id = null)
    {
        // check if user has access to editing attribute
        if (Auth::has_access('event.edit_attribute'))
        {
            // check if user has access to editing event
            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];
            $query = Model_Orm_Organizator::query()
                ->where('event_id', $event_id)
                     ->and_where_open()
                     ->where('user_id', $user_id)
                ->and_where_close();
            $has_access = $query->get_one();

            if ( ! is_null($has_access))
            {
                // user has acess to edit this event, check if its not blocked
                if (Auth::has_access('event.edit_attribute'))
                {
                    if (Input::method() == 'POST')
                    {
                        // get existing attribute values
                        $event_model = Model_Orm_Event::find($event_id);
                        // form submited, validate it
                        $is_error = false;
                        $errors = array();
                        $event = array();

                        // check if title set
                        if (Input::post('title'))
                        {
                            // check if it have changed
                            Input::post('title') != $event_model->title and $event['title'] = Input::post('title');
                        }
                        else
                        {
                            $is_error = true;
                            $errors[] = 'Lūdzu ievadiet pasākuma nosaukumu!';
                        }

                        // check if link title is set
                        if (Input::post('link_title'))
                        {
                            // check if it have changed
                            if (Input::post('link_title') != $event_model->event_id)
                            {
                                // string has changed, validate it
                                if( ! preg_match('/^[a-zA-Z0-9]+$/', Input::post('link_title')) == 1)
                                {
                                    // string contained illegal characters
                                    $is_error = true;
                                    $errors[] = 'Lūdzu ievadiet pasākuma saites nosaukumu, tas drīkst saturēt tikai burtus bez mīkstinājuma zīmēm un ciparus!';
                                }
                                else
                                {
                                    // check if link doesn't already exist in system
                                    $query = Model_Orm_Event::query()->where('event_id', Input::post('link_title'));
                                    $event_obj = $query->get_one();

                                    if (empty($event_obj))
                                    {
                                        $event['event_id'] = Input::post('link_title');
                                    }
                                    else
                                    {
                                        $is_error = true;
                                        $errors[] = 'Saites nosaukums jau aizņemts, izvēlies citu!';
                                    }
                                }
                            }
                        }
                        else
                        {
                            // link title not set
                            $is_error = true;
                            $errors[] = 'Lūdzu ievadiet pasākuma saites nosaukumu!';
                        }

                        // check if dexcription set
                        if (Input::post('desc'))
                        {
                            // check if it have changed
                            Input::post('desc') != $event_model->description and $event['description'] = Input::post('desc');
                        }
                        else
                        {
                            $is_error = true;
                            $errors[] = 'Lūdzu ievadiet pasākuma aprakstu!';
                        }

                        // check if location set
                        if (Input::post('location'))
                        {
                            // check if it have changed
                            Input::post('location') != $event_model->location and $event['location'] = Input::post('location');
                        }
                        else
                        {
                            $is_error = true;
                            $errors[] = 'Lūdzu ievadiet pasākuma atrašanās vietu!';
                        }

                        // check if date set
                        if (Input::post('date'))
                        {
                            // check if it have changed
                            Input::post('date') != $event_model->date and $event['date'] = Input::post('date');
                        }
                        else
                        {
                            $is_error = true;
                            $errors[] = 'Lūdzu ievadiet pasākuma norises datumu!';
                        }

                        $part_min_valid = false;
                        // check if participants min set
                        if (Input::post('part_min'))
                        {
                            // check if it have changed
                            if (Input::post('part_min') != $event_model->participants_min)
                            {
                                // string has changed, check if its numeric
                                if (is_numeric(Input::post('part_min')))
                                {
                                    // min participants is numeric, check if its not real number
                                    if (strpos(Input::post('part_min'), '.') != false)
                                    {
                                        $is_error = true;
                                        $errors[] = 'Minimālajam dalībnieku skaitam jābūt veselam skaitlim!';
                                    }
                                    else
                                    {
                                        // min participants is integer, check if positive
                                        if (Input::post('part_min') <= 0)
                                        {
                                            $is_error = true;
                                            $errors[] = 'Minimālajam dalībnieku skaitam jābūt pozitīvam skaitlim!';
                                        }
                                        else
                                        {
                                            $event['participants_min'] = Input::post('part_min');
                                            $part_min_valid = true;
                                            $part_min = Input::post('part_min');
                                        }
                                    }
                                }
                                else
                                {
                                    // min participants wasn't numeric
                                    $is_error = true;
                                    $errors[] = 'Minimālajam dalībnieku skaitam jābūt skaitlim!';
                                }
                            }
                            else
                            {
                                // string hasn't changed, but still it exists
                                $part_min_valid = true;
                                $part_min = $event_model->participants_min;
                            }
                        }
                        else
                        {
                            // if not set, delete previous value
                            $event['participants_min'] = null;
                        }

                        // check if participants max set
                        if (Input::post('part_max'))
                        {
                            // check if it have changed
                            if (Input::post('part_max') != $event_model->participants_max)
                            {
                                // string has changed, check if its numeric
                                if (is_numeric(Input::post('part_max')))
                                {
                                    // max participants is numeric, check if its not real number
                                    if (strpos(Input::post('part_max'), '.') != false)
                                    {
                                        $is_error = true;
                                        $errors[] = 'Maksimālajam dalībnieku skaitam jābūt veselam skaitlim!';
                                    }
                                    else
                                    {
                                        // min participants is integer, check if positive
                                        if (Input::post('part_max') > 0)
                                        {
                                            // min participants is positive integer
                                            // check if min value set
                                            if ($part_min_valid)
                                            {
                                                // check if max bigger than min participants
                                                if (Input::post('part_max') >= $part_min)
                                                {
                                                    // values are correct, check if not equal
                                                    if (Input::post('part_max') == $part_min)
                                                    {
                                                        $is_error = true;
                                                        $errors[] = 'Maksimālā un minimālā dalībnieku skaita vērtības sakrīt!';
                                                    }
                                                    else
                                                    {
                                                        $event['participants_max'] = Input::post('part_max');
                                                    }
                                                }
                                                else
                                                {
                                                    $is_error = true;
                                                    $errors[] = 'Minimālajam dalībnieku ir jābūt mazākam par maksimālo dalībnieku skaitu!';
                                                }
                                            }
                                            else
                                            {
                                                // min participants not set, set max participants
                                                $event['participants_max'] = Input::post('part_max');
                                            }

                                        }
                                        else
                                        {
                                            $is_error = true;
                                            $errors[] = 'Minimālajam dalībnieku skaitam jābūt pozitīvam skaitlim!';
                                        }
                                    }
                                }
                                else
                                {
                                    // max participants wasn't numeric
                                    $is_error = true;
                                    $errors[] = 'Maksimālajam dalībnieku skaitam jābūt skaitlim!';
                                }
                            }
                            else
                            {
                                // max participants hasn't changed, check if possible
                                // new min participants value set and cnonflicts with old max participatns value
                                if (Input::post('part_min') <= $event_model->participants_max)
                                {
                                    // values are correct, check if not equal
                                    if ($event_model->participants_max == $part_min)
                                    {
                                        $is_error = true;
                                        $errors[] = 'Maksimālā un minimālā dalībnieku skaita vērtības sakrīt!';
                                    }
                                }
                                else
                                {
                                    $is_error = true;
                                    $errors[] = 'Minimālajam dalībnieku ir jābūt mazākam par maksimālo dalībnieku skaitu!';
                                }
                            }
                        }
                        else
                        {
                            // if not set, delete previous value
                            $event['participants_max'] = null;
                        }



                        // check if entry fee set
                        if (Input::post('entry_fee'))
                        {
                            // check if it have changed
                            if (Input::post('entry_fee') != $event_model->entry_fee)
                            {
                                // entry fee set, check if its positive number
                                if ( ! is_numeric(Input::post('entry_fee')) and Input::post('entry_fee') <= 0)
                                {
                                    $is_error = true;
                                    $errors[] = 'Ieejas maksai jābūt pozitīvam skaitlim!';
                                }
                                else
                                {
                                    $event['entry_fee'] = Input::post('entry_fee');
                                }
                            }
                        }
                        else
                        {
                            // if not set, delete previous value
                            $event['entry_fee'] = null;
                        }

                        // check if takeway items set
                        if (Input::post('takeaway'))
                        {
                            // check if it have changed
                            if (Input::post('takeaway') != $event_model->takeaway)
                            {
                                $event['takeaway'] = Input::post('takeaway');
                            }
                        }
                        else
                        {
                            // if not set, delete previous value
                            $event['takeaway'] = null;
                        }

                        // check if dress code set
                        if (Input::post('dress_code'))
                        {
                            // check if it have changed
                            if (Input::post('dress_code') != $event_model->dress_code)
                            {
                                $event['dress_code'] = Input::post('dress_code');
                            }
                        }
                        else
                        {
                            // if not set, delete previous value
                            $event['dress_code'] = null;
                        }

                        // check if assistants set
                        if (Input::post('assistants'))
                        {
                            // check if it have changed
                            if (Input::post('assistants') != $event_model->assistants)
                            {
                                $event['assistants'] = Input::post('assistants');
                            }
                        }
                        else
                        {
                            // if not set, delete previous value
                            $event['assistants'] = null;
                        }

                        if ( ! $is_error)
                        {
                            // form valid, try to save it
                            $event_model->set($event);

                            if ($event_model and $event_model->save())
                            {
                                // event successfully edited and saved
                                Session::set_flash('success', 'Pasākums "'.$event_model->title.'" veiksmīgi izmainīts.');
                                Response::redirect('event/view/'.$event_model->event_id);
                            }
                            else
                            {
                                Session::set_flash('errors', 'Piedod, bet kaut kas nogāja greizi, mēģini vēlreiz!');
                                $this->template->page_title = 'Izveido pasākumu!';
                                $this->template->content = View::forge('event/create');
                            }
                        }
                        else
                        {
                            // some error in validation, render editing form with errors
                            Session::set_flash('errors', $errors);
                            $this->template->page_title = 'Izveido pasākumu!';
                            $this->template->content = View::forge('event/create');
                        }
                    }
                    else
                    {
                        // No form submited, render edit form with existing values
                        $query = Model_Orm_Event::query()->where('event_id', $event_id);
                        $event_obj = $query->get_one();

                        // save mandetory model atributes in post array
                        $_POST['title'] = $event_obj->title;
                        $_POST['link_title'] = $event_obj->event_id;
                        $_POST['desc'] = $event_obj->description;
                        $_POST['location'] = $event_obj->location;
                        $_POST['date'] = $event_obj->date;

                        // check for additional attributes
                        is_null($event_obj->participants_min) or $_POST['part_min'] = $event_obj->participants_min;
                        is_null($event_obj->participants_max) or $_POST['part_max'] = $event_obj->participants_max;
                        is_null($event_obj->entry_fee) or $_POST['entry_fee'] = $event_obj->entry_fee;
                        is_null($event_obj->takeaway) or $_POST['takeaway'] = $event_obj->takeaway;
                        is_null($event_obj->dress_code) or $_POST['dress_code'] = $event_obj->dress_code;
                        is_null($event_obj->assistants) or $_POST['assistants'] = $event_obj->assistants;

                        $this->template->page_title = 'Izlabo '.$event_obj->title;
                        $this->template->content = View::forge('event/edit');
                        $this->template->content->form_title = 'Izlabo '.$event_obj->title;
                    }
                }
                else
                {
                    // user is blocked and can't edit event
                    $error[] = 'Tu esi bloķēts un tev nav pieejas labot šo pasākumu!';
                    Session::set_flash('errors', $error);
                    Response::redirect('event/view/'.$event_id);
                }
            }
            else
            {
                // user doesn't have access to edit this event
                $error[] = 'Tev nav pieejas labot šo pasākumu!';
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