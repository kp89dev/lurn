<?php
namespace Tests\Unit\Listeners\Account\Normal;

use App\Events\User\ImportedUserMerged;
use App\Events\User\UserMerged;
use App\Listeners\Account\Normal\AdjustToolsEmail;
use App\Models\Course;
use App\Models\CourseTool;
use App\Models\Source;
use App\Models\User;
use GuzzleHttp\Client;

class AdjustToolsEmailTest extends \TestCase
{
    /**
     * @test
     */
    public function successfully_updates_tool_email_when_user_has_access_to_tool_normal_user()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);
        $userToMerge = factory(User::class)->create(['email' => 'user@user.com']);

        $course = factory(Course::class)->create();
        //create a course relation
        $userToMerge->courses()->attach($course);

        factory(CourseTool::class)->create([
            'course_id' => $course->id,
            'tool_name' => 'Test1'
        ]);

        $source1 = factory(Source::class)->create([
            'url' => 'test1.local'
        ]);

        config(['tools' => [
            [
                'name'      => 'Test1',
                'updateUrl' => 'http://test1.local'
            ],
        ]]);

        $guzzleMock = $this->createMock(Client::class);
        $guzzleMock->expects(self::once())
                   ->method('__call')
                    ->with(
                        self::equalTo('post'),
                        [
                            'http://test1.local',
                            [
                                'form_params' => [
                                    'secret'   => $source1->token,
                                    'oldEmail' => $userToMerge->email,
                                    'newEmail' => $user->email
                                ]
                            ]
                        ]
                    );

        $this->app->bind(Client::class, function ($app) use ($guzzleMock) {
            return $guzzleMock;
        });

        $event    = new UserMerged($user, $userToMerge);
        $listener = new AdjustToolsEmail();
        $result = $listener->handle($event);

        static::assertNull($result);
    }
}
