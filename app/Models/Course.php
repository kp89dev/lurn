<?php

namespace App\Models;

use App\Models\QueryBuilder\CourseCounters;
use App\Models\Resources\CourseRelatives;
use App\Models\Traits\Searchable;
use App\Models\Traits\Sluggable;
use App\Models\Traits\FindBySlug;
use App\Models\Traits\SeoCustom;
use App\Models\Traits\SearchableCourse;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Class Course
 *
 * @package App\Models
 * @method static byProductId(int $product_id)
 */
class Course extends Model
{
    use Sluggable,
        FindBySlug,
        SeoCustom,
        Searchable,
        SearchableCourse;

    static public $userStatuses = [
        'in-progress',
        'completed',
    ];

    protected $guarded = ['id'];
    protected $appends = ['price'];

    protected $casts = [
        'free' => 'boolean',
    ];

    public function scopeEnabled($query)
    {
        $query->whereStatus(1);
    }

    public function scopeFree($query)
    {
        $query->whereFree(1);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function bonuses()
    {
        return $this->hasMany(CourseBonus::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    public function container()
    {
        return $this->belongsTo(CourseContainer::class, 'course_container_id', 'id');
    }

    public function label()
    {
        return $this->belongsTo(Labels::class);
    }

    public function recommendations()
    {
        return $this->hasMany(CourseRecommendations::class);
    }

    public function feature()
    {
        return $this->belongsTo(CourseFeature::class);
    }

    public function seocourse()
    {
        return $this->hasOne(SeoCourse::class);
    }

    public function badges()
    {
        return $this->hasMany(Badge::class);
    }

    public function badgesEnabled()
    {
        return $this->hasMany(Badge::class)->enabled();
    }

    public function bonusOf()
    {
        return $this->hasOne(CourseBonus::class, 'bonus_course_id');
    }

    public function tools()
    {
        return $this->hasMany(CourseTool::class);
    }

    public function likes()
    {
        return $this->hasMany(CourseLike::class);
    }

    public function getRelativesInstance()
    {
        static $courseRelatives;

        if (isset($courseRelatives[$this->id])) {
            return $courseRelatives[$this->id];
        }

        return $courseRelatives[$this->id] = new CourseRelatives($this);
    }

    public function getRecommended($howMany = 4)
    {
        return $this->getRelativesInstance()->getRecommended($howMany);
    }

    public function getRecommedations()
    {
        return $this->getRelativesInstance()->getRecommendations();
    }

    public function setRecommendations($recommendedList)
    {
        return $this->getRelativesInstance()->setRecommendations($recommendedList);
    }

    public function getFeatured($isAdmin = false)
    {
        return $this->getRelativesInstance()->getFeatured($isAdmin);
    }

    public function getCourseSEO()
    {
        $seoDefaults = $this->getDefaults();
        $seoCourse = $this->seocourse;
        if ($seoCourse) {
            $seoCourse = $seoCourse->toArray();
            $coureSEO = [];
            foreach ($seoDefaults as $k => $v) {
                if (str_contains($k, 'webmaster') || str_contains($k, 'analytics')) {
                    $coureSEO[$k] = $v;
                }
                else {
                    $coureSEO[$k] = ($seoCourse[$k] ?: $v);
                }
            }

            return $coureSEO;
        }
        else {
            $seoDefaults['title'] = sprintf('%s', $this->title);
        }

        return $seoDefaults;
    }

    /**
     * Returns the percentage of completed lessons / test in the course.
     *
     * @return int
     */
    public function getProgress()
    {
        static $courseProgress;

        if (! user()) {
            return 0;
        }

        if (isset($courseProgress[$this->id])) {
            return $courseProgress[$this->id];
        }

        $lessons = "
            select count(*)
            from lessons l
            inner join modules m on (m.id = l.module_id)
            where m.course_id = :course
              and m.status = 1
              and l.status = 1
              and m.type = 'Module'";
        $completed = "$lessons and exists
            (select * from lesson_subscriptions ls where l.id = ls.lesson_id and user_id = :user)";
        $tests = "
            select count(*) from tests t
            where t.course_id = :course
              and t.course_id is not null
              and t.status = 1";
        $passed = "$tests and exists (select * from test_users tu
              where t.id = tu.test_id and user_id = :user and mark >= 75)";

        $data = DB::select(
            "select (($completed) + ($passed)) / (($lessons) + ($tests)) * 100 as progress",
            ['course' => $this->id, 'user' => user()->id]
        );

        return $courseProgress[$this->id] = number_format($data[0]->progress);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'course_categories');
    }

    public function getCategoryListAttribute()
    {
        static $categoryList;

        if (isset($categoryList[$this->id])) {
            return $categoryList[$this->id];
        }

        return $categoryList[$this->id] = $this->categories()->pluck('name');
    }

    public function getCategoryListWithLabelsAttribute()
    {
        $categories = $this->getCategoryListAttribute();

        $this->label && $categories->prepend($this->label->title);

        return $categories;
    }

    public function getCurrentLesson()
    {
        return user()->getFirstIncompleteLesson($this);
    }

    public function getPrintableImageUrl(): string
    {
        $file = "courses/$this->id/$this->thumbnail";
        $disk = Storage::disk('static');
        $cdnUrl = sprintf('%s/%s', config('app.cdn_static'), $file);

        // Check the file availability only locally or in the admin panel.
        if (! app()->environment('production')) {
            return $disk->exists($file) ? $cdnUrl : url('images/default-course-thumbnail.png');
        }

        return $cdnUrl;
    }

    public function infusionsoft()
    {
        return $this->hasOne(CourseInfusionsoft::class)->withDefault(function () {
            return new CourseInfusionsoft();
        });
    }

    public static function getHeaderData()
    {
        $data = collect([]);
        $bonusCourses = CourseBonus::all()->pluck('bonus_course_id');

        $menuCourses = self::whereNotIn('id', $bonusCourses)->limit(6)->get();

        $menuCourses->each(function ($course) use ($data) {
            $data->push([
                'id'       => $course->id,
                'image'    => $course->getPrintableImageUrl(),
                'title'    => $course->title,
                'url'      => route('course', $course->slug),
                'snippet'  => $course->snippet,
                'modules'  => shorter_number($course->getCounters()->modules),
                'lessons'  => shorter_number($course->getCounters()->lessons),
                'students' => shorter_number($course->getCounters()->students),
            ]);
        });

        return $data;
    }

    public function sendlane()
    {
        return $this->hasOne(CourseSendlane::class)->withDefault(function () {
            return new CourseSendlane();
        });
    }

    public function getOrderedTests()
    {
        return $this->tests()->orderBy('after_lesson_id');
    }

    public function getTestsAndMarkByLessonAndUser($lesson)
    {
        return $this->tests()
            ->select('tests.*', 'test_users.mark as user_mark')
            ->where('after_lesson_id', $lesson)
            ->where('tests.status', 1)
            ->leftJoin('test_users', function ($join) {
                $join->on('tests.id', '=', 'test_users.test_id')
                    ->where('user_id', '=', user()->id);
            })
            ->orderBy('test_users.mark', 'desc')
            ->take(1)
            ->get();
    }

    public function getPriceAttribute()
    {
        return $this->infusionsoft->price;
    }

    public function vanillaForum()
    {
        return $this->hasOne(CourseVanillaForum::class)->withDefault(function () {
            return new CourseVanillaForum();
        });
    }

    public function getUrlAttribute()
    {
        return route('course', $this->slug);
    }

    public function getOnboardingModule()
    {
        $key = sprintf('%s.onboarding-module', $this->slug);

        if ($onboardingModule = Cache::get($key)) {
            return $onboardingModule;
        }

        $onboardingModule = $this->modules()->whereSlug('on-boarding')->first();

        Cache::put($key, $onboardingModule, 1);

        return $onboardingModule;
    }

    public function userIsBoarded($userId = null)
    {
        // If there's no onboarding module for this course, we're going to mark the user boarded.
        if (! $onboardingModule = $this->getOnboardingModule()) {
            return true;
        }

        $userId = $userId ?? user()->id;
        $key = $this->getOnboardingCacheKey($userId);

        if (Cache::get($key)) {
            return true;
        }

        if (! $onboardingLesson = $onboardingModule->lessons()->enabled()->first()) {
            return false;
        }

        if ($boarded = (bool) $onboardingLesson->subscriptions()->whereUserId($userId)->first()) {
            Cache::put($key, true, 10);
        }

        return $boarded;
    }

    /**
     * Sets the onboarding status by marking the first lesson
     * of on-boarding module as completed
     *
     * @param $userId
     * @param $status
     */
    public function setUserOnboardingStatus($userId, $enable = false)
    {
        if (! $onboardingModule = $this->getOnboardingModule()) {
            throw new \DomainException("Trying to toggle onboarding on a course which doesn't have onboarding module");
        }

        if (! $onboardingLesson = $onboardingModule->lessons()->enabled()->first()) {
            throw new \DomainException("Trying to toggle onboarding on a course which doesn't have onboarding lesson");
        }

        $lessonSubscription = LessonSubscriptions::firstOrNew([
            'user_id'   => $userId,
            'lesson_id' => $onboardingLesson->id
        ]);

        if ($enable) {
            $lessonSubscription->save();
        } else {
            $lessonSubscription->delete();
        }

        Cache::forget($this->getOnboardingCacheKey($userId));
    }

    public function getCounters()
    {
        static $courseCounters;

        if (isset($courseCounters[$this->id])) {
            return $courseCounters[$this->id];
        }

        return $courseCounters[$this->id] = (new CourseCounters($this))->get();
    }

    /**
     * @return BelongsToMany
     */
    public function customDescriptions()
    {
        return $this->belongsToMany(CustomDescription::class, 'course_custom_description')->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function postRegistrationDescriptions()
    {
        return $this->customDescriptions()->postRegistration();
    }

    /**
     * @param $value
     * @return string
     */
    public function getPostRegistrationDescriptionAttribute($value)
    {
        $postRegistrationDescription = $this->postRegistrationDescriptions->first();

        return $postRegistrationDescription ? $postRegistrationDescription->description : '';
    }

    public function getCourseDescriptionAttribute($value)
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        if ($authUser && user_enrolled($this, $authUser)) {
            return $this->postRegistrationDescription;
        }

        return $this->description;
    }

    /**
     * @param $userId
     *
     * @return string
     */
    private function getOnboardingCacheKey($userId)
    {
        return  sprintf('%d.%s.boarded', $userId, $this->slug);
    }
}
