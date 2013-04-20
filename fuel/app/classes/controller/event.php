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

        // get event
        $query = Model_Orm_Event::query()->where('link_title', $linkTitle);
        $eventObj = $query->get_one();

        // save mandetory model atributes in an array
        $event = array(
            'type' => $eventObj->type,
            'title' => $eventObj->title,
            'link_title' => $eventObj->link_title,
            'desc' => $eventObj->description,
            'location' => $eventObj->location,
            'date' => $eventObj->date,
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
            $event['assist'] = $eventObj->assistants;
        }

        $this->template->page_title = $event['title'] . ' | Pasākumu organizēšanas vietne';
        $this->template->content = View::forge('event/view');
        $this->template->content->set('event', $event);
    }

    /**
     * Validates creation form and creates new event
     */
    public function action_create() {
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
                    Session::set_flash('success', 'Added a new agenda item to the event "' . $newEvent->title . '".');

                    //Response::redirect('event/view/' . $event['link_title']);
                }
                else {
                    Session::set_flash('errors', 'Piedod, bet kaut kas nogāja greizi, mēģini vēlreiz!');
                    $this->template->page_title = 'Izveido pasākumu!';
                    $this->template->content = View::forge('event/create');
                }
            }
            else {
                // Some error in validation, render registeration form with errors
                Session::set_flash('errors', $errors);
                $this->template->page_title = 'Izveido pasākumu!';
                $this->template->content = View::forge('event/create');
            }
        }
        else {
            // No form submited, render creation form
            $this->template->page_title = 'Izveido pasākumu!';
            $this->template->content = View::forge('event/create');
        }
    }
}

?>