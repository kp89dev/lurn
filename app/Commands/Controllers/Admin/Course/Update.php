<?php

namespace App\Commands\Controllers\Admin\Course;

use App\Commands\Controllers\Admin\Course\CourseBase;
use App\Http\Requests\Admin\Course\StoreCourseRequest;
use App\Models\Course;
use App\Models\CourseBonus;
use App\Models\CourseFeature;
use App\Models\CourseRecommendations;
use Exception;

/**
 * This class was derived from the logic that was inside the
 * App\Http\Controllers\Admin\CourseController.php > update method.
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
 * Class CourseUpdate
 * @package App\Commands\Controllers\Admin
 */
class Update extends CourseBase
{
    /**
     * @param StoreCourseRequest $request
     * @return Update
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
        $course->setRecommendations(
            $this->request->only('recommended1', 'recommended2', 'recommended3', 'recommended4')
        );
        $course->categories()->sync($this->request->category);
        $this->setBonusOf($course);
        $this->storeThumbnail($course);
        $this->pushPurchasable($course);
    }

    /**
     * @param Course $course
     * @throws Exception
     */
    private function setBonusOf(Course $course)
    {
        if ($this->request->bonus_of) {
            $bonusOf = CourseBonus::where('bonus_course_id', $this->request->course)->first();
            $existing = CourseBonus::where([
                'bonus_course_id' => $this->request->course,
                'course_id' => $this->request->bonus_of,
            ])->first();
            if ($bonusOf && $bonusOf instanceof CourseBonus) {
                if ($this->request->bonus_of == 'none' || $existing instanceof CourseBonus) {
                    $bonusOf->delete();
                } else {
                    $bonusOf->course_id = $this->request->bonus_of;
                    $bonusOf->save();
                }
            } else {
                if ($this->request->bonus_of !== 'none') {
                    $bonusOf = new CourseBonus;
                    $bonusOf->course_id = $this->request->bonus_of;
                    $bonusOf->bonus_course_id = $course->id;
                    $bonusOf->save();
                }
            }
        }
    }

    /**
     * @return Course
     */
    private function buildCourse()
    {
        /** @var Course $course */
        $course = $this->course->find($this->request->course);
        $data = $this->request->only(
            'title',
            'description',
            'snippet',
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
        $course->fill($data);
        $course->save();

        return $course;
    }
    
    /**
     * @param Course $course
     * @throws Exception
     */
    private function pushPurchasable (Course $course)
    {
        if ($course->purchasable != 1) {
            CourseFeature::where('course_id',$course->id)->delete();
            CourseRecommendations::where('recommended_course_id', $course->id)->delete();
        } 
    }
}
