<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Constants\SendOtp;
use App\Http\Requests\AuthResetPasswordRequest;
use App\Interfaces\AuthInterface;
use App\Mail\ForgotPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository extends MainRepository implements AuthInterface
{
    const testNumber = ['85266452041', '85298179219'];

    private function checkPhone($phone)
    {
        $user = User::where('phone', $phone)->first();
        if (in_array($phone, self::testNumber))
            return true;
        else if ($user && $user->phone == $phone)
            return false;
        else return true;
    }

    public function register($request)
    {
        try {
            $phone = $request->mobile_code . str_replace(' ', '', $request->phone);
            if (!$this->checkPhone($phone))
                return $this->error('Phone number already taken', ErrorCodes::FORBIDDEN);
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $phone,
                'role_id' => 1,
                'lang_pref' => $request->lang_pref ? $request->lang_pref : 'en'
            ]);
            if ($user) {
                $user->otp_code = SendOtp::sendOtp($user->phone);
                $user->save();
            }
            return $this->success('User Successfully Registered', [], ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function login($request)
    {
        try {
            if (!$token = auth()->attempt(request(['email', 'password']))) {
                return $this->error('Invalid Credentials', ErrorCodes::UNAUTHORIZED);
            }
            if (!$this->isVerified($request->email)) {
                $user = User::where('email', $request->email)->first();
                $code = SendOtp::sendOtp($user->phone);
                $user->otp_code = $code;
                $user->save();
                return $this->error('Your Account is not verified', ErrorCodes::FORBIDDEN);
            }
            return $this->createNewToken($token);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    private function isVerified($email)
    {
        $user = User::where('email', $email)->first();
        return $user->otp_verified == 1;
    }

    public function verify($request)
    {
        try {
            if (!$request->code) return $this->error('Please enter Code', ErrorCodes::VALIDATION_FAILED);
            $user = User::where('otp_code', $request->code)->first();
            if ($user) {
                $user->otp_verified = 1;
                $user->otp_code = null;
                $user->save();
                return $this->createNewToken(JWTAuth::fromUser($user));
            } else return $this->error('Verify Code is not Correct. Please try again', ErrorCodes::NOT_FOUND);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();
            return $this->success('Successfully logged Out', null, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    public function UserProfile()
    {
        return response()->json(auth()->user());
    }

    public function resendCode($request)
    {
        try {
            if (!$request->email)
                return $this->error('please provide email', ErrorCodes::VALIDATION_FAILED);
            $user = User::where('email', $request->email)->first();
            if (!$user)
                return $this->error('User not Found', ErrorCodes::VALIDATION_FAILED);
            else if ($user->opt_verified == 1)
                return $this->error('Already verified', ErrorCodes::VALIDATION_FAILED);
            else {
                $user->otp_code = SendOtp::sendOtp($user->phone);
                $user->save();
                return $this->success('Code Sent to your phone', null, ErrorCodes::SUCCESS);
            }

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function updateProfile($request)
    {
        try {
            $user = User::find(Auth::guard('api')->id());
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->save();
            return $this->success('User information has been updated', $user, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function changePassword($request)
    {
        try {
            $user = User::find(Auth::guard('api')->id());
            $check = Hash::check($request->old_password, $user->password);
            if (!$check)
                return $this->error('Your Old password is not correct', ErrorCodes::VALIDATION_FAILED);
            $user->password = bcrypt($request->new_password);
            $user->save();
            return $this->success('Your password has been successfully changed', $user, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function forgotPassword($request)
    {
        if (!$request->email) return $this->error('Please enter Email', ErrorCodes::VALIDATION_FAILED);

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user)
                return $this->error('User not found', ErrorCodes::NOT_FOUND);
            $otp = SendOtp::sendOtpEmail($user->email);
            $user->otp_email_verify = $otp;
            $user->otp_email_expiry = Carbon::now()->addHours(1);
            $user->save();
            return $this->success('Reset Code has been sent', [], ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function resetPassword($request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user)
                return $this->error('User not found', ErrorCodes::NOT_FOUND);
            elseif ($user->otp_email_verify == null)
                return $this->error('Try resetting password again', ErrorCodes::NOT_FOUND);
            else if (Carbon::now() > $user->otp_email_expiry) {
                return $this->error('Your reset code is expired, Please try a new one.', ErrorCodes::NOT_FOUND);
            } else if ($request->reset_code != $user->otp_email_verify) {
                return $this->error('Your reset code is not correct.', ErrorCodes::NOT_FOUND);
            }
            $user->password = bcrypt($request->password);
            $user->otp_email_expiry = null;
            $user->otp_email_verify = null;
            $user->save();

            return $this->success('password changed', [], ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function loginAs($request)
    {
        try {
            $user = User::findorfail($request->id);
            if ($user && $user->otp_verified == 1) {
                Auth::guard('api')->login($user);
                return $this->createNewToken(JWTAuth::fromUser($user));
            } else return $this->error('Otp not verified yet to login', ErrorCodes::NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
