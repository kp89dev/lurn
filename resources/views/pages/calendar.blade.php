@extends('layouts.app')

@section('content')
    <div id="calendar" class="shadow">
        <div class="ui grid">
            <div id="events-wrapper" class="four wide column padded">
                <h3>
                    <i class="calendar alternate outline icon"></i>
                    Calendar Events
                </h3>

                <p>
                    <span style="font-weight: normal">Legend</span>
                    &mdash; Click on items to toggle them on and off
                </p>

                <ul id="courses-legend" v-cloak>
                    <li v-for="course in courses" :style="{ backgroundColor: course.color }"
                        :class="{ on: course.render }"
                        @click="toggle(course)">
                        @{{ course.title }}
                    </li>
                </ul>

                <hr>

                <strong v-if="events.length" v-cloak>@{{ events[0].start.format('MMMM YYYY') }}</strong>

                <ul id="events" v-cloak>
                    <li v-for="event in orderedEvents">
                        <div :style="{ backgroundColor: event.color }" @click="showEvent(event)">
                            <span class="date">@{{ event.start | date }}</span>
                            <span class="event" v-text="event.title"></span>
                        </div>
                    </li>
                </ul>
            </div>
            <div id="calendar-wrapper" class="twelve wide column relative">
                <div class="ui inverted dimmer" :class="{ active: loading }">
                    <div class="ui loader"></div>
                </div>
                <div id="calendar-plugin" class="fullcalendar"></div>
            </div>
        </div>
    </div>

    <div id="event-details" class="ui small modal">
        <i class="close icon"></i>
        <div class="header"></div>
        <div class="content"></div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/dimmer.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/modal.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.3.0/fullcalendar.min.js"></script>
    <script type="text/javascript" src="{{ mix('js/calendar.js') }}"></script>
@endsection
