<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Routing\Controller;
use App\Http\Helpers\HttpCodes;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class PasswordResetController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware(\App\Http\Middleware\SetLocale::class);
    }

    public function sendResetCode(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $response = $this->authService->sendOtp($request->email);

        if ($response->status()) {

           return response()->sendResponse([], $response->message());
        }

        return response()->sendError(HttpCodes::FATAL_ERROR, $response->message());
    }

    public function resendCodeForReset(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $response = $this->authService->resendCodeForReset($request->email);

        if ($response->status()) {

           return response()->sendResponse([], $response->message());
        }

        return response()->sendError(HttpCodes::UNAUTHENTICATED, $response->message());
    }

    public function verifyResetCode(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string'
        ]);

        $response = $this->authService->confirmOtp($data);

        if($response->status()){
            return response()->sendResponse([], $response->message());
        }

        return response()->sendError(HttpCodes::UNAUTHENTICATED, $response->message());
    }

    public function resetPassword(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $response = $this->authService->updatePassword($data,$request);

        if ($response->status()) {
            return response()->sendResponse([
                'token' => $response->data()['token'],
                'user' => new UserResource($response->data()['user'])
            ] , $response->message());
        }

        return response()->sendError(HttpCodes::UNAUTHENTICATED,$response->message());
    }
}
