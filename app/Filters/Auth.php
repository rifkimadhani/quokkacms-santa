<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/9/2023
 * Time: 10:53 AM
 */

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
// if user not logged in
        if(session()->get('admin_id')==false){
// then redirct to login page
            return redirect()->to('login');
        }
    }
//--------------------------------------------------------------------
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
// Do something here
    }
}