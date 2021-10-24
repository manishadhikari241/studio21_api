<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthResetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserInformationRequest;
use App\Interfaces\AuthInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authInterface;

    public function __construct(AuthInterface $authInterface)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verify', 'resendCode', 'forgotPassword', 'resetPassword']]);
        $this->authInterface = $authInterface;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param AuthLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthLoginRequest $request)
    {
        return $this->authInterface->login($request);
    }

    /**
     * Register a User.
     *
     * @param AuthRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AuthRegisterRequest $request)
    {
        return $this->authInterface->register($request);
    }

    public function verify(Request $request)
    {
        return $this->authInterface->verify($request);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        return $this->authInterface->logout();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->authInterface->refresh();
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return $this->authInterface->userProfile();
    }

    public function updateProfile(UserInformationRequest $request)
    {
        return $this->authInterface->updateProfile($request);
    }

    public function resendCode(Request $request)
    {
        return $this->authInterface->resendCode($request);
    }

    public function forgotPassword(Request $request)
    {
        return $this->authInterface->forgotPassword($request);
    }

    public function resetPassword(AuthResetPasswordRequest $request)
    {
        return $this->authInterface->resetPassword($request);
    }

    public function loginAs(Request $request)
    {
        return $this->authInterface->loginAs($request);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->authInterface->changePassword($request);
    }

}
