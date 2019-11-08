<?php

namespace App\Commands\Controllers\Admin\Course;

use App\Commands\Controllers\Admin\Course\CourseBase;
use App\Http\Requests\Admin\Course\StoreCourseRequest;
use App\Models\Course;
use App\Models\CourseBonus;
use Exception;

/**
 * This class was derived from the logic that was inside the
 * App\Http\Controllers\Admin\CourseController.php > store method.
 *
 * The concept behind a command is to move functionality from
 * a controller or class that it does not belong in. By creating
 * a command, it follows the Single Responsibility rule in
 * PHP SOLID design principles.
 *
 * In plain terms, it cleans up the class using this class to make
 * it more readable and easier to interpret. It also eliminates
 * bugs from not being able to see something for many lines of code.
 *
 * Everything in this class follows the SOLID design principles.
 *
 * To use for other controllers or classes, simply create a
 * command class in the App\Commands namespace, and extend the base
 * App\Commands\Base.php Class either through another Base class like
 * this one, or directly.
 *
 * The only public methods on Command classes should be setter
 * methods and a process method that takes no parameters.
 *
 * Class CourseStore
 * @package App\Commands\Controllers\Admin
 */
class Store extends CourseBase
{
    /**
     * @param StoreCourseRequest $request
     * @return Store
     */
    public function setRequest(StoreCourseRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function process()
    {
        $course = $this->buildCourse();
        $this->addPostRegistrationDescription($course);
        $this->saveSendlaneRelatedData($course);
        $this->saveInfusionsoftRelatedData($course);
        $this->saveVanillaForumRelatedData($course);
        $this->setCourseRecommendations($course);
        $course->categories()->sync($this->request->category);
        $this->setBonusOf($course);
        $this->storeThumbnail($course);
    }

    /**
     * @param Course $course
     */
    private function setCourseRecommendations(Course $course)
    {
        if ($this->request->has('post-registration-description')) {
            $this->courseQueries->addPostRegistrationDescription(
                $course,
                $this->request->get('post-registration-description')
            );
        }
    }

    /**
     * @param Course $course
     * @throws Exception
     */
    private function setBonusOf(Course $course)
    {
        if ($this->request->bonus_of && $this->request->bonus_of !== 'none') {
            CourseBonus::firstOrCreate(['course_id' => $course->id, 'bonus_course_id' => $this->request->bonus_of]);
        }
    }

    /**
     * @return Course
     */
    private function buildCourse()
    {
        $data = $this->request->only(
            'title',
            'snippet',
            'description',
            'course_container_id',
            'status',
            'purchasable',
            'drip',
            'confirm_after',
            'label_id',
            'free',
            'buy_with_points'
        );
        $data['status'] = $data['status'] ?? 0;
        $data['free'] = $data['free'] ?? 0;
        $data['drip'] = $data['drip'] ?? 0;
        $data['purchasable'] = $data['purchasable'] ?? 0;
        $data['purchasable'] = $data['status'] == 0 ? 0 : $data['purchasable'];

        return $this->course->create($data);
    }
}