<?php

namespace Danielpk74\LaravelAuthStarter\Http\Controllers\Admin;

use Danielpk74\LaravelAuthStarter\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Danielpk74\LaravelAuthStarter\Models\User;

class UserController extends \Illuminate\Routing\Controller
{
    public function index()
    {
        return User::latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    }

    public function update(User $user)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ];
        
        // Only validate password if it's provided
        if (request('password')) {
            $validationRules['password'] = 'required|string|min:8';
        }
        
        request()->validate($validationRules);
        
        $data = [
            'name' => request('name'),
            'email' => request('email'),
        ];
        if (request('password')) {
            $data['password'] = bcrypt(request('password'));
        }
        $user->update($data);
        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }

    public function changeRole(User $user)
    {
        $user->update([
            'role' => request('role'),
        ]);

        return response()->json(['success' => true]);
    }

    public function searchUser(Request $request)
    {
        $searchUserQuery = $request->get('query');
        $users = User::where('name', 'like', "%{$searchUserQuery}%")->paginate(20);

        return response()->json($users);
    }

    public function deleteUserBulk(Request $request)
    {
        User::whereIn('id', $request->get('ids'))->delete();
        return response()->json(['message' => 'Users deleted']);
    }
}
