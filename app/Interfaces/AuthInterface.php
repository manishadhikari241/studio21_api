<?php


namespace App\Interfaces;


interface AuthInterface
{
    public function register($request);

    public function login($request);

    public function verify($request);

    public function logout();

    public function refresh();

    public function userProfile();

    public function resendCode($request);

    public function updateProfile($request);

    public function forgotPassword($request);

    public function resetPassword($request);

    public function loginAs($request);

    public function changePassword($request);
}
