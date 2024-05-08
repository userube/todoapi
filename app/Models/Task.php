<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['description', 'due_date', 'status', 'todo_list_id'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
