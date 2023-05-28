<?php

namespace App\Services\Telegram\Commands;

use App\Services\Telegram\Handlers\Commands\StartCommandHandler;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Start Command to get you started';

    public function handle()
    {
        return app(StartCommandHandler::class)->handle($this);
    }
}
