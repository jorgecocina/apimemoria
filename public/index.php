<?php

use App\Exceptions\Http404Exception;
use App\Exceptions\Http403Exception;
use App\Exceptions\Http401Exception;
use App\Controllers\AbstractHttpException;
use Phalcon\Mvc\Micro;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Session\Adapter\Files as Session;
use App\Models\Movements;
use App\Business\AnalyticsBSN;

define('BASE_DIR', dirname(__DIR__));
define('APP_DIR', BASE_DIR . '/app');

try {
    // Loading Configs
    $config = require(__DIR__ . '/../app/config/config.php');

    // Autoloading classes
    require __DIR__ . '/../app/config/loader.php';

    // Initializing DI container
    /** @var \Phalcon\DI\FactoryDefault $di */
    require __DIR__ . '/../app/config/di.php';

    $app->before(function () use($app, $config) {

        $movement = new Movements();
        $adminRequired = false;
        $token = $app->request->getHeader("accesstoken");
        $auth = $app->di->get('AuthToken');
        $noAuth = $config->noLoginRequired;
        $adminAuth = $config->adminLoginRequired;
        $uri = explode('/', str_replace($config->application->baseUri, '',strtolower($app->request->getURI())));
        $movement->uri = str_replace($config->application->baseUri, '',strtolower($app->request->getURI()));
        $method = strtolower($app->request->getMethod());
        $movement->method = $method;
        $noAuthRequired = false;
        if (count($uri) == 1) {
            $noAuthRequired = (isset($noAuth[$uri[0]][$method]) && $noAuth[$uri[0]][$method]) || (isset($noAuth[$uri[0]]['*']) && $noAuth[$uri[0]]['*']);
        } else if ($noAuth[$uri[0]]['*'] || (isset($noAuth[$uri[0]][$method]['*']) && $noAuth[$uri[0]][$method]['*'])) {
            $noAuthRequired = true;
        } else if (isset($noAuth[$uri[0]][$method])) {
            foreach ($noAuth[$uri[0]][$method] as $key => $val) {
                if (preg_match('#'.$key.'#', $uri[1]) && $val) {
                    $noAuthRequired = true;
                }
            }
        }

        if (count($uri) == 1) {
            $adminRequired = (isset($adminAuth[$uri[0]][$method]) && $adminAuth[$uri[0]][$method]) || (isset($adminAuth[$uri[0]]['*']) && $adminAuth[$uri[0]]['*']);
        } else if ($adminAuth[$uri[0]]['*'] || (isset($adminAuth[$uri[0]][$method]['*']) && $adminAuth[$uri[0]][$method]['*'])) {
            $adminRequired = true;
        } else if (isset($adminAuth[$uri[0]][$method])) {
            foreach ($adminAuth[$uri[0]][$method] as $key => $val) {
                if (preg_match('#'.$key.'#', $uri[1]) && $val) {
                    $adminRequired = true;
                }
            }
        }

        if (!$config->jwt->protected) {
            return;
        }

        if ((empty($token) || !$auth->checkToken($token)) && (!$noAuthRequired || $adminRequired)) {
            $app->session->set('movement', $movement);
            $app->session->set("error", true);
            $app->session->set("error_response", ['status' => STATUS_ERROR_UNAUTHORIZED, 'data' => ['description' => [['code' => STATUS_ERROR_UNAUTHORIZED, 'message' => "Falta 'accesstoken' valido, por favor realize login para obtener uno."]]]]);
            return;

        }

        if (!empty($token)) {
            $info = $auth->getInfo($token);
            if ($adminRequired && $info['role'] != 1) {
                throw new Http403Exception('No tiene permisos para realizar esta acción.');
            }
            $app->session->set('user', $info);
            $movement->user_id = $info['id'];
        } else {
            $app->session->set('user', false);
        }

        $app->session->set('movement', $movement);
        $app->session->set("error", false);

        return;
    });

    // Making the correct answer after executing

    $app->after(
        function () use ($app) {
            // Getting the return value of method
            $return = $app->getReturnedValue();
            $movement = $app->session->get('movement');
            $app->session->destroy();
            if (is_array($return)) {
                // Transforming arrays to JSON
                switch ($return['status']) {
                    case 200:
                        $app->response->setStatusCode('200', 'OK');
                        break;
                    case 401:
                        $app->response->setStatusCode('401', 'Unauthorized');
                        break;
                    case 403:
                        $app->response->setStatusCode('403', 'Forbidden');
                        break;
                    case 500:
                        $app->response->setStatusCode('500', 'Internal Server Error');
                        break;
                }
                $movement->status = isset($return['status'])?$return['status']:500;
                if (strpos($movement->uri,'users/login') !== false) {
                    $movement->user_id = $return['data']['id'];
                }
                if ($movement->save() && $movement->method == 'post') {
                    $post = $app->request->getPost();
                    if (isset($post['stack_click']) || isset($post['stack_over']) || isset($post['stack_click_map'])) {
                        $post['movement_id'] = $movement->id;
                        $bsn = new AnalyticsBSN();
                        $bsn->setMovements($post);
                    }
                }

                $app->response->setContent(json_encode($return['data']));
            } elseif (!strlen($return)) {
                // Successful response without any content
                $app->response->setStatusCode('204', 'No Content');
                $movement->status = 204;
                $movement->save();
            } else {
                // Unexpected response
                throw new Exception('Bad Response');
            }

            // Sending response to the client
            $app->response->send();
        }
    );

    // Processing request
    $app->handle();


} catch (AbstractHttpException $e) {
    $movement = $app->session->get('movement');
    if (!isset($movement)) {
        $movement = new Movements();
        $movement->uri = str_replace($config->application->baseUri, '',strtolower($app->request->getURI()));
        $method = strtolower($app->request->getMethod());
        $movement->method = $method;
    }
    $movement->status = 500;
    if ($movement->save() && $movement->method == 'post') {
        $post = $app->request->getPost();
        if (isset($post['stack_click']) || isset($post['stack_over']) || isset($post['stack_click_map'])) {
            $post['movement_id'] = $movement->id;
            $bsn = new AnalyticsBSN();
            $bsn->setMovements($post);
        }
    }
    $response = $app->response;
    $response->setStatusCode($e->getCode(), $e->getMessage());
    $response->setJsonContent($e->getAppError());
    $response->send();
} catch (\Phalcon\Http\Request\Exception $e) {
    $movement = $app->session->get('movement');
    if (!isset($movement)) {
        $movement = new Movements();
        $movement->uri = str_replace($config->application->baseUri, '',strtolower($app->request->getURI()));
        $method = strtolower($app->request->getMethod());
        $movement->method = $method;
    }
    $movement->status = 400;
    if ($movement->save() && $movement->method == 'post') {
        $post = $app->request->getPost();
        if (isset($post['stack_click']) || isset($post['stack_over']) || isset($post['stack_click_map'])) {
            $post['movement_id'] = $movement->id;
            $bsn = new AnalyticsBSN();
            $bsn->setMovements($post);
        }
    }
        $app->response->setStatusCode(400, 'Bad request')
        ->setJsonContent([
            AbstractHttpException::KEY_CODE    => 400,
            AbstractHttpException::KEY_MESSAGE => 'Bad request'
        ])
        ->send();
} catch (Http404Exception $e) {
    $result = [
        'error'    => 404,
        'message'  => $e->getMessage()
    ];
    $movement = $app->session->get('movement');
    if (!isset($movement)) {
        $movement = new Movements();
        $movement->uri = str_replace($config->application->baseUri, '',strtolower($app->request->getURI()));
        $method = strtolower($app->request->getMethod());
        $movement->method = $method;
    }
    $movement->status = 404;
    if ($movement->save() && $movement->method == 'post') {
        $post = $app->request->getPost();
        if (isset($post['stack_click']) || isset($post['stack_over']) || isset($post['stack_click_map'])) {
            $post['movement_id'] = $movement->id;
            $bsn = new AnalyticsBSN();
            $bsn->setMovements($post);
        }
    }
    $app->response->setStatusCode(404, 'Not Found')
        ->setJsonContent($result)
        ->send();
} catch (Http403Exception $e) {
    $result = [
        'error'    => 403,
        'message'  => $e->getMessage()
    ];
    $movement = $app->session->get('movement');
    if (!isset($movement)) {
        $movement = new Movements();
        $movement->uri = str_replace($config->application->baseUri, '',strtolower($app->request->getURI()));
        $method = strtolower($app->request->getMethod());
        $movement->method = $method;
    }
    $movement->status = 403;
    if ($movement->save() && $movement->method == 'post') {
        $post = $app->request->getPost();
        if (isset($post['stack_click']) || isset($post['stack_over']) || isset($post['stack_click_map'])) {
            $post['movement_id'] = $movement->id;
            $bsn = new AnalyticsBSN();
            $bsn->setMovements($post);
        }
    }
    $app->response->setStatusCode(403, 'Forbidden')
        ->setJsonContent($result)
        ->send();
}  catch (Http401Exception $e) {
    $result = [
        'error'    => 401,
        'message'  => $e->getMessage()
    ];
    $movement = $app->session->get('movement');
    if (!isset($movement)) {
        $movement = new Movements();
        $movement->uri = str_replace($config->application->baseUri, '',strtolower($app->request->getURI()));
        $method = strtolower($app->request->getMethod());
        $movement->method = $method;
    }
    $movement->status = 401;
    if ($movement->save() && $movement->method == 'post') {
        $post = $app->request->getPost();
        if (isset($post['stack_click']) || isset($post['stack_over']) || isset($post['stack_click_map'])) {
            $post['movement_id'] = $movement->id;
            $bsn = new AnalyticsBSN();
            $bsn->setMovements($post);
        }
    }
    $app->response->setStatusCode(401, 'Unauthorized')
        ->setJsonContent($result)
        ->send();
} catch (\Exception $e) {
    // Standard error format
    /*$result = [
        AbstractHttpException::KEY_CODE    => 500,
        AbstractHttpException::KEY_MESSAGE => 'Some error occurred on the server.'
    ];*/

    $movement = $app->session->get('movement');
    if (!isset($movement)) {
        $movement = new Movements();
        $movement->uri = str_replace($config->application->baseUri, '',strtolower($app->request->getURI()));
        $method = strtolower($app->request->getMethod());
        $movement->method = $method;
    }
    $movement->status = 500;
    if ($movement->save() && $movement->method == 'post') {
        $post = $app->request->getPost();
        if ( isset($post['stack_click']) || isset($post['stack_over']) || isset($post['stack_click_map'])) {
            $post['movement_id'] = $movement->id;
            $bsn = new AnalyticsBSN();
            $bsn->setMovements($post);
        }
    }
    $result = [
        'error'    => 500,
        'message'  => 'Some error occurred on the server.',
        'error_string' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ];

    // Sending error response
    $app->response->setStatusCode(500, 'Internal Server Error')
        ->setJsonContent($result)
        ->send();
}