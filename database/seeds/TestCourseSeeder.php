<?php

use Illuminate\Database\Seeder;

class TestCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("courses")->insert([
            "starting_point" => '{"x": 49, "y":80}',
            "finish_line" => '{
                "start": {
                     "x": 50,
                     "y": 60
                },
                "end": {
                    "x": 50,
                    "y": 100
                }
            }',
            "starting_speed" => '{
                "top": 0,
                "left": -1
            }',
            "inner_track" => '[{"x":40,"y":40},{"x":60,"y":40},{"x":60,"y":60},{"x":40,"y":60}]',
            "outer_track" => '[{"x":0,"y":0},{"x":100,"y":0},{"x":100,"y":100},{"x":0,"y":100}]'
        ]);
    }
}
