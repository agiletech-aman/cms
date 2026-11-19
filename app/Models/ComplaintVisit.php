<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintVisit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
