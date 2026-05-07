<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // validate the request
        $request->validate([
            'username'      => 'required|string|unique:users',
            'email_address' => 'required|string|email|unique:users,email_address',
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'password'      => 'required|string',
        ]);

        // hash the password
        $hashedPassword = Hash::make($request->password);

        // create the user
        $user = User::create([
            'username'      => $request->username,
            'email_address' => $request->email_address,
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'password'      => $hashedPassword,
        ]);

        // return a json response
        return response()->json([
            'status'    => 'success',
            'message'   => 'User created successfully',
            'user_id'   => $user->id,
        ], 201);
    }

    public function login(Request $request)
    {
        // validate the request
        $request->validate([
            'username'      => 'required|string',
            'password'      => 'required|string',
        ]);

        // find the user by username
        $user = User::where('username', $request->username)->first();

        if(!$user) {
            return response()->json(['status' => 'error', 'message' => 'Username not found'], 400);
        }

        // verify the password
        $validPassword = Hash::check($request->password, $user->password);

        // return a json response
        if($validPassword) {
            return response()->json(['status' => 'success', 'data' => $user], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Provided credentials are invalid'], 400);
        }
    }

    public function getUsers()
    {
        // fetch all users
        $users = User::all(['id', 'username', 'email_address', 'first_name', 'last_name', 'created_at', 'updated_at'])
            ->sortByDesc('id')
            ->values();

        // return a json response
        return response()->json($users);
    }

    public function getUserById($id)
    {
        // find the user by id
        $user = User::find($id);

        // return a json response (including a 404 if not found)
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function getUserByUsername($username)
    {
        // find the user by username
        $user = User::where('username', $username)->first();

        // return a json response (including a 404 if not found)
        if(!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        } 

        return response()->json($user);
    }

    public function updateUser(Request $request, $id)
    {
        // validate the request
        $request->validate([
            'email_address'     => 'sometimes|string|email|unique:users,email_address,' . $id,
            'first_name'        => 'sometimes|string',
            'last_name'         => 'sometimes|string',
        ]);

        // find the user by id
        $user = User::find($id);

        if(!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        // update the user
        $user->update([
            'email_address'     => $request->input('email_address', $user->email_address),
            'first_name'        => $request->input('first_name', $user->first_name),
            'last_name'         => $request->input('last_name', $user->last_name),
        ]);

        // return a json response
        return response()->json(['status' => 'success', 'message' => 'User updated successfully']);
    }

    public function deleteUser($id)
    {
        // find the user by id
        $user = User::find($id);

        if(!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        // delete the user
        $user->delete();

        // return a json response
        return response()->json(['status' => 'success', 'message' => 'User deleted successfuly']);
    }
}