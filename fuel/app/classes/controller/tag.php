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
            $error[] = 'Birka netika atrasta!';
            Session::set_flash('errors', $error);
            Response::redirect('event/home');
        }
    }
}

?>
