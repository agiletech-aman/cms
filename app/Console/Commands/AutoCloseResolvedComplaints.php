<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Complaint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCloseResolvedComplaints extends Command
{
    protected $signature = 'complaints:auto-close';

    protected $description = 'Close complaints that have been in Resolved status for more than the configured hours (default 72)';

    public function handle()
    {
        $hours = config('complaint.auto_close_hours', 72);

        $complaints = Complaint::where('complaint_status', 2)
            ->whereNotNull('resolved_date')
            ->where('resolved_date', '<=', Carbon::now()->subHours($hours))
            ->get();

        // Comments need a user; use the first SuperAdmin as the "system" user
        $systemUser = User::role('SuperAdmin')->orderBy('id')->first();

        foreach ($complaints as $complaint) {
            DB::transaction(function () use ($complaint, $systemUser, $hours) {
                $now = Carbon::now();
                $userId = $systemUser->id ?? $complaint->status_change_by;

                $complaint->update([
                    'complaint_status' => 4,
                    'closed_date'      => $now,
                    'status_change_by' => $userId,
                ]);

                Comment::create([
                    'complaint_id' => $complaint->id,
                    'comment'      => 'This complaint is Closed',
                    'remark'       => "Auto closed after {$hours} hours of resolution",
                    'user_id'      => $userId,
                ]);

                DB::table('alert_logs')->insert([
                    'complaint_id'     => $complaint->id,
                    'complaint_type'   => 1,
                    'user_id'          => $userId,
                    'complaint_status' => 4,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            });

            $this->info("Auto closed complaint: {$complaint->complaint_id}");
        }

        $this->info("Total auto closed: {$complaints->count()}");

        return 0;
    }
}
