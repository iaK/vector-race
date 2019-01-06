<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|unique:users|max:20|min:4',
        ]);

        $user->update($request->only(["username"]));

        return response()->json([
            "status" => "ok",
            "data" => [
                "user" => $user->fresh(),
            ]
        ]);
    }

}
