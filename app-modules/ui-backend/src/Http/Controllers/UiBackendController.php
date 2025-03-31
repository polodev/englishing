<?php

namespace Modules\UiBackend\Http\Controllers;

use Illuminate\Http\Request;

class UiBackendController
{
    public function index()
    {
        return view('ui-backend::index');
    }
}
