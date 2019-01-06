<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            "username" => "iak",
            "trace_color" => "green",
            "car_color" => "black",
            "password" => bcrypt("gallo23a"),
            "email" => "info@isakberglind.se",
        ]);
        DB::table("users")->insert([
            "username" => "enemy",
            "trace_color" => "red",
            "car_color" => "white",
            "password" => bcrypt("gallo23a"),
            "email" => "info2@isakberglind.se",
        ]);
    }
}
