<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'title', 'duration', 'is_visible', 'premium', 'status'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function results()
    {
        return $this->hasMany(QuizResult::class);
    }
}







