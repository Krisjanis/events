<?php
/**
 * The Event Controller.
 *
 * Event controller validates event form and creates new event,
 *
 * @package  app
 * @extends  Public
 */
class Controller_Event extends Controller_Public
{
    /**
     * Displays event with given link title
     *
     * @param string $linkTitle unique string of needed event
     */
    public function action_view($linkTitle = null) {
        // if no link title given redirect to home page
        if (is_null($linkTitle)) {
            Response::redirect('/');
        }

        // id of current user and author and organizator access
        $userId = Auth::instance()->get_user_id();
        $userId = $userId[1];
        $organizatorAccess = false;
        $authorAccess = false;

        // get event
        $query = Model_Orm_Event::query()->where('link_title', $linkTitle);
        $eventObj = $query->get_one();

        // if no user found redirect to home page
        if (is_null($eventObj)) {
            Response::redirect('/');
        }

        // save mandetory model atributes in an array
        $event = array(
            'id'         => $eventObj->event_id,
            'type'       => $eventObj->type,
            'title'      => $eventObj->title,
            'link_title' => $eventObj->link_title,
            'desc'       => $eventObj->description,
            'location'   => $eventObj->location,
            'date'       => $eventObj->date,
        );

        // check for additional model attributes
        if (!is_null($eventObj->participants_min)) {
            $event['part_min'] = $eventObj->participants_min;
        }
        if (!is_null($eventObj->participants_max)) {
            $event['part_max'] = $eventObj->participants_max;
        }
        if (!is_null($eventObj->entry_fee)) {
            $event['entry_fee'] = $eventObj->entry_fee;
        }
        if (!is_null($eventObj->takeaway)) {
            $event['takeaway'] = $eventObj->takeaway;
        }
        if (!is_null($eventObj->dress_code)) {
            $event['dress_code'] = $eventObj->dress_code;
        }
        if (!is_null($eventObj->assistants)) {
            $event['assistants'] = $eventObj->assistants;
        }

        // get events author
        $organizators = array();
        $query = Model_Orm_Organizator::query()
            ->where('event_id', $eventObj->event_id)
            ->and_where_open()
                ->where('is_author', 1)
            ->and_where_close();
        $authorId = $query->get_one()->user_id;

        $query = Model_Orm_User::query()->where('user_id', $authorId);
        $author = $query->get_one();

        $organizators['author'] = array(
            'id'        => $author->user_id,
            'username'  => $author->username,
        );

        if ($author->user_id == $userId) {
            $authorAccess = true;
            $organizatorAccess = true;
        }
        // get organizators
        $query = Model_Orm_Organizator::query()
            ->where('event_id', $eventObj->event_id)
            ->and_where_open()
                ->where('is_author', 0)
            ->and_where_close();
        $organizatorsObj = $query->get();

        $organizators['organizators'] = array();
        $i = 0;

        // get username for each organizator
        foreach($organizatorsObj as $o) {
            $query = Model_Orm_User::query()->where('user_id', $o->user_id);
            $organizator = $query->get_one();
            $organizators['organizators'][$i]['id'] = $organizator->user_id;
            $organizators['organizators'][$i]['username'] = $organizator->username;

            // check if current user is an organizator for current event
            if ($organizator->user_id == $userId) {
                $organizatorAccess = true;
            }
            $i++;
        }

        $this->template->page_title = $event['title'];
        $this->template->content = View::forge('event/view');
        $this->template->content->set('event', $event);
        $this->template->content->set('organizators', $organizators);
        $this->template->content->set('organizatorAccess', $organizatorAccess);
        $this->template->content->set('authorAccess', $authorAccess);
    }

    /**
     * Validates creation form and creates new event
     */
    public function action_create() {
        // check if user has accesss to create new event
        if (Auth::has_access('event.create')) {
            if (Input::method() == 'POST') {
                // form submited, validate it
                $is_error = false;
                $errors = array();
                $event = array();

                // check type set
                if (Input::post('type') == 'private') {
                    $event['type'] = 'private';
                }
                else {
                    $event['type'] = 'public';
                }

                // check if title set
                if (!Input::post('title')) {
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma nosaukumu!';
                }
                else {
                    $event['title'] = Input::post('title');
                }

                // check if link title is set
                if (Input::post('link_title')) {
                    if(!preg_match('/^[a-zA-Z0-9]+$/', Input::post('link_title')) == 1) {
                        // string contained illegal characters
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadiet pasākuma saites nosaukumu, tas drīkst saturēt tikai burtus bez mīkstinājuma zīmēm un ciparus!';
                    }
                    else {
                        // check if link doesn't already exist in system
                        $link = Model_Orm_Event::find('all', array(
                            'where' => array(
                                array('link_title', Input::post('link_title')),
                            ),
                        ));
                        if (empty($link)) {
                            $event['link_title'] = Input::post('link_title');
                        }
                        else {
                            $is_error = true;
                            $errors[] = 'Saites nosaukums jau aizņemts, izvēlies citu!';
                        }
                    }
                }
                else {
                    // link title not set
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma saites nosaukumu!';
                }

                // check if dexcription set
                if (!Input::post('desc')) {
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma aprakstu!';
                }
                else {
                    $event['description'] = Input::post('desc');
                }

                // check if location set
                if (!Input::post('location')) {
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma atrašanās vietu!';
                }
                else {
                    $event['location'] = Input::post('location');
                }

                // check if date set
                if (!Input::post('date')) {
                    $is_error = true;
                    $errors[] = 'Lūdzu ievadiet pasākuma norises datumu!';
                }
                else {
                    $event['date'] = Input::post('date');
                }

                $partMinValid = false;
                // check if participants min set
                if (Input::post('part_min')) {
                    // min participants set, check if its numeric
                    if (is_numeric(Input::post('part_min'))) {
                        // min participants is numeric, check if its not real number
                        if (strpos(Input::post('part_min'), '.') !== false) {
                            $is_error = true;
                            $errors[] = 'Minimālajam dalībnieku skaitam jābūt veselam skaitlim!';
                        }
                        else {
                            // min participants is integer, check if positive
                            if (Input::post('part_min') <= 0) {
                                $is_error = true;
                                $errors[] = 'Minimālajam dalībnieku skaitam jābūt pozitīvam skaitlim!';
                            }
                            else {
                                $event['participants_min'] = Input::post('part_min');
                                $partMinValid = true;
                            }
                        }
                    }
                    else {
                        // min participants wasn't numeric
                        $is_error = true;
                        $errors[] = 'Minimālajam dalībnieku skaitam jābūt skaitlim!';
                    }
                }

                // check if participants max set
                if (Input::post('part_max')) {
                    // max participants set, check if its numeric
                    if (is_numeric(Input::post('part_max'))) {
                        // max participants is numeric, check if its not real number
                        if (strpos(Input::post('part_max'), '.') !== false) {
                            $is_error = true;
                            $errors[] = 'Maksimālajam dalībnieku skaitam jābūt veselam skaitlim!';
                        }
                        else {
                            // min participants is integer, check if positive
                            if (Input::post('part_max') > 0) {
                                // min participants is positive integer
                                // check if max bigger than min participants if both set
                                if ($partMinValid && Input::post('part_max') >= Input::post('part_min')) {
                                    // values are correct, check if not equal
                                    if (Input::post('part_max') == Input::post('part_min')) {
                                        $is_error = true;
                                        $errors[] = 'Maksimālā un minimālā dalībnieku skaita vērtības sakrīt!';
                                    }
                                    else {
                                        $event['participants_max'] = Input::post('part_max');
                                    }
                                }
                                else {
                                    $is_error = true;
                                    $errors[] = 'Minimālajam dalībnieku ir jābūt mazākam par maksimālo dalībnieku skaitu!';
                                }
                            }
                            else {
                                $is_error = true;
                                $errors[] = 'Minimālajam dalībnieku skaitam jābūt pozitīvam skaitlim!';
                            }
                        }
                    }
                    else {
                        // max participants wasn't numeric
                        $is_error = true;
                        $errors[] = 'Maksimālajam dalībnieku skaitam jābūt skaitlim!';
                    }
                }

                // check if entry fee set
                if (Input::post('entry_fee')) {
                    // entry fee set, check if its positive number
                    if (!is_numeric(Input::post('entry_fee')) && Input::post('entry_fee') <= 0) {
                        $is_error = true;
                        $errors[] = 'Ieejas maksai jābūt pozitīvam skaitlim!';
                    }
                    else {
                        $event['entry_fee'] = Input::post('entry_fee');
                    }
                }

                // check if takeway items set
                if (Input::post('takeaway')) {
                    $event['takeaway'] = Input::post('takeaway');
                }

                // check if dress code set
                if (Input::post('dress_code')) {
                    $event['dress_code'] = Input::post('dress_code');
                }

                // check if assistants set
                if (Input::post('assistants')) {
                    $event['assistants'] = Input::post('assistants');
                }

                if (!$is_error) {
                    // form valid, try to save it
                    $newEvent = Model_Orm_Event::forge($event);

                    if ($newEvent && $newEvent->save()) {
                        // save event organizator
                        $userId = Auth::instance()->get_user_id();
                        $organizator = array(
                            'event_id'  => $newEvent->event_id,
                            'user_id'   => $userId[1],
                            'is_author' => 1
                        );
                        $newOrganizator = Model_Orm_Organizator::forge($organizator);

                        if ($newOrganizator && $newOrganizator->save()) {
                            // organizator successfully added
                            Session::set_flash('success', 'Pasākums "' . $newEvent->title . '" veiksmīgi izveidots.');
                            Response::redirect('event/view/' . $event['link_title']);
                        }
                        else {
                            // something wen't wrong with adding organizator
                            Session::set_flash('errors', 'Kaut kas nogāja greizi ar pasākuma autoru, mēģini vēlreiz!');
                            $this->template->page_title = 'Izveido pasākumu!';
                            $this->template->content = View::forge('event/create');
                            $this->template->content->formTitle = 'Izveido jaunu pasākumu!';
                        }
                    }
                    else {
                        Session::set_flash('errors', 'Piedod, bet kaut kas nogāja greizi, mēģini vēlreiz!');
                        $this->template->page_title = 'Izveido pasākumu!';
                        $this->template->content = View::forge('event/create');
                        $this->template->content->formTitle = 'Izveido jaunu pasākumu!';
                    }
                }
                else {
                    // some error in validation, render registeration form with errors
                    Session::set_flash('errors', $errors);
                    $this->template->page_title = 'Izveido pasākumu!';
                    $this->template->content = View::forge('event/create');
                    $this->template->content->formTitle = 'Izveido jaunu pasākumu!';
                }
            }
            else {
                // No form submited, render creation form
                $this->template->page_title = 'Izveido pasākumu!';
                $this->template->content = View::forge('event/create');
                $this->template->content->formTitle = 'Izveido jaunu pasākumu!';
            }
        }
        else {
            // user doesn't have access to creeating new event
            Response::redirect('/');
        }
    }

    /**
     * Add given organizator to given event
     */
    public function action_add_organizator() {
        // check if user has access to adding orginazator
        $userId = Auth::instance()->get_user_id();
        $query = Model_Orm_Organizator::query()
            ->where('event_id', Input::post('event_id'))
            ->and_where_open()
                 ->where('user_id', $userId[1])
            ->and_where_close();

        $hasAccess = $query->get_one();

        if (!is_null($hasAccess)) {
            if (Input::method() == 'POST') {
                if (Input::post('organizator')) {
                    // check if user exists
                    $query = Model_Orm_User::query()->where('username', Input::post('organizator'));
                    $user = $query->get_one();
                    if (!is_null($user)) {
                        // check if it isn't already an organizator for this event
                        $query = Model_Orm_Organizator::query()
                            ->where('event_id', Input::post('event_id'))
                            ->and_where_open()
                                ->where('user_id', $user->user_id)
                            ->and_where_close();
                        $organizatorObj = $query->get_one();
                        if (is_null($organizatorObj)) {
                            // give this user organizer status
                            $organizator = array(
                                'event_id'  => Input::post('event_id'),
                                'user_id'   => $user->user_id,
                                'is_author' => 0
                            );
                            $newOrganizator = Model_Orm_Organizator::forge($organizator);

                            if ($newOrganizator && $newOrganizator->save()) {
                                // users is now organizer
                                Session::set_flash('success', $user->username . ' veiksmīgi pievienots kā organizators.');
                                Response::redirect('event/view/' . Input::post('link_title'));
                            }
                        }
                        else {
                            // user is already organizator for this event
                            $error[] = 'Piedod, bet šis lietotājs jau ir organizators šim pasākumam.';
                            Session::set_flash('errors', $error);
                        }
                    }
                    else {
                        // user doesn't exists
                        $error[] = 'Piedod, bet šis lietotājs neekistē.';
                        Session::set_flash('errors', $error);
                    }
                }
                else {
                    // no username specified
                    $error[] = 'Norādi lietotājvārdu, kuru vēlies pievienot.';
                    Session::set_flash('errors', $error);
                }
            }
            Response::redirect('event/view/' . Input::post('link_title'));
        }
        else {
            // user doesn't have access to adding organizator
            $error[] = 'Piedod, bet tev nav pieejas pievienot organizatoru šim pasākumam.';
            Session::set_flash('errors', $error);
            Response::redirect('event/view/' . Input::post('link_title'));
        }
    }

    /**
     * Delete give organizator form given event
     *
     * @param string $linkTitle title link of event to which needs to be redirected
     * @param integer $eventId ID of event form which is needed to delete given user
     * @param integer $userId ID of user needed to be deleted
     */
    public function action_delete_organizator($linkTitle = '', $eventId = '', $userId = '') {
        // if user has access to delete user, delete it
        $curUserId = Auth::instance()->get_user_id();
        $query = Model_Orm_Organizator::query()
            ->where('event_id', Input::post('event_id'))
                 ->and_where_open()
                 ->where('user_id', $curUserId[1])
            ->and_where_close();
        $hasAccess = $query->get_one();

        if (!is_null($hasAccess)) {
            $organizatorModel = Model_Orm_Organizator::query()
                ->where('event_id', $eventId)
                ->and_where_open()
                    ->where('user_id', $userId)
                ->and_where_close();
            $organizator = $organizatorModel->get_one();
            if ($organizator->is_author == 1) {
                // can't delete events author
                $error[] = 'Nedrīkst dzēst pasākuma autoru!';
                Session::set_flash('errors', $error);
                Response::redirect('event/view/' . $linkTitle);
            }
            else {
                // isn't events author, delte it
                $organizator->delete();

                Session::set_flash('success', 'Lietotājs veiksmīgi izdzēsts kā organizators.');
                Response::redirect('event/view/' . $linkTitle);
            }
        }
        else {
            $error[] = 'Tev nav pieejas dzēst šo lietotāju kā organizatoru.';
            Session::set_flash('errors', $error);
            Response::redirect('event/view/' . $linkTitle);
        }
    }

    /**
     * Edit atributes of given event
     *
     * @param integer $linkTitle is link to were redirect
     * @param integer $eventId is ID of event needed to be edited
     */
    public function action_edit_attribute($linkTitle = '', $eventId = '') {
        // check if user has access to editing event
        $UserId = Auth::instance()->get_user_id();
        $query = Model_Orm_Organizator::query()
            ->where('event_id', $eventId)
                 ->and_where_open()
                 ->where('user_id', $UserId[1])
            ->and_where_close();
        $hasAccess = $query->get_one();

        if (!is_null($hasAccess)) {
            // user has acess to edit this event, check if its not blocked
            if (Auth::has_access('event.edit_attribute')) {
                if (Input::method() == 'POST') {
                    // get existing attribute values
                    $eventModel = Model_Orm_Event::find($eventId);
                    // form submited, validate it
                    $is_error = false;
                    $errors = array();
                    $event = array();

                    // check if title set
                    if (Input::post('title')) {
                        // check if it have changed
                        if (Input::post('title') != $eventModel->title) {
                            $event['title'] = Input::post('title');
                        }
                    }
                    else {
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadiet pasākuma nosaukumu!';
                    }

                    // check if link title is set
                    if (Input::post('link_title')) {
                        // check if it have changed
                        if (Input::post('link_title') != $eventModel->link_title) {
                            // string has changed, validate it
                            if(!preg_match('/^[a-zA-Z0-9]+$/', Input::post('link_title')) == 1) {
                                // string contained illegal characters
                                $is_error = true;
                                $errors[] = 'Lūdzu ievadiet pasākuma saites nosaukumu, tas drīkst saturēt tikai burtus bez mīkstinājuma zīmēm un ciparus!';
                            }
                            else {
                                // check if link doesn't already exist in system
                                $link = Model_Orm_Event::find('all', array(
                                    'where' => array(
                                        array('link_title', Input::post('link_title')),
                                    ),
                                ));
                                if (empty($link)) {
                                    $event['link_title'] = Input::post('link_title');
                                }
                                else {
                                    $is_error = true;
                                    $errors[] = 'Saites nosaukums jau aizņemts, izvēlies citu!';
                                }
                            }
                        }
                    }
                    else {
                        // link title not set
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadiet pasākuma saites nosaukumu!';
                    }

                    // check if dexcription set
                    if (Input::post('desc')) {
                        // check if it have changed
                        if (Input::post('desc') != $eventModel->description) {
                            $event['description'] = Input::post('desc');
                        }
                    }
                    else {
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadiet pasākuma aprakstu!';
                    }

                    // check if location set
                    if (Input::post('location')) {
                        // check if it have changed
                        if (Input::post('location') != $eventModel->location) {
                            $event['location'] = Input::post('location');
                        }
                    }
                    else {
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadiet pasākuma atrašanās vietu!';
                    }

                    // check if date set
                    if (Input::post('date')) {
                        // check if it have changed
                        if (Input::post('date') != $eventModel->date) {
                            $event['date'] = Input::post('date');
                        }
                    }
                    else {
                        $is_error = true;
                        $errors[] = 'Lūdzu ievadiet pasākuma norises datumu!';
                    }

                    $partMinValid = false;
                    // check if participants min set
                    if (Input::post('part_min')) {
                        // check if it have changed
                        if (Input::post('part_min') != $eventModel->participants_min) {
                            // string has changed, check if its numeric
                            if (is_numeric(Input::post('part_min'))) {
                                // min participants is numeric, check if its not real number
                                if (strpos(Input::post('part_min'), '.') != false) {
                                    $is_error = true;
                                    $errors[] = 'Minimālajam dalībnieku skaitam jābūt veselam skaitlim!';
                                }
                                else {
                                    // min participants is integer, check if positive
                                    if (Input::post('part_min') <= 0) {
                                        $is_error = true;
                                        $errors[] = 'Minimālajam dalībnieku skaitam jābūt pozitīvam skaitlim!';
                                    }
                                    else {
                                        $event['participants_min'] = Input::post('part_min');
                                        $partMinValid = true;
                                        $partMin = Input::post('part_min');
                                    }
                                }
                            }
                            else {
                                // min participants wasn't numeric
                                $is_error = true;
                                $errors[] = 'Minimālajam dalībnieku skaitam jābūt skaitlim!';
                            }
                        }
                        else {
                            // string hasn't changed, but still it exists
                            $partMinValid = true;
                            $partMin = $eventModel->participants_min;
                        }
                    }
                    else {
                        // if not set, delete previous value
                        $event['participants_min'] = null;
                    }

                    // check if participants max set
                    if (Input::post('part_max')) {
                        // check if it have changed
                        if (Input::post('part_max') != $eventModel->participants_max) {
                            // string has changed, check if its numeric
                            if (is_numeric(Input::post('part_max'))) {
                                // max participants is numeric, check if its not real number
                                if (strpos(Input::post('part_max'), '.') != false) {
                                    $is_error = true;
                                    $errors[] = 'Maksimālajam dalībnieku skaitam jābūt veselam skaitlim!';
                                }
                                else {
                                    // min participants is integer, check if positive
                                    if (Input::post('part_max') > 0) {
                                        // min participants is positive integer
                                        // check if min value set
                                        if ($partMinValid) {
                                            // check if max bigger than min participants
                                            if (Input::post('part_max') >= $partMin) {
                                                // values are correct, check if not equal
                                                if (Input::post('part_max') == $partMin) {
                                                    $is_error = true;
                                                    $errors[] = 'Maksimālā un minimālā dalībnieku skaita vērtības sakrīt!';
                                                }
                                                else {
                                                    $event['participants_max'] = Input::post('part_max');
                                                }
                                            }
                                            else {
                                                $is_error = true;
                                                $errors[] = 'Minimālajam dalībnieku ir jābūt mazākam par maksimālo dalībnieku skaitu!';
                                            }
                                        }
                                        else {
                                            // min participants not set, set max participants
                                            $event['participants_max'] = Input::post('part_max');
                                        }

                                    }
                                    else {
                                        $is_error = true;
                                        $errors[] = 'Minimālajam dalībnieku skaitam jābūt pozitīvam skaitlim!';
                                    }
                                }
                            }
                            else {
                                // max participants wasn't numeric
                                $is_error = true;
                                $errors[] = 'Maksimālajam dalībnieku skaitam jābūt skaitlim!';
                            }
                        }
                        else {
                            // max participants hasn't changed, check if possible
                            // new min participants value set and cnonflicts with old max participatns value
                            if (Input::post('part_min') <= $eventModel->participants_max) {
                                // values are correct, check if not equal
                                if ($eventModel->participants_max == $partMin) {
                                    $is_error = true;
                                    $errors[] = 'Maksimālā un minimālā dalībnieku skaita vērtības sakrīt!';
                                }
                            }
                            else {
                                $is_error = true;
                                $errors[] = 'Minimālajam dalībnieku ir jābūt mazākam par maksimālo dalībnieku skaitu!';
                            }
                        }
                    }
                    else {
                        // if not set, delete previous value
                        $event['participants_max'] = null;
                    }



                    // check if entry fee set
                    if (Input::post('entry_fee')) {
                        // check if it have changed
                        if (Input::post('entry_fee') != $eventModel->entry_fee) {
                            // entry fee set, check if its positive number
                            if (!is_numeric(Input::post('entry_fee')) && Input::post('entry_fee') <= 0) {
                                $is_error = true;
                                $errors[] = 'Ieejas maksai jābūt pozitīvam skaitlim!';
                            }
                            else {
                                $event['entry_fee'] = Input::post('entry_fee');
                            }
                        }
                    }
                    else {
                        // if not set, delete previous value
                        $event['entry_fee'] = null;
                    }

                    // check if takeway items set
                    if (Input::post('takeaway')) {
                        // check if it have changed
                        if (Input::post('takeaway') != $eventModel->takeaway) {
                            $event['takeaway'] = Input::post('takeaway');
                        }
                    }
                    else {
                        // if not set, delete previous value
                        $event['takeaway'] = null;
                    }

                    // check if dress code set
                    if (Input::post('dress_code')) {
                        // check if it have changed
                        if (Input::post('dress_code') != $eventModel->dress_code) {
                            $event['dress_code'] = Input::post('dress_code');
                        }
                    }
                    else {
                        // if not set, delete previous value
                        $event['dress_code'] = null;
                    }

                    // check if assistants set
                    if (Input::post('assistants')) {
                        // check if it have changed
                        if (Input::post('assistants') != $eventModel->assistants) {
                            $event['assistants'] = Input::post('assistants');
                        }
                    }
                    else {
                        // if not set, delete previous value
                        $event['assistants'] = null;
                    }

                    if (!$is_error) {
                        // form valid, try to save it
                        $eventModel->set($event);

                        if ($eventModel && $eventModel->save()) {
                            // event successfully edited and saved
                            Session::set_flash('success', 'Pasākums "' . $eventModel->title . '" veiksmīgi izmainīts.');
                            Response::redirect('event/view/' . $eventModel->link_title);
                        }
                        else {
                            Session::set_flash('errors', 'Piedod, bet kaut kas nogāja greizi, mēģini vēlreiz!');
                            $this->template->page_title = 'Izveido pasākumu!';
                            $this->template->content = View::forge('event/create');
                        }
                    }
                    else {
                        // some error in validation, render editing form with errors
                        Session::set_flash('errors', $errors);
                        $this->template->page_title = 'Izveido pasākumu!';
                        $this->template->content = View::forge('event/create');
                    }
                }
                else {
                    // No form submited, render edit form with existing values
                    $query = Model_Orm_Event::query()->where('link_title', $linkTitle);
                    $eventObj = $query->get_one();

                    // save mandetory model atributes in post array
                    $_POST['event_id'] = $eventObj->event_id;
                    $_POST['title'] = $eventObj->title;
                    $_POST['link_title'] = $eventObj->link_title;
                    $_POST['desc'] = $eventObj->description;
                    $_POST['location'] = $eventObj->location;
                    $_POST['date'] = $eventObj->date;

                    // check for additional attributes
                    if (!is_null($eventObj->participants_min)) {
                        $_POST['part_min'] = $eventObj->participants_min;
                    }
                    if (!is_null($eventObj->participants_max)) {
                        $_POST['part_max'] = $eventObj->participants_max;
                    }
                    if (!is_null($eventObj->entry_fee)) {
                        $_POST['entry_fee'] = $eventObj->entry_fee;
                    }
                    if (!is_null($eventObj->takeaway)) {
                        $_POST['takeaway'] = $eventObj->takeaway;
                    }
                    if (!is_null($eventObj->dress_code)) {
                        $_POST['dress_code'] = $eventObj->dress_code;
                    }
                    if (!is_null($eventObj->assistants)) {
                        $_POST['assistants'] = $eventObj->assistants;
                    }

                    $this->template->page_title = 'Izlabo ' . $eventObj->title;
                    $this->template->content = View::forge('event/edit');
                    $this->template->content->formTitle = 'Izlabo ' . $eventObj->title;
                }
            }
            else {
                // user is blocked and can't edit event
                $error[] = 'Tu esi bloķēts un tev nav pieejas labot šo pasākumu!';
                Session::set_flash('errors', $error);
                Response::redirect('event/view/' . $linkTitle);
            }
        }
        else {
            // user doesn't have access to edit this event
            $error[] = 'Tev nav pieejas labot šo pasākumu!';
            Session::set_flash('errors', $error);
            Response::redirect('event/view/' . $linkTitle);
        }
    }
}

?>