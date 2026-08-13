<?php

namespace App\Console\Commands;

use App\Models\StudentProfile;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetInactiveStreaks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streaks:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'reset the streaks for inactive users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $affected = StudentProfile::where('streak', '>', 0)
            ->where(function ($q) use ($yesterday) {
                $q->whereNull('last_activate_date')
                    ->orWhere('last_activate_date', '<', $yesterday);
            })
            ->update(['streak' => 0]);

        $this->info(" reset streak for {$affected} students. ");
    }
}
