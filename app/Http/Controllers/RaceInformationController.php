<?php

namespace App\Http\Controllers;

use App\Race;
use Illuminate\Http\Request;

class RaceInformationController extends Controller
{
    public function index(Race $race)
    {
        return $this->respond(
            $race->toArray()
        );
    }
}
