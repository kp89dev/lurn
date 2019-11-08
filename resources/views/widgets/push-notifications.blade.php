<div v-show="pushNotifications.length" id="push-notification" style="display: none;">
    <div id="push-notification-bar" v-for="notifications in pushNotifications">
        <div class="wrapper padded not-top not-bottom">
            <div class="center aligned padded not-top not-bottom">
                <span class="close-wrapper padded not-bottom not-top">
                    <a v-on:click="setUserViewed(notifications.id, $event)" class="woopra-track" data-woopra='{"type":"pushNotification", "action":"close"}' v-bind:data-title="notifications.admin_title">
                        <i class="icon small close"/></i>
                    </a>
                </span>
                <div class="ui two columns grid">
                    <div class="column right aligned">
                        <div><strong v-html="notifications.content"></strong></div>
                    </div>
                    <div class="column left aligned">
                        <div v-if="notifications.cta_type == 'Internal'">
                            <div v-if="notifications.internal_cta_type == 'Course'">
                                <a v-on:click.prevent="setUserViewed(notifications.id, $event)" v-bind:data-title="notifications.admin_title" v-bind:href="'/classroom/'+notifications.internal_course_slug" class="woopra-track ui primary right labeled icon button" data-woopra='{"type":"pushNotification", "action":"follow"}'>
                                    @{{ notifications.button_text }}
                                    <i class="caret right icon"></i>
                                </a>
                            </div>
                            <div v-if="notifications.internal_cta_type == 'News'">
                                <a v-on:click.prevent="setUserViewed(notifications.id, $event)" v-bind:data-title="notifications.admin_title" v-bind:href="'/news/'+notifications.internal_news_slug" class="woopra-track ui primary right labeled icon button" data-woopra='{"type":"pushNotification", "action":"follow"}'>
                                    @{{ notifications.button_text }}
                                    <i class="caret right icon"></i>
                                </a>
                            </div>
                            <div v-if="notifications.internal_cta_type == 'Link'">
                                <a v-on:click.prevent="setUserViewed(notifications.id, $event)" v-bind:data-title="notifications.admin_title" v-bind:href="notifications.internal_link" class="woopra-track ui primary right labeled icon button" data-woopra='{"type":"pushNotification", "action":"follow"}'>
                                    @{{ notifications.button_text }}
                                    <i class="caret right icon"></i>
                                </a>
                            </div>
                        </div>
                        <div v-if="notifications.cta_type == 'External'">
                            <a v-on:click.prevent="setUserViewed(notifications.id, $event)" v-bind:href="'http://'+notifications.external_link" v-bind:data-title="notifications.admin_title" class="woopra-track woopra-outbound ui primary right labeled icon button" target="_blank" data-woopra='{"type":"pushNotification", "action":"follow"}'>
                                @{{ notifications.button_text }}
                                <i class="caret right icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
