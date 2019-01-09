<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => ['required','max:20','min:4', Rule::unique('users')->ignore($user->id)],
            "car_color" => ['required', Rule::in(["red", "green", "yellow", "purple", "orange", "blue"])],
            "trace_color" => ['required', Rule::in("red", "green", "yellow", "purple", "orange", "blue")],
        ]);

        $user->update($request->only(["username", "car_color", "trace_color"]));

        return response()->json([
            "status" => "ok",
            "data" => [
                "user" => $user->fresh(),
            ]
        ]);
    }

}
