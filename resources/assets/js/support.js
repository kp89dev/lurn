new Vue({
    el: '#support',

    data: {
        user,
        message: null,
        loading: false,
    },

    methods: {
        sendMessage() {
            if (this.loading) return;

            this.loading = true;

            $.post('/api/support-message', { user: this.user, message: this.message }, res => {
                if (res.success === true) {
                    this.message = null;

                    iziSuccess(
                        'Your message has been successfully delivered!',
                        'Please watch your inbox the next few days for a response from us, if applicable.',
                        5
                    );
                }
            }, 'json').fail(res => {
                let message = 'Something went wrong. Please try again later!';

                if (res.responseJSON) {
                    if (res.responseJSON.message) {
                        message = res.responseJSON.message.join('<br>');
                    } else {
                        let newMessage = '';

                        for (let name in res.responseJSON) {
                            if (res.responseJSON.hasOwnProperty(name)) {
                                newMessage += res.responseJSON[name] + '<br>';
                            }
                        }

                        if (newMessage.length > 5) {
                            message = newMessage;
                        }
                    }
                }

                iziError('Validation Error', message);
            }).always(() => this.loading = false);
        }
    }
});