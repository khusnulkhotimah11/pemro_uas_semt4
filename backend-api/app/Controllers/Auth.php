<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\User;

class Auth extends ResourceController
{
    protected $modelName = 'App\Models\User';
    protected $format    = 'json';


public function login()
    {
        $json = $this->request->getJSON();
        $username = $json ? $json->username : $this->request->getVar('username');
        $password = $json ? $json->password : $this->request->getVar('password');

        $user = $this->model->where('username', $username)->first();

        if ($user && $password === $user['password']) {
            $token = bin2hex(random_bytes(32));
            $this->model->update($user['id'], ['token' => $token]);

            return $this->respond([
                'status' => 200,
                'message' => 'Login Berhasil',
                'token' => $token
            ]);
        }

        // Trik debugging: balikin value asli yang ditangkap CI4 ke frontend
        return $this->failUnauthorized([
            'error' => 'Gagal login',
            'debug_input_user' => $username,
            'debug_input_pass' => $password,
            'debug_db_user' => $user ? $user['username'] : 'USER GAK KETEMU DI DB',
            'debug_db_pass' => $user ? $user['password'] : 'KOSONG'
        ]);
    }

    public function logout()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        $user = $this->model->where('token', $token)->first();

        if ($user) {
            $this->model->update($user['id'], ['token' => null]);
            return $this->respond(['message' => 'Logout Berhasil']);
        }

        return $this->failUnauthorized('Token tidak valid');
    }

    public function index() {}
    public function show($id = null) {}
    public function create() {}
    public function update($id = null) {}
    public function delete($id = null) {}
}
