<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserService;

class UserStatusCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive user statuses everyday';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new UserService;
        $service->archive();
    }
}
