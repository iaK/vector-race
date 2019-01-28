<?php

namespace Tests\Helpers;

use App\Race;
use App\User;
use App\Course;

trait HandlesRaces
{
    protected $users = [];
    protected $race = null;

    public function createRaceAndAttachUsers($amountOfUsers = 1, $args = [])
    {
        for ($i = 0; $i < $amountOfUsers; $i++) {
            $this->users[] = create(User::class);
        }

        $this->race = create(Race::class, array_merge([
            "user_turn_id" => $this->users[0]->id,
            "host_id" => $this->users[0]->id,
            "course_id" => function() {
                return factory(Course::class)->state('test-course')->create()->id;
            }
        ], $args));

        $this->race->addParticipants($this->users);
    }

    public function createRaceWithSatusAndAttachUsers($status, $amountOfUsers = 1)
    {
        $this->createRaceAndAttachUsers($amountOfUsers, ["status" => $status]);
    }
}
