<?php
namespace App\Http\Controllers\Admin\Tests;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestQuestionAnswer;
use Illuminate\Support\Facades\Storage;
use niklasravnsborg\LaravelPdf\PdfWrapper;

class TestsController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:courses,read')->only('index', 'show');
        $this->middleware('admin.role.auth:courses,write')->only('create', 'store', 'edit', 'update', 'destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Course $course)
    {
        $tests = $course->getOrderedTests()->simplePaginate(20);

        return view('admin.tests.index', compact('tests', 'course'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Course $course)
    {
        $test = new Test(['course_id' => $course->id]);

        $lessons = $course->lessons()->alphabetical()->get();

        $action = route('tests.store', ['course' => $course->id]);
        $method = '';

        return view('admin.tests.create-edit', compact('test', 'lessons', 'course', 'action', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only('title', 'after_lesson_id', 'certificate_id', 'status');

        if ($request->certificate_id === '') {
            $data['certificate_id'] = null;
        }

        $test = Test::create(
            $data +
            [
                'course_id' => $request->course,
                'custom_completion_status' => $request->get('custom_completion_status', 0),
                'custom_completion_style' => $request->get('custom_completion_style', null),
                'custom_completion_body' => $request->get('custom_completion_body', null)
            ]
        );

        $where = 'test/custom-completions' . $test->id;

        if (!empty($request->file())) {
            foreach ($request->file() as $name => $image) {
                $image = $image->store($where, 'static');
                $test->$name = $image;
            }
        }

        $test->save();

        return redirect()->route('tests.index', ['course' => $request->course])
                ->with('alert-success', 'Test succesfully added');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course, Test $test)
    {
        $action = route('tests.update', ['course' => $course->id, 'test' => $test->id]);
        $method = method_field('PUT');

        $lessons = $course->lessons()->alphabetical()->get();

        return view('admin.tests.create-edit', compact('course', 'test', 'action', 'method', 'lessons'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->only('title', 'after_lesson_id', 'certificate_id');

        if ($request->certificate_id === '') {
            $data['certificate_id'] = null;
        }

        $test = Test::find($request->test);
        $test->fill(
            $data +
            [
                'id' => $test->id,
                'course_id' => $request->course,
                'status' => $request->get('status', 0),
                'custom_completion_status' => $request->get('custom_completion_status', 0),
                'custom_completion_style' => $request->get('custom_completion_style', null),
                'custom_completion_body' => $request->get('custom_completion_body', null)
            ]
        );
        
        $where = 'test/custom-completions' . $test->id;

        if (!empty($request->file())) {
            foreach ($request->file() as $name => $image) {
                $image = $image->store($where, 'static');
                $test->$name = $image;
            }
        }

        $test->save();

        return redirect()->route('tests.index', ['course' => $request->course])
                ->with('alert-success', 'Test succesfully modified');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $test
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $test, Request $request)
    {
        $test = Test::find($test);

        foreach ($test->questions as $question) {
            $this->deleteQuestion($question->id);
        }

        $test->delete();

        request()->session()->flash('alert-success', 'Test was successfully deleted!');
        return redirect()->back();
    }

    public function show(Course $course, Test $test)
    {
        return view('admin.tests.show', compact('course', 'test'));
    }

    public function downloadPdf(Course $course, Test $test)
    {
        $pdf = app(PdfWrapper::class)->loadView('admin.tests.pdf', compact('test', 'course'));

        return $pdf->stream("test-results.pdf");
    }

    public function storeQuestion(Request $request, int $test)
    {
        $answers = $request->input('answers');

        if (!$request->filled('question_id')) {
            $question = new TestQuestion();

            $qextra = [
                'status' => 1,
                'test_id' => $test,
                'order' => 0,
            ];
        } else {
            $question = TestQuestion::find($request->input('question_id'));
            //delete missing answers
            $current_answers = $question->answers;
            $existing_ids = array_column($current_answers->toArray(), 'id');
            $remaining_ids = array_column($answers, 'id');

            $deletes = array_diff($existing_ids, $remaining_ids);
            if (count($deletes)) {
                TestQuestionAnswer::destroy($deletes);
            }

            $qextra = ['id' => $request->input('question_id')];
        }

        $question->fill(
            $request->only('title', 'question_type') + $qextra
        );

        $question->save();
        foreach ($answers as $answer) {
            $this->createAnswer($answer, $question->id);
        }

        request()->session()->flash('alert-success', 'Question successfully saved.');
        return redirect()->back();
    }

    public function editQuestion(Request $request, $course, $questionId)
    {
        $question = TestQuestion::find($questionId);

        $answers = $question->load('answers');

        return $question;
    }

    public function removeQuestion(Request $request, int $testId, int $questionId)
    {
        $this->deleteQuestion($questionId);

        if ($request->ajax()) {
            return ['success' => true];
        }

        request()->session()->flash('alert-success', 'Question was successfully deleted!');
        return redirect()->back();
    }

    protected function createAnswer(array $answer_data, int $questionId)
    {

        if (!isset($answer_data['is_answer'])) {
            $answer_data['is_answer'] = 0;
        }

        if (!empty($answer_data['id'])) {
            $answer = TestQuestionAnswer::find($answer_data['id']);
            $answer->id = $answer_data['id'];

            $answer->fill($answer_data);
            $answer->save();
        } else {
            $answer = new TestQuestionAnswer();
            $extra = ['status' => 1, 'order' => 0, 'question_id' => $questionId];
            $answer->fill($answer_data + $extra);
            $answer->save();
        }

        return $answer;
    }

    protected function deleteAnswer(int $answerId)
    {
        TestQuestionAnswer::find($answerId)->delete();
    }

    protected function deleteQuestion(int $questionId)
    {
        $question = TestQuestion::find($questionId);
        $question->load('answers');
        $answers = $question->answers;

        foreach ($answers as $answer) {
            $this->deleteAnswer($answer->id);
        }
        $question->delete();
    }

    /**
     * @param Course      $course
     * @param Test        $test
     * @param             spec image $image
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function removeImage(Course $course, Test $test, $image = '')
    {
        switch ($image) {
            case 'custom_completion_background':
                Storage::disk('static')->delete($test->custom_completion_background);
                $test->custom_completion_background = '';
                Break;
            case 'custom_completion_header':
                Storage::disk('static')->delete($test->custom_completion_header);
                $test->custom_completion_header = '';
                Break;
            case 'custom_completion_badge':
                Storage::disk('static')->delete($test->custom_completion_badge);
                $test->custom_completion_badge = '';
                Break;
        }

        $test->save();

        return redirect()->route('tests.edit', ['course' => $course, 'test' => $test])
                ->with('alert-success', 'Image succesfully deleted');
    }
}
