@auth
    <script>
        window.onboarding = {
            points: {{ user()->getMission()->getPoints() }},
            totalPoints: {{ user()->getMission()->getTotalPoints() }},
            activeScenario: null,
            afterOnboarding: '{{ session('after-onboarding') }}',
            scenarios: [
                @foreach (user()->getMission()->scenarios as $scenario)
                    {!! $scenario->display(user()) !!},
                @endforeach
            ],
            hasCourses: {{ user()->courses()->count() }},
            courses: [
                @if (count($freeCourses))
                    @foreach ($freeCourses as $i => $course)
                        {
                            id: {{ $course->id }},
                            url: "{!! route('course', $course->slug) !!}",
                            title: {!! json_encode($course->title) !!},
                            image: "{{ $course->getPrintableImageUrl() }}",
                            modules: "{!! $course->getCounters()->modules  !!}",
                            lessons: "{!! shorter_number($course->getCounters()->lessons) !!}",
                            students: "{!! shorter_number($course->getCounters()->students) !!}",
                        },
                        @break($i == 2)
                    @endforeach
                @endif
            ],
            profilePic: '{{ user()->getPrintableImageUrl() }}',
            referralLink: '{{ route('referral.index', ['code' => user()->getReferralCode()]) }}',
            @php $survey = App\Models\Survey::getOnboardingSurvey() @endphp
            survey: {!! $survey ? $survey->toJson() : 0 !!},
        };
    </script>

    <div id="onboarding" :class="[activeScenario ? 'visible' : '', activeScenario ? 'scenario-' + activeScenario.id : '']">
        <div class="wrapper" v-if="activeScenario">
            <div class="content">
                <i class="close icon" @click="onboarding.activeScenario = null"></i>
                <scenario></scenario>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ mix('js/onboarding/popup.js') }}"></script>
    <script type="text/javascript" src="https://static.addtoany.com/menu/page.js"></script>
    <script type="text/javascript">
        $(document).ready(function($){
            if (location.href.includes('onboardme')) {
                onboarding.activeScenario = onboarding.scenarios[0];
            }
        });
    </script>
@endauth