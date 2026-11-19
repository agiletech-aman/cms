<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;
    protected $guarded = [];

    public const ROOT_CAUSES = [
        'Power issues',
        'Static IP issues',
        'Low bandwidth issues',
        'LED issues',
        'Short Circuit/Burnt issues',
        'Thunder storms',
        'Water logging',
        'Monkey Menace',
        'Repeated Complaint',
        'Complaint belongs to Old Infra',
        'Inconsistent plastering',
        'Connectivity issue',
        'Power fluctuation',
        'Lightening issue',
        'Construction/Renovation/Shifting',
        'Earthing/Surge issue',
        'Excess Load',
        'Heavy Rains',
        'Power Unavailability',
        'Physical damage',
        'Others',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }


    public function item(){
        return $this->belongsTo(items::class,'item_id','id');
    }
    
    public function category(){
        return $this->belongsTo(Category::class,'cat_id','id');
    }

    public function region()
    {
        return $this->user->belongsTo(Region::class,'region_id','id');
    }

    public function raisedBy()
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    public function statusUpdates()
    {
        return $this->hasMany(Comment::class, 'complaint_id', 'id')
            ->orderBy('created_at');
    }

    public function engineer()
    {
        return $this->belongsTo(Engineer::class, 'engineer_id', 'id');
    }

    public function visits()
    {
        return $this->hasMany(ComplaintVisit::class, 'complaint_id', 'id')
            ->orderBy('visit_date');
    }

    public function uploadedFiles()
    {
        return $this->hasMany(ComplaintImage::class, 'complaint_id', 'id')
            ->orderBy('created_at');
    }
}
