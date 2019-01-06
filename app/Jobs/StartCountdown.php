<?php

namespace App\Jobs;

use App\Race;
use App\Events\TurnChanged;
use App\Events\GameClosed;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StartCountdown implements ShouldQueue
{
    protected $race;
    protected $moves;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Race $race, $moves)
    {
        $this->race = $race;
        $this->moves = $moves;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(config("app.sleep"));

        $this->race = $this->race->fresh();

        if ($this->race->skipped > 10) {
            return broadcast(new GameClosed($this->race));
        }

        if ($this->moves == $this->race->moves && $this->race->status == "going") {
            $this->race->increaseSkipped();
            $nextUser = $this->race->changeTurn();
            broadcast(new TurnChanged($this->race, $nextUser->id));

            if (config("app.env") != "testing") {
                dispatch(new StartCountdown($this->race, $this->moves));
            }

            return;
        }

        $this->resetSkipped();
    }
}
