<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use App\Models\QuizResult; //not created
use Illuminate\Support\Facades\Auth;

class QuizResultController extends Controller
{
public function getUserResults(Request $request)
{
    $userId = $request->user()->id;
    $quizResults = QuizResult::with('quiz') // Eager load the quiz relationship
        ->where('user_id', $userId)
        ->get();

    // Map the results to include the quiz title
    $results = $quizResults->map(function ($result) {
        return [
            'id' => $result->id,
            'quiz_id' => $result->quiz_id,
            'quiz_title' => $result->quiz ? $result->quiz->title : 'Deleted Quiz', // Handle null quiz
            'score' => $result->score,
            'correct_answers' => $result->correct_answers,
            'wrong_answers' => $result->wrong_answers,
            'correct_answers_percentage' => $result->correct_answers_percentage,
        ];
    });

    return response()->json([
        'status' => 200,
        'results' => $results,
    ]);
}

public function getAllResults(Request $request)
{
    $quizResults = QuizResult::with(['quiz', 'user']) // Eager load quiz and user relationships
        ->get();

    // Map the results to include user details and quiz title
    $results = $quizResults->map(function ($result) {
        return [
            'id' => $result->id,
            'user_id' => $result->user_id,
            'user_name' => $result->user ? $result->user->name : 'Unknown User', // Handle null user
            'user_email' => $result->user ? $result->user->email : 'Unknown Email', // Handle null user
            'quiz_id' => $result->quiz_id,
            'quiz_title' => $result->quiz ? $result->quiz->title : 'Unknown Quiz', // Handle null quiz
            'score' => $result->score,
            'correct_answers' => $result->correct_answers,
            'wrong_answers' => $result->wrong_answers,
            'correct_answers_percentage' => $result->correct_answers_percentage,
        ];
    });

    return response()->json([
        'status' => 200,
        'results' => $results,
    ]);
}
    public function startQuiz(Request $request)
    {
        // Create a new quiz attempt
        $quizAttempt = new QuizAttempt();
        $quizAttempt->user_id = Auth::id();
        $quizAttempt->save();

        return response()->json([
            'quiz_id' => $quizAttempt->id,
        ], 200);
    }

   

    public function storeQuizResults(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quiz_attempts,id',
            'score' => 'required|integer',
            'correctAnswers' => 'required|integer',
            'wrongAnswers' => 'required|integer',
            'correctAnswersPercentage' => 'required|numeric',
        ]);

        $quizResult = new QuizResult(); 
        $quizResult->user_id = Auth::id();
        $quizResult->quiz_id = $request->quiz_id;
        $quizResult->score = $request->score;
        $quizResult->correct_answers = $request->correctAnswers;
        $quizResult->wrong_answers = $request->wrongAnswers;
        $quizResult->correct_answers_percentage = $request->correctAnswersPercentage;
        $quizResult->save();

        return response()->json([
            'status' => 200,
            'message' => 'Quiz results saved successfully',
        ], 200);
    }
}


