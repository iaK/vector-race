<?php

namespace App\Events;

use App\Race;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CarMoved implements ShouldBroadcastNow
{
    public $race;
    public $location;
    public $id;
    public $speed;

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Race $race, $location, $id, $speed)
    {
        $this->race = $race;
        $this->location = $location;
        $this->id = $id;
        $this->speed = $speed;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('race.' . $this->race->id);
    }
}
