<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|string',
            'questions.*.correctAnswer' => 'required|string',
            'questions.*.choices' => 'required|array',
            'questions.*.choices.*' => 'required|string',
            'questions.*.icon' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'duration' => $request->duration,
        ]);

        foreach ($request->questions as $questionData) {
            $question = new Question();
            $question->question = $questionData['question'];
            $question->type = $questionData['type'];
            $question->correct_answer = $questionData['correctAnswer'];
            $question->quiz_id = $quiz->id;

            // if (isset($questionData['icon']) && $questionData['icon']->isValid()) {
            //     $path = $questionData['icon']->store('public/icons');
            //     $iconName = basename($path);
            //     $frontendPath = public_path('../../sepp/seppfrontend/public/icons');
            //     $questionData['icon']->move($frontendPath, $iconName);
            //     $question->icon = $iconName;
            // }
                if (isset($questionData['icon']) && $questionData['icon']->isValid()) {
                $path = $questionData['icon']->store('public/icons');
                $iconName = basename($path);
                
                // Define the correct path relative to the Laravel project
                $frontendPath = public_path('../../seppfrontend/public/icons');
                
                // Ensure the directory exists
                if (!file_exists($frontendPath)) {
                    mkdir($frontendPath, 0777, true);
                }
                
                // Move the uploaded file to the correct directory
                $questionData['icon']->move($frontendPath, $iconName);
                $question->icon = $iconName;
            }
            $question->save();

            foreach ($questionData['choices'] as $choiceData) {
                $choice = new Choice();
                $choice->choice = $choiceData;
                $choice->question_id = $question->id;
                $choice->save();
            }
        }

        return response()->json(['message' => 'Quiz created successfully'], 201);
    }

    public function index()
    {
        $quizzes = Quiz::all();
        return response()->json($quizzes);
    }

    public function show($id)
    {
        $quiz = Quiz::with('questions.choices')->find($id);
        return response()->json([
            'title' => $quiz->title,
            'duration' => $quiz->duration,
            'questions' => $quiz->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'type' => 'MCQs',
                    'correctAnswer' => $question->correct_answer ? trim($question->correct_answer) : '',
                    'isVisible' => $question->is_visible,
                    'icon' => $question->icon ? asset('storage/icons/' . $question->icon) : null,
                    'question' => $question->question,
                    'choices' => $question->choices->pluck('choice')->map(function ($choice) {
                        return trim($choice);
                    })->toArray(),
                ];
            })
        ]);
    }

   public function update(Request $request, $id)
{
    Log::info('Received update request', ['data' => $request->all()]);

    $request->validate([
        'title' => 'sometimes|string|max:255',
        'duration' => 'sometimes|integer',
        'questions' => 'sometimes|array',
        'questions.*.id' => 'sometimes|integer|exists:questions,id',
        'questions.*.question' => 'sometimes|string',
        'questions.*.type' => 'sometimes|string',
        'questions.*.correctAnswer' => 'sometimes|string',
        'questions.*.choices' => 'sometimes|array',
        'questions.*.choices.*' => 'sometimes|string',
        'questions.*.icon' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    ]);

    try {
        $quiz = Quiz::findOrFail($id);
        Log::info('Quiz found', ['quiz_id' => $quiz->id]);

        if ($request->has('title')) {
            $quiz->title = $request->title;
            $quiz->save();
            Log::info('Quiz title updated', ['title' => $quiz->title]);
        }
        if ($request->has('duration')) {
            $quiz->duration = $request->duration;
            $quiz->save();
            Log::info('Quiz duration updated', ['duration' => $quiz->duration]);
        }

        if ($request->has('questions')) {
            $existingQuestionIds = $quiz->questions->pluck('id')->toArray();
            $updatedQuestionIds = [];
            

           foreach ($request->questions as $questionData) {
    $question = isset($questionData['id']) ? Question::find($questionData['id']) : new Question();

    if (!isset($questionData['question']) || empty(trim($questionData['question']))) {
        Log::warning('Missing or empty question text', ['data' => $questionData]);
        continue; // Skip this question if question text is missing or empty
    }

    $question->question = $questionData['question'];
    $question->type = $questionData['type'] ?? $question->type;
    $question->correct_answer = $questionData['correctAnswer'] ?? $question->correct_answer;
    $question->quiz_id = $quiz->id;

    if (isset($questionData['icon']) && is_file($questionData['icon']) && $questionData['icon']->isValid()) {
        $path = $questionData['icon']->store('public/icons');
        $iconName = basename($path);
        $frontendPath = public_path('../../sepp/seppfrontend/public/icons');
        $questionData['icon']->move($frontendPath, $iconName);
        $question->icon = $iconName;
        Log::info('Icon uploaded', ['icon' => $iconName]);
    }

    $question->save();
    Log::info('Question saved', ['question_id' => $question->id]);
    $updatedQuestionIds[] = $question->id;

    // Handle choices similarly




                if (isset($questionData['choices'])) {
                    $existingChoiceIds = $question->choices->pluck('id')->toArray();
                    $updatedChoiceIds = [];

                    foreach ($questionData['choices'] as $choiceValue) {
                        $choice = new Choice();

                        if (!empty($choiceValue)) {
                            $choice->choice = $choiceValue;
                            Log::info('Choice data', ['choice' => $choiceValue]);
                        } else {
                            Log::warning('Missing choice data', ['question_id' => $question->id, 'choiceData' => $choiceValue]);
                            continue; // Skip if choice is not set
                        }

                        $choice->question_id = $question->id;
                        $choice->save();
                        Log::info('Choice saved', ['choice_id' => $choice->id]);
                        $updatedChoiceIds[] = $choice->id;
                    }

                    $choicesToDelete = array_diff($existingChoiceIds, $updatedChoiceIds);
                    Choice::destroy($choicesToDelete);
                }
            }

            $questionsToDelete = array_diff($existingQuestionIds, $updatedQuestionIds);
            Question::destroy($questionsToDelete);
        }

        return response()->json(['message' => 'Quiz updated successfully']);
    } catch (\Exception $e) {
        Log::error('Failed to update quiz', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to update quiz', 'error' => $e->getMessage()], 500);
    }
}



    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->questions()->delete();
        $quiz->delete();

        return response()->json(['message' => 'Quiz deleted successfully']);
    }
}
