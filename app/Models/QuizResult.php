<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'quiz_id', 'score', 'correct_answers', 'wrong_answers', 'correct_answers_percentage'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_id');
    }
}
