<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    public $guarded = [];
    public $casts = [
        "participant_data" => "array",
    ];

    public function addParticipant($participant)
    {
        $this->participants()->attach($participant["id"]);

        tap($this->participant_data, function($participant_data) use ($participant) {
            $participant_data[] = [
                "id" => $participant["id"],
                "status" => "going",
                "trace" => [$this->course->starting_point],
            ];
            $this->update(["participant_data" => $participant_data]);
        });

        return $this;
    }

    public function addParticipants($participants)
    {
        $participants->each(function ($participant) {
            $this->addParticipant($participant);
        });

        return $this;
    }

    public function increaseSkipped()
    {
        $this->skipped++;
        $this->save();
    }

    public function resetSkipped()
    {
        $this->update(["skipped" => 0]);
    }

    public function stillInRace()
    {
        $pdata = $this->participant_data;

        return collect($pdata)
            ->filter(function ($participant) {
                return $participant["status"] == "going";
            })->values();
    }

    public function next($userId)
    {
        $stillInRace = $this->stillInRace();

        $index = $stillInRace->search(function ($participant) use ($userId) {
            return $participant["id"] == $userId;
        });

        if (++$index >= $stillInRace->count()) {
            $index = 0;
        }

        return $stillInRace[$index]["id"];
    }

    public function changeTurn($user = null)
    {
        $this->update([
            "user_turn_id" => $user ?? $this->next($this->user_turn_id)
        ]);

        return $this->userTurn;
    }

    public function start()
    {
        $this->update([
            "status" => "going",
            "user_turn_id" => $this->participants->first()["id"],
        ]);

        return $this;
    }

    public function kickUser($user)
    {
        $this->participant_data = collect($this->participant_data, true)
            ->map(function ($participant) use ($user) {
                if ($participant["id"] == $user->id) {
                    $participant["status"] = "kicked";
                }
                return $participant;
            });

        $this->update();

        return $this;
    }

    public function leaveUser($user)
    {
        $this->participant_data = collect($this->participant_data, true)
            ->map(function ($participant) use ($user) {
                if ($participant["id"] == $user->id) {
                    $participant["status"] = "left";
                }
                return $participant;
            });

        $this->update();

        return $this;
    }

    public function failUser($user, $reason)
    {
        $this->participant_data = collect($this->participant_data, true)
            ->map(function ($participant) use ($user) {
                if ($participant["id"] == $user->id) {
                    $participant["status"] = "failed";
                }
                return $participant;
            });

        $this->update();

        return $this;
    }

    public function addMove($user, $location, $speed)
    {
        $this->participant_data = collect($this->participant_data)
            ->map(function ($participant) use ($user, $location, $speed) {
                if ($participant["id"] == $user->id) {
                    $participant["trace"][] = $location;
                    $participant["speed"] = $speed;
                }
                return $participant;
            });
        $this->moves++;
        $this->update();
    }

    public function removeParticipant($participant)
    {
        $this->participants()->detach($participant->id);

        $this->participant_data = collect($this->participant_data)
            ->filter(function ($participantData) use ($participant) {
                return $participantData["id"] != $participant->id;
            })->values();

        $this->save();
    }

    public function crownWinner(User $user)
    {
        $this->update([
            "winner_id" => $user->id,
            "status" => "ended",
        ]);

        return $this;
    }

    public function isInRace(User $user)
    {
        return $this->participants->contains($user);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, "winner_id");
    }

    public function userTurn()
    {
        return $this->belongsTo(User::class, "user_turn_id");
    }

    public function host()
    {
        return $this->belongsTo(User::class);
    }
    public function participants()
    {
        return $this->belongsToMany(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getParticipantDataForUser($id)
    {
        return collect($this->participant_data)->first( function($user) use ($id) {
            return $user["id"] == $id;
        });
    }

    public function participantsAsJson()
    {
        return $this->participants->map(function ($participant) {
            $data = $this->getParticipantDataForUser($participant->id);

            return [
                "id" => $participant->id,
                "username" => $participant->username,
                "carColor" => $participant->car_color,
                "traceColor" => $participant->trace_color,
                "status" => $data["status"],
                "trace" => $data["trace"],
                "speed" => $data["speed"] ?? null,
            ];
        });
    }
}
