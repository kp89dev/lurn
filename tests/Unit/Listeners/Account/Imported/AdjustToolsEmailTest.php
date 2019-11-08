<?php
namespace Tests\Unit\Listeners\Account\Imported;

use App\Events\User\ImportedUserMerged;
use App\Listeners\Account\Imported\AdjustToolsEmail;
use App\Models\CourseTool;
use App\Models\ImportedUser;
use App\Models\Source;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class AdjustToolsEmailTest extends \TestCase
{
    /**
     * @test
     */
    public function successfully_updates_tool_email_when_user_has_access_to_tool()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);
        $userToMerge = factory(ImportedUser::class)->create([
            'email'      => 'imported@import.com',
            'connection' => 'inbox'
        ]);
        factory(ImportedUser::class)->create([
            'email'      => 'imported@import.com',
            'connection' => 'another'
        ]);

        //create a course relation
        DB::table('course_subscriptions')->insert([
            [
                'user_id'    => $userToMerge->user_id,
                'course_id'  => 117,
                'from_table' => 'inbox'
            ]
        ]);

        factory(CourseTool::class)->create([
            'course_id' => 117,
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

        $event    = new ImportedUserMerged($user, $userToMerge);
        $listener = new AdjustToolsEmail();
        $result = $listener->handle($event);

        static::assertNull($result);
    }
}
