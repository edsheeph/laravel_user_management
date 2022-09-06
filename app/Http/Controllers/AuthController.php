<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register (Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        $userData = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $userData->save();

        return customResponse()
            ->message('Successfully created user!')
            ->data($userData)
            ->success()
            ->generate();
    }

    public function login (Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return customResponse()
                ->message('Unauthorized')
                ->data(null)
                ->failed(404)
                ->generate();
        }

        $userData = $request->user();

        $tokenResult = $userData->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return customResponse()
            ->message('Successfully logged in!')
            ->data([
                'user' => $userData,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ])
            ->success()
            ->generate();
    }

    public function logout (Request $request) {
        $request->user()->token()->revoke();
        return customResponse()
            ->message('Successfully logged out!')
            ->data(null)
            ->success()
            ->generate();
    }

    public function user (Request $request) {
        $userData = $request->user();
        return customResponse()
            ->message('Success in Getting User.')
            ->data($userData)
            ->success()
            ->generate();
    }
}
