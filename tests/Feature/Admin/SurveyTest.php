<?php

namespace Tests\Admin;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionOrdering;
use App\Models\SurveyTriggerType;
use App\Models\SurveyType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Mockery;

class SurveyTest extends \AdminLoggedInTestCase
{

    use DatabaseTransactions;

    /**
     * @test
     */
    public function survey_test_index()
    {
        $surveys = factory(Survey::class, 5)->create();

        $this->followRedirects = true;
        $response = $this->get(route('surveys.index'));

        foreach ($surveys as $survey) {
            $this->assertContains($survey->title, $response->getContent());
        }
    }

    /**
     * @test
     */
    public function survey_test_show()
    {
        $survey = factory(Survey::class)->create();

        $this->followRedirects = true;
        $response = $this->get(route('surveys.show', [$survey->id]));

        $this->assertContains($survey->title, $response->getContent());
    }

    /**
     * @test
     */
    public function survey_test_create()
    {
        $this->followRedirects = true;
        $response = $this->get(route('surveys.create'));
        $response->assertSeeText('Surveys');
    }

    /**
     * @test
     */
    public function survey_test_store()
    {
        /** @var Carbon $startDate */
        $startDate = Carbon::now();

        /** @var Carbon $endDate */
        $endDate = $startDate->addDays(mt_rand(30, 70));

        $title = ucwords($this->faker->words(mt_rand(2, 4), true));

        $questions = convert_to_collection([
            'questions' => [
                [
                    'title'         => ucwords($this->faker->words(mt_rand(2, 4), true)),
                    'order'         => $this->faker->randomNumber(3),
                    'answer_choice' => array_random(['single', 'multiple']),
                    'enabled'       => mt_rand(0, 1),
                    'answers'       => [
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                    ],
                ],
                [
                    'title'         => ucwords($this->faker->words(mt_rand(2, 4), true)),
                    'order'         => $this->faker->randomNumber(3),
                    'answer_choice' => array_random(['single', 'multiple']),
                    'enabled'       => mt_rand(0, 1),
                    'answers'       => [
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                    ],
                ],
                [
                    'title'         => ucwords($this->faker->words(mt_rand(2, 4), true)),
                    'order'         => $this->faker->randomNumber(3),
                    'answer_choice' => array_random(['single', 'multiple']),
                    'enabled'       => mt_rand(0, 1),
                    'answers'       => [
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                    ],
                ],
            ],
        ]);

        $parameters = [
            'title'                         => $title,
            'description'                   => $this->faker->sentence,
            'survey_type'                   => array_random(array_keys(Survey::$types)),
            'enabled'                       => mt_rand(0, 1),
            'require_login'                 => mt_rand(0, 1),
            'start_date'                    => $startDate->toDateString(),
            'end_date'                      => $endDate->toDateString(),
            'question_data'                 => json_encode($questions),
            'survey_type_id'                => factory(SurveyType::class)->create()->id,
            'survey_trigger_type_id'        => factory(SurveyTriggerType::class)->create()->id,
            'survey_question_ordering_id'   => factory(SurveyQuestionOrdering::class)->create()->id,
        ];

        $this->followRedirects = true;
        $response = $this->post(route('surveys.store'), $parameters);

        $this->assertContains($title, $response->getContent());
    }

    /**
     * @test
     */
    public function survey_test_edit()
    {
        $survey = factory(Survey::class)->create();

        $this->followRedirects = true;
        $response = $this->get(route('surveys.edit', [$survey->id]));

        $this->assertContains($survey->title, $response->getContent());
    }

    /**
     * @test
     */
    public function survey_test_update()
    {
        /** @var Survey $survey */
        $survey = factory(Survey::class)->create();

        /** @var Collection $surveyQuestions */
        $surveyQuestions = factory(SurveyQuestion::class, 5)->create(['survey_id' => $survey->id]);

        foreach ($surveyQuestions as $surveyQuestion) {
            factory(SurveyAnswer::class, 5)->create([
                'survey_id'   => $survey->id,
                'question_id' => $surveyQuestion->id,
            ]);
        }

        $surveyQuestions->load(['answers']);

        /** @var Survey $survey2 */
        $survey2 = factory(Survey::class)->create();

        /** @var Collection $surveyQuestions2 */
        $surveyQuestions2 = factory(SurveyQuestion::class, 5)->create(['survey_id' => $survey2->id]);

        foreach ($surveyQuestions2 as $surveyQuestion2) {
            factory(SurveyAnswer::class, 5)->create([
                'survey_id'   => $survey2->id,
                'question_id' => $surveyQuestion2->id,
            ]);
        }

        $extraQuestions = [];

        /** @var SurveyQuestion $surveyQuestion2 */
        foreach ($surveyQuestions2 as $surveyQuestion2) {
            $answers = collect($surveyQuestion2->answers->toArray())->except('id');
            array_push($extraQuestions, array_merge(
                $surveyQuestion2->toArray(),
                ['answers' => $answers->toArray(),]
            ));
        }

        /** @var Collection $extraQuestionsCollection */
        $extraQuestionsCollection = convert_to_collection($extraQuestions);

        /** @var Carbon $startDate */
        $startDate = Carbon::now();

        /** @var Carbon $endDate */
        $endDate = $startDate->addDays(mt_rand(30, 70));

        $title = ucwords($this->faker->words(mt_rand(2, 4), true));

        $questions = convert_to_collection([
            'questions' => array_merge(collect($extraQuestions)->random(2)->toArray(), [
                [
                    'title'         => ucwords($this->faker->words(mt_rand(2, 4), true)),
                    'order'         => $this->faker->randomNumber(3),
                    'answer_choice' => array_random(['single', 'multiple']),
                    'enabled'       => mt_rand(0, 1),
                    'answers'       => array_merge(
                        $extraQuestionsCollection->random(1)->first()->get('answers')->toArray(),
                        [
                            [
                                'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                                'order'   => $this->faker->randomNumber(3),
                                'enabled' => mt_rand(0, 1),
                            ],
                            [
                                'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                                'order'   => $this->faker->randomNumber(3),
                                'enabled' => mt_rand(0, 1),
                            ],
                            [
                                'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                                'order'   => $this->faker->randomNumber(3),
                                'enabled' => mt_rand(0, 1),
                            ],
                        ]
                    ),
                ],
                [
                    'title'         => ucwords($this->faker->words(mt_rand(2, 4), true)),
                    'order'         => $this->faker->randomNumber(3),
                    'answer_choice' => array_random(['single', 'multiple']),
                    'enabled'       => mt_rand(0, 1),
                    'answers'       => [
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                    ],
                ],
                [
                    'title'         => ucwords($this->faker->words(mt_rand(2, 4), true)),
                    'order'         => $this->faker->randomNumber(3),
                    'answer_choice' => array_random(['single', 'multiple']),
                    'enabled'       => mt_rand(0, 1),
                    'answers'       => [
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                        [
                            'title'   => ucwords($this->faker->words(mt_rand(2, 4), true)),
                            'order'   => $this->faker->randomNumber(3),
                            'enabled' => mt_rand(0, 1),
                        ],
                    ],
                ],
            ]),
            'delete'    => [
                'questions' => $surveyQuestions->random(2)->pluck('id')->toArray(),
                'answers'   => $surveyQuestions->random(1)->first()->answers->random(2)->pluck('id')->toArray(),
            ],
        ]);

        $parameters = [
            'title'                         => $title,
            'description'                   => $this->faker->sentence,
            'survey_type'                   => array_random(array_keys(Survey::$types)),
            'enabled'                       => mt_rand(0, 1),
            'require_login'                 => mt_rand(0, 1),
            'start_date'                    => $startDate->toDateString(),
            'end_date'                      => $endDate->toDateString(),
            'question_data'                 => json_encode($questions),
            'survey_type_id'                => factory(SurveyType::class)->create()->id,
            'survey_trigger_type_id'        => factory(SurveyTriggerType::class)->create()->id,
            'survey_question_ordering_id'   => factory(SurveyQuestionOrdering::class)->create()->id,
        ];

        $this->followRedirects = true;
        $response = $this->put(route('surveys.update', [$survey->id]), $parameters);

        $this->assertContains('Surveys <small>list</small>', $response->getContent());
    }

    /**
     * @test
     */
    public function survey_test_destroy()
    {
        $survey = factory(Survey::class)->create();

        $this->followRedirects = true;
        $this->delete(route('surveys.destroy', [$survey->id]));

        $this->assertDatabaseMissing('surveys', ['id' => $survey->id]);
    }

    /**
     * @test
     */
    public function survey_test_stats()
    {
        $survey = factory(Survey::class)->create();

        $this->followRedirects = true;
        $response = $this->get(route('surveys.stats', [$survey->id]));

        $this->assertEquals('', $response->getContent());
    }
}