<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueueStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'queue:status {--queue=default : The queue to check}';

    /**
     * The console command description.
     */
    protected $description = 'Display the status of queue jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queueName = $this->option('queue');

        // Get queue statistics
        $pendingJobs = DB::table('jobs')
            ->where('queue', $queueName)
            ->where('available_at', '<=', now()->timestamp)
            ->count();

        $delayedJobs = DB::table('jobs')
            ->where('queue', $queueName)
            ->where('available_at', '>', now()->timestamp)
            ->count();

        $failedJobs = DB::table('failed_jobs')->count();

        // Display statistics
        $this->info("Queue Status for: {$queueName}");
        $this->line('================================');
        $this->line("Pending Jobs: {$pendingJobs}");
        $this->line("Delayed Jobs: {$delayedJobs}");
        $this->line("Failed Jobs: {$failedJobs}");

        // Show recent jobs
        if ($pendingJobs > 0 || $delayedJobs > 0) {
            $this->line('');
            $this->info('Recent Jobs:');
            
            $jobs = DB::table('jobs')
                ->where('queue', $queueName)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $headers = ['ID', 'Attempts', 'Available At', 'Created At'];
            $rows = [];

            foreach ($jobs as $job) {
                $availableAt = date('Y-m-d H:i:s', $job->available_at);
                $createdAt = date('Y-m-d H:i:s', $job->created_at);
                
                $rows[] = [
                    $job->id,
                    $job->attempts,
                    $availableAt,
                    $createdAt
                ];
            }

            $this->table($headers, $rows);
        }

        // Show failed jobs if any
        if ($failedJobs > 0) {
            $this->line('');
            $this->error("There are {$failedJobs} failed jobs. Use 'php artisan queue:failed' to view them.");
        }

        return 0;
    }
}