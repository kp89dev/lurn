import _ from 'lodash';

let removeParentIfSuccessful = (parent, e) => {
    return res => {
        if (res.success === true)
            $(e.target).parents(parent).remove();
    }
};

new Vue({
    el: '#content',

    data: {
        loading: true,
        news: []
    },

    methods: {
        getNews() {
            $.getJSON('/api/unread-news', news => news && (this.news = news))
                .always(() => this.loading = false);
        },

        markNewsRead() {
            this.loading = true;

            $.post('/api/mark-news-read', { ids: _.map(this.news, 'id') })
                .done(() => this.getNews())
                .always(() => this.loading = false);
        },

        hideCongratsMessage(e) {
            $.post('/api/hide-message/dashboard-congrats', removeParentIfSuccessful('#main-message', e), 'json');
        },

        hideMissingCourseLink(e) {
            $.post('/api/hide-message/dashboard-missing-course-question', removeParentIfSuccessful('p', e), 'json');
        },
    },

    mounted() {
        this.getNews();
    }
});