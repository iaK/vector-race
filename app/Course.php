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

    public function __toString()
    {
        return json_encode([
            "id" => $this->id,
            "starting_point" => $this->starting_point,
            "starting_speed" => $this->starting_speed,
            "finish_line" => $this->finish_line,
            "inner_track" => $this->inner_track,
            "outer_track" => $this->outer_track,
        ]);
    }
}
