<?php

namespace App\Models;

use App\Engines\Survey\Traits\SurveyTracker;
use App\Models\Badge\BadgeRequest;
use App\Models\Queries\Traits\UserSurveys;
use App\Models\QueryBuilder\FirstIncompleteLesson;
use App\Models\QueryBuilder\RoiCalculator;
use App\Models\QueryBuilder\UserSpendings;
use App\Models\Traits\MergeableUser;
use App\Models\Traits\SearchableUser;
use App\Models\Onboarding\BaseScenario;
use App\Models\Onboarding\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Notifications\Account\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @property $status
 * @property $statusName
 * @property Collection infusionsoftContact
 * @property Collection courseSubscriptions
 * @property bool isAdmin
 * @property bool isSuperAdmin
 * @property int id
 */
class User extends Authenticatable
{
    use Notifiable,
        SoftDeletes,
        SearchableUser,
        MergeableUser;

    public static $statuses = [
        'unconfirmed',
        'confirmed',
        80 => 'admin',
        99 => 'super-admin',
    ];

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'deleted_at',
    ];

    protected $dates = ['deleted_at'];

    /**
     * @return BelongsToMany
     */
    public function mergedAccounts()
    {
        return $this->belongsToMany(User::class, 'user_merges', 'into_user_id', 'merged_user_id')
            ->wherePivot('from_table', '=', 'users');
    }

    public function mergedIntoAccount()
    {
        return $this->belongsToMany(User::class, 'user_merges', 'merged_user_id', 'into_user_id')
            ->wherePivot('from_table', '=', 'users');
    }

    /**
     * @return BelongsToMany
     */
    public function mergedImportedAccounts()
    {
        return $this->belongsToMany(ImportedUser::class, 'user_merges', 'into_user_id', 'merged_user_id')
            ->wherePivot('from_table', '=', 'users_import_all');
    }

    /**
     * @param int $tableId
     *
     * @return mixed
     */
    public function getMergedImportedAccountByImportTableId(int $tableId)
    {
        return $this->mergedImportedAccounts()
            ->where('users_import_all.an_id', $tableId);
    }

    /**
     * @param int $userId
     *
     * @return mixed
     */
    public function getMergedAccountByUserId(int $userId)
    {
        return $this->mergedAccounts()
            ->where('users.id', $userId);
    }

    public function enroll(Course $course)
    {
        return $this->courses()->save($course);
    }

    public function enrolled($course)
    {
        $courseId = $course instanceof Course ? $course->id : $course;

        return gcache("user:$this->id.enrolled:$courseId", function () use ($courseId) {
            return $this->courseSubscriptions()
                ->whereCourseId($courseId)
                ->whereNull('cancelled_at')
                ->first();
        });
    }

    public function completed($lesson)
    {
        $lessonId = $lesson instanceof Lesson ? $lesson->id : $lesson;

        return $this->lessonSubscriptions()
            ->whereLessonId($lessonId)
            ->first();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses')
            ->withTimestamps()
            ->withPivot(['status', 'cancelled_at', 'refunded_at']);
    }

    public function adminRole()
    {
        return $this->belongsToMany(UserRole::class, 'user_roles');
    }

    /**
     * Checks if the current user has access to a certain admin area and permission.
     *
     * @param string $area
     * @param mixed $permission
     * @return bool
     */
    public function hasAdminAccess(string $area, $permission)
    {
        $role = gcache("user:$this->id.admin-role", function () {
            return $this->adminRole()->first();
        });

        return $role ? $role->hasAccess($area, $permission) : false;
    }

    public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }

    /**
     * Get user country from first login.
     *
     * @return mixed
     */
    public function getCountry()
    {
        return $this->hasOne(UserLogin::class)->orderBy('created_at')->value('country');
    }

    /**
     * Get the user's unique referral code.
     *
     * @return string
     */
    public function getReferralCode()
    {
        return md5('$' . $this->id . '.' . $this->created_at->format('Y-m-d'));
    }

    /**
     * Get user total spendings / ROI.
     *
     * @return int
     */
    public function getTotalSpendings()
    {
        return gcache('user:spendings', function () {
        	return (new UserSpendings($this))->get();
        });
    }

    /**
     * @return HasMany
     */
    public function getTrackerCampaignDetails()
    {
        $identity = $this->hasMany(Tracker\Identity::class)->first();
        $visit = $identity ? $identity->getCampaignVisits()->first() : null;
        $campaign = $visit ? $visit->campaign()->first() : null;

        return $campaign;
    }

    public function courseSubscriptions()
    {
        return $this->hasMany(CourseSubscriptions::class);
    }

    public function lessonSubscriptions()
    {
        return $this->hasMany(LessonSubscriptions::class);
    }

    public function pushNotifications()
    {
        return $this->belongsToMany(PushNotifications::class);
    }

    public function scenarios()
    {
        $this->belongsToMany(BaseScenario::class);
    }

    public function likes()
    {
        return $this->hasMany(CourseLike::class);
    }

    public function engagements()
    {
        return $this->hasMany(UserPointActivity::class)->orderBy('created_at', 'DESC');
    }

    /*
     * @return HasOne
     */
    public function setting()
    {
        return $this->hasOne(UserSetting::class)->withDefault(function () {
            return new UserSetting(['user_id' => $this->id]);
        });
    }

    public function notes($course)
    {
        if ($course instanceof Course) {
            return $this->hasMany(LessonUserNote::class)->whereCourseId($course->id);
        }

        return $this->hasMany(LessonUserNote::class);
    }

    public function readNews()
    {
        return $this->belongsToMany(News::class, 'news_reads');
    }

    public function getUnreadNews()
    {
        $ids = DB::table('news_reads')->where('user_id', user()->id)->pluck('news_id');

        return News::whereNotIn('id', $ids)->orderBy('id', 'desc');
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function getFirstNameAttribute()
    {
        return str_before($this->name, ' ');
    }

    public function getPointsAttribute()
    {
        return $this->engagements->sum('points');
    }

    public function getPointsEarnedAttribute()
    {
        return (int) $this->engagements()->wherePending(0)->sum('points');
    }

    public function getStatusNameAttribute()
    {
        return self::$statuses[$this->status];
    }

    public function getIsAdminAttribute()
    {
        return in_array($this->statusName, ['admin', 'super-admin']);
    }

    /**
     * @return bool
     */
    public function getIsSuperAdminAttribute()
    {
        return $this->statusName === 'super-admin';
    }

    public function setStatusAttribute($value)
    {
        if (($index = array_search($value, self::$statuses)) !== false) {
            return $this->attributes['status'] = $index;
        }

        return $this->attributes['status'] = $value;
    }

    public function infusionsoftContact()
    {
        return $this->hasMany(InfusionsoftContact::class);
    }

    public function scopeContactIdFor($query, $account)
    {
        return $this->infusionsoftContact()->where('is_account', $account);
    }

    public function certificates()
    {
        return $this->hasMany(UserCertificate::class);
    }

    public function badgeRequests()
    {
        return $this->hasMany(BadgeRequest::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges');
    }

    public function tools()
    {
        return $this
            ->hasManyThrough(CourseTool::class, CourseSubscriptions::class, null, 'course_id', null, 'course_id')
            ->groupBy('tool_name')
            ->orderby('tool_name');
    }

    public function getMission()
    {
        return new Mission($this);
    }

    public function getInProgressCourse()
    {
        $courses = $this->courses()
            ->whereNull('cancelled_at')
            ->wherePivot('status', 0)
            ->orderBy('user_courses.id', 'desc')
            ->get();

        foreach ($courses as $course) {
            if ($course->getProgress() < 100) {
                return $course;
            }
        }
    }

    public function getPrintableImageUrl(): string
    {
        return is_object($this->setting) && $this->setting->image
            ? str_finish(config('app.cdn_static'), '/') . $this->setting->image
            : sprintf('https://www.gravatar.com/avatar/%s', md5($this->email));
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function activeCourses()
    {
        return $this->courses->filter(function (Course $course) {
            return ! $course->bonuses()->whereBonusCourseId($course->id)->count()
                && is_null($course->pivot->cancelled_at)
                && is_null($course->pivot->refunded_at)
                && $course->status != 0;
        })->unique();
    }

    public function isOlderThanOneMonth()
    {
        return $this->logins
            && $this->logins->first()
            && $this->logins->first()->created_at->diffInMonths(now()) >= 1;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getFirstIncompleteLesson(Course $course, Lesson $ignoreLesson = null)
    {
        return gcache("user:course:$course->id:first-incomplete-lesson", function () use ($course, $ignoreLesson) {
        	return (new FirstIncompleteLesson($this, $course, $ignoreLesson))->get();
        });
    }

    /**
     * @return Collection
     */
    public function enrolledCourses()
    {
        return $this->courses->filter(function (Course $course) {
            return $course->pivot->status != 1;
        });
    }

    /**
     * @return BelongsToMany
     */
    public function acquiredBadges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges');
    }

    /**
     * @return HasMany
     */
    public function userRefunds()
    {
        return $this->hasMany(UserRefund::class);
    }

    /*
     * Returns this user's referral, if there is one.
     *
     * @return HasOne
     */
    public function referral()
    {
        return $this->hasOne(User::class, 'id', 'referral_id');
    }

    /**
     * Returns the users referred by this user.
     *
     * @return HasMany
     */
    public function referees()
    {
        return $this->hasMany(User::class, 'referral_id');
    }
}
