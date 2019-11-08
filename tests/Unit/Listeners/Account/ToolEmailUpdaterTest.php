<?php
namespace Tests\Unit\Listeners\Account;

use App\Events\User\UserEmailChanged;
use App\Listeners\Account\ToolEmailUpdater;
use App\Models\Course;
use App\Models\CourseTool;
use App\Models\Source;
use App\Models\User;
use GuzzleHttp\Client;

class ToolEmailUpdaterTest extends \TestCase
{
    /**
     * @test
     */
    public function successfully_updates_tools_when_user_changes_his_email()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);

        $course = factory(Course::class)->create();
        $user->courses()->attach($course);

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

        $user->email = 'changed@lurn.com';
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
                            'oldEmail' => $user->getOriginal()['email'],
                            'newEmail' => $user->email
                        ]
                    ]
                ]
            );

        $this->app->bind(Client::class, function ($app) use ($guzzleMock) {
            return $guzzleMock;
        });

        $event    = new UserEmailChanged($user);
        $listener = new ToolEmailUpdater();
        $listener->handle($event);
    }
}
