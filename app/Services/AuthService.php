<?php

namespace App\Services;

use App\Constants\ResponseMessages;
use App\Mail\OtpMail;
use App\Models\City;
use App\Models\File;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorRegistrationRequest;
use App\Traits\ImageUploadTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{

    use ImageUploadTrait;

    protected $status = false;
    protected $message = '';
    protected $data = [];


    public function register($data)
    {

        $otp = rand(100000, 999999);

        $user = [
            'name' => $data['name'],
            'email' => $data['email'],
            'type' => 'user',
            'profile_image' => $data['profile_image'] ?? null ,
            'phone' => [
                'number' => $data['number'],
                'country_code' => $data['country_code'],
                'iso_code' => $data['iso_code'],
            ],
        ];

        $hashedPassword = Hash::make($data['password']);

        try{
            Mail::to($data['email'])->send(new OtpMail($otp));
        }
        catch (Exception $e) {
            $this->status = false;
            $this->message = __('general.general_failure');
        }

        Cache::put($data['email'], [
            'code' => $otp,
            'last_sent_at' => now(),
            'user_data' => array_merge($user, ['password' => $hashedPassword])
        ], now()->addMinutes(10));

        $this->status = true;
        $this->message =  __('auth.verification_sent');
        $this->data['user'] = $user;

        return $this;
    }

    public function login($request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            $this->status = false;
            $this->message = __('auth.failed');
            return $this;
        }

        if ($user->status !== 'active') {
            $this->status = false;
            $this->message = __('auth.inactive');
            return $this;
        }

        $token = $this->loginAndCreateToken($user, $request);

        $this->status = true;
        $this->data['token'] = $token;
        $this->data['user'] = $user;

        return $this;
    }

    public function logout()
    {
        $user = Auth::user();

        return $user->tokens()->delete();
    }

    public function updateProfile(array $data)
    {
        $user = Auth::user();

        if (array_key_exists('profile_image', $data)) {
            if ($data['profile_image'] === null) {
                if ($user->profile_image) {
                    $this->deleteImage($user->profile_image);
                }
                $data['profile_image'] = null;
            } elseif ($data['profile_image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($user->profile_image) {
                    $this->deleteImage($user->profile_image);
                }
                $data['profile_image'] = $this->uploadImage(
                    $data['profile_image'],
                    'profile-images'
                );
            }
        }

        $user->update($data);
        return $user;
    }

    public function sendOtp($email)
    {
        $otp = rand(100000, 999999);

        try{
            Mail::to($email)->send(new OtpMail($otp));


           DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15),
                'created_at' => now(),
            ]);

            $this->status = true;
            $this->message = __('auth.verification_sent') ;

            return $this;
        }
        catch (Exception $e) {
            $this->status = false;
            $this->message = __('general.general_failure');
        }

        return $this;
    }

    public function confirmOtp($data)
    {
        $record = DB::table('password_resets')->where('email',$data['email'])->first();

        if($record && $record->otp == $data['token']){
            if(Carbon::parse($record->expires_at)->lt(Carbon::now())) {

                $this->status = false;
                $this->message = __('auth.code_expired');

                return $this;
            }
            $this->status = true;
            $this->message = __('auth.code_verified');
        }else{
            $this->status = false;
            $this->message = __('auth.invalid_code');
        }

        return $this;

    }

    public function updatePassword($data,$request)
    {

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $user->password = Hash::make($data['password']);
            $user->save();

            $user->tokens()->delete();

            $token = $this->loginAndCreateToken($user,$request);

            DB::table('password_resets')->where('email', $data['email'])->delete();

            $this->status = true;
            $this->message = __('auth.password_reset');
            $this->data['token'] = $token;
            $this->data['user'] = $user;
        } else {
            $this->message = __('auth.user_not_found');
        }

        return $this;
    }

    public function verifyOtpAndRegister($request)
    {
        $tempData = Cache::get($request['email']);

        if (!$tempData) {
            $this->status = false;
            $this->message = __('auth.user_not_found');
            return $this;
        }

        $storedOtp = $tempData['code'];

        if ($request['token'] != $storedOtp) {
            $this->status = false;
            $this->message =  __('auth.invalid_code');
            return $this;
        }

        $tempData;

        $user = User::create([
            'name' => $tempData['user_data']['name'],
            'email' => $tempData['user_data']['email'],
            'password' => $tempData['user_data']['password'],
            'type' => $tempData['user_data']['type'],
            'city_id' => $tempData['user_data']['city']['id'],
            'profile_image' => $tempData['user_data']['profile_image'],
            'status' => $tempData['user_data']['type'] == 'vendor' ? 'inactive' : 'active',
            'number' => $tempData['user_data']['phone']['number'] ?? null,
            'country_code' => $tempData['user_data']['phone']['country_code'] ?? null,
            'iso_code' => $tempData['user_data']['phone']['iso_code'] ?? null,
        ]);

        $user->joined_at = now();
        $user->email_verified_at = now();
        $user->save();

        $fcm_token = $request->header('x-token');
        if($fcm_token){
            $user->updateDeviceToken($fcm_token);
        }

        $token = $this->loginAndCreateToken($user,$request);

        Cache::forget($request['email']);

        $this->status = true;
        $this->message =   __('auth.registration_success');
        $this->data['token'] = $token;
        $this->data['user'] = $user;

        return $this;

    }

    public function refreshToken($request)
    {
        $user = Auth::user();

        $token = $this->loginAndCreateToken($user, $request);

        $this->status = true;
        $this->message = __('auth.token_refreshed');

        $this->data['token'] = $token;
        $this->data['user'] = $user;

        return $this;

    }

    private function loginAndCreateToken($user,$request = null)
    {
        if($token = $request->header('x-token')){
            $user->updateDeviceToken($token);
            $user->save();
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return $token;
    }

    public function resendCode($email)
    {

        $cachedData = Cache::get($email);

        if (!$cachedData) {
            $this->status = false;
            $this->message =  __('auth.user_not_found');
            return $this;
        }

        $lastSent = Carbon::parse($cachedData['last_sent_at']);
        $cooldownEnd = $lastSent->addMinutes(2);

        if (now()->lt($cooldownEnd)) {
            $remainingSeconds = now()->diffInSeconds($cooldownEnd);
            $this->status = false;
            $this->message = __('auth.otp_cooldown', ['seconds' => $remainingSeconds]);
            return $this;
        }

        $newOtp = rand(100000, 999999);

        $cachedData['code'] = $newOtp;

        Cache::put($email, $cachedData, now()->addMinutes(10));

        try {
            Mail::to($email)->send(new OtpMail($newOtp));

            $this->status = true;
            $this->message =   __('auth.new_verification_sent');
        } catch (Exception $e) {
            $this->status = false;
            $this->message = __('general.general_failure');
        }

        return $this;
    }

    public function resendCodeForReset($email)
    {
        $existingOtp = DB::table('password_resets')
                        ->where('email', $email)
                        ->first();

        if ($existingOtp && now()->lt($existingOtp->expires_at)) {
            $cooldownEnd = Carbon::parse($existingOtp->created_at)->addMinutes(2);

            if (now()->lt($cooldownEnd)) {
                $remainingSeconds = now()->diffInSeconds($cooldownEnd);
                $this->status = false;
                $this->message = __('auth.otp_cooldown', ['seconds' => $remainingSeconds]);
                return $this;
            }
        }

        return $this->sendOtp($email);
    }

    public function status()
    {
        return $this->status;
    }

    public function message()
    {
        return $this->message;
    }

    public function data()
    {
        return $this->data;
    }
}
