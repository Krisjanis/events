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
}

?>
