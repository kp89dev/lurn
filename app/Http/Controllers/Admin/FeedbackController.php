<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Course\StoreFaqRequest;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use League\Csv\Writer;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:feedback,read')
            ->only('index', 'downloadCsv');
        $this->middleware('admin.role.auth:feedback,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    /**
     * @return View
     */
    public function index()
    {
        $feedbacks = Feedback::orderBy('id', 'desc')->paginate(20);

        return view('admin.feedback.index', compact('feedbacks'));
    }

    /**
     * @param Feedback $feedback
     * @return RedirectResponse
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        request()->session()->flash('alert-success', 'Question & Answer were successfully deleted!');

        return redirect()->back();
    }

    /**
     * @param Feedback $feedback
     * @return RedirectResponse
     */
    public function show(Feedback $feedback)
    {
        return view('admin.feedback.show', compact('feedback'));
    }

    public function downloadCsv()
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject);
        $csv->insertOne(['id', 'user_id', 'user_name', 'grade', 'feedback', 'date']);

        Feedback::with('user')->chunk(100, function ($feedback) use ($csv) {
        	foreach ($feedback as $message) {
                $csv->insertOne([
                    $message->id,
                    $message->user->id,
                    $message->user->name,
                    $message->grade,
                    $message->feedback,
                    $message->created_at,
                ]);
            }
        });

        $csv->output('feedback_' . date('m-d-y_His') . '.csv');
        die;
    }
}
