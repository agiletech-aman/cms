<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintNotification extends Notification
{
    use Queueable;

    protected $complaint;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($complaint, $type = 'new')
    {
        $this->complaint = $complaint;
        $this->type = $type; // 'new' for new complaint, 'update' for status update, 'unresolved' for 48+ hours unresolved
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage();

        if ($this->type === 'new') {
            // For new complaint notification
            $mail->subject('New Complaint Registered')
                ->line('New Complaint Added: ' . $this->complaint->subject)
                ->line('Complaint ID: ' . $this->complaint->complaint_id)
                ->line('Description: ' . $this->complaint->description)
                ->action('View Complaint', url('/'))
                ->line('Thank you for using our application!');
        } elseif ($this->type === 'update') {
            // For complaint status update notification
            $mail->subject('Complaint Status Updated')
                ->line('The status of your complaint has been updated.')
                ->line('Complaint ID: ' . $this->complaint->complaint_id)
                ->line('New Status: ' . $this->getStatusText($this->complaint->complaint_status))
                ->action('View Complaint', url('/'))
                ->line('Thank you for using our application!');
        } elseif ($this->type === 'unresolved') {
            // For complaints unresolved after 48 hours
            $mail->subject('URGENT: Complaint Pending Resolution')
                ->line('The following complaint has been unresolved for more than 48 hours:')
                ->line('Complaint ID: ' . $this->complaint->complaint_id)
                ->line('Subject: ' . $this->complaint->subject)
                ->line('Status: ' . $this->getStatusText($this->complaint->complaint_status))
                ->line('Date Reported: ' . $this->complaint->created_at->format('Y-m-d H:i'))
                ->line('Time Elapsed: ' . $this->complaint->created_at->diffForHumans())
                ->action('View Complaint Details', url('/'))
                ->line('This requires immediate attention. Please update the status or escalate as needed.');
        }

        return $mail;
    }

    /**
     * Get complaint status text.
     */
    private function getStatusText($status)
    {
        $statusMessages = [
            0 => 'Open',
            1 => 'InProgress',
            2 => 'Resolved',
            3 => 'Duplicate',
            4 => 'Closed'
        ];
        return $statusMessages[$status] ?? 'Unknown';
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'status' => $this->complaint->complaint_status
        ];
    }
}