new Vue({
    el: '#calendar',
    data: {
        events: [],
        courses: [],
        loading: false,
        courseColors: ['#dbe7f9', '#d7e8e3', '#f7f5c4', '#ffcc97', '#c9bdef', '#ade8ff', '#ffd3f4', '#c9f5ae', '#fbce7b'],
        lastUsedColor: 0,
    },

    methods: {
        toggle(course) {
            typeof course.render === 'undefined'
                ? this.$set(course, 'render', true)
                : (course.render = !course.render);
        },

        course(course_container_id) {
            for (let i = 0; i < this.courses.length; i++)
                if (this.courses[i].course_container_id === course_container_id)
                    return this.courses[i];

            return null;
        },

        getNextAvailableColor() {
            return this.courseColors[this.lastUsedColor++ % this.courseColors.length];
        },

        showEvent(e) {
            let ed = $('#event-details');

            ed.find('.header').text(e.title);
            ed.find('.content').html(e.description);

            ed.modal('show');
        }
    },

    mounted() {
        let that = this;

        window.onload = () => {
            $('#calendar-plugin').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                lazyFetching: true,
                events(start, end, tz, cb) {
                    that.loading = true;

                    $.getJSON('/api/events', {
                        start: start.format('YYYY-MM-DD'),
                        end: end.format('YYYY-MM-DD')
                    }, data => {
                        if (data.courses) {
                            for (let i = 0; i < data.courses.length; i++) {
                                let course = data.courses[i];
                                course.color = that.getNextAvailableColor();
                            }

                            that.courses = data.courses;
                        }

                        if (data.events) {
                            for (let i = 0; i < data.events.length; i++) {
                                let event = data.events[i];

                                event.start = moment(event.start_ts * 1000);
                                event.end = moment(event.end_ts * 1000);
                                event.color = that.course(event.course_container_id).color;
                            }

                            that.events = data.events;
                        }

                        cb(that.events);
                    }).always(() => that.loading = false);
                },
                eventClick(e) {
                    that.showEvent(e);
                }
            });
        }
    },

    computed: {
        activeEvents() {
            let events = [];

            for (let i = 0; i < this.events.length; i++) {
                if (this.course(this.events[i].course_container_id).render) {
                    events.push(this.events[i]);
                }
            }

            return events;
        },

        orderedEvents() {
            return this.activeEvents.sort((a, b) => {
                return a.start.unix() - b.start.unix();
            });
        }
    },

    watch: {
        courses: {
            handler() {
                $('#calendar-plugin').fullCalendar('removeEvents');
                $('#calendar-plugin').fullCalendar('addEventSource', this.activeEvents);
            },
            deep: true
        }
    }
});

Vue.filter('ucwords', value => {
    return value.replace(/[^a-z0-9]+/gi, ' ').trim().replace(/\w+/g, match => {
        return match[0].toUpperCase() + match.substr(1).toLowerCase();
    });
});

Vue.filter('date', date => {
    return date.format('Do @ hh:mma')
});
