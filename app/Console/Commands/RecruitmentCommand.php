<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecruitmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recruitment:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Recruitment';

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
        DB::table('recruitments')->where('expiry_date', '<', Carbon::now())->update(['is_closed' => true]);
        $this->info('recruitment:update Cummand Run successfully!');
        // return 0;
    }
}