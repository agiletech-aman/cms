<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\ComplaintImage;
use App\Models\IncidentComment;
use App\Models\IncidentImage;
use App\Models\OldComment;
use App\Models\OldComplaintImage;
use App\Models\User;
use App\Notifications\CommentNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    public function StoreComment(Request $request, $id)
    {

        $users = User::role('SuperAdmin')->get();

        $this->validateCommentWithFiles($request);

        $user = User::find(Auth::id());
        $comment = Comment::create([
            'complaint_id' => $id,
            'comment' => $request->comment,
            'user_id' => $user->id,
            'created_at' => Carbon::now(),
        ]);

        foreach ($this->moveCommentFiles($request) as $path) {
            ComplaintImage::create([
                'complaint_id' => $id,
                'user_id' => $user->id,
                'complaint_image' => $path,
                'comment_id' => $comment->id,
                'created_at' => Carbon::now(),
            ]);
        }

        Notification::send($users, new CommentNotification($request->only('comment')));

        $notification = array(
            'message' => 'Comment Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function StoreOldComment(Request $request, $id)
    {

        $users = User::role('SuperAdmin')->get();

        $this->validateCommentWithFiles($request);

        $user = User::find(Auth::id());
        $comment = OldComment::create([
            'complaint_id' => $id,
            'comment' => $request->comment,
            'user_id' => $user->id,
            'created_at' => Carbon::now(),
        ]);

        foreach ($this->moveCommentFiles($request) as $path) {
            OldComplaintImage::create([
                'complaint_id' => $id,
                'user_id' => $user->id,
                'complaint_image' => $path,
                'comment_id' => $comment->id,
                'created_at' => Carbon::now(),
            ]);
        }

        Notification::send($users, new CommentNotification($request->only('comment')));

        $notification = array(
            'message' => 'Comment Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function StoreIncidentComment(Request $request, $id)
    {

        $users = User::role('SuperAdmin')->get();

        $this->validateCommentWithFiles($request);

        $user = User::find(Auth::id());
        $comment = IncidentComment::create([
            'incident_id' => $id,
            'comment' => $request->comment,
            'user_id' => $user->id,
            'created_at' => Carbon::now(),
        ]);

        foreach ($this->moveCommentFiles($request) as $path) {
            IncidentImage::create([
                'incident_id' => $id,
                'user_id' => $user->id,
                'incident_image' => $path,
                'comment_id' => $comment->id,
                'created_at' => Carbon::now(),
            ]);
        }

        Notification::send($users, new CommentNotification($request->only('comment')));

        $notification = array(
            'message' => 'Comment Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method

    private function validateCommentWithFiles(Request $request): void
    {
        $request->validate([
            'comment' => 'required',
            'comment_files' => 'nullable|array',
            'comment_files.*' => 'file|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
        ], [
            'comment_files.*.mimes' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
            'comment_files.*.max' => 'Each file must not exceed 2MB.',
        ]);
    }

    /** Moves uploaded comment files to public/upload/complaint_files and returns their relative paths. */
    private function moveCommentFiles(Request $request): array
    {
        $paths = [];
        foreach ($request->file('comment_files', []) as $file) {
            $name_gen = hexdec(uniqid()) . '.' . strtolower($file->getClientOriginalExtension());
            $file->move(public_path('upload/complaint_files'), $name_gen);
            $paths[] = 'upload/complaint_files/' . $name_gen;
        }
        return $paths;
    }
}
