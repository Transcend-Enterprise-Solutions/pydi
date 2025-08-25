<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ProcessEmailQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'emails:process 
                            {--queue=default : The queue to process}
                            {--timeout=60 : Maximum execution time}
                            {--max-jobs=10 : Maximum number of jobs to process}';

    /**
     * The console command description.
     */
    protected $description = 'Process queued email jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queue = $this->option('queue');
        $timeout = $this->option('timeout');
        $maxJobs = $this->option('max-jobs');

        $this->info("Processing email queue: {$queue}");
        $this->info("Timeout: {$timeout} seconds");
        $this->info("Max jobs: {$maxJobs}");

        // Check if there are any jobs in the queue
        $jobCount = DB::table('jobs')->where('queue', $queue)->count();
        
        if ($jobCount === 0) {
            $this->info('No jobs found in the queue.');
            return 0;
        }

        $this->info("Found {$jobCount} job(s) in the queue.");

        try {
            // Process the queue
            $exitCode = Artisan::call('queue:work', [
                '--queue' => $queue,
                '--stop-when-empty' => true,
                '--max-time' => $timeout,
                '--max-jobs' => $maxJobs,
                '--tries' => 3,
            ]);

            if ($exitCode === 0) {
                $this->info('Email queue processed successfully.');
            } else {
                $this->error('Queue processing completed with errors.');
            }

            return $exitCode;
        } catch (\Exception $e) {
            $this->error('Failed to process email queue: ' . $e->getMessage());
            return 1;
        }
    }
}