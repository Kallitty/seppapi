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
            'duration' => 'sometimes|integer|min:1',
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

        if (isset($questionData['icon'])) {
    if ($questionData['icon'] instanceof \Illuminate\Http\UploadedFile) {
        // If it's a new file, store it
        $path = $questionData['icon']->store('public/icons');
        $question->icon = str_replace('public/', 'storage/', $path);
    } else {
        // If it's a string (existing image path), keep it
        $question->icon = $questionData['icon'];
    }
}


            
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


     public function view($id)
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
        'duration' => 'nullable|integer',
        'questions' => 'sometimes|array',
        'questions.*.id' => 'sometimes|integer|exists:questions,id',
        'questions.*.question' => 'required|string',
        'questions.*.type' => 'required|string',
        'questions.*.correctAnswer' => 'required|string',
        'questions.*.choices' => 'required|array',
        'questions.*.choices.*' => 'required|string',
        'questions.*.icon' => [
            'nullable',
            function ($attribute, $value, $fail) {
                if (!($value instanceof \Illuminate\Http\UploadedFile || is_string($value))) {
                    $fail('The ' . $attribute . ' field must be a file or an existing image path.');
                }
            },
            function ($attribute, $value, $fail) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $allowedMimeTypes = ['image/jpeg', 'image/png'];
                    $mimeType = $value->getMimeType();
                    if (!in_array($mimeType, $allowedMimeTypes)) {
                        $fail('The ' . $attribute . ' field must be a file of type: jpg, jpeg, png.');
                    }
                }
            },
        ],
    ]);

    try {
        $quiz = Quiz::findOrFail($id);
        Log::info('Quiz found', ['quiz_id' => $quiz->id]);

        $quiz->title = $request->title;
        $quiz->duration = $request->duration;
        $quiz->save();

        if ($request->has('questions')) {
            $existingQuestionIds = $quiz->questions->pluck('id')->toArray();
            $updatedQuestionIds = [];

            foreach ($request->questions as $questionData) {
                $question = isset($questionData['id']) ? Question::find($questionData['id']) : new Question();

                $question->question = $questionData['question'];
                $question->type = $questionData['type'];
                $question->correct_answer = $questionData['correctAnswer'];
                $question->quiz_id = $quiz->id;

                // Handle the icon field
                if (isset($questionData['icon'])) {
                    if ($questionData['icon'] instanceof \Illuminate\Http\UploadedFile) {
                        // Store the new image and save only the filename
                        $path = $questionData['icon']->store('public/icons');
                        $question->icon = basename($path); // Save only the filename
                    } elseif (is_string($questionData['icon'])) {
                        // Retain the existing filename (not the full URL)
                        $question->icon = basename($questionData['icon']);
                    }
                }

                $question->save();
                Log::info('Question saved', ['question_id' => $question->id]);
                $updatedQuestionIds[] = $question->id;

                // Update choices
                $question->choices()->delete(); // Remove existing choices
                foreach ($questionData['choices'] as $choiceValue) {
                    if (!empty($choiceValue)) {
                        $choice = new Choice();
                        $choice->choice = $choiceValue;
                        $choice->question_id = $question->id;
                        $choice->save();
                    }
                }
            }

            // Delete questions that are no longer present
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
