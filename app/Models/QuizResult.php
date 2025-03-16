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

   // Defines the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_id');
    }

    // Defines the relationship to the Quiz model 12022025
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

}
