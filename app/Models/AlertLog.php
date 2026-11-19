<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertLog extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id', 'id');
    }

    // App\Models\AlertLog.php
    public function relatedComplaint()
    {
        switch ($this->complaint_type) {
            case 1:
                return \App\Models\Complaint::find($this->complaint_id);
            case 2:
                return \App\Models\OldComplaint::find($this->complaint_id);
            case 3:
                return \App\Models\Incident::find($this->complaint_id);
        }
        return null;
    }

    public function complaintNumber()
    {
        $c = $this->relatedComplaint();
        if (!$c) return 'Unknown';
        return $this->complaint_type == 3 ? $c->incident_id : $c->complaint_id;
    }

    public function complaintTypeName()
    {
        return [
            1 => 'New Infrastructure Complaint',
            2 => 'Old Infrastructure Complaint',
            3 => 'Incident',
        ][$this->complaint_type] ?? 'Unknown';
    }

    public function complaintStatusName()
    {
        return [
            0 => 'Open',
            1 => 'Inprogress',
            2 => 'Resolved',
            3 => 'Duplicate',
            4 => 'Closed',
        ][$this->complaint_status] ?? 'Unknown';
    }
}
