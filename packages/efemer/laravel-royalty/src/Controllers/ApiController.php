<?php

namespace Efemer\Royalty\Controllers;

use Efemer\Higg\Controllers\HiggApiController;
use Efemer\Higg\Factory\Handlers\ActionHandler;
use Efemer\Royalty\Factory\Models\User;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use \Higg;

class ApiController extends HiggApiController {


    function debug() {
        //
    }

    function apiHandle(){

        $params = $this->routeParams();
        $except = config('higg.api.public', [ 'public' ]);
        $action = array_get($params, 'action');
        $endpoint = array_get($params, 'endpoint');

        $parts = explode('.', $action);
        $action_namespace = $parts[0];
        if (!in_array($action_namespace, $except) && !in_array($endpoint, $except) ) {
            $response = $this->authApiUser();

            if (isset($response['error'])) {
                $clientIp = request()->getClientIp();
                return response()->json([
                    'status' => false,
                    'code' => 0,
                    'message' => "Restricted Area. Your ID ($clientIp) is logged for further inspection.",
                    'info' => $response['error']
                ], HTTP_FORBIDDEN);
            }

            // pr(\Auth::check());
            // must be logged in
            if (!isLogged()) {
                // if (!is_local()) {
                    // $clientIp = request()->getClientIp();
                    // return response()->json(['error' => "Restricted Area. Your IP Address ($clientIp) has been logged for further inspection." ], HTTP_FORBIDDEN);
                // }
                // @todo login as test user id
                // $user = userById(138);
                // $user = userById(13822);
                // \Auth::loginUsingId($user->id);
            }
        }

        // $refDomain = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        // header('Access-Control-Allow-Origin', 'http://localhost:4200');

        $endpoint = $this->routeParams('endpoint');
        switch($endpoint) {
            case 'public':
                return $this->publicHandler();
            case 'search':
                return $this->globalSearch();
            case 'browser':
                return $this->handleBrowser();
            case 'form':
                return $this->handleForm();
            case 'action':
                return $this->handleAction();
            case 'verify':
                return $this->verifyCurrentUser();
                break;
            case 'pubnub':
                return $this->pubnubHandler($action);
            default:
                return $this->doAction();
        }

    }

    // api/action/key - POST data, query, json
    function handleAction(){

        $params = $this->routeParams();

        if (isset($params['action'])) $actionKey = $params['action'];
        else $actionKey = array_get($params, 'key');

        $transform = array_get($params, 'transform');

        if (is_null($transform)) $json = null;
        else $json = ($transform == 'json') ? true : false;

        $handler = new ActionHandler();
        if ($handler->isKnown($actionKey)) {

            $handler->init($actionKey);

            $customHandler = $handler->getHandler();
            if ($customHandler instanceof ActionHandler) {
                $handler = $customHandler;
            }

            $result = $handler->returnJson($json)     // content type
                              ->parseRequest()    // parse post data
                              ->exec();       // handle

            if ($result instanceof \Illuminate\Http\Response) {
                return $result;
            } else if ($result instanceof BinaryFileResponse) {
                return $result;
            }

        } else {
            if (!Higg::hasError()) Higg::error('Unknown action requested.');
        }

        return $handler->respond();
    }

    function globalSearch(){
        $term = post('query.term');
        $object_type = post('query.object_type');

        $must = [];
        if (!empty($object_type)) {
            $must = [ 'object_type' => $object_type ];
        }

        $index = new ForceObject('force_objects');
        return $index->queryMultiMatch( [ 'display_name', 'description' ], $term, [ 'from' => 0, 'limit' => 50, 'must' => $must ] );
    }

    function doAction(){
        return [
            'error' => 'Knowledge is Vanity'
        ];
    }

    function verifyCurrentUser(){
        $this->authApiUser();
        if (isLogged()) {
            $user = app('user')->getObject(user()->id);
            // $user = $user->toHumanize( [  'id', 'accessToken', 'verified', 'username', 'uniqid' ] );
            $user = $user->toSessionUser();
            return response()->json(['data' => $user]);
        }
        return response()->json(['error' => 'unauthorized'], HTTP_FORBIDDEN);
    }

    // somewhere in your controller
    public function authApiUser() {

        // $user = \JWTAuth::parseToken()->authenticate();
        // pr($user);

        try {
            if (! $user = \JWTAuth::parseToken()->authenticate()) {
                return ['error' => 'user_not_found'];
            }
        } catch (TokenExpiredException $e) {
            return ['error' => 'token_expired'];
        } catch (TokenInvalidException $e) {
            return ['error' => 'token_invalid'];
        } catch (JWTException $e) {
            return ['error' => 'token_absent'];
        }

        \Auth::loginUsingId($user->id);
        return [ 'success' => isLogged() ];
    }

    /*
    public function authenticate(Request $request) {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }
    */

    function pubnubHandler($action){

        if (is_null($action)) $action = $this->routeParams('action');

        switch($action) {
            case 'authKeys':
                $pubnub = new PubNubHelper();
                $result = $pubnub->grant( [ 'hammer' ] );
                if (!higg()->error()) {
                    return higg()->respondWithJson($result);
                }
                break;
        }

    }

    function publicHandler(){
        $action = $this->routeParams('action');
        if (strpos($action, 'public') === 0) {
            return $this->handleAction();
        }
        abort(404, 'No idea why you would ask me that!');
    }


}
