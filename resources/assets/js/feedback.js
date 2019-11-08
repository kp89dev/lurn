new Vue({
    el: '#feedback',

    data: {
        sent: false,
        grade: 10,
        scale: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        sending: false,
        feedback: null,
    },

    methods: {
        showModal () {
            $('#feedback-modal').modal('show');
        },

        submitFeedback () {
            this.sending = true;

            $.post('/api/feedback', { grade: this.grade, feedback: this.feedback })
                .always(() => {
                    this.sending = false;
                    this.sent = true;

                    $('#feedback').remove();
                });
        },
    }
});
