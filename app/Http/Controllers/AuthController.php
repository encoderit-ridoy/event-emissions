<?php

namespace App\Http\Controllers;

use App\Mail\EmailVarify;
use App\Mail\ForgotPasswordMail;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\MailForgotpassword;
use App\Models\Company;
use App\Rules\NotFreeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate_data = [
            'company_name'           => 'required|string',
            'name'                   => 'required|string',
            'phone'                  => 'sometimes|required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'email'                  => ['required', 'email', 'unique:users,email', new NotFreeEmail],
            'password'               => 'required|min:8',
            'event_date'             => 'sometimes|required',
            'no_of_event_attendance' => 'sometimes|required',
            'role_id'                => 'required|exists:roles,id',
        ];
        $validator = $request->validate($validate_data);
        $company = Company::firstOrCreate(
            ['slug' => Str::slug($request->company_name)],
            ['name' => $request->company_name]
        );
        $validator['company_id'] = $company->id;
        $validator['email_varify_token'] = Str::random(64);
        $user = User::create($validator);
        if ($user)
            $user->roles()->attach($request->role_id);
        Mail::to($user->email)->send(new EmailVarify($user->email_varify_token, $request->redirect_url));
        return response()->json([
            // 'token' => $user->createToken('Api Token')->plainTextToken
            'message' => '確認用のメールを送信しました！メール受信箱を確認して説明に従ってください',
        ], 200);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'      => 'required|email|exists:users,email',
                'password'   => 'required|min:6',
                'login_with' => 'required|string'
            ]);
            if (Auth::attempt($request->only(['email', 'password']))) {
                $user = User::findOrFail(Auth::id());

                if ($user->status != 'active')
                    abort(403, "User is not active");

                if ($request->login_with == 'admin' && $user->hasRole('admin') != true)
                    return response()->json(['message' => 'You are not an admin'], 401);
                if ($request->login_with == 'user' && $user->hasRole('user') != true)
                    return response()->json(['message' => 'You are not an user'], 401);

                return response()->json([
                    'token' => $user->createToken('Api Token')->plainTextToken,
                ], 200);
            }
            abort(401, "Credentials doen't match");
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::user()->tokens()->delete();

            return response()->json([
                'message' => 'User Logged Out Successfully'
            ], 200);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function getAuthUser()
    {
        $user = User::with(['company', 'roles' => function ($role) {
            $role->with('permissions');
        }])->findOrFail(Auth::id());

        return response()->json([
            'user' => $user
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $validate_data = [
            'email' => 'required|string|email|exists:users,email',
            'redirect_url' => 'required|string',
        ];
        $request->validate($validate_data);

        $user = User::where('email', $request->email)->where('status', 'active')->first();
        if ($user) {
            $token = Str::random(64);
            DB::table('password_resets')->insert([
                'email'      => $request->email,
                'token'      => $token,
                'created_at' => Carbon::now()
            ]);
            Mail::to($user->email)->send(new ForgotPasswordMail($token, $request->redirect_url));

            return response()->json([
                'message' => 'メールが送信されました'
            ], 200);
        } else {
            abort(404, 'User associated with this email is not found.');
        }
    }

    public function verifyForgotPasswordToken(Request $request)
    {
        $request->validate([
            'token' => 'required|exists:password_resets,token',
        ]);

        $password_reset = DB::table('password_resets')->where('token', $request->token)->first();
        if ($password_reset === null)
            abort(400, 'Token not valid');

        if (Carbon::parse($password_reset->created_at)->addMinutes(720)->isPast()) {
            $password_reset->delete();
            abort(500, 'Password reset token is expired.');
        }

        return response()->json([
            'message' => 'Password reset token is valid.',
            'token'  => $password_reset
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'             => 'required|email|exists:users,email',
            'password'          => 'required|different:previous_password|min:8|confirmed',
            'token'             => 'required_without:previous_password',
            'previous_password' => 'required_without:token',
        ]);
        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user) abort(404, 'User not found or invalid email address.');

        if ($request->has('previous_password')) {
            if ($user && !Hash::check($request->previous_password, $user->password))
                return abort(422, 'The previous password you gave is incorrect.');
        } else {
            $reset_token = DB::table('password_resets')->where([
                ['token', $request->token],
                ['email', $request->email]
            ])->first();
            if (!$reset_token) abort(404, 'Token not found or invalid.');
        }
        $user->update(['password' => $request->password]);

        return response()->json([
            'message' => 'Password reset or changed successfully.'
        ], 200);
    }

    public function varifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required|exists:users,email_varify_token',
        ]);

        $user =  User::where('email_varify_token', $request->token)->firstOrFail();
        $user->update(['email_verified_at' => now(), 'email_varify_token' => null, 'status' => 'active']);

        return response()->json([
            'token' => $user->createToken('Api Token')->plainTextToken,
        ]);
    }
}
