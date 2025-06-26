<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    protected $fillable = [
        'title',
        'content',
        'color',
        'pinned',
        'user_id',
    ];
    protected $casts = [
        'pinned' => 'boolean',
        'color' => 'string',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
