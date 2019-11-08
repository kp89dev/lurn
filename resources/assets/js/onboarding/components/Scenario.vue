<template v-cloak>
    <div class="ui form">
        <div v-show="scenario.id === 1">
            <h1>Hey {{ user.name }} - Welcome To Lurn Nation</h1>
            <p>Follow These 5 Simple On-boarding Steps &amp; <strong class="med">Earn Up To {{ onboarding.totalPoints }} Points!</strong></p>
        </div>

        <div class="numberedsteps">
            <div class="stepcontainer">
                <div :class="{ active: scenario.id === 1 }">
                    <span>Profile Pic</span>
                </div>
                <div :class="{ active: scenario.id === 2 }" v-if="onboarding.survey">
                    <span>About You</span>
                </div>
                <div :class="{ active: scenario.id === 3 }" v-if="! onboarding.hasCourses">
                    <span>Enroll in a Course</span>
                </div>
                <div :class="{ active: scenario.id === 4 }">
                    <span>Share on Social</span>
                </div>
                <div :class="{ active: scenario.id === 5 }">
                    <span>Recruit Friends</span>
                </div>
            </div>
        </div>

        <div class="scenario profile-pic-scenario" v-if="scenario.id === 1">
            <div class="obstep first">
                <p><strong>ADD A PICTURE TO YOUR PROFILE</strong> <span>(EARN 100 POINTS)</span></p>

                <div class="ui grid obstep">
                    <div class="profilepic six wide column right aligned" @click="triggerUpload()">
                        <form id="upload-profile-picture">
                            <a class="profile">
                                <img :src="onboarding.profilePic">
                                <a class="blue-btn">Edit</a>
                            </a>
                            <input id="hiddenupload" type="file" name="file" accept="image/*" style="display:none;">
                        </form>
                    </div>

                    <div class="instructions first four wide column">
                        <p>Personalize Your Account with a Profile Picture.</p>
                        <p><em>Pro Tip: Square Images Work Best!</em></p>
                        <p>Click <strong>Next Step</strong> to Continue&hellip;</p>
                    </div>

                    <div class="six wide column profile-co">
                        <img src="/images/onboarding/profile-points.png">
                    </div>
                </div>
            </div>
        </div>

        <div class="scenario eval-scenario" v-if="scenario.id === 2">
            <div class="obstep two">
                <div class="ui negative message" v-if="incompleteSurvey">
                    Please answer all the questions and try again!
                </div>

                <form class="fancy">
                    <p>
                        <strong>ANSWER {{ onboarding.survey.questions.length }} QUESTIONS ABOUT YOURSELF</strong>
                        <span>(EARN 100 POINTS)</span>
                    </p>

                    <div class="ui two columns grid">
                        <div class="wide column" v-for="(question, index) in onboarding.survey.questions">
                            <div class="question">
                                <p>{{ index + 1}}. {{ question.title }}</p>

                                <div class="ui checkbox answer" :class="{
                                         radio: question.answer_choice === 'single',
                                         checked: selectedAnswer(answer, question),
                                     }"
                                     v-for="answer in question.answers" @click="toggleAnswer(answer, question)">
                                    <input :type="question.answer_choice === 'single' ? 'radio' : 'checkbox'"
                                           :checked="selectedAnswer(answer, question)">
                                    <label v-text="answer.title"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="scenario courses-scenario" v-if="scenario.id === 3">
            <div class="obstep third">
                <div class="ui negative message" v-if="firstCourseError" v-text="firstCourseError"></div>

                <form>
                    <p><strong>ENROLL IN YOUR FIRST FREE COURSE</strong> (EARN 100 POINTS)</p>

                    <div class="grid-helper">
                        <div>
                            <div class="courses ui three columns grid center aligned">
                                <div class="column" v-for="course in onboarding.courses">
                                    <a href="#" class="course" :class="{ selected: selectedCourse.id === course.id }"
                                       @click="selectCourse(course, $event)">
                                        <div class="photo">
                                            <img :src="course.image">
                                            <div class="check-icon">
                                                <i class="check icon"></i>
                                            </div>
                                        </div>
                                        <div class="content-parent">
                                            <div class="padded">
                                                <h3>{{ course.title }}</h3>
                                            </div>
                                            <div class="meta">
                                                <div class="ui three columns grid">
                                                    <div class="wide column">
                                                        <div class="value">{{ course.modules }}</div>
                                                        <div class="lbl">Modules</div>
                                                    </div>
                                                    <div class="wide column">
                                                        <div class="value">{{ course.lessons }}</div>
                                                        <div class="lbl">Lessons</div>
                                                    </div>
                                                    <div class="wide column">
                                                        <div class="value">{{ course.students }}</div>
                                                        <div class="lbl">Students</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <img src="/images/onboarding/course-points.png">
                        </div>
                    </div>

                    <div class="center aligned mt-30">
                        <div class="greyed" style="display: inline-block">
                            <p><strong><em>Looking for something else?</em></strong> We've got TONS more options
                                inside the Classroom.</p>
                            <p>Select <a href="/classroom" class="green" target="_blank">Browse Courses</a> to find the right one for you.</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="scenario share-scenario" v-if="scenario.id === 4">
            <div class="obstep fourth">
                <div class="ui negative message" v-if="sharingError">
                    <i class="warning sign icon"></i>
                    <strong>Oops!</strong>
                    {{ sharingError }}
                </div>
                <p><strong>SHARE LURN NATION </strong> (300 POINTS <u>EACH</u>)</p>

                <div class="ui grid">
                    <div class="nine wide column">
                        <h1>Spread The Love!</h1>
                        <h2>Ready to Earn LOTS of Extra Points?</h2>
                        <p>Help us spread the word to your family, friends &amp; followers!<br>
                            Just Click each social media button below &amp; you'll get an extra<br>
                            <span class="emerald underlined">300 Points PER Platform</span>. That's it! We'll
                            take care of the rest&hellip;</p>

                        <div class="ss-buttons a2a_kit a2a_kit_size_32 a2a_default_style ui four columns grid">
                            <div class="column">
                                <a class="ui fluid facebook button a2a_button_facebook" @click="socialShare('fb')">
                                    <i class="facebook icon"></i>
                                    <span>Facebook</span>
                                </a>
                                +300 Points
                            </div>
                            <div class="column">
                                <a class="ui fluid twitter button a2a_button_twitter" @click="socialShare('tw')">
                                    <i class="twitter icon"></i>
                                    <span>Twitter</span>
                                </a>
                                +300 Points
                            </div>
                            <div class="column">
                                <a class="ui fluid instagram button a2a_button_instagram disabled">
                                    <i class="instagram icon"></i>
                                    <span>Instagram</span>
                                </a>
                                <em>Coming Soon</em>
                            </div>
                            <div class="column">
                                <a class="ui fluid linkedin button a2a_button_linkedin disabled">
                                    <i class="linkedin icon"></i>
                                    <span>LinkedIn</span>
                                </a>
                                <em>Coming Soon</em>
                            </div>
                        </div>
                    </div>
                    <div class="seven wide column badge">
                        <img src="/images/onboarding/social-points.png">
                    </div>
                </div>
            </div>
        </div>

        <div class="scenario refer-friends-scenario" v-if="scenario.id === 5">
            <div class="ui negative message" v-if="invitationsSendError">
                <i class="warning sign icon"></i>
                <strong>Oops!</strong>
                {{ invitationsSendError }}
            </div>

            <div class="ui positive message" v-if="invitationsSent">
                Congratulations! The invitation has been sent.
            </div>

            <div class="obstep fifth">
                <div class="ui grid">
                    <div class="twelve wide column">
                        <p><strong>RECRUIT YOUR FRIENDS </strong> (300 POINTS <u>EACH</u>)</p>
                        <h1>Send Out Some Email Invites!</h1>
                        <h2>
                            For EACH New Member Who Signs Up, You'll Be
                            <span class="emerald underlined">Awarded 300 Points!</span>
                        </h2>
                        <div class="greyed">
                            <p>We'll Send An Email Invite For You:</p>
                            <textarea placeholder="Invite Up To 5 Emails Using Commas OR Line Breaks To Separate
E.g.:
john.doe@gmail.com
jane.doe@gmail.com"></textarea>
                            <p>
                                Or You Can Send This Link:
                                <span class="ui right action input">
                                    <input type="text" :value="onboarding.referralLink">
                                    <button class="blue-btn">Copy</button>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="four wide column badge"></div>
                </div>
            </div>
        </div>

        <div class="center aligned nextbtn">
            <div>
                <div class="pts" v-if="onboarding.points">
                    <div class="label">
                        Points Earned
                    </div>
                    <div class="points">
                        {{ onboarding.points }}
                    </div>
                </div>
            </div>

            <div class="ui secondary button massive" @click="nextScenario()" :class="{ loading: loadingNextScenario }">
                <i class="check icon"></i>
                <strong>Save &amp; Continue</strong>
                <span v-if="lastScenario">to My Course</span>
                <span v-else="lastScenario">to the Next Step</span>
            </div>

            <div>
                <img class="navi-badge" src="/images/onboarding/eval-points.png" v-if="scenario.id === 2">
            </div>
        </div>
        <div class="center aligned">
            <a href="#" class="grey" @click="nextScenario(true)">
                Skip To Next Step &mdash; I'll Do This Later
            </a>
        </div>
    </div>
</template>

<script>
    import _reject from 'lodash/reject'

    export default {
        data () {
            return {
                user,
                onboarding,

                referrals: [
                    { name: null, email: null, },
                    { name: null, email: null, },
                    { name: null, email: null, },
                ],

                picture: null,
                pictureUploaded: false,
                pictureUploadError: false,
                loadingNextScenario: false,

                invitationsSent: false,
                sendingInvitations: false,
                invitationsSendError: false,

                lastScenario: false,
                selectedCourse: {},
                firstCourseError: false,

                incompleteSurvey: false,
            };
        },

        computed: {
            scenario () {
                return this.onboarding.activeScenario;
            },

            onboardingScenarios () {
                return _reject(this.onboarding.scenarios, scenario => {
                    if (scenario.id === 2) {
                        return ! this.onboarding.survey;
                    } else if (scenario.id === 3) {
                        return this.onboarding.hasCourses;
                    }
                });
            },
        },

        methods: {
            nextScenario (skipStep) {
                let handleNextScenario = () => {
                    this.loadingNextScenario = false;

                    let found = false, scenario;

                    for (let i = 0; i < this.onboardingScenarios.length; i++) {
                        scenario = this.onboardingScenarios[i];

                        if (found) {
                            this.onboarding.activeScenario = scenario;
                            this.lastScenario = i === this.onboarding.scenarios.length - 1;

                            setTimeout(() => {
                                if (scenario.id === 4) {
                                    this.setupA2a();
                                }
                            }, 0);

                            return;
                        }

                        if (scenario.id === this.scenario.id) {
                            found = true;
                        }
                    }

                    if (this.onboarding.afterOnboarding) {
                        window.location = this.onboarding.afterOnboarding;
                        return;
                    }

                    this.onboarding.activeScenario = null;
                };

                this.loadingNextScenario = true;

                if (skipStep) {
                    return handleNextScenario();
                }

                // Handle the profile picture scenario.
                if (this.scenario.id === 1) {
                    this.uploadPicture(() => {
                        if (this.pictureUploaded) {
                            handleNextScenario();
                        }
                    });
                }
                // Handle the survey.
                else if (this.scenario.id === 2) {
                    this.saveSurvey(handleNextScenario);
                }
                // Handle the survey.
                else if (this.scenario.id === 3) {
                    this.saveFirstCourse(handleNextScenario);
                }
                // For all the other steps.
                else {
                    handleNextScenario();
                }
            },

            triggerUpload () {
                $('#hiddenupload').trigger('click');
            },

            uploadPicture (cb) {
                this.pictureUploaded = false;
                this.pictureUploadError = false;

                $.ajax({
                    type: 'POST',
                    url: '/api/onboarding-picture',
                    data: new FormData($('#upload-profile-picture').get(0)),
                    cache: false,
                    contentType: false,
                    processData: false,
                }).done(res => {
                    this.pictureUploaded = true;

                    if (! this.scenario.done) {
                        this.onboarding.points += this.scenario.points;
                        this.scenario.done = true;
                    }

                    onboarding.profilePic = res.image;
                }).fail(res => this.pictureUploadError = this.getErrorFromStack(res)).always(() => {
                    this.loadingNextScenario = false;
                    cb();
                });
            },

            saveSurvey (cb) {
                // Validate the scenario.
                let question, answers = {};

                for (let i = 0; i < this.onboarding.survey.questions.length; i++) {
                	question = this.onboarding.survey.questions[i];

                	if (! question.answer || ! question.answer.length) {
                	    this.incompleteSurvey = true;
                	    this.loadingNextScenario = false;
                	    return;
                    }

                    answers[question.id] = question.answer;
                }

                // Submit the answers.
                $.post(`/api/onboarding-survey/${this.onboarding.survey.id}`, { answers }).always(cb);
            },

            saveFirstCourse (cb) {
                this.firstCourseError = false;

                $.post(`/api/first-course`, { id: this.selectedCourse.id }, 'json')
                    .done(res => cb(this.onboarding.afterOnboarding = res.courseUrl))
                    .fail(res => this.firstCourseError = this.getErrorFromStack(res))
                    .always(() => this.loadingNextScenario = false);
            },

            sendInvitations () {
                this.invitationsSent = false;
                this.sendingInvitations = true;
                this.invitationsSendError = false;

                $.post('/api/onboarding-email', { referrals: this.referrals })
                    .done(res => {
                        if (res.success) {
                            this.invitationsSent = true;

                            if (! this.scenario.done) {
                                this.scenario.done = true;
                                this.onboarding.points += this.scenario.points;
                            }
                        }
                    })
                    .fail(res => this.invitationsSendError = this.getErrorFromStack(res))
                    .always(() => this.sendingInvitations = false);
            },

            getErrorFromStack (res) {
                try {
                    var json = JSON.parse(res.responseText);
                } catch (e) {
                    return 'An unidentified error occured. Please try again later!';
                }

                if (! json) {
                    return 'An unidentified error occured. Please try again later!';
                }

                if (json.errors) {
                    for (let key in json.errors) {
                        return json.errors[key][0];
                    }
                } else if (json.messages) {
                    for (let key in json.messages) {
                        return json.messages[key];
                    }
                }
            },

            addReferral () {
                this.referrals.push({ name: null, email: null });
            },

            socialShare (service) {
                $.post('/api/onboarding-complete', { scenario_id: this.scenario.id, details: service })
                    .done(res => {
                        if (res.success) {
                            if (!this.scenario.done) {
                                this.scenario.done = true;
                                this.onboarding.points += this.scenario.points;
                            }
                        }
                    })
                    .fail(res => this.invitationsSendError = this.getErrorFromStack(res));
            },

            addToAnyButtonLink (button, link, title) {
                return `https://www.addtoany.com/add_to/${button.type}?linkurl=${encodeURIComponent(link)}&linkname=${encodeURIComponent(title)}`;
            },

            selectCourse (course, e) {
                e.preventDefault && e.preventDefault();
                this.selectedCourse = course;
            },

            setupA2a () {
                let a2a_config = a2a_config || {};

                a2a_config.linkname = 'Lurn Nation - A Transformation Home for Entrepreneurs';
                a2a_config.linkurl = 'https://www.lurn.com';
                a2a.init('page');
            },

            toggleAnswer (answer, question) {
                this.incompleteSurvey = false;

                if (! question.answer) {
                    this.$set(question, 'answer', []);
                }

                if (question.answer_choice === 'single') {
                    question.answer = [answer.id];
                } else {
                    let index = question.answer.indexOf(answer.id);

                    index === -1
                        ? question.answer.push(answer.id)
                        : question.answer.splice(index, 1);

                    setTimeout(() => this.$forceUpdate, 0);
                }
            },

            selectedAnswer (answer, question) {
                if (! question.answer) {
                    return false;
                }

                return question.answer.indexOf(answer.id) > -1;
            },
        },

        watch: {
            referral () {
                this.invitationsSent = false;
                this.invitationsSendError = false;
            },
        },

        mounted () {
            $('#upload-profile-picture [type=file]').on('change', function (e) {
                var reader = new FileReader();

                reader.readAsDataURL(e.target.files[0]);
                reader.onload = e => onboarding.profilePic = e.target.result;
            });

            this.setupA2a();
        },
    }
</script>