<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/20/2023
 * Time: 1:23 PM
 */

namespace App\Controllers;

use App\Models\AdminForm;
use App\Models\AdminModel;
use App\Models\RoleModel;

class AdminProfile extends BaseController
{
    public function index()
    {
        $mainview = "admin/index_profile";
        $pageTitle = 'Admin Profile';

        $adminId = session()->get('admin_id');

        $model = new AdminModel();
        $data = $model->getById($adminId);

        return view('layout/template', compact('mainview','pageTitle', 'data'));
    }

    //ganti password
    public function changePassword(){
        require_once __DIR__ . '/../../library/Security.php';

        $adminId = session()->get('admin_id');
        $username = session()->get('username');
        $password = $_POST['password'];

        $hash = \Security::genHash($username, $password);

        $model = new AdminModel();
        $model->modifyPassword($adminId, $hash);

        $this->setSuccessMessage('Change password success');

        return redirect()->to($this->baseUrl);
    }
}