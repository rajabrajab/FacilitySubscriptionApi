<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Routing\Controller;
use App\Http\Helpers\HttpCodes;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ImageUploadTrait;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ImageUploadTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware(\App\Http\Middleware\SetLocale::class);
    }

    public function login(Request $request)
    {

        $response = $this->authService->login($request);

        if (!$response->status()) {
           return response()->sendError(HttpCodes::UNAUTHENTICATED, $response->message());
        }

        $user = $response->data()['user'];

        return response()->sendResponse([
            'token' => $response->data()['token'],
            'user' => new UserResource($user)
        ] , $response->message());
    }

    public function register(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'number' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|max:5',
            'iso_code' => 'nullable|string|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $imageUrl =  $this->uploadImage($data['image'], 'profile-images');
        $data['profile_image'] = $imageUrl;

        $response = $this->authService->register($data);
        $user = $response->data()['user'];
        $user['profile_image'] =  asset('storage/'. $user['profile_image']);

        return response()->sendResponse(['user' => $user],$response->message());
    }

    public function verifyOtpAndRegister(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string'
        ]);

        $response = $this->authService->verifyOtpAndRegister($request);

        if ($response->status()) {
            return response()->sendResponse([
                'token' => $response->data()['token'],
                'user' => new UserResource($response->data()['user'])
            ] , $response->message());
        }

        return response()->sendError(HttpCodes::UNAUTHENTICATED, $response->message());
    }

    public function resendRegisterCode(Request $request)
    {

        $data = $request->validate([
            'email' => 'required'
        ]);

        $response = $this->authService->resendCode($data['email']);

        if ($response->status()) {

           return response()->sendResponse([], $response->message());
        }

        return response()->sendError(HttpCodes::UNAUTHENTICATED, $response->message());
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return response()->sendResponse([],__('auth.logout_success'));
    }

    public function updateProfile(Request $request)
    {

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.Auth::id(),
            'profile_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $this->authService->updateProfile($validated);

        return response()->sendResponse(new UserResource($user), __('updated_successfully'));

    }

    public function refresh(Request $request)
    {

        $response = $this->authService->refreshToken($request);

        return response()->sendResponse([
                'token' => $response->data()['token'],
                'user' => new UserResource($response->data()['user'])
        ] , $response->message());

    }

    public function userProfile(Request $request)
    {

        $user = Auth::user();

        return response()->sendResponse(new UserResource($user), __('retrieved_success'));
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        $user->tokens()->delete();
        $user->delete();

        return response()->sendResponse([], __('auth.deleted_account_successfully'));
    }
}
