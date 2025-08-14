<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable =['title','description','priority','status','due_date','project_id','assigned_to','created_by'];

    protected $hidden=['created_at', 'updated_at'];

    public function project()
    {
      return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
      return $this->belongsTo(User::class,
                              'created_by',
                              'id'
                                      );
    }
    public function assignedTo()
    {
      return $this->belongsTo(User::class,
                              'assigned_to',
                              'id'
                                      );
    }
}
