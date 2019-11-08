<?php

namespace App\Models\Queries;

use App\Models\Course;
use App\Models\CustomDescription;
use App\Models\DescriptionType;

/**
 * The concept behind a query class is to move functionality from
 * a model or class that it does not belong in. By creating
 * a query, it follows the Single Responsibility rule in
 * PHP SOLID design principles.
 *
 * In plain terms, it cleans up the model or class using this class to make
 * it more readable and easier to interpret. It also eliminates
 * bugs from not being able to see something for many lines of code.
 *
 * Everything in this class follows the SOLID design principles.
 *
 * Class CourseQueries
 * @package App\Models\Queries
 */
class CourseQueries
{
    /**
     * @param Course $course
     * @param $description
     */
    public function addPostRegistrationDescription(Course $course, $description)
    {
        /** @var CustomDescription $postRegistrationDescription */
        $postRegistrationDescription = $course->postRegistrationDescriptions->first();

        if ($postRegistrationDescription) {
            $postRegistrationDescription->update(['description' => $description]);
        } else {
            /** @var DescriptionType $postRegistrationDescription */
            $postRegistrationDescription = DescriptionType::postRegistration()->first();

            $course->postRegistrationDescriptions()->create([
                'description_type_id' => $postRegistrationDescription->id,
                'description' => $description,
            ]);
        }
    }
}