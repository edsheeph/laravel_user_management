<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserSession;

class AuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'user_role_id' => UserRole::APPLICANT,
            'password' => bcrypt($request->password)
        ]);

        $user->save();

        $this->logActivity(
            $user->id,
            'Register',
            'Register Account',
            'Successfully created user!'
        );

        return customResponse()
            ->message('Successfully created user!')
            ->data($user)
            ->success()
            ->generate();
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return customResponse()
                ->message('These credentials do not match our records.')
                ->data(null)
                ->failed(404)
                ->generate();
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        $userSession = UserSession::where("user_id", $user->id)->first();
        if (empty($userSession)) {
            $userSession = new UserSession;
        }
        $userSession->user_id = $user->id;
        $userSession->access_token = $tokenResult->accessToken;
        $userSession->ip_address = $request->ip();
        $userSession->save();

        $this->logActivity(
            $user->id,
            'Login',
            'Login Account',
            'Successfully logged in!'
        );

        return customResponse()
            ->message('Successfully logged in!')
            ->data([
                'user' => $user,
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
        $this->logActivity(
            $request->user()->id,
            'Logout',
            'Logout Account',
            'Successfully logged out!'
        );

        $request->user()->token()->revoke();

        return customResponse()
            ->message('Successfully logged out!')
            ->data(null)
            ->success()
            ->generate();
    }

    public function user (Request $request) {
        $user = $request->user();
        $userRole = $user->userRole;
        $userSession = $user->userSession;

        return customResponse()
            ->message('Success in Getting User.')
            ->data($user)
            ->success()
            ->generate();
    }
}
