<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected $except = [];
    protected $unauthorized = [];

    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $except = $this->except;

        $this->middleware('auth:api', ['except' => $except]);
    }
}
