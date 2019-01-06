<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public $guarded = [];

    public $casts = [
        "starting_point" => "array",
        "starting_speed" => "array",
        "finish_line" => "array",
        "inner_track" => "array",
        "outer_track" => "array",
    ];

    public function races()
    {
        return $this->hasMany(Race::class);
    }

    // public function __toString()
    // {
    //     return json_encode([
    //         "startingPoint" => $this->starting_point,
    //         "startingSpeed" => $this->starting_speed,
    //         "finishLine" => $this->finish_line,
    //         "inner" => $this->inner_track,
    //         "outer" => $this->outer_track,
    //     ]);
    // }
}
