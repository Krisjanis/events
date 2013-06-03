<?php
/**
 * The Comment Controller.
 *
 * Has function for comment creating, editing, deleting
 *
 * @package  app
 * @extends  Public
 */
class Controller_Comment extends Controller_Public
{
    /**
     * Creates new comment on event attribute
     *
     * string $atr is attribute to which this comment is adressed
     * string $event_id is id of event to which add comment
     */
    public function action_create($atr = null, $event_id = null)
    {
        if (Auth::has_access('comment.create'))
        {
            is_null($atr) and Response::redirect('/');
            is_null($event_id) and Response::redirect('/');

            $this->template->page_title = 'Pievieno komentāru!';
            $this->template->content = View::forge('comment/create');
            if ($atr == 'w' )
            {
                // 'whole' stands for comment to whole event
                $this->template->content->form_title = 'Pievieno komentāru pasākumam';
            }
            elseif ($atr == 'l')
            {
                // 'l' stands for comment to event location
                $this->template->content->form_title = 'Pievieno komentāru norises vietai';
            }
            elseif ($atr == 'd')
            {
                // 'd' stands for comment to event date
                $this->template->content->form_title = 'Pievieno komentāru norises laikam';
            }
            elseif ($atr == 'p')
            {
                // 'p' stands for comment to event participants
                $this->template->content->form_title = 'Pievieno komentāru dalībnieku skaitam';
            }
            elseif ($atr == 'f')
            {
                // 'f' stands for comment to event fee
                $this->template->content->form_title = 'Pievieno komentāru dalības maksai';
            }
            elseif ($atr == 't')
            {
                // 't' stands for comment to event takeaway
                $this->template->content->form_title = 'Pievieno komentāru līdzi ņemamajām lietām';
            }
            elseif ($atr == 'dc')
            {
                // 'dc' stands for comment to event dress code
                $this->template->content->form_title = 'Pievieno komentāru ģebšanās sitlam';
            }
            elseif ($atr == 'a')
            {
                // 'a' stands for comment to event assistants
                $this->template->content->form_title = 'Pievieno komentāru nepieciešamajiem palīgiem';
            }

            if (Input::method() == 'POST')
            {
                // comment submited, validate it
                if (Input::post('comment') and Input::post('comment') != '')
                {
                    // check if comment isn't too long
                    if (strlen(Input::post('comment')) <= 300)
                    {
                        // comments length is valid, create it
                        $user_id = Auth::instance()->get_user_id();
                        $user_id = $user_id[1];

                        $comment = array(
                            'author_id'  => $user_id,
                            'event_id'   => $event_id,
                            'attribute'  => $atr,
                            'message'    => Input::post('comment'),
                            'created_at' => Date::time()->get_timestamp()
                        );

                        $new_comment = Model_Orm_Comment::forge($comment);
                        $new_comment->save();

                        Session::set_flash('success', 'Komentārs veiksmīgi pievienots');
                        Response::redirect('event/view/'.$event_id);
                    }
                    else
                    {
                        // comment too long
                        $errors[] = 'Komentāra ziņa nevar būt garāka par 300 simboliem!';
                        Session::set_flash('errors', $errors);
                    }
                }
                else
                {
                    // comment not set
                    $errors[] = 'Ievadi komentāru!';
                    Session::set_flash('errors', $errors);
                }
            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Returns array of comments for given event and attriute
     *
     * @param string $atr is events attribute whichs comments are needed
     * @param string $event_id is id of event whichs comments are needed
     */
    public function action_view($atr = null, $event_id = null)
    {
        is_null($atr) and Response::redirect('/');
        is_null($event_id) and Response::redirect('/');

        // get all comments for given attribute in given event
        $comments_obj = Model_Orm_Comment::get_comment_by_event_and_attribute($event_id, $atr);

        // save each comment in array
        $comments = array();
        $i = 0;
        foreach ($comments_obj as $comment)
        {
            $comments[$i]['id'] = $comment->comment_id;
            $comments[$i]['author'] = $comment->author_id;
            $author = Model_Orm_User::find($comment->author_id);
            $comments[$i]['author_username'] = $author->username;
            $comments[$i]['message'] = $comment->message;

            // check if comment has been edited
            if (is_null($comment->edited_at)) {
                // comment hasn't been edited, get creation date
                $date = Date::forge($comment->created_at);
                $time_ago = 'Pirms ';
            }
            else
            {
                // comment has been edited, get edition date
                $date = Date::forge($comment->edited_at);
                $time_ago = 'Labots pirms ';
            }
            $time_ago_string = Date::time_ago($date, Date::time()->get_timestamp(), 'second');
            $time_ago_value = explode(' ', $time_ago_string);
            $time_ago_value = $time_ago_value[0];

            if ($time_ago_value < 1)
            {
                // just now
                $time_ago = 'Tikko';
            }
            elseif ($time_ago_value < 60)
            {
                // less than a minute ago
                $time_ago .= $time_ago_value.' sekundēm';
            }
            elseif ($time_ago_value < 120)
            {
                // a minute ago
                $time_ago .= 'minūtes';
            }
            elseif ($time_ago_value < 3600)
            {
                // less than a hour ago
                $time_ago_value = round($time_ago_value/60);
                $time_ago .= $time_ago_value.' minūtēm';
            }
            elseif ($time_ago_value < 7200)
            {
                // a hour ago
                $time_ago .= 'stundas';
            }
            elseif ($time_ago_value < 86400)
            {
                // less than a day ago
                $time_ago_value = round($time_ago_value/3600);
                $time_ago .= $time_ago_value.' stundām';
            }
            elseif ($time_ago_value < 172800)
            {
                // a day ago
                $time_ago .= 'dienas';
            }
            elseif ($time_ago_value < 2678400)
            {
                // less than a month ago
                $time_ago_value = round($time_ago_value/86400);
                $time_ago .= $time_ago_value.' dienām';
            }
            elseif ($time_ago_value < 5356800)
            {
                // a month ago
                $time_ago .= 'mēneša';
            }
            elseif ($time_ago_value < 32140800)
            {
                // less than a year ago
                $time_ago_value = round($time_ago_value/2678400);
                $time_ago .= $time_ago_value.' mēnešiem';
            }
            elseif ($time_ago_value < 64281600)
            {
                // less than two years ago
                $time_ago_value = round($time_ago_value/32140800);
                $time_ago .= 'gada';
            }
            else
            {
                // more than a year ago
                $time_ago_value = round($time_ago_value/32140800);
                $time_ago .= $time_ago_value.' gadiem';
            }

            $comments[$i]['created_at'] = $time_ago;
            $i++;
        }

        // check if function requested from other controller
        if( ! Request::is_hmvc())
        {
            Response::redirect('/');
        }
        else
        {
            return $comments;
        }
    }

    /**
     * Deletes given comment
     *
     * @param integer $comment_id is id if comment to be deleted
     * @param string $event_id is id of event to which this event is
     */
    public function action_delete($comment_id = null, $event_id = null)
    {
        // check if user has access to deleting comment
        if (Auth::has_access('comment.delete'))
        {
            is_null($event_id) and Response::redirect('/');
            is_null($comment_id) and Response::redirect('event/view/'.$event_id);

            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];

            $comment_obj = Model_Orm_Comment::get_comment($comment_id);

            // if comment found, check if has access to deleting
            if ( ! empty($comment_obj))
            {
                if ($comment_obj->author_id == $user_id)
                {
                    // has access, delete comment
                    if ($comment_obj->delete())
                    {
                        // comment successfuly deleted
                        Session::set_flash('success', 'Komentārs veiksmīgi izdzēsts');
                        Response::redirect('event/view/'.$event_id);
                    }
                    else
                    {
                        // could not delete comment
                        $error[] = 'Piedod, bet kaut kas nogāja greizi un neizdevās izdzēst komentāru.';
                        Session::set_flash('errors', $error);
                        Response::redirect('event/view/'.$event_id);
                    }
                }
                else
                {
                    // doesn't have access
                    $error[] = 'Piedod, bet tev nav pieejas dzēst šo komentāru.';
                    Session::set_flash('errors', $error);
                    Response::redirect('event/view/'.$event_id);
                }
            }
            else
            {
                // no comment found
                $error[] = 'Piedod, bet komentārs netika atrasts.';
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
     * Edits given comment
     *
     * @param integer $comment_id is ID of comment to be edited
     * @param string $event_id is ID of event, were given comment is published
     */
    public function action_edit($comment_id = null, $event_id = null)
    {
        // check if user has access to deleting comment
        if (Auth::has_access('comment.delete'))
        {
            is_null($event_id) and Response::redirect('/');
            is_null($comment_id) and Response::redirect('event/view/'.$event_id);

            $comment_obj = Model_Orm_Comment::get_comment($comment_id);

            if (Input::method() == 'POST')
            {
                // edit form submited, validate it
                $user_id = Auth::instance()->get_user_id();
                $user_id = $user_id[1];

                if (Input::post('comment') and Input::post('comment') != '')
                {
                    // if comment found, check if has access to edit it
                    if ( ! empty($comment_obj))
                    {
                        if ($comment_obj->author_id == $user_id)
                        {
                            // has access, edit comment
                            $comment = array(
                                'message'   => Input::post('comment'),
                                'edited_at' => Date::time()->get_timestamp()
                            );
                            $comment_obj->set($comment);

                            if ($comment_obj->save())
                            {
                                // comment successfuly edited
                                Session::set_flash('success', 'Komentārs veiksmīgi izlabots');
                                Response::redirect('event/view/'.$event_id);
                            }
                            else
                            {
                                // could not edit comment
                                $error[] = 'Piedod, bet kaut kas nogāja greizi un neizdevās izlabot tavu komentāru.';
                                Session::set_flash('errors', $error);
                                Response::redirect('event/view/'.$event_id);
                            }
                        }
                        else
                        {
                            // doesn't have access
                            $error[] = 'Piedod, bet tev pieejas labot šo komentāru.';
                            Session::set_flash('errors', $error);
                            Response::redirect('event/view/'.$event_id);
                        }
                    }
                    else
                    {
                        // no comment found
                        $error[] = 'Piedod, bet komentārs netika atrasts.';
                        Session::set_flash('errors', $error);
                        Response::redirect('event/view/'.$event_id);
                    }
                }
                else
                {
                    // comment not set
                    $errors[] = 'Ievadi komentāru!';
                    Session::set_flash('errors', $errors);
                    $this->template->page_title = 'Izlabo savu profilu';
                    $this->template->content = View::forge('comment/create');
                }
            }
            else
            {
                if ( ! empty($comment_obj))
                {
                    // no form submied, render edit form
                    $_POST['comment'] = $comment_obj->message;
                    $this->template->page_title = 'Izlabo komentāru!';
                    $this->template->content = View::forge('comment/create');
                    $this->template->content->form_title = 'Izlabo komentāru';
                }
                else
                {
                    // no comment found
                    $error[] = 'Komentārs netika atrasts.';
                    Session::set_flash('errors', $error);
                    Response::redirect('event/view/'.$event_id);
                }
            }
        }
        else
        {
            Response::redirect('/');
        }
    }
}

?>
