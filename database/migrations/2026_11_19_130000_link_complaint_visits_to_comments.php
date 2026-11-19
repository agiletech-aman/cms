<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfilled visits have no comment_id, so their remark/attachments were not linked.
 * Link each one to the latest "This complaint is Inprogress" comment of its complaint.
 */
return new class extends Migration
{
    public function up(): void
    {
        $visits = DB::table('complaint_visits')->whereNull('comment_id')->get();

        foreach ($visits as $visit) {
            $comment = DB::table('comments')
                ->where('complaint_id', $visit->complaint_id)
                ->where('comment', 'This complaint is Inprogress')
                ->orderByDesc('id')
                ->first();

            if ($comment) {
                DB::table('complaint_visits')->where('id', $visit->id)->update([
                    'comment_id' => $comment->id,
                    'remark'     => $visit->remark ?? $comment->remark,
                    'user_id'    => $visit->user_id ?? $comment->user_id,
                ]);
            }
        }
    }

    public function down(): void
    {
        // data-only fix; nothing to undo
    }
};
