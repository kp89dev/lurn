<?php
namespace App\Http\Controllers\Classroom;

use App\Http\Middleware\RedirectIfResourceIsLink;
use App\Models\Course;
use App\Models\QueryBuilder\CourseResources;
use App\Models\Test;
use App\Models\CourseBonus;
use Closure;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use \App\Models\UserCertificate;
use Illuminate\Support\Facades\View;
use PDF;
use App\Models\UserCourse;
use App\Models\SeoCourse;

class ResourceController extends Controller
{

    public function __construct()
    {
        $this->middleware('resolve.resources')
            ->only('course', 'module', 'lesson', 'test', 'notes', 'printNotes', 'accessDenied');
        $this->middleware('enrollment.check')->only('module', 'lesson', 'test');
        $this->middleware('course.access')->only('course', 'module', 'lesson', 'test');
        $this->middleware('onboarding')->only('module', 'lesson', 'test');
        $this->middleware(RedirectIfResourceIsLink::class)->only('module', 'lesson');

        $this->middleware(function ($request, Closure $next) {
            if (!is_string(request('course'))) {
                $this->seoSetup($request);
            }

            return $next($request);
        })->except('accessDenied');

        $this->middleware(function ($request, Closure $next) {
            $modules = (new CourseResources(request('course')))
                ->modulesWithCountersAndProgress()
                ->addLessons()
                ->get();

            View::share(compact('modules'));

            return $next($request);
        })->only('course', 'lesson', 'test');
    }

    /**
     * Displays a list of all courses.
     * It is searchable based on the field in the top bar.
     *
     * @return View
     */
    public function index()
    {
        if (request('q')) {
            $metaSearchIDs = SeoCourse::search()->pluck('course_id');
            $courseSearchIDs = Course::search()->pluck('id');
            $fullSearchIDs = $metaSearchIDs->merge($courseSearchIDs)->unique();
            $bonusSetsOfFullSearch = CourseBonus::whereIn('bonus_course_id', $fullSearchIDs)->select('bonus_course_id', 'course_id')->get();
            $bonusIds = collect();
            foreach ($bonusSetsOfFullSearch as $bonusSet) {
                $bonusIds->push($bonusSet->bonus_course_id);
                $fullSearchIDs->push($bonusSet->course_id);
            }

            $searchIDs = $fullSearchIDs->filter(function ($value) use ($bonusIds) {
                return (!$bonusIds->contains($value));
            });
            $courses = Course::whereIn('id', $searchIDs->unique())->where('purchasable', 1)->enabled();
        } else {
            $courses = Course::whereNotIn('id', CourseBonus::pluck('bonus_course_id'))->where('purchasable', 1)->enabled();
        }

        list($courses, $categories) = $this->getCategoryListFromCourses($courses);

        return view('pages.classroom.index', compact('courses', 'categories'));
    }

    /**
     * Returns a collection of sorted categories with
     * an "All" category prepended form the given $courses collection.
     *
     * @param Builder $courses
     * @return array
     */
    private function getCategoryListFromCourses(Builder $courses): array
    {
        $labels = collect([]);
        $categories = collect([]);
        $coursesList = collect([]);

        $courses->with('label', 'categories')->each(function ($course) use ($categories, $labels, $coursesList) {
            $course->categories->pluck('name')->each(function ($category) use ($categories) {
                $categories->contains($category) ?: $categories->push($category);
            });

            if ($course->label && !$labels->contains($course->label->title)) {
                $labels->push($course->label->title);
            }

            $coursesList->push($course);
        });

        $labels->sort();
        $categories->sort();
        $categories = $labels->toBase()->merge($categories)->prepend('All');

        return [$coursesList, $categories];
    }

    /**
     * Returns a course page.
     *
     * @return View
     */
    public function course()
    {
        if (request()->course->status == 0) {
            abort(404);
        }

        $recommended = request()->course->getRecommended(6);
        $bonusenrollment = 0;

        /** @var Collection $userBadges */
        $userBadges = new Collection();

        if (user() && user()->id) {
            if (!user_enrolled(request()->course) && request()->course->purchasable == 0) {
                abort(404);
            }
            $bonusenrollment = UserCourse::whereIn('course_id', CourseBonus::select('bonus_course_id')
                    ->where('course_id', '=', request()->course->id)
                    ->pluck('bonus_course_id'))
                ->where('user_id', '=', user()->id)
                ->count();
            $userBadges = user()->acquiredBadges->where('course_id', request()->course->id);
        } elseif (request()->course->purchasable == 0) {
            abort(404);
        }

        return view('pages.classroom.course', compact('recommended', 'bonusenrollment', 'userBadges'));
    }

    /**
     * Returns a module page.
     *
     * @return View
     * @throws \Exception
     */
    public function module()
    {
        $courseProgress = request('course')->getProgress();
        $moduleProgress = request('module')->getProgress();

        /** @var Collection $userBadges */
        $userBadges = new Collection();

        $lessons = (new CourseResources(request('course')))
            ->modulesWithCountersAndProgress(request('module'))
            ->addLessons()
            ->get()
            ->first();

        if (user() && user()->id) {
            $userBadges = user()->acquiredBadges->where('course_id', request()->course->id);
        }

        $lessons = $lessons ? $lessons->orderedLessons : [];

        return view('pages.classroom.module', compact('title', 'courseProgress', 'moduleProgress', 'lessons', 'userBadges'));
    }

    /**
     * Returns a course lesson.
     *
     * @return View
     */
    public function lesson()
    {
        $courseProgress = request('course')->getProgress();
        $relatedLessons = request('lesson')->getRelated();

        return view('pages.classroom.lesson', compact('courseProgress', 'relatedLessons'));
    }

    public function test()
    {
        $action = route('test-submit', [request('course')->slug, request('module')->slug, request('test')]);
        $method = "POST";
        $currentTest = request('test');
        $courseProgress = request('course')->getProgress();
        $relatedLessons = $currentTest->getRelated();
        $testResult = $currentTest->userHasPassed(user());
        $module = $currentTest->getModule();
        $test_cert = $currentTest->certificate_id;

        if ($testResult && $test_cert) {
            if ($test_cert) {
                $user_cert = UserCertificate::whereTestId($currentTest->id)->whereUserId(user()->id)->get();

                if (!$user_cert->count() > 0) {
                    $cert = new UserCertificate();
                    $cert = $cert->issueCert(request('test')->id, user()->id, $test_cert);
                } else {
                    $cert = $user_cert;
                }
            }

            return view('pages.classroom.test-success', compact('module', 'courseProgress', 'relatedLessons', 'testResult', 'cert', 'currentTest'));
        } else {
            return view('pages.classroom.test', compact('module', 'courseProgress', 'relatedLessons', 'action', 'method', 'testResult', 'currentTest'));
        }
    }

    public function checkTest(Request $request)
    {
        $questions = request('question');
        $questions = is_array($questions) ? $questions : [$questions];
        $test = Test::find(request('test'));
        $results = $test->checkAnswers($questions);

        return redirect()->back()->with($results + compact('questions'));
    }

    public function testCertificate($course, $module, $test)
    {
        $user = user();
        $cert = UserCertificate::where([['test_id', $test], ['user_id', user()->id]])->first();
        $body = str_replace('$$USERNAME$$', $user->name, $cert->certificate_body);
        $data = [
            'certTitle' => $cert->certificate_title,
            'certStyle' => $cert->certificate_style,
            'body' => $body,
            'crLogo' => $cert->getSrc('logo'),
            'crLogoStyle' => $cert->certificate_logo_style,
            'crBorder' => $cert->getSrc('border'),
            'crBorderStyle' => $cert->certificate_border_style,
            'background' => $cert->getSrc('background'),
            'crSign' => $cert->getSrc('sign'),
            'crSignStyle' => $cert->certificate_sign_style,
            'crBadge' => $cert->getSrc('badge'),
            'crBadgeStyle' => $cert->certificate_badge_style
        ];
        if ($cert->certificate_date_bg) {
            $data['crDate'] = date('F jS, Y');
            $data['crDateBG'] = $cert->getSrc('date_bg');
            $data['crDateStyle'] = $cert->date_style;
            $pdf = Pdf::loadView('pages.classroom.certificate-dated', $data);
        } else {
            $pdf = PDF::loadView('pages.classroom.certificate', $data);
        }

        return $pdf->stream("{$cert->title}.pdf");
    }

    /**
     * Returns a list of the notes, either for print or simply for review.
     *
     * @return View
     */
    public function notes()
    {
        return view('pages.classroom.notes', $this->getNotes())->withTitle(
                sprintf('%s Notes Taken', request('course')->title)
        );
    }

    public function printNotes()
    {
        return view('pages.classroom.printable-notes', $this->getNotes());
    }

    private function getNotes()
    {
        $course = request('course');

        user_enrolled($course) || abort(404);

        // Get the user's notes on this course, sorted by module and lesson index.
        $notes = user()->notes($course)->get()->sort(function ($a, $b) {
            return strcmp($a->moduleIndex, $b->moduleIndex) ?: strcmp($a->lessonIndex, $b->lessonIndex);
        });

        return compact('notes');
    }

    public function accessDenied()
    {
        $subscription = user()->enrolled(request('course'));

        if (!$subscription->expired) {
            return redirect()->route('course', request('course')->slug);
        }

        return view('pages.classroom.access-denied');
    }
}
