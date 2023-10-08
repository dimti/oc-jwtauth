<?php namespace Vdomah\JWTAuth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Vdomah\JWTAuth\Classes\OctoberJWTAuth;
use Vdomah\JWTAuth\Models\Settings;

class RefreshController extends Controller
{
    /**
     * @var OctoberJWTAuth
     */
    protected $jwtAuth;

    public function __construct(OctoberJWTAuth $jwtAuth)
    {
        $this->jwtAuth = $jwtAuth;
    }

    public function refresh(Request $request)
    {
        if (Settings::get('is_refresh_disabled')) {
            App::abort(404, 'Page not found');
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

        // if no errors are encountered we can return a new JWT
        return response()->json(compact('token', 'exp'));
    }
}
