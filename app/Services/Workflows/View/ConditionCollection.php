<?php
namespace App\Services\Workflows\View;

use App\Services\Workflows\View\Conditions\Country;
use App\Services\Workflows\View\Conditions\CameThroughCampaign;
use App\Services\Workflows\View\Conditions\CameThroughCampaignSource;
use App\Services\Workflows\View\Conditions\DosentOwnCourse;
use App\Services\Workflows\View\Conditions\OwnsCourse;
use App\Services\Workflows\View\Conditions\TimeSinceLogin;
use App\Services\Workflows\View\Conditions\TotalPurchasedAmount;
use App\Services\Workflows\View\Contracts\ConditionContract;
use App\Services\Workflows\View\Conditions\CourseCompleted;
use Log;

class ConditionCollection implements ConditionContract
{
    const CONDITIONS = [
        OwnsCourse::class,
        DosentOwnCourse::class,
        TotalPurchasedAmount::class,
        CameThroughCampaign::class,
        CameThroughCampaignSource::class,
        Country::class,
        TimeSinceLogin::class,
        CourseCompleted::class
    ];

    public function getRepresentation()
    {
        $results = [];
        foreach (self::CONDITIONS as $condition) {
            $object = new $condition;

            array_push($results, $object->getRepresentation());
        }

        return $results;
    }

    /**
     * @param $data
     * @return bool
     */
    public function isValid($data)
    {
        foreach ($data as $condition) {
            $this->validateCondition($condition);
        }

        return true;
    }

    /**
     * @param $condition
     * @return array
     */
    private function validateCondition($condition)
    {
        try {
            if (isset($condition['key']) && class_exists($condition['key'])) {
                $comparator = new $condition['key'];

                if (! $comparator->isValid($condition)) {
                    return [[$comparator->title ." condition is invalid. Please select a value."]];
                }
            }
        } catch (\Exception $e) {
            catch_and_return('Condition is missing the "key" parameter.', $e);
        }
    }
}
