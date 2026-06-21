<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');
        $token = str_replace('Bearer ', '', $authHeader);

        $userModel = new User();
        $user = $userModel->where('token', $token)->first();

        if (!$user) {
            return service('response')->setJSON([
                'status' => 401,
                'error' => 'Unauthorized: Token tidak valid'
            ])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
