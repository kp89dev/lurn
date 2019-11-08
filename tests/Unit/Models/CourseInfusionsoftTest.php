<?php
namespace Unit\Models;

use App\Models\CourseInfusionsoft;
use App\Models\InfusionsoftMerchantId;
use Illuminate\Support\Facades\Cache;

class CourseInfusionsoftTest extends \TestCase
{
    /**
     * @test
     */
    public function next_id_is_returned_when_exists_in_cache()
    {
        $courseInfusionsoft = factory(CourseInfusionsoft::class)->create([
            'is_account' => 'uv222'
        ]);

        factory(InfusionsoftMerchantId::class)->create([
            'account' => 'uv222',
            'ids'     => [22, 33, 44]
        ]);

        self::assertEquals(33, $courseInfusionsoft->getNextMerchantId());
        $courseInfusionsoft->rotateMerchantId();
        self::assertEquals(44, $courseInfusionsoft->getNextMerchantId());
        $courseInfusionsoft->rotateMerchantId();
        self::assertEquals(22, $courseInfusionsoft->getNextMerchantId());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function rotating_merchant_id_throws_error_when_no_id_is_defined()
    {
        $courseInfusionsoft = factory(CourseInfusionsoft::class)->create([
            'is_account' => 'uv222'
        ]);

        $courseInfusionsoft->rotateMerchantId();
    }
}
