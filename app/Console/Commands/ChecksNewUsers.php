<?php

namespace App\Console\Commands;

use App\DBConnectP2B;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChecksNewUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChecksNewUsers:checksUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks new users registered';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('ChecksNewUsers comando rodando com êxito');
    }
}
