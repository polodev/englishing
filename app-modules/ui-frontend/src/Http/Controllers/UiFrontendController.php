<?php

namespace Modules\UiFrontend\Http\Controllers;

use Illuminate\Http\Request;

class UiFrontendController
{
    public function index()
    {
        return view('ui-frontend::index');
    }
}
