<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldComplaint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }
    public function region()
    {
        return $this->user->belongsTo(Region::class, 'region_id', 'id');
    }

    public function raisedBy()
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    public function statusUpdates()
    {
        return $this->hasMany(OldComment::class, 'complaint_id', 'id')
            ->orderBy('created_at');
    }

    public function uploadedFiles()
    {
        return $this->hasMany(OldComplaintImage::class, 'complaint_id', 'id')
            ->orderBy('created_at');
    }

}
