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
        $quizResults = QuizResult::where('user_id', $userId)->get();

        return response()->json([
            'status' => 200,
            'results' => $quizResults
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


