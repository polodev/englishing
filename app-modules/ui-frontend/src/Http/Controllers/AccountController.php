<?php

namespace Modules\UiFrontend\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AccountController extends Controller
{
    /**
     * Display the user's dashboard.
     * 
     * @return Renderable
     */
    public function dashboard()
    {
        return view('ui-frontend::account.dashboard');
    }

    /**
     * Display the user's profile.
     * 
     * @return Renderable
     */
    public function profile()
    {
        return view('ui-frontend::account.profile');
    }

    /**
     * Display the user's bookmarks.
     * 
     * @return Renderable
     */
    public function bookmarks()
    {
        return view('ui-frontend::account.bookmarks');
    }

    /**
     * Display the user's liked content.
     * 
     * @return Renderable
     */
    public function liked()
    {
        return view('ui-frontend::account.liked');
    }

    /**
     * Display the user's completed content.
     * 
     * @return Renderable
     */
    public function completed()
    {
        return view('ui-frontend::account.completed');
    }

    /**
     * Display the user's courses.
     * 
     * @return Renderable
     */
    public function courses()
    {
        return view('ui-frontend::account.courses');
    }

    /**
     * Display the user's account settings.
     * 
     * @return Renderable
     */
    public function settings()
    {
        return view('ui-frontend::account.settings');
    }
}
