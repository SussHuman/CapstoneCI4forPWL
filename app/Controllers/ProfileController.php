<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
    public function index()
    {
        $data = [
            'username' => session()->get('username'),
            'email' => session()->get('email'),
            'role' => session()->get('role'),
            'login_time' => session()->get('login_time'),
            'status' => session()->get('status'),
        ];
        return view('v_profile', $data);
    }
}
