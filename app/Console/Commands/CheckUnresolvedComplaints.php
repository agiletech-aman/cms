<?php

namespace App\Console\Commands;

use App\Models\Complaint;
use App\Models\User;
use App\Notifications\ComplaintNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckUnresolvedComplaints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'complaints:check-unresolved';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for complaints that have been unresolved for more than 48 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find all open complaints older than 48 hours
        $unresolvedComplaints = Complaint::where('complaint_status', 0)
            ->where('created_at', '<', Carbon::now()->subHours(48))
            ->get();

        $this->info("Found {$unresolvedComplaints->count()} unresolved complaints older than 48 hours.");

        foreach ($unresolvedComplaints as $complaint) {
            // Get the user who created the complaint
            $complaintUser = User::find($complaint->user_id);
            
            // Get admin users
            $adminUsers = User::role('Railtel')->get();

            // Send notification to the complaint user
            if ($complaintUser) {
                Notification::send($complaintUser, new ComplaintNotification($complaint, 'unresolved'));
                $this->info("Notification sent to user: {$complaintUser->name} for complaint: {$complaint->complaint_id}");
            }

            // Send notification to all admin users
            Notification::send($adminUsers, new ComplaintNotification($complaint, 'unresolved'));
            $this->info("Notification sent to admin users for complaint: {$complaint->complaint_id}");
        }

        // Optional: Log the last run time
        // \Illuminate\Support\Facades\DB::table('cron_jobs_log')->updateOrInsert(
        //     ['job_name' => 'check_unresolved_complaints'],
        //     ['last_run' => Carbon::now(), 'updated_at' => Carbon::now()]
        // );

        return 0;
    }
}