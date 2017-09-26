<?php

namespace App\Controllers;

use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;


/**
 * Class AbstractController
 *
 * @property \Phalcon\Http\Request              $request
 * @property \Phalcon\Http\Response             $htmlResponse
 * @property \Phalcon\Db\Adapter\Pdo\Postgresql $db
 * @property \Phalcon\Config                    $config
 * @property \App\Services\UsersService         $usersService
 * @property \App\Models\Users                  $user
 */
define('STATUS_OK', 200);
define('STATUS_ERROR_UNAUTHORIZED', 401);
define('STATUS_ERROR_FORBIDDEN', 403);
define('STATUS_ERROR', 500);
class BaseController extends Controller
{

    public $error = false;
    public $user_id = null;

    public $error_response = null;

    const BAD_JSON_REQUEST          = ['code' => '5001', 'message' => 'JSON de entrada vacio o mal construido.'];


}