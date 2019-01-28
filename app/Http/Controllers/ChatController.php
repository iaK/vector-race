<?php

namespace App\Http\Controllers;

use App\Race;
use Illuminate\Http\Request;
use App\Events\MessagePosted;

class ChatController extends Controller
{
    public function chat(Request $request, Race $race)
    {
        broadcast(new MessagePosted($race, $request->message, $request->type))->toOthers();
    }
}
