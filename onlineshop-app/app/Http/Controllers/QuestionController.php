<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Http\Resources\QuestionResource;
use App\Http\Requests\QuestionRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Answer;
use App\Http\Resources\AnswerResource;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        return QuestionResource::collection(Question::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuestionRequest $request): Response|QuestionResource
    {
        $request->validated();
        $create_question = Question::create([
            'user_id' => auth()->user()->id,
            'question' => $request->question,
            'product_id' => $request->product_id,
        ]);
        return new QuestionResource($create_question);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question): Response|QuestionResource
    {
        if (auth()->user()->id == $question->user_id || auth()->user()->isAdmin()) {
            return new QuestionResource(Question::findOrFail($question->id));
        }
        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question): Response|QuestionResource
    {
        if (auth()->user()->id == $question->user_id) {
            $request->validate([
                'question' => 'required|string',
            ]);
            $question->update([
                'question' => $request->question,
            ]);
            return new QuestionResource($question);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question): Response
    {
        if (auth()->user()->id == $question->user_id || auth()->user()->isAdmin()) {
            $question->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     *
     * @param int $product_id
     * @return \Illuminate\Http\Response or array
     */
    public function questionOfProduct(int $product_id): Response|array
    {
        $questions = DB::table('questions')->where('product_id', $product_id)->pluck('question');
        $allQuestions = [];
        $i = 0;
        foreach ($questions as $question) {
            $allQuestions[$i] = $question;
            $i++;
        }
        return $allQuestions;
    }

    public function answerForQuestion(Request $request, int $id): AnswerResource
    {
        $request->validate([
            'answer' => 'required|string',
        ]);

        $create_answer = Answer::create([
            'user_id' => auth()->user()->id,
            'answer' => $request->answer,
            'question_id' => $id,
        ]);

        return new AnswerResource($create_answer);
    }

    public function answerUpdate(Request $request, int $id){
        $answer = Answer::find($id);
        if (auth()->user()->id == $answer->user_id) {
            $request->validate([
                'answer' => 'required|string',
            ]);
            $answer->update([
                'answer' => $request->answer,
            ]);
            return new AnswerResource($answer);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }


    public function answerDelete(int $id){
        $answer = Answer::find($id);
        if (auth()->user()->id == $answer->user_id || auth()->user()->isAdmin()) {
            $answer->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }

}
