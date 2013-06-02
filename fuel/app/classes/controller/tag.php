<?php
/**
 * The Tag Controller.
 *
 * Has function for comment creating, editing, deleting
 *
 * @package  app
 * @extends  Public
 */
class Controller_Tag  extends Controller_Public
{
    public function action_create()
    {
        if (Auth::has_access('tag.create'))
        {
            if (Input::method() == 'POST')
            {
                // tag submited, validate it
                if (Input::post('title') and Input::post('title') != '')
                {
                    // comment submited, check if tag already exists
                    $query = Model_Orm_Tag::query()->where('title', Input::post('title'));
                    $tag_obj = $query->get_one();
                    if (empty($tag_obj))
                    {
                        // tag doesn't exist, create it
                        $user_id = Auth::instance()->get_user_id();
                        $user_id = $user_id[1];

                        $tag = array(
                            'author_id'    => $user_id,
                            'title'        => Input::post('title'),
                            'event_count'  => 0
                        );
                        $new_tag = Model_Orm_Tag::forge($tag);

                        if ($new_tag and $new_tag->save())
                        {
                            Session::set_flash('success', 'Birka veiksmīgi pievienota');
                            Response::redirect('/');
                        }
                    }
                    else
                    {
                        // tag already exists
                        $errors[] = 'Birka jau ekistē!';
                        Session::set_flash('errors', $errors);
                        Response::redirect('tag/create');
                    }
                }
                else
                {
                    // tag not set
                    $errors[] = 'Ievadi birku!';
                    Session::set_flash('errors', $errors);
                    Response::redirect('tag/create');
                }
            }
            else
            {
                // no form submited, render create form
                $this->template->page_title = 'Pievieno birku';
                $this->template->content = View::forge('tag/create');

            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Displays events with given tag
     *
     * @param string $tag_title is id of needed tag which events needs to be viewed
     */
    public function action_view($tag_id = null)
    {
        // if no tag id given redirect to home page
        is_null($tag_id) and Response::redirect('/');

        // get events with given tag
        $query = Model_Orm_HasTag::query()->where('tag_id', $tag_id);
        $has_tag_obj = $query->get();

        // check if event found
        if ( ! empty($has_tag_obj))
        {
            // get tag title
            $query = Model_Orm_Tag::query()->where('tag_id', $tag_id);
            $tag_title = $query->get_one()->title;

            // get event data
            $events = array();
            $i = 0;
            foreach ($has_tag_obj as $has_tag)
            {
                $query = Model_Orm_Event::query()->where('event_id', $has_tag->event_id);
                $event_obj = $query->get_one();

                $events[$i]['id'] = $event_obj->event_id;
                $events[$i]['title'] = $event_obj->title;
                $events[$i]['desc'] = $event_obj->description;
                $i++;
            }

            $this->template->page_title = $tag_title;
            $this->template->content = View::forge('tag/view');
            $this->template->content->set('events', $events);
            $this->template->content->set('title', $tag_title);
        }
        else
        {
            // no events found
            $error[] = 'Birka nav izmantota nevienā pasākumā!';
            Session::set_flash('errors', $error);
            Response::redirect('event/home');
        }
    }

    /**
     * Searches for events with given tag combination
     */
    public function action_search()
    {
        $this->template->page_title = 'Meklē birkas';
        $this->template->content = View::forge('tag/search');
        if (Input::method() == 'POST')
        {
            // tags submited, validate it
            if (Input::post('tags') and Input::post('tags') != '')
            {
                $tags = explode(',', Input::post('tags'));
                if (sizeof($tags) > 1)
                {
                    // searching for more than one tag, check if not too many
                    if (sizeof($tags) > 7)
                    {
                        // tag not set
                        $errors[] = 'Tik daudz birkas pasākumam nevar būt, maskimālais skaits ir 7!';
                        Session::set_flash('errors', $errors);
                        Response::redirect('tag/search');
                    }

                    // build select for selecting tag ID
                    $select = '';
                    foreach ($tags as $tag)
                    {
                        $tag = strtolower(trim($tag));
                        $select .= "'$tag', ";
                    }
                    $select = substr($select, 0, -2);

                    // select all needed tag IDs
                    $tag_ids_obj = DB::query("SELECT tag_id FROM `tags` WHERE `title` IN ($select)")->as_object('Model_Orm_Tag')->execute();
                    // check if all tags are valid
                    if (sizeof($tag_ids_obj) != sizeof($tags))
                    {
                        // some tags don't exists
                        $errors[] = 'Kāda no birkām neeksistē!';
                        Session::set_flash('errors', $errors);
                        Response::redirect('tag/search');
                    }

                    // get first tags events
                    $query = Model_Orm_HasTag::query()->where('tag_id', $tag_ids_obj[0]->tag_id);
                    $has_tag_obj = $query->get();
                    $valid_events = array();
                    $fist_tag_id = $tag_ids_obj[0]->tag_id;

                    foreach ($has_tag_obj as $has_tag)
                    {
                        $valid_events[] = $has_tag->event_id;
                    }

                    // search for rest of tags
                    foreach ($tag_ids_obj as $tag_id)
                    {
                        if ($tag_id->tag_id != $fist_tag_id)
                        {
                            // not first tag, get tags events
                            $query = Model_Orm_HasTag::query()->where('tag_id', $tag_id->tag_id);
                            $has_tag_obj = $query->get();

                            $possible_events = array();
                            foreach ($has_tag_obj as $event)
                            {
                                // save all event is in array
                                $possible_events[] = $event->event_id;
                            }

                            // valid events ar only those, which are present in both arrays
                            $valid_events = array_intersect($valid_events, $possible_events);

                            // check if its worth to go on
                            if (empty($valid_events))
                            {
                                // no such event with these tags
                                $errors[] = 'Neeksistē pasākums ar šādām birkām!';
                                Session::set_flash('errors', $errors);
                                Response::redirect('tag/search');
                            }
                        }
                    }
                    if ( ! empty($valid_events))
                    {
                        // events found with all needed tags
                        $events = array();
                        $i = 0;
                        foreach ($valid_events as $event_id)
                        {
                            // get event details
                            $query = Model_Orm_Event::query()->where('event_id', $event_id);
                            $event_obj = $query->get_one();

                            $events[$i]['id'] = $event_obj->event_id;
                            $events[$i]['title'] = $event_obj->title;
                            $events[$i]['desc'] = $event_obj->description;
                            $i++;
                        }
                        $this->template->content->set('events', $events);
                    }
                    else
                    {
                        // no such event with these tags
                        $errors[] = 'Neeksistē pasākums ar šādām birkām!';
                        Session::set_flash('errors', $errors);
                        Response::redirect('tag/search');
                    }
                }
                else
                {
                    // searching for only one tag
                    $tag = strtolower(trim($tags[0]));

                    // check if tag exists
                    $query = Model_Orm_Tag::query()->where('title', $tag);
                    $tag_obj = $query->get_one();

                    if ( ! empty($tag_obj))
                    {
                        // tag exists, get its events
                        $query = Model_Orm_HasTag::query()->where('tag_id', $tag_obj->tag_id);
                        $has_tag_obj = $query->get();

                        // get events details
                        $events = array();
                        $i = 0;
                        foreach ($has_tag_obj as $event)
                        {
                            $query = Model_Orm_Event::query()->where('event_id', $event->event_id);
                            $event_obj = $query->get_one();

                            $events[$i]['id'] = $event_obj->event_id;
                            $events[$i]['title'] = $event_obj->title;
                            $events[$i]['desc'] = $event_obj->description;
                            $i++;
                        }
                        $this->template->content->set('events', $events);
                    }
                    else
                    {
                        // tag doesn't exist
                        $errors[] = 'Šāda birka neeksistē!';
                        Session::set_flash('errors', $errors);
                        Response::redirect('tag/search');
                    }
                }
            }
            else
            {
                // tag not set
                $errors[] = 'Ievadi birku!';
                Session::set_flash('errors', $errors);
                Response::redirect('tag/search');
            }
        }
    }
}

?>
