<?php
 namespace Vdomah\JWTAuth\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use October\Rain\Support\Facades\Input;
use Tymon\JWTAuth\Exceptions\JWTException;
use Vdomah\JWTAuth\Models\Settings;
use Vdomah\JWTAuth\Resources\UserResource;

class AuthController extends BaseAPIController
{
    protected $jwtAuth;

    public function user()
    {
        return new UserResource($this->authenticatedUser);
    }

    public function signup(Request $request)
    {
        if (Settings::get('is_signup_disabled')) {
            App::abort(404, 'Page not found');
        }

        $arFields = Settings::get('signup_fields');
        if (!is_array($arFields) || empty($arFields)) {
            $arFields = ['email', 'password', 'password_confirmation'];
        }

        $credentials = Input::only($arFields);

        try {
            $userModel = UserModel::create($credentials);

            if ($userModel->methodExists('getAuthApiSignupAttributes')) {
                $user = $userModel->getAuthApiSignupAttributes();
            } else {
                $user = [
                    'id'           => $userModel->id,
                    'name'         => $userModel->name,
                    'surname'      => $userModel->surname,
                    'username'     => $userModel->username,
                    'email'        => $userModel->email,
                    'is_activated' => $userModel->is_activated,
                ];
            }
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 401);
        }

        $token = $this->jwtAuth->fromUser($userModel);

        return Response::json(compact('token', 'user'));
    }

    public function invalidate(Request $request)
    {
        if (Settings::get('is_invalidate_disabled')) {
            App::abort(404, 'Page not found');
        }

        $token = Request::get('token');

        try {
            // invalidate the token
            $this->jwtAuth->invalidate($token);
        } catch (Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_invalidate_token'], 500);
        }

        // if no errors we can return a message to indicate that the token was invalidated
        return response()->json('token_invalidated');
    }

    public function login(Request $request)
    {
        if (Settings::get('is_login_disabled')) {
            App::abort(404, 'Page not found');
        }

        $arFields = Settings::get('login_fields');
        if (!is_array($arFields) || empty($arFields)) {
            $arFields = ['login', 'password', 'password_confirmation'];
        }

        $credentials = Input::only($arFields);

        try {
            // verify the credentials and create a token for the user
            if (!$token = $this->jwtAuth->attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        // @TODO add a setting to configure if the user data should be sent with the token
        return $this->respondWithDataAndToken(UserResource::make($this->jwtAuth->user()), $token);
    }

    protected function respondWithDataAndToken(UserResource $resource, $token)
    {
        $resource->with = $this->generateTokenResponse($token);
        return $resource;
    }

    protected function generateTokenResponse($token)
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->jwtAuth->factory()->getTTL() * 60,
        ];
    }
}
