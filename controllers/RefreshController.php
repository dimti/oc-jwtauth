<?php namespace Vdomah\JWTAuth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Vdomah\JWTAuth\Classes\OctoberJWTAuth;
use Vdomah\JWTAuth\Models\Settings;
use App;
use Vdomah\JWTAuth\Resources\UserResource;

class RefreshController extends BaseAPIController
{
    /**
     * @var OctoberJWTAuth
     */
    protected $jwtAuth;

    public function refresh(Request $request)
    {
        if (Settings::get('is_refresh_disabled')) {
            return $this->setStatusCode(404)->respondWithError('Page not found', 1);
        }

        $token = $request->get('token');

        try {
            $this->jwtAuth->setToken($token);

            if (!$token = $this->jwtAuth->refresh()) {
                return response()->json(['error' => 'could_not_refresh_token'], 401);
            }
        } catch (TokenBlacklistedException $e) {
            // something went wrong
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $this->jwtAuth->setToken($token);

        $exp = $this->jwtAuth->getPayload()->getClaims()->get('exp')->getValue();

        $responseData = [
            'access_token' => $token,
            'exp' => $exp,
        ];

        Event::fire('vdomah.jwtauth.extendRefreshResponse', [&$responseData, $this]);

        return response()->json($responseData);
    }
}
