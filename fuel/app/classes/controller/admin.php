<?php
/**
 * The Admin Controller.
 *
 * Admin conntroller renders admin panel with event, user, comment and tag manu,
 * where admin can manage them
 *
 * @package  app
 * @extends  Public
 */
class Controller_Admin extends Controller_Public
{
    /**
     * Shows recently created events in admin panel
     * Also provides search after event id
     */
    public function action_event()
    {
        // check if has admin access
        if (Auth::has_access('admin.event'))
        {
            $event = true;
            // check if search form submited
            if (Input::method() == 'POST')
            {
                // search form submited, search for event with given id
                $query = Model_Orm_Event::query()->where('event_id', Input::post('value'));
                $event_obj = $query->get_one();

                // check if event found
                if ( ! empty($event_obj))
                {
                    // event found, get its info
                    $events[0]['id'] = $event_obj->event_id;
                    $events[0]['title'] = $event_obj->title;
                    if ($event_obj->type == 'public')
                    {
                        $events[0]['type'] = 'Publisks';
                    }
                    else
                    {
                        $events[0]['type'] = 'Privāts';
                    }
                    $query = Model_Orm_Participant::query()
                       ->where('event_id', $event_obj->event_id)
                       ->and_where_open()
                            ->where('role', 10)
                       ->and_where_close();
                    $author_id = $query->get_one()->user_id;
                    $events[0]['author_id'] = $author_id;
                    $query = Model_Orm_User::query()->where('user_id', $author_id);
                    $author_obj = $query->get_one();
                    $events[0]['author'] = $author_obj->username;
                    $author_obj->group == 100 and $events[0]['admin'] = true;

                    Session::set_flash('success', 'Pasākums "'.Input::post('value').'" veiksmīgi atrasts.');
               }
               else
               {
                    // no event found, show error
                    $error[] = 'Šāds pasāsums netika atrasts, mēģini vēlreiz!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/event');
                }
            }
            else
            {
                // no form submited, render recent events
                $query = Model_Orm_Event::query()
                    ->order_by('created_at', 'desc')
                    ->limit(20);
                $event_obj = $query->get();

                $events = array();
                $i = 0;
                foreach ($event_obj as $event)
                {
                    $events[$i]['id'] = $event->event_id;
                    $events[$i]['title'] = $event->title;
                    if ($event->type == 'public')
                    {
                        $events[$i]['type'] = 'Publisks';
                    }
                    else
                    {
                        $events[$i]['type'] = 'Privāts';
                    }
                    $query = Model_Orm_Participant::query()
                        ->where('event_id', $event->event_id)
                        ->and_where_open()
                            ->where('role', 10)
                        ->and_where_close();
                    $author_id = $query->get_one()->user_id;
                    $events[$i]['author_id'] = $author_id;
                    $query = Model_Orm_User::query()->where('user_id', $author_id);
                    $author_obj = $query->get_one();
                    $events[$i]['author'] = $author_obj->username;
                    $author_obj->group == 100 and $events[$i]['admin'] = true;
                    $i++;
                }
            }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
        $this->template->page_title = 'Pasākumi | Operatora panelis';
        $this->template->content = View::forge('admin/template');
        $this->template->content->set('event', $event);
        $this->template->content->panel = View::forge('admin/event');
        isset($events) and $this->template->content->panel->set('events', $events);
    }

    /**
     * Deletes given event, all invites to it, comments to it and organizator statuses and blocks its author
     *
     * @param string $event_id is id of event to be deleted
     */
    public function action_block_event($event_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.block_event'))
        {
            // user has access to admin panel, check if no id given redirect to panel
            is_null($event_id) and Response::redirect('admin/event');

            // get needed event
            $query = Model_Orm_Event::query()->where('event_id', $event_id);
            $event_obj = $query->get_one();

            if ( ! empty($event_obj))
            {
                // find its author
                $query = Model_Orm_Participant::query()
                    ->where('event_id', $event_id)
                    ->and_where_open()
                        ->where('role', 10)
                    ->and_where_close();
                $author_id = $query->get_one()->user_id;

                // check if author is not admin
                $query = Model_Orm_User::query()->where('user_id', $author_id);
                $user_obj = $query->get_one();

                if ($user_obj->group != 100 and $author_id != 0)
                {
                    // user not admin or deleted user
                    // delete all invites to this event
                    $query = Model_Orm_Invite::query()->where('event_id', $event_id);
                    $invite_obj = $query->get();

                    foreach ($invite_obj as $invite)
                    {
                        $invite->delete();
                    }

                    // delete all participants for this event
                    $query = Model_Orm_Participant::query()->where('event_id', $event_id);
                    $participant_obj = $query->get();

                    foreach ($participant_obj as $participant)
                    {
                        $participant->delete();
                    }

                    // delete all comments to this event
                    $comment_obj = Model_Orm_Comment::get_comment_by_event($event_id);

                    foreach ($comment_obj as $comment)
                    {
                        $comment->delete();
                    }

                    // delete all requests to this event
                    $query = Model_Orm_Request::query()->where('event_id', $event_id);
                    $request_obj = $query->get();

                    foreach ($request_obj as $request)
                    {
                        $request->delete();
                    }

                    // delete all tag relation to this event
                    $query = Model_Orm_HasTag::query()->where('event_id', $event_id);
                    $tag_relation_obj = $query->get();
                    $tag_ids = array();

                    foreach ($tag_relation_obj as $tag)
                    {
                        DB::delete('has_tag')
                            ->where('tag_id', '=', $tag->tag_id)
                            ->and_where_open()
                                ->where('event_id', $tag->event_id)
                            ->and_where_close()
                            ->execute();

                        $tag_ids[] = $tag->tag_id;
                    }

                    // deincrease tag count for each tag
                    foreach ($tag_ids as $tag)
                    {
                        $query = Model_Orm_Tag::query()->where('tag_id', $tag->tag_id);
                        $tag_obj = $query->get_one();
                        $count = $tag_obj->event_count;
                        $tag_obj->event_count = --$count;
                        $tag_obj->save();
                    }

                    // block author
                    $user_obj->group = -1;
                    $user_obj->save();

                    // send author an alert
                    $alert = array(
                        'recipient_id'  => $user_obj->user_id,
                        'type'   => 'demote',
                        'message' => 'Tavā pasākumā '.$event_obj->event_id.' tika pārkāpti vietnes lietošanas noteikumi, pasākums tika dzēsts un tavs profils ir bloķēts!'
                    );
                    $new_alert = Model_Orm_Alert::forge($alert);
                    $new_alert->save();

                    // delete event
                    $event_obj->delete();
                }
                else
                {
                    // event author is admin, can't block admin
                    $error[] = 'Šis ir operātora vai dzēsta lietotāja pasākums, to nevar bloķēt un dzēst!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/event');
                }
            }
            else
            {
                // event no longer exists
                $error[] = 'Šāds pasāsums vairs neeksistē!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/event');
            }

            Session::set_flash('success', 'Pasākums izdzēsts un autors bloķēts!');
            Response::redirect('admin/event');
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Only deletes given event not blocking its author
     *
     * @param string $event_id is id of event to be deleted
     */
    public function action_delete_event($event_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.delete_event'))
        {
            // user has access to admin panel, check if no id given redirect to panel
            is_null($event_id) and Response::redirect('admin/event');

            // get needed event
            $query = Model_Orm_Event::query()->where('event_id', $event_id);
            $event_obj = $query->get_one();

            if ( ! empty($event_obj))
            {
                // user not admin or deleted user
                // delete all invites to this event
                $query = Model_Orm_Invite::query()->where('event_id', $event_id);
                $invite_obj = $query->get();

                foreach ($invite_obj as $invite)
                {
                    $invite->delete();
                }

                // delete all participants for this event
                $query = Model_Orm_Participant::query()->where('event_id', $event_id);
                $participant_obj = $query->get();

                foreach ($participant_obj as $participant)
                {
                    $participant->delete();
                }

                // delete all comments to this event
                $comment_obj = Model_Orm_Comment::get_comment_by_event($event_id);

                foreach ($comment_obj as $comment)
                {
                    $comment->delete();
                }

                // delete all requests to this event
                $query = Model_Orm_Request::query()->where('event_id', $event_id);
                $request_obj = $query->get();

                foreach ($request_obj as $request)
                {
                    $request->delete();
                }

                // delete all tag relation to this event
                $query = Model_Orm_HasTag::query()->where('event_id', $event_id);
                $tag_relation_obj = $query->get();
                $tag_ids = array();

                foreach ($tag_relation_obj as $tag)
                {
                    DB::delete('has_tag')
                        ->where('tag_id', '=', $tag->tag_id)
                        ->and_where_open()
                            ->where('event_id', $tag->event_id)
                        ->and_where_close()
                        ->execute();

                    $tag_ids[] = $tag->tag_id;
                }

                // deincrease tag count for each tag
                foreach ($tag_ids as $tag)
                {
                    $query = Model_Orm_Tag::query()->where('tag_id', $tag);
                    $tag_obj = $query->get_one();
                    $count = $tag_obj->event_count;
                    $tag_obj->event_count = --$count;
                    $tag_obj->save();
                }

                // delete event
                $event_obj->delete();
            }
            else
            {
                // event no longer exists
                $error[] = 'Šāds pasāsums vairs neeksistē!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/event');
            }

            Session::set_flash('success', 'Pasākums izdzēsts!');
            Response::redirect('admin/event');
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Shows last logged in users
     */
    public function action_user()
    {
        // check if has admin access
        if (Auth::has_access('admin.user'))
        {
            $user_id = Auth::instance()->get_user_id();
            $user_id = $user_id[1];
            $user = true;
            // check if search form submited
            if (Input::method() == 'POST')
            {
                // search form submited, search for user with given username
                $query = Model_Orm_User::query()->where('username', Input::post('username'));
                $user_obj = $query->get_one();

                // check if event found
                if ( ! empty($user_obj))
                {
                    // user found, get its info
                    $users[0]['id'] = $user_obj->user_id;
                    $users[0]['username'] = $user_obj->username;
                    ! is_null($user_obj->name) and $users[0]['name'] = $user_obj->name;
                    ! is_null($user_obj->surname) and $users[0]['surname'] = $user_obj->surname;
                    $users[0]['email'] = $user_obj->email;
                    $users[0]['last_login'] = Date::forge($user_obj->last_login);
                    $users[0]['registered'] = Date::forge($user_obj->created_at);
                    // get user group
                    if ($user_obj->group == 1)
                    {
                        $users[0]['group'] = 'Lietotājs';
                    }
                    elseif ($user_obj->group == -1)
                    {
                        $users[0]['group'] = 'Bloķēts';
                    }
                    elseif ($user_obj->group == 10)
                    {
                        $users[0]['group'] = 'Prasmīgs';
                    }
                    elseif ($user_obj->group == 100)
                    {
                        $users[0]['group'] = 'Operators';
                    }

                    Session::set_flash('success', 'Lietotājs "'.Input::post('username').'" veiksmīgi atrasts.');
               }
               else
               {
                    // no user found, show error
                    $error[] = 'Šāds lietotājs netika atrasts, mēģini vēlreiz!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/user');
                }
            }
            else
            {
                // no form submited, render recently logged in users
                $query = Model_Orm_User::query()
                    ->order_by('last_login', 'desc')
                    ->limit(20);
                $user_obj = $query->get();

                $users = array();
                $i = 0;
                foreach ($user_obj as $user)
                {
                    // don't show deleted user
                    if ($user->user_id == 0)
                    {
                        break;
                    }

                    $users[$i]['id'] = $user->user_id;
                    $users[$i]['username'] = $user->username;
                    ! is_null($user->name) and $users[$i]['name'] = $user->name;
                    ! is_null($user->surname) and $users[$i]['surname'] = $user->surname;
                    $users[$i]['email'] = $user->email;
                    $users[$i]['last_login'] = Date::forge($user->last_login);
                    $users[$i]['registered'] = Date::forge($user->created_at);
                    // get user group
                    if ($user->group == 1)
                    {
                        $users[$i]['group'] = 'Lietotājs';
                    }
                    elseif ($user->group == -1)
                    {
                        $users[$i]['group'] = 'Bloķēts';
                    }
                    elseif ($user->group == 10)
                    {
                        $users[$i]['group'] = 'Prasmīgs';
                    }
                    elseif ($user->group == 100)
                    {
                        $users[$i]['group'] = 'Operators';
                    }
                    $i++;
                }
           }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
        $this->template->page_title = 'Lietotāji | Operatora panelis';
        $this->template->content = View::forge('admin/template');
        $this->template->content->set('user', $user);
        $this->template->content->panel = View::forge('admin/user');
        $this->template->content->panel->set('user_id', $user_id);
        isset($users) and $this->template->content->panel->set('users', $users);
    }

    /**
     * Changes blocked users group to user
     *
     * @param integer $user_id is id of blocked user to be unblocked
     */
    public function action_unblock_user($user_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.unblock_user'))
        {
            // is user id not given, redirect away
            is_null($user_id) and Response::redirect('/');

            // get user to be unblocked
            $query = Model_Orm_User::query()->where('user_id', $user_id);
            $user_obj = $query->get_one();

            // check if user found
            if ( ! empty($user_obj))
            {
                // user found, check if its blocked
                if ($user_obj->group == -1)
                {
                    // user blocked, unblock it
                    $user_obj->group = 1;
                    $user_obj->save();

                    // send user an alert
                    $alert = array(
                        'recipient_id'  => $user_obj->user_id,
                        'type'   => 'promote',
                        'message' => 'Tavs profils tika atbloķēts!'
                    );
                    $new_alert = Model_Orm_Alert::forge($alert);
                    $new_alert->save();

                    Session::set_flash('success', 'Lietotājs "'.$user_obj->username.'" veiksmīgi atbloķēts!');
                    Response::redirect('admin/user');
                }
                else
                {
                    // user is not blocked
                    $error[] = 'Nevar atbloķēt lietotāju, kurš nav bloķēts!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/user');
                }
            }
            else
            {
                // no user found, show error
                $error[] = 'Šāds lietotājs netika atrasts, mēģini vēlreiz!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/user');
            }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Blocks user
     *
     * @param integer $user_id id of user to be blocked
     */
    public function action_block_user($user_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.block_user'))
        {
            // is user id not given, redirect away
            is_null($user_id) and Response::redirect('/');

            // get user to be blocked
            $query = Model_Orm_User::query()->where('user_id', $user_id);
            $user_obj = $query->get_one();

            // check if user found
            if ( ! empty($user_obj))
            {
                // user found, check if its not blocked
                if ($user_obj->group != -1)
                {
                    // user blocked, check if not admin
                    if ($user_obj->group != 100)
                    {
                        // not admin, block it
                        $user_obj->group = -1;
                        $user_obj->save();

                        // send user an alert
                        $alert = array(
                            'recipient_id'  => $user_obj->user_id,
                            'type'   => 'demote',
                            'message' => 'Tu parkāpi vietnes lietošanas noteikumus un tavs profils ir bloķēts!'
                        );
                        $new_alert = Model_Orm_Alert::forge($alert);
                        $new_alert->save();

                        Session::set_flash('success', 'Lietotājs "'.$user_obj->username.'" veiksmīgi bloķēts!');
                        Response::redirect('admin/user');
                    }
                    else
                    {
                        // user is admim
                        $error[] = 'Nevar bloķēt Operātoru!';
                        Session::set_flash('errors', $error);
                        Response::redirect('admin/user');
                    }
                }
                else
                {
                    // user is blocked already
                    $error[] = 'Nevar bloķēt lietotāju, kurš jau ir bloķēts!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/user');
                }
            }
            else
            {
                // no user found, show error
                $error[] = 'Šāds lietotājs netika atrasts, mēģini vēlreiz!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/user');
            }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Changes users group to power user
     *
     * @param integer $user_id is id of user to be promoted to power user
     */
    public function action_power_user($user_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.power_user'))
        {
            // is user id not given, redirect away
            is_null($user_id) and Response::redirect('/');

            // get user to be promoted
            $query = Model_Orm_User::query()->where('user_id', $user_id);
            $user_obj = $query->get_one();

            // check if user found
            if ( ! empty($user_obj))
            {
                // user found, check if its not power user
                if ($user_obj->group != 10)
                {
                    // user not power user, check if not admin
                    if ($user_obj->group != 100)
                    {
                        // promote user to power user
                        $user_obj->group = 10;
                        $user_obj->save();

                        // send user an alert
                        $alert = array(
                            'recipient_id'  => $user_obj->user_id,
                            'type'   => 'promote',
                            'message' => 'Tu esi paaugstināts par prasmīgu lietotāju, tagad tev ir tiesības pievienot jaunas birkas!'
                        );
                        $new_alert = Model_Orm_Alert::forge($alert);
                        $new_alert->save();

                        Session::set_flash('success', 'Lietotājs "'.$user_obj->username.'" veiksmīgi paaugstināts par prasmīgu lietotāju!');
                        Response::redirect('admin/user');
                    }
                    else
                    {
                        // can't demote admin to power user
                        $error[] = 'Operātoru nevar pazemināt pat prasmīgu lietotāju!';
                        Session::set_flash('errors', $error);
                        Response::redirect('admin/user');
                    }
                }
                else
                {
                    // user is already power user
                    $error[] = 'Lietotājs jau ir prasmīgs lietotājs!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/user');
                }
            }
            else
            {
                // no user found, show error
                $error[] = 'Šāds lietotājs netika atrasts, mēģini vēlreiz!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/user');
            }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Changes power users group to user
     *
     * @param int $user_id is id of power user to be changed to user
     */
    public function action_demote_power_user($user_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.demote_power_user'))
        {
            // is user id not given, redirect away
            is_null($user_id) and Response::redirect('/');

            // get user to be demoted
            $query = Model_Orm_User::query()->where('user_id', $user_id);
            $user_obj = $query->get_one();

            // check if user found
            if ( ! empty($user_obj))
            {
                // user found, check if its power user
                if ($user_obj->group == 10)
                {
                    // user is power user, change to user
                    $user_obj->group = 1;
                    $user_obj->save();

                    // send user an alert
                    $alert = array(
                        'recipient_id'  => $user_obj->user_id,
                        'type'   => 'demote',
                        'message' => 'Tu pārkāpi birku pievienošanas noteikumus un tu esi pazemināts par lietotāju!'
                    );
                    $new_alert = Model_Orm_Alert::forge($alert);
                    $new_alert->save();

                    Session::set_flash('success', 'Lietotājs "'.$user_obj->username.'" veiksmīgi pazemināts par lietotāju!');
                    Response::redirect('admin/user');
                }
                else
                {
                    // user is not power user
                    $error[] = 'Nevar pazemināt lietotāju, kurš nav prasmīgs lietotājs!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/user');
                }
            }
            else
            {
                // no user found, show error
                $error[] = 'Šāds lietotājs netika atrasts, mēģini vēlreiz!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/user');
            }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Changes admins group to power user
     *
     * @param integer $user_id is id of power user to be changed to user
     */
    public function action_demote_admin($user_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.demote_admin'))
        {
            // is user id not given, redirect away
            is_null($user_id) and Response::redirect('/');

            // get user to be demoted
            $query = Model_Orm_User::query()->where('user_id', $user_id);
            $user_obj = $query->get_one();

            // check if user found
            if ( ! empty($user_obj))
            {
                // user found, check if its admin
                if ($user_obj->group == 100)
                {
                    // user is amdin, check if not himself
                    $cur_user_id = Auth::instance()->get_user_id();
                    $cur_user_id = $cur_user_id[1];
                    if ($user_obj->user_id != $cur_user_id)
                    {
                        // not himself, demote it
                        $user_obj->group = 10;
                        $user_obj->save();

                        // send admin an alert
                        $alert = array(
                            'recipient_id'  => $user_obj->user_id,
                            'type'   => 'demote',
                            'message' => 'Tu pārkāpi operatoru noteikumus un esi pazemināts par prasmīgu lietotāju!'
                        );
                        $new_alert = Model_Orm_Alert::forge($alert);
                        $new_alert->save();

                        Session::set_flash('success', 'Operātors "'.$user_obj->username.'" veiksmīgi pazemināts par prasmīgu lietotāju!');
                        Response::redirect('admin/user');
                    }
                    else
                    {
                        // himself, can't change his own group
                        $error[] = 'Nevar pazemināt savu statusu!';
                        Session::set_flash('errors', $error);
                        Response::redirect('admin/user');
                    }
                }
                else
                {
                    // user is not admin
                    $error[] = 'Nevar atņem operatora statusu lietotājam, kurš nav operators!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/user');
                }
            }
            else
            {
                // no user found, show error
                $error[] = 'Šāds operators netika atrasts, mēģini vēlreiz!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/user');
            }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Shows recenlty added and edited comments in admin panel
     * Also provides search after event id, username or string in message
     */
    public function action_comment()
    {
        // check if has admin access
        if (Auth::has_access('admin.comment'))
        {
            $comment = true;
            // check if search form submited
            if (Input::method() == 'POST')
            {
                // search form submited, check search type
                if (Input::post('search_type') == 'event')
                {
                    // search after event, search given event comments
                    $comment_obj = Model_Orm_Comment::get_comment_by_event(Input::post('value'));;

                    if (empty($comment_obj))
                    {
                        // event comments not found
                        $error[] = 'Netika astrasts neviens šī pasākuma komentārs!';
                        Session::set_flash('errors', $error);
                        Session::set_flash('search_type', Input::post('search_type'));
                        Session::set_flash('value', Input::post('value'));
                        Response::redirect('admin/comment');
                    }
                }
                elseif (Input::post('search_type') == 'user')
                {
                    // search after username, search for given users comments
                    // get given users id from given username
                    $query = Model_Orm_User::query()->where('username', Input::post('value'));
                    $user_obj = $query->get_one();

                    if (empty($user_obj))
                    {
                        // user not found
                        $error[] = 'Šāds lietotājs nav atrasts!';
                        Session::set_flash('errors', $error);
                        Session::set_flash('search_type', Input::post('search_type'));
                        Session::set_flash('value', Input::post('value'));
                        Response::redirect('admin/comment');
                    }
                    else
                    {
                        $user_id = $user_obj->user_id;
                    }

                    $comment_obj = Model_Orm_Comment::get_comment_by_user($user_id);

                    if (empty($comment_obj))
                    {
                        // user comments not found
                        $error[] = 'Netika astrasts neviens šī lietotāja komentārs!';
                        Session::set_flash('errors', $error);
                        Session::set_flash('search_type', Input::post('search_type'));
                        Session::set_flash('value', Input::post('value'));
                        Response::redirect('admin/comment');
                    }
                }
                else
                {
                    // search after string in comment
                    // check if not empty value
                    if (Input::post('value') == '' )
                    {
                        // empty value submited
                        $error[] = 'Lūdzu ievadi frāzi, ar kuru meklēt komentārus!';
                        Session::set_flash('errors', $error);
                        Session::set_flash('search_type', Input::post('search_type'));
                        Session::set_flash('value', Input::post('value'));
                        Response::redirect('admin/comment');
                    }

                    // not empty value, get comments
                    $comment_obj = Model_Orm_Comment::get_comment_by_string(Input::post('value'));

                    if (empty($comment_obj) or Input::post('value') == '' )
                    {
                        // comments containg string not found
                        $error[] = 'Netika astrasts neviens komentārs, kas saturētu šādu frāzi!';
                        Session::set_flash('errors', $error);
                        Session::set_flash('search_type', Input::post('search_type'));
                        Session::set_flash('value', Input::post('value'));
                        Response::redirect('admin/comment');
                    }
                }


                // comments found, get info
                $comments = array();
                $i = 0;
                foreach ($comment_obj as $comment)
                {
                    $comments[$i]['id'] = $comment->comment_id;
                    $comments[$i]['author_id'] = $comment->author_id;
                    $query = Model_Orm_User::query()->where('user_id', $comment->author_id);
                    $author_obj = $query->get_one();
                    $comments[$i]['author'] = $author_obj->username;
                    $author_obj->group == 100 and $comments[$i]['admin'] = true;
                    $comments[$i]['event_id'] = $comment->event_id;
                    $comments[$i]['message'] = $comment->message;
                    $i++;
                }

                Session::set_flash('success', 'Komentāri veiksmīgi atrasti!');
                Session::set_flash('search_type', Input::post('search_type'));
                Session::set_flash('value', Input::post('value'));
            }
            else
            {
                // no form submited, render recent comments
                $comment_obj = Model_Orm_Comment::get_recenty_comments();

                $comments = array();
                $i = 0;
                foreach ($comment_obj as $comment)
                {
                    $comments[$i]['id'] = $comment->comment_id;
                    $comments[$i]['author_id'] = $comment->author_id;
                    $query = Model_Orm_User::query()->where('user_id', $comment->author_id);
                    $author_obj = $query->get_one();
                    $comments[$i]['author'] = $author_obj->username;
                    $author_obj->group == 100 and $comments[$i]['admin'] = true;
                    $comments[$i]['event_id'] = $comment->event_id;
                    $comments[$i]['message'] = $comment->message;
                    $i++;
                }

                // get recent edited comments
                $comment_obj = Model_Orm_Comment::get_recently_edited_comments();

                $newest_edited_comments = array();
                $i = 0;
                foreach ($comment_obj as $comment)
                {
                    // if edited
                    if ( ! is_null($comment->edited_at))
                    {
                        $newest_edited_comments[$i]['id'] = $comment->comment_id;
                        $newest_edited_comments[$i]['author_id'] = $comment->author_id;
                        $query = Model_Orm_User::query()->where('user_id', $comment->author_id);
                        $author_obj = $query->get_one();
                        $newest_edited_comments[$i]['author'] = $author_obj->username;
                        $author_obj->group == 100 and $newest_edited_comments[$i]['admin'] = true;
                        $newest_edited_comments[$i]['event_id'] = $comment->event_id;
                        $newest_edited_comments[$i]['message'] = $comment->message;
                        $i++;
                    }
                }
            }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
        $this->template->page_title = 'Komentāri | Operatora panelis';
        $this->template->content = View::forge('admin/template');
        $this->template->content->set('comment', $comment);
        $this->template->content->panel = View::forge('admin/comment');
        isset($comments) and $this->template->content->panel->set('comments', $comments);
        isset($newest_edited_comments) and $this->template->content->panel->set('newest_edited_comments', $newest_edited_comments);
    }

    /**
     * Deletes given comment and blocks its author
     *
     * @param integer $comment_id is ID of comment to be deleted
     */
    public function action_block_comment($comment_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.block_comment'))
        {
            // user has access to admin panel, check if no id given redirect to panel
            is_null($comment_id) and Response::redirect('admin/comment');

            // get needed comment
            $query = Model_Orm_Comment::query()->where('comment_id', $comment_id);
            $comment_obj = $query->get_one();

            if ( ! empty($comment_obj))
            {
                // if comment found, delete delete it and block its author
                // check if author is not admin
                $query = Model_Orm_User::query()->where('user_id', $comment_obj->author_id);
                $user_obj = $query->get_one();

                if ($user_obj->group != 100 and $comment_obj->author_id != 0)
                {
                    // user not admin, delete comment
                    $comment_obj->delete();

                    // block author
                    $user_obj->group = -1;
                    $user_obj->save();

                    // send author an alert
                    $alert = array(
                        'recipient_id'  => $user_obj->user_id,
                        'type'   => 'demote',
                        'message' => 'Tu savā pievienotajā komentārā pārkāpi vietnes noteikumus, komentārs tika izdzēsts un tavs profils bloķēts!'
                    );
                    $new_alert = Model_Orm_Alert::forge($alert);
                    $new_alert->save();
                }
                else
                {
                    // event author is admin, can't block admin
                    $error[] = 'Šis ir operātora vai dzēsta lietotāja komentārs, to nevar bloķēt un dzēst!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/comment');
                }
            }
            else
            {
                // comment no longer exists
                $error[] = 'Šis komentārs vairs neeksistē!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/comment');
            }

            Session::set_flash('success', 'Komentārs izdzēsts un autors bloķēts!');
            Response::redirect('admin/comment');
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Only deletes comment, not blocking its author
     *
     * @param integer $comment_id is ID of comment to be deleted
     */
    public function action_delete_comment($comment_id)
    {
        // check if has admin access
        if (Auth::has_access('admin.delete_comment'))
        {
            // user has access to admin panel, check if no id given redirect to panel
            is_null($comment_id) and Response::redirect('admin/comment');

            // get needed comment
            $comment_obj = Model_Orm_Comment::get_comment($comment_id);

            if ( ! empty($comment_obj))
            {
                // delete comment
                $comment_obj->delete();

            }
            else
            {
                // comment no longer exists
                $error[] = 'Šis komentārs vairs neeksistē!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/comment');
            }

            Session::set_flash('success', 'Komentārs izdzēsts!');
            Response::redirect('admin/comment');
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Shows recently added users
     */
    public function action_tag()
    {
        // check if has admin access
        if (Auth::has_access('admin.tag'))
        {
            $tag = true;
            // check if search form submited
            if (Input::method() == 'POST')
            {
                // search form submited, search for tag
                $query = Model_Orm_Tag::query()->where('event_id', Input::post('value'));
                $comment_obj = $query->get();

                if (empty($comment_obj))
                {
                    // event comments not found
                    $error[] = 'Netika astrasts neviens šī pasākuma komentārs!';
                    Session::set_flash('errors', $error);
                    Session::set_flash('search_type', Input::post('search_type'));
                    Session::set_flash('value', Input::post('value'));
                    Response::redirect('admin/comment');
                }


                // comments found, get info
                $comments = array();
                $i = 0;
                foreach ($comment_obj as $comment)
                {
                    $comments[$i]['id'] = $comment->comment_id;
                    $comments[$i]['author_id'] = $comment->author_id;
                    $query = Model_Orm_User::query()->where('user_id', $comment->author_id);
                    $author_obj = $query->get_one();
                    $comments[$i]['author'] = $author_obj->username;
                    $author_obj->group == 100 and $comments[$i]['admin'] = true;
                    $comments[$i]['event_id'] = $comment->event_id;
                    $comments[$i]['message'] = $comment->message;
                    $i++;
                }

                Session::set_flash('success', 'Komentāri veiksmīgi atrasti!');
                Session::set_flash('search_type', Input::post('search_type'));
                Session::set_flash('value', Input::post('value'));
            }
            else
            {
                // no form submited, render recent tags
                $query = Model_Orm_Tag::query()
                    ->order_by('tag_id', 'desc')
                    ->limit(20);
                $tag_obj = $query->get();

                $tags = array();
                $i = 0;
                foreach ($tag_obj as $tag)
                {
                    $tags[$i]['id'] = $tag->tag_id;
                    $tags[$i]['title'] = $tag->title;
                    $tags[$i]['author_id'] = $tag->author_id;
                    $query = Model_Orm_User::query()->where('user_id', $tag->author_id);
                    $author_obj = $query->get_one();
                    $tags[$i]['author'] = $author_obj->username;
                    $author_obj->group == 100 and $tags[$i]['admin'] = true;
                    $i++;
                }
            }
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
        $this->template->page_title = 'Birkas | Operatora panelis';
        $this->template->content = View::forge('admin/template');
        $this->template->content->set('tag', $tag);
        $this->template->content->panel = View::forge('admin/tag');
        isset($tags) and $this->template->content->panel->set('tags', $tags);
    }


    /**
     * Creates new tag from admin panel
     */
    public function action_tag_create()
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
                            Session::set_flash('tag_success', 'Birka veiksmīgi pievienota');
                            Response::redirect('admin/tag');
                        }
                    }
                    else
                    {
                        // tag already exists
                        $errors[] = 'Birka jau ekistē!';
                        Session::set_flash('errors', $errors);
                        Response::redirect('admin/tag');
                    }
                }
                else
                {
                    // tag not set
                    $errors[] = 'Ievadi birku!';
                    Session::set_flash('tag_errors', $errors);
                    Response::redirect('admin/tag');
                }
            }
        }
        else
        {
            Response::redirect('/');
        }
    }

    /**
     * Delete tag and demote it authors status from power user to user
     *
     * @param integer $tag_id is ID of tag, to be deleted
     */
    public function action_demote_tag($tag_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.demote_tag'))
        {
            // user has access to admin panel, check if no id given redirect to panel
            is_null($tag_id) and Response::redirect('admin/tag');

            // get needed tag
            $query = Model_Orm_Tag::query()->where('tag_id', $tag_id);
            $tag_obj = $query->get_one();

            if ( ! empty($tag_obj))
            {
                // if tag found, delete it, its relations to events and demote its author
                // check if author is not admin
                $query = Model_Orm_User::query()->where('user_id', $tag_obj->author_id);
                $user_obj = $query->get_one();

                if ($user_obj->group != 100 and $tag_obj->author_id != 0)
                {
                    // user not adminor deleted user, delete its relations to event
                    $query = Model_Orm_HasTag::query()->where('tag_id', $tag_obj->tag_id);
                    $tag_relation_obj = $query->get();

                    foreach ($tag_relation_obj as $tag)
                    {
                        DB::delete('has_tag')
                        ->where('tag_id', '=', $tag->tag_id)
                        ->and_where_open()
                            ->where('event_id', $tag->event_id)
                        ->and_where_close()
                        ->execute();
                    }

                    // delete tag
                    $tag_obj->delete();

                    // demote author
                    $user_obj->group = 1;
                    $user_obj->save();

                    // send author an alert
                    $alert = array(
                        'recipient_id'  => $user_obj->user_id,
                        'type'   => 'demote',
                        'message' => 'Tu savā pievienotajā birkā pārkāpi vietnes noteikumus, birka tika izdzēsta un tavs status pazemināts par lietotāju!'
                    );
                    $new_alert = Model_Orm_Alert::forge($alert);
                    $new_alert->save();
                }
                else
                {
                    // event author is admin, can't block admin
                    $error[] = 'Šī ir operātora vai dzēsta lietotāja birka, to nevar pazemināt un dzēst!';
                    Session::set_flash('errors', $error);
                    Response::redirect('admin/tag');
                }
            }
            else
            {
                // comment no longer exists
                $error[] = 'Šī birka vairs neeksistē!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/tag');
            }

            Session::set_flash('success', 'Birka izdzēsta un autora status pazemināts!');
            Response::redirect('admin/tag');
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }

    /**
     * Deleted tag not demoting is athor statuss
     *
     * @param integer $tag_id is ID of tag, to be deleted
     */
    public function action_delete_tag($tag_id = null)
    {
        // check if has admin access
        if (Auth::has_access('admin.delete_tag'))
        {
            // user has access to admin panel, check if no id given redirect to panel
            is_null($tag_id) and Response::redirect('admin/tag');

            // get needed tag
            $query = Model_Orm_Tag::query()->where('tag_id', $tag_id);
            $tag_obj = $query->get_one();

            if ( ! empty($tag_obj))
            {
                //  delete its relations to event
                $query = Model_Orm_HasTag::query()->where('tag_id', $tag_obj->tag_id);
                $tag_relation_obj = $query->get();

                foreach ($tag_relation_obj as $tag)
                {
                    DB::delete('has_tag')
                        ->where('tag_id', '=', $tag->tag_id)
                        ->and_where_open()
                            ->where('event_id', $tag->event_id)
                        ->and_where_close()
                        ->execute();
                }
                $tag_obj->delete();
            }
            else
            {
                // comment no longer exists
                $error[] = 'Šī birka vairs neeksistē!';
                Session::set_flash('errors', $error);
                Response::redirect('admin/tag');
            }

            Session::set_flash('success', 'Birka izdzēsta un autora status pazemināts!');
            Response::redirect('admin/tag');
        }
        else
        {
            // doesn't have admin access, redirect away
            Response::redirect('/');
        }
    }
}

?>