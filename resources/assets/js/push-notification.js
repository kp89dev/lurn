new Vue({
    el: '#push-notification',

    data: {
        loading: true,
        pushNotifications: [],
        browserTime: ''
    },

    methods: {
        getPushNotifications() {

            var today = new Date();
            var jan = new Date(today.getFullYear(), 0, 1);
            var jul = new Date(today.getFullYear(), 6, 1);
            var stdTZOffset = Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset()) / -60;
            var browserTZOffset = new Date().getTimezoneOffset() / -1;
            var dst = false;
            if (browserTZOffset < stdTZOffset) {
                dst = true;
            }
            $.getJSON('/api/unread-push-notifications', {browserTZOffset: browserTZOffset, dst: dst},
                    pushNotifications => pushNotifications && (this.pushNotifications = pushNotifications),
                    browserTime => browserTime && (this.browserTime = browserTime))
                    .done(function (data) {
                        if (data[0] && data[0].hasOwnProperty("id")){
                            woopra.track("pushNotification", {
                                toURL: 'none',
                                title: data[0].title,
                                action: 'viewed',
                                viewedURL: window.location.href
                            });
                        }
                    })
                    .always(() => this.loading = false);
        },

        setUserViewed(pushNotificationId, event) {
            var whereTo = event.target.href;
            $.post('/api/mark-push-notification-read', {pushNotificationId: pushNotificationId})
                    .then(function (response) {
                        if (response === 'success' && whereTo !== 'undefined') {
                            window.location.assign(whereTo);
                        }
                        this.getPushNotifications();
                    })
                    .always(() => this.loading = false);
        }
    },

    mounted() {
        this.getPushNotifications();
    }
});