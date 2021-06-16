<?php


namespace Vdomah\JWTAuth\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vdomah\JWTAuth\Classes\OctoberJWTAuth;

class BaseAPIController extends Controller
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var
     */
    protected $payload;
    /**
     * @var OctoberJWTAuth
     */
    protected $jwtAuth;
    /**
     * @var
     */
    protected $authenticatedUser;

    public function __construct(OctoberJWTAuth $jwtAuth, Request $request)
    {
        $this->request = $request;
        $this->jwtAuth = $jwtAuth;

        if ($token = $request->bearerToken()) {
            $this->setToken($token);
        }
    }

    public function setToken($token)
    {
        $this->jwtAuth->setToken($token);
        $this->payload = $this->jwtAuth->getPayload();
        $this->authenticatedUser = $this->jwtAuth->user();
    }

    public function getAuthenticatedUser()
    {
        return $this->authenticatedUser;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getAuth()
    {
        return $this->jwtAuth;
    }

    /**
     * Respond with success.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondSuccess()
    {
        return $this->respond(null, 204);
    }

    /**
     * Return generic json response with the given data.
     *
     * @param $data
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($data, $statusCode = 200, $headers = [])
    {
        return response($data, $statusCode, $headers);
    }
}
