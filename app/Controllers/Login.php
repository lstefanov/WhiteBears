<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;

class Login extends BaseController
{
    public function index()
    {
        //add login.css to the css array
        $this->data['css'][] = 'login.css';

        return view('login');
    }

    public function authenticate()
    {
        $session = session();
        $userModel = new UsersModel();

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $userModel->where('username', $username)->first();

        if(is_null($user)) {
            return redirect()->back()->withInput()->with('error', 'Невалиден вход!');
        }

        $pwd_verify = password_verify($password, $user['password']);

        if(!$pwd_verify) {
            return redirect()->back()->withInput()->with('error', 'Невалиден вход!');
        }

        $ses_data = [
            'id' => $user['id'],
            'email' => $user['email'],
            'isLoggedIn' => TRUE
        ];

        $session->set($ses_data);
        return redirect()->to('/dashboard');


    }

    public function logout() {
        session_destroy();
        return redirect()->to('/login');
    }
}