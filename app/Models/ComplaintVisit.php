<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintVisit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    public function engineer()
    {
        return $this->belongsTo(Engineer::class, 'engineer_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
