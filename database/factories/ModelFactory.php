<?php

use App\Models\User;
use Carbon\Carbon;

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name'           => $faker->firstName . $faker->numberBetween(1, 100),
        'email'          => $faker->unique()->safeEmail,
        'password'       => $password ?: $password = bcrypt('secret'),
        'status'         => $faker->numberBetween(0, 1),
        'remember_token' => str_random(10),
        'created_at'     => Carbon::now(),
    ];
});

$factory->define(App\Models\ImportedUser::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'user_id'             => $faker->randomNumber(3),
        'connection'          => $faker->randomElement(['inbox', 'publishacademy']),
        'role_id'             => 2,
        'name'                => $faker->firstName,
        'email'               => $faker->unique()->safeEmail,
        'password'            => $password ?: $password = bcrypt('secret'),
        'md5password'         => md5($password),
        'salt'                => str_random(5),
        'description'         => $faker->text(),
        'status'              => 1,
        'infusion_order_id'   => $faker->numberBetween(10, 1000),
        'infusion_contact_id' => $faker->numberBetween(10, 1000),
        'timezone'            => 'America/New_York',
        'updated_at'          => $faker->dateTime,
        'created_at'          => $faker->dateTime,
        'expiry_date'         => $faker->dateTime,
        'unsubscribe'         => 0,
        'settings'            => '',
    ];
});

$factory->define(App\Models\CartReminder::class, function (Faker\Generator $faker) {
    return [];
});

$factory->define(App\Models\Course::class, function (Faker\Generator $faker) {
    $container = factory(\App\Models\CourseContainer::class)->create();

    return [
        'course_container_id' => $container->id,
        //'user_id' => $faker->numberBetween(1, 100),
        'title'               => $faker->firstName . $faker->numberBetween(1, 100),
        'description'         => $faker->text(),
        'snippet'             => $faker->text(),
        'slug'                => $faker->slug(5),
        //'image' => $faker->imageUrl(),
        'status'              => 1,
    ];
});

$factory->define(App\Models\CourseContainer::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->firstName . $faker->numberBetween(1, 100),
    ];
});

$factory->define(App\Models\Module::class, function (Faker\Generator $faker) {
    $type = $faker->randomElement(['Module', 'Link']);

    return [
        'title'       => $faker->firstName . $faker->numberBetween(1, 100),
        'description' => $faker->text(),
        'order'       => $faker->numberBetween(0, 100),
        'status'      => $faker->numberBetween(0, 1),
        'hidden'      => 0,
        'type'        => $type,
        'link'        => $type == 'Link' ? '/dashboard' : null,
    ];
});

$factory->define(App\Models\Lesson::class, function (Faker\Generator $faker) {
    $type = $faker->randomElement(['Lesson', 'Link']);

    return [
        'title'       => $faker->firstName . $faker->numberBetween(1, 100),
        'description' => $faker->text(),
        'type'        => $type,
        'order'       => $faker->numberBetween(0, 100),
        'status'      => $faker->numberBetween(0, 1),
        'link'        => $type == 'Link' ? '/dashboard' : null,
    ];
});

$factory->define(App\Models\Event::class, function (Faker\Generator $faker) {
    $start = Carbon::createFromTimeStamp($faker->dateTimeBetween('-30 days', '+30 days')->getTimestamp());
    $end = Carbon::createFromFormat('Y-m-d H:i:s', $start)->addHours(2);

    return [
        'course_container_id' => App\Models\CourseContainer::all()->random()->id,
        'title'               => implode(' ', $faker->words(4)),
        'description'         => implode(' ', $faker->words(12)),
        'start_date'          => $start->format('Y-m-d'),
        'end_date'            => $end->format('Y-m-d'),
        'start_time'          => $start->format('H:i:s'),
        'end_time'            => $end->format('H:i:s'),
        'all_day'             => $faker->boolean(5),
        'address'             => $faker->streetAddress(),
        'city'                => $faker->city(),
        'state'               => $faker->stateAbbr(),
        'postcode'            => $faker->randomNumber(5),
        'country'             => 'US',
        'created_at'          => $faker->dateTime->format('Y-m-d H:i:s'),
        'updated_at'          => $faker->dateTime->format('Y-m-d H:i:s'),
    ];
});

$factory->define(App\Models\TestResult::class, function (Faker\Generator $faker) {
    return [
        'test_id'     => $faker->randomNumber(3),
        'user_id'     => $faker->randomNumber(3),
        'result'      => $faker->randomNumber(1),
        'mark'        => $faker->randomFloat(2, 1, 10),
        'answer'      => "some json which is not json",
        'no_of_tries' => $faker->numberBetween(1, 5),
        'test_again'  => $faker->numberBetween(0, 1),
        'from_table'  => $faker->randomElement(['inbox', 'other', 'and_another_one']),
        'status'      => $faker->numberBetween(0, 1),
    ];
});

$factory->define(App\Models\LessonSubscriptions::class, function (Faker\Generator $faker) {
    return [
        'user_id'    => $faker->randomNumber(3),
        'lesson_id'  => $faker->randomNumber(1),
        'from_table' => $faker->randomElement(['inbox', 'other', 'and_another_one']),
    ];
});

$factory->define(App\Models\Category::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
    ];
});

$factory->define(App\Models\Faq::class, function (Faker\Generator $faker) {
    return [
        'question' => $faker->firstName . '?',
        'answer'   => $faker->randomAscii,
    ];
});

$factory->define(\App\Models\CourseInfusionsoft::class, function (Faker\Generator $faker) {
    return [
        'course_id'     => $faker->randomNumber(3),
        'price'         => $faker->numberBetween(50, 1500),
        'subscription'  => 0,
        'is_product_id' => $faker->randomDigitNotNull,
        'is_account'    => $faker->randomElement(['uf233', 'uv222', 'ae244']),
        'upsell'        => 0,
    ];
});

$factory->define(\App\Models\InfusionsoftContact::class, function (Faker\Generator $faker) {
    return [
        'user_id'       => $faker->randomNumber(3),
        'is_contact_id' => $faker->randomNumber(1),
        'is_account'    => $faker->randomElement(['uf233', 'uv222', 'ae244']),
    ];
});

$factory->define(\App\Models\InfusionsoftToken::class, function (Faker\Generator $faker) {
    return [
        'account'       => $faker->randomLetter . $faker->randomLetter . $faker->numberBetween(100, 999),
        'access_token'  => $faker->sha1,
        'refresh_token' => $faker->sha1,
        'end_of_life'   => strtotime(Carbon::now()->addHour(1)->toDateTimeString()),
    ];
});

$factory->define(\App\Models\News::class, function (Faker\Generator $faker) {
    return [
        'title'   => $faker->sentence,
        'content' => $faker->paragraph(3),
    ];
});

$factory->define(\App\Models\UserSetting::class, function (Faker\Generator $faker) {
    return [
        'receive_updates' => $faker->numberBetween(0, 1),
        'timezone'        => $faker->timezone,
        'image'           => str_slug($faker->sentence),
    ];
});

$factory->define(\App\Models\Badge::class, function (Faker\Generator $faker) {
    $course = factory(\App\Models\Course::class)->create();

    return [
        'course_id' => $course->id,
        'title'     => $faker->sentence,
        'content'   => $faker->paragraph,
        'image'     => str_slug($faker->sentence),
        'status'    => $faker->numberBetween(0, 1),
    ];
});

$factory->define(\App\Models\Badge\BadgeRequest::class, function (Faker\Generator $faker) {
    return [
        'badge_id' => $faker->numberBetween(1, 100),
        'user_id'  => $faker->numberBetween(1, 100),
        'comment'  => $faker->paragraph,
        'status'   => $faker->numberBetween(0, 1),
    ];
});

$factory->define(\App\Models\Sendlane::class, function (Faker\Generator $faker) {
    return [
        'email'     => $faker->email,
        'subdomain' => $faker->domainWord,
        'api'       => $faker->md5,
        'hash'      => $faker->md5,
    ];
});

$factory->define(\App\Models\CourseSendlane::class, function (Faker\Generator $faker) {
    return [
        'course_id'   => $faker->numberBetween(1, 100),
        'sendlane_id' => $faker->numberBetween(1, 100),
        'list_id'     => $faker->numberBetween(1, 100),
        'list_name'   => $faker->word,
    ];
});

$factory->define(\App\Models\CourseSubscriptions::class, function (Faker\Generator $faker) {
    return [
        'user_id'      => $faker->numberBetween(1, 100),
        'course_id'    => $faker->numberBetween(1, 100),
        'status'       => $faker->numberBetween(0, 1),
        'invoice_id'   => $faker->word,
        'paid_at'      => $faker->time('Y-m-d H:i:s'),
        'cancelled_at' => $faker->time('Y-m-d H:i:s'),
    ];
});

$factory->define(\App\Models\UserLogin::class, function (Faker\Generator $faker) {
    return [
        'user_id'    => 1,
        'ip'         => $faker->ipv4,
        'user_agent' => $faker->userAgent,
        'city'       => $faker->city,
        'country'    => $faker->country,
        'regionName' => $faker->word,
        'timezone'   => $faker->timezone,
        'successful' => $faker->boolean(),
    ];
});

$factory->define(App\Models\Test::class, function (Faker\Generator $faker) {
    $course = factory(App\Models\Course::class)->create();

    return [
        'title'           => implode(' ', $faker->words(4)),
        'course_id'       => $course->id, //$course->id,
        'after_lesson_id' => $faker->randomDigit, //$lesson->id,
        'status'          => $faker->numberBetween(0, 1),
    ];
});

$factory->define(App\Models\TestQuestion::class, function (Faker\Generator $faker) {

    $test = App\Models\Test::all()->random();

    $created_at = $faker->dateTimeThisYear();
    $updated_at = $faker->dateTimeThisYear();

    return [
        'test_id'       => $test->id,
        'title'         => implode(' ', $faker->words(5)) . '?',
        'order'         => $faker->numberBetween(0, 100),
        'status'        => $faker->numberBetween(0, 1),
        'created_at'    => $created_at->format('Y-m-d H:i:s'),
        'updated_at'    => $updated_at->format('Y-m-d H:i:s'),
        'question_type' => $faker->randomElement(['Radio', 'Checkbox']),
    ];
});

$factory->define(App\Models\TestQuestionAnswer::class, function (Faker\Generator $faker) {

    $test_question = App\Models\TestQuestion::all()->random();

    $created_at = $faker->dateTimeThisYear();
    $updated_at = $faker->dateTimeThisYear();

    return [
        'title'       => implode(' ', $faker->words(7)),
        'question_id' => $test_question->id,
        'order'       => $faker->numberBetween(0, 100),
        'status'      => $faker->numberBetween(0, 1),
        'created_at'  => $created_at->format('Y-m-d H:i:s'),
        'updated_at'  => $updated_at->format('Y-m-d H:i:s'),
        'is_answer'   => $faker->numberBetween(0, 1),
    ];
});

$factory->define(App\Models\Feedback::class, function (Faker\Generator $faker) {
    return [
        'user_id'  => \App\Models\User::first()->id,
        'grade'    => rand(1, 10),
        'feedback' => implode(' ', $faker->words(10)),
    ];
});

$factory->define(App\Models\CourseTool::class, function (Faker\Generator $faker) {
    return [
        'course_id' => $faker->randomDigit,
        'tool_name' => $faker->word,
    ];
});

$factory->define(App\Models\Source::class, function (Faker\Generator $faker) {
    return [
        'url'         => $faker->domainName,
        'ip'          => $faker->ipv4,
        'token'       => $faker->sha256,
        'access_word' => $faker->word,
        'secure'      => 0,
    ];
});

$factory->define(App\Models\SourceToken::class, function (Faker\Generator $faker) {
    return [
        'user_id'   => $faker->randomNumber(4),
        'source_id' => $faker->randomNumber(2),
        'token'     => $faker->sha256,
        'used'      => 0,
    ];
});

$factory->define(App\Models\CourseUpsell::class, function (Faker\Generator $faker) {
    return [
        'course_infusionsoft_id' => $faker->randomDigit,
        'succeeds_course_id'     => $faker->randomDigit,
        'html'                   => $faker->sentence(5),
        'css'                    => $faker->sentence(5),
        'status'                 => 1,
    ];
});

$factory->define(App\Models\CourseVanillaForum::class, function (Faker\Generator $faker) {
    return [
        'course_id'     => $faker->randomDigit,
        'client_id'     => $faker->randomNumber(5) . $faker->randomNumber(5),
        'client_secret' => $faker->sha256,
        'url'           => $faker->domainName,
        'forum_rules'   => $faker->randomHtml(),
    ];
});

$factory->define(App\Models\CourseUpsellToken::class, function (Faker\Generator $faker) {
    return [
        'course_upsell_id' => $faker->randomDigit,
        'token'            => $faker->word,
        'used'             => 0,
    ];
});

$factory->define(App\Models\PushNotifications::class, function (Faker\Generator $faker) {
    return [
        'admin_title' => $faker->sentence(6),
        'start_date'  => $faker->dateTimeThisYear()->format('Y-m-d'),
        'end_date'    => $faker->dateTimeThisYear()->format('Y-m-d'),
        'start_time'  => $faker->dateTime()->format('H:i:s'),
        'end_time'    => $faker->dateTime()->format('H:i:s'),
        'timezone'    => $faker->randomDigitNotNull(),
        'start_utc'   => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        'end_utc'     => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        'content'     => $faker->word,
        'cta_type'    => $faker->randomElement(['Internal', 'External']),
    ];
});

$factory->define(App\Models\CourseBonus::class, function (Faker\Generator $faker) {
    return [
        'course_id'       => $faker->randomDigit,
        'bonus_course_id' => $faker->randomDigit,
    ];
});

$factory->define(App\Models\Ad::class, function (Faker\Generator $faker) {
    return [
        'admin_title' => $faker->sentence(6),
        'image'       => str_slug($faker->sentence),
        'hover_image' => str_slug($faker->sentence),
        'link'        => $faker->domainName,
        'location'    => $faker->randomElement(['dashboard', 'home', 'enroll-complete']),
        'position'    => $faker->randomElement(['first', 'second', 'third']),
    ];
});

$factory->define(App\Models\NicheDetective\Niche::class, function (Faker\Generator $faker) {
    return [
        'category_id'    => $faker->randomDigit(),
        'label'          => $faker->word(),
        'total_products' => $faker->randomNumber(2),
        'audience_size'  => $faker->randomDigit(),
    ];
});

$factory->define(App\Models\NicheDetective\Category::class, function (Faker\Generator $faker) {
    return [
        'label'        => $faker->word,
        'main_offer'   => $faker->randomFloat(2, 1, 100),
        'upsell1'      => $faker->randomDigit,
        'upsell2'      => $faker->randomDigit,
        'image'        => str_slug($faker->sentence),
        'created_at'   => $faker->dateTime,
        'updated_date' => $faker->dateTime,
    ];
});

$factory->define(App\Models\EmailStatus::class, function (Faker\Generator $faker) {
    return [
        'aws_id'         => $faker->uuid,
        'workflow_id'    => $faker->randomDigitNotNull,
        'step'           => $faker->randomDigitNotNull,
        'status'         => $faker->randomElement(['0', '50', '100', '200', '25']),
        'last_timestamp' => $faker->dateTime,
        'updated_at'     => $faker->dateTime,
        'created_at'     => $faker->dateTime,
    ];
});

$factory->define(App\Models\Template::class, function (Faker\Generator $faker) {
    return [
        'user_id'    => $faker->randomNumber(4),
        'title'      => $faker->sentence,
        'subject'    => $faker->sentence,
        'content'    => $faker->text(),
        'updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
    ];
});
$factory->define(App\Models\Certificate::class, function (Faker\Generator $faker) {
    return [
        'course_id'    => $faker->randomDigit,
        'title'        => $faker->sentence,
        'logo'         => $faker->imageUrl(),
        'body'         => $faker->randomHtml(),
        'logo_style'   => $faker->word,
        'border_style' => $faker->word,
    ];
});
$factory->define(App\Models\UserCertificate::class, function (Faker\Generator $faker) {
    $user = factory(App\Models\User::class)->create();
    $test = factory(App\Models\Test::class)->create();

    return [
        'user_id'                => $user->id,
        'test_id'                => $test->id,
        'certificate_title'      => $faker->sentence,
        'certificate_logo'       => $faker->imageUrl(),
        'certificate_body'       => $faker->randomHtml(),
        'issued'                 => $faker->date(),
        'certificate_logo_style' => $faker->word,
        'certificate_border'     => $faker->word,
    ];
});

$factory->define(App\Models\CourseVanillaForum::class, function (Faker\Generator $faker) {
    return [
        'course_id'     => $faker->randomDigit,
        'client_id'     => $faker->uuid,
        'client_secret' => $faker->uuid,
        'url'           => $faker->url,
    ];
});

$factory->define(App\Models\UserCourse::class, function (Faker\Generator $faker) {
    return [
        'course_id' => $faker->randomDigit,
        'user_id'   => $faker->randomNumber(4),
    ];
});

$factory->define(App\Models\Labels::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
    ];
});
$factory->define(App\Models\CourseFeature::class, function (Faker\Generator $faker) {
    return [
        'course_id'     => $faker->randomNumber(1),
        'order'         => $faker->randomNumber(1),
        'free_bootcamp' => $faker->randomNumber(1),
    ];
});

$factory->define(App\Models\UserActivities::class, function (Faker\Generator $faker) {
    return [
        'user_id'       => $faker->randomNumber(2),
        'activity_type' => $faker->randomDigit,
        'activity_time' => $faker->dateTime,
        'activity_text' => $faker->text(),
    ];
});

$factory->define(App\Models\UserRole::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->words(4, true),
    ];
});

$factory->define(App\Models\Workflows\UserWorkflow::class, function (Faker\Generator $faker) {
    return [
        'user_id'     => $faker->randomNumber(2),
        'workflow_id' => $faker->randomNumber(2),
        'hit_goal'    => $faker->boolean(),
        'next_step'   => $faker->randomNumber(2),
    ];
});

$factory->define(App\Models\Workflows\Workflow::class, function (Faker\Generator $faker) {
    return [
        'name'     => $faker->sentence(),
        'enroll'   => $faker->words(5),
        'goal'     => $faker->words(5),
        'workflow' => $faker->words(5),
        'status'   => (int) $faker->boolean(),
    ];
});

$factory->define(App\Models\UserSetting::class, function (Faker\Generator $faker) {
    return [
        'user_id'         => $faker->randomNumber(2),
        'receive_updates' => 0,
    ];
});

$factory->define(\App\Models\Tracker\Identity::class, function (Faker\Generator $faker) {
    return [
        'user_id'    => $faker->randomNumber(2),
        'visitor_id' => $faker->word,
        'email'      => $faker->email,
    ];
});

$factory->define(\App\Models\Tracker\Campaign::class, function (Faker\Generator $faker) {
    return [
        'hash' => $faker->password,
    ];
});

$factory->define(\App\Models\Tracker\Visit::class, function (Faker\Generator $faker) {
    return [
        'visitor_id' => $faker->word,
    ];
});

$factory->define(\App\Models\InfusionsoftMerchantId::class, function (Faker\Generator $faker) {
    return [
        'account' => 'ig302',
        'ids'     => [1, 2, 3],
    ];
});

$factory->define(\App\Models\SourceEmail::class, function (Faker\Generator $faker) {
    return [
        'user_id'   => $faker->randomDigitNotNull,
        'source_id' => 200,
        'email'     => $faker->email,
    ];
});

$factory->define(\App\Models\Survey::class, function (\Faker\Generator $faker) {
    $startDate = Carbon::now();
    $endDate = $startDate->addDays(mt_rand(30, 70));
    $surveyType = factory(App\Models\SurveyType::class)->create();
    $surveyTriggerType = factory(App\Models\SurveyTriggerType::class)->create();
    $surveyQuestionOrdering = factory(App\Models\SurveyQuestionOrdering::class)->create();

    return [
        'title'                         => ucwords($faker->words(mt_rand(2, 4), true)),
        'description'                   => $faker->sentence,
        'survey_type'                   => array_random(array_keys(\App\Models\Survey::$types)),
        'enabled'                       => mt_rand(0, 1),
        'require_login'                 => mt_rand(0, 1),
        'start_date'                    => $startDate,
        'end_date'                      => $endDate,
        'survey_type_id'                => $surveyType->id,
        'survey_trigger_type_id'        => $surveyTriggerType->id,
        'survey_question_ordering_id'   => $surveyQuestionOrdering->id,
    ];
});

$factory->define(\App\Models\SurveyType::class, function (\Faker\Generator $faker) {
    $displayName = ucwords($faker->words(2, true));
    $key = str_replace(' ', '-', strtolower($displayName));

    return [
        'key' => $key,
        'display_name' => $displayName,
        'description' => $faker->sentence,
    ];
});

$factory->define(\App\Models\SurveyTriggerType::class, function (\Faker\Generator $faker) {
    $displayName = ucwords($faker->words(2, true));
    $key = str_replace(' ', '-', strtolower($displayName));

    return [
        'key' => $key,
        'display_name' => $displayName,
        'description' => $faker->sentence,
    ];
});

$factory->define(\App\Models\SurveyQuestionOrdering::class, function (\Faker\Generator $faker) {
    $displayName = ucwords($faker->words(2, true));
    $key = str_replace(' ', '-', strtolower($displayName));

    return [
        'key' => $key,
        'display_name' => $displayName,
        'description' => $faker->sentence,
    ];
});

$factory->define(\App\Models\SurveyQuestion::class, function (\Faker\Generator $faker) {
    $survey = factory(\App\Models\Survey::class)->create();

    return [
        'survey_id'     => $survey->id,
        'title'         => ucwords($faker->words(mt_rand(2, 4), true)),
        'answer_choice' => array_random(['single', 'multiple']),
        'order'         => $faker->randomNumber(3),
        'enabled'       => mt_rand(0, 1),
    ];
});

$factory->define(\App\Models\SurveyAnswer::class, function (\Faker\Generator $faker) {
    $survey = factory(\App\Models\Survey::class)->create();
    $surveyQuestion = factory(\App\Models\SurveyQuestion::class)->create();

    return [
        'survey_id'   => $survey->id,
        'question_id' => $surveyQuestion->id,
        'title'       => ucwords($faker->words(mt_rand(2, 4), true)),
        'order'       => $faker->randomNumber(3),
        'enabled'     => mt_rand(0, 1),
    ];
});

$factory->define(\App\Models\Bonus::class, function (\Faker\Generator $faker) {
    return [
        'points_required' => $faker->randomNumber(),
    ];
});
