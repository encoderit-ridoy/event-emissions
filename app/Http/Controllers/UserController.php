<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles', 'company', 'events')->whereHas('roles', function ($q) {
            return $q->where('slug', '=', 'user');
        });

        $query->when($request->has('company_id'), function ($q) use ($request) {
            return $q->where('company_id', $request->company_id);
        });

        $users = $query->latest()->paginate($request->per_page ?? 25);

        return response()->json([
            'status' => 'Success',
            'users'   => $users
        ], 200);
    }

    public function store(Request $request)
    {
        $validate_data = [
            'company_name'           => 'required|string',
            'name'                   => 'required|string',
            'phone'                  => 'sometimes|required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'email'                  => 'required|email|unique:users,email',
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
        $user = User::create($validator);
        if ($user)
            $user->roles()->attach($request->role_id);

        return $this->getSingleUser($user->id, 'User Created Successfully.', 201);
    }

    public function update(Request $request)
    {
        $validate_data = [
            'company_name'           => 'required|string',
            'name'                   => 'required|string',
            'phone'                  => 'sometimes|required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'email'                  => 'required|email|unique:users,email,' . $request->user_id,
            'event_date'             => 'sometimes|required',
            'no_of_event_attendance' => 'sometimes|required',
            'user_id'                => 'required|exists:users,id'
        ];
        $validator = $request->validate($validate_data);

        $user = User::findOrFail($request->user_id);
        Company::findOrFail($user->company_id)->update([
            'name' => $request->company_name,
            'slug' => Str::slug($request->company_name),
        ]);
        $user->update($validator);

        return $this->getSingleUser($user->id, 'User Updated Successfully.', 200);
    }

    public function getSingleUser($id, $message = 'User found.', $code = 200)
    {
        $user = User::with('roles', 'company', 'events')->findOrFail($id);

        return response()->json([
            'message' => $message,
            'user'   => $user
        ], $code);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User Deleted Successfully.',
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $validate_data = [
            'id'     => 'required|integer|exists:users,id',
            'status' => 'required|in:active,inactive',
        ];
        $request->validate($validate_data);
        $user = User::findOrFail($request->id);
        $user->update(['status' => $request->status]);

        return response()->json([
            'message' => 'User status updated Successfully.',
            'user'   => $user
        ], 200);
    }
}
