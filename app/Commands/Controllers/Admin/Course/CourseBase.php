<?php

namespace App\Commands\Controllers\Admin\Course;

use App\Commands\Base;
use App\Http\Requests\Admin\Course\StoreCourseRequest;
use App\Models\Course;
use App\Models\CourseBonus;
use App\Models\Queries\CourseQueries;
use App\Models\Sendlane;
use App\Services\Sendlane\Sendlane as SendlaneService;
use Illuminate\Http\Request;

/**
 * This class was derived from the logic that was inside the
 * App\Http\Controllers\Admin\CourseController.php > store and update methods.
 *
 * This class houses the shared methods and variables between the
 * Command classes extending it.
 *
 * Abstract Class CourseBase
 * @package App\Commands\Controllers\Admin
 */
abstract class CourseBase extends Base
{
    /** @var StoreCourseRequest|Request */
    protected $request;

    /** @var CourseQueries */
    protected $courseQueries;

    /**
     * @var Sendlane
     */
    protected $sendlane;

    /**
     * @var CourseBonus
     */
    protected $courseBonus;

    /**
     * @var Course
     */
    protected $course;

    /**
     * CourseUpdate constructor.
     * @param Course $course
     * @param CourseQueries $courseQueries
     * @param Sendlane $sendlane
     * @param CourseBonus $courseBonus
     * @param Request $request
     */
    public function __construct(
        Course $course,
        CourseQueries $courseQueries,
        Sendlane $sendlane,
        CourseBonus $courseBonus,
        Request $request
    ) {
        $this->courseQueries = $courseQueries;
        $this->sendlane = $sendlane;
        $this->courseBonus = $courseBonus;
        $this->course = $course;
        $this->request = $request;
    }

    /**
     * @param Course $course
     */
    protected function addPostRegistrationDescription(Course $course)
    {
        if ($this->request->has('post-registration-description')) {
            /**
             * Use CourseQueries to do a query rather than the model directly
             */
            $this->courseQueries->addPostRegistrationDescription(
                $course,
                $this->request->get('post-registration-description')
            );
        }
    }

    /**
     * @param int $sendlane
     *
     * @return mixed
     */
    protected function getSendlaneLists(int $sendlane)
    {
        $apiData = $this->sendlane->find($sendlane)->toArray();
        $apiData = array_intersect_key(
            $apiData,
            array_flip(['api', 'hash', 'subdomain'])
        );

        $service = app(SendlaneService::class, $apiData);

        return (string) $service->lists->get(0, 100)->getBody();
    }

    /**
     * @param Course $course
     */
    protected function saveSendlaneRelatedData(Course $course)
    {
        if ($this->request->sendlaneAccount && $this->request->sendlaneList) {
            $listId = (int) strstr($this->request->sendlaneList, '|', true);
            $listName = substr(strstr($this->request->sendlaneList, '|'), 1);

            $course->sendlane->fill([
                'sendlane_id' => $this->request->sendlaneAccount,
                'list_id'     => $listId,
                'course_id'   => $course->id,
                'list_name'   => $listName,
            ]);
            $course->sendlane->save();
        }
    }

    /**
     * @param Course $course
     */
    protected function saveInfusionsoftRelatedData(Course $course)
    {
        if (($this->request->is_product_id ||
                $this->request->is_subscription_product_id) && $this->request->is_account) {
            $course->infusionsoft->fill($this->request->only(
                'is_product_id',
                'is_account',
                'price'
            ));
            $course->infusionsoft->subscription = $this->request->subscription ? 1 : 0;
            $course->infusionsoft->course_id = $course->id;
            $course->infusionsoft->payments_required = (int) request('payments_required') ?: null;
            $course->infusionsoft->is_subscription_product_id = (int) request('is_subscription_product_id') ?: null;
            $course->infusionsoft->subscription_price = (int) request('subscription_price') ?: null;
            $course->infusionsoft->subscription_payment_url = request('subscription_payment_url') ?: null;
            $course->infusionsoft->is_subscription_discount_product_id = request('is_subscription_discount_product_id');
            $course->infusionsoft->is_subscription_discount_product_url =
                request('is_subscription_discount_product_url');

            $course->infusionsoft->save();
        }
    }

    /**
     * @param Course $course
     */
    protected function saveVanillaForumRelatedData(Course $course)
    {
        $course->vanillaForum->fill($this->request->only('client_id', 'client_secret', 'url', 'forum_rules'));
        $course->vanillaForum->course_id = $course->id;
        $course->vanillaForum->save();
    }

    /**
     * @param Course $course
     */
    protected function storeThumbnail(Course $course)
    {
        if ($thumbnail = request()->file('thumbnail')) {
            $randomString = str_random($n = 15);
            $ext = request()->file('thumbnail')->getClientOriginalExtension();
            $thumbnailName = 'featured-image-'.$randomString.'.'.$ext;
            $thumbnail->storeAs("courses/$course->id", $thumbnailName, 'static');
            $course->thumbnail = $thumbnailName;
            $course->save();
        }
    }

    /**
     * @return string
     */
    protected function getPostRegistrationDescription()
    {
        return $this->course->postRegistrationDescriptions->first() ?
            $this->course->postRegistrationDescriptions->first()->description : '';
    }
}
