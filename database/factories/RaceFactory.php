<?php

use App\Race;
use App\User;
use App\Course;
use Faker\Generator as Faker;

$factory->define(Race::class, function (Faker $faker) {
    return [
        "participant_data" => null,
        "course_id" => function () {
            return factory(Course::class)->create()->id;
        },
        "host_id" => function() {
            return factory(User::class)->create()->id;
        },
        "status" => "lobby",
    ];
});
