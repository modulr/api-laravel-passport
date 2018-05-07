<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Storage;
use Avatar;
use App\Notifications\SignupActivate;
use App\Notifications\SignupSuccess;
use App\User;

class AuthController extends Controller
{
    /**
     * Create an user deactivate and send notification to account confirm
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [json] message, errors
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'activation_token' => str_random(60)
        ]);

        $user->save();

        $avatar = Avatar::create($user->name)->getImageObject()->encode('png');
        Storage::put('avatars/'.$user->id.'/avatar.png', (string) $avatar);

        $user->notify(new SignupActivate($user));

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Confirm your account activate user
     *
     * @param  [type] $token
     * @return [string] error
     * @return [obj] user
     */
    public function signupActivate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'This activation token is invalid.'
            ], 404);
        }

        $user->active = true;
        $user->activation_token = '';
        $user->save();

        $user->notify(new SignupSuccess($user));

        return $user;
    }

    /**
     * Loggin user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [json] token, error
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'password']);
        $credentials['active'] = 1;
        $credentials['deleted_at'] = null;

        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return [json] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return [json] user obj
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
