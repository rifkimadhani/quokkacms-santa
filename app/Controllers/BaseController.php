<?php

namespace App\Controllers;

use App\Models\SettingModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $className;
    protected $baseUrl;
    protected $baseHost;
    protected $roles;

    function __construct()
    {
        $setting = new SettingModel();
        $timeZome = $setting->getTimeZone();
        date_default_timezone_set($timeZome);

        //init className & baseUrl
        $router = service('router');
        $controllerName = $router->controllerName();
        $this->className = strtolower(class_basename($controllerName));
        $this->baseHost = base_url();
        $this->baseUrl = base_url('/' . $this->className);
        $this->roles = session()->get('roles');
    }

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    protected function getBaseHost(){
        return $this->baseHost;
    }

    protected function getBaseUrl(){
        return $this->baseUrl;
//        $router = service('router');
//        $controllerName = $router->controllerName();
//
//        $className = strtolower(class_basename($controllerName));
//
//        return base_url('/' . $className);
    }

    protected function setSuccessMessage($message){
        $session = session();
        $session->setFlashdata('type', 'success');
        $session->setFlashdata('message', $message);
    }
    protected function setErrorMessage($message){
        $session = session();
        $session->setFlashdata('type', 'danger');
        $session->setFlashdata('message', $message);
    }

    /**
     * log error
     *
     * @param $message
     */
    function loge($message){
        log_message('error', $message);
    }

    /**
     * Vardump var --> log info
     *
     * @param $var
     */
    function varDump($var){
        $dump = $this->dump_to_var($var);

        log_message('error', $dump);
    }

    /**
     * Vardump data -> string
     *
     * @param $data
     * @return string
     */
    function dump_to_var($data) {
        ob_start();
        var_dump($data);
        $output = ob_get_clean();
        return $output;
    }

    function hasRole($roleName){
        return in_array($roleName, $this->roles) ? true : false;
    }
}
