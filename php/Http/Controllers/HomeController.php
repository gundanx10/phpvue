<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    protected $except = [];

    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
    }

    public function home()
    {
        return redirect('/shopadmin');
    }
}
