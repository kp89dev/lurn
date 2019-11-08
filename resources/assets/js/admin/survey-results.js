jQuery($ => {

    var colors = [
        '#BC8ECC',
        '#5C2E6D',
        '#287EB6',
        '#45C8B0',
        '#229859',
        '#F2C42F',
        '#E77D2D',
        '#D3550F'
    ];

    $('.question').each((questionId, questionEl) => {

        let titles = [];
        let counts = [];

        $('.answer', questionEl).each((answerId, answerEl) => {
            let $el = $(answerEl);

            titles.push($('.answer-title', $el).text());
            counts.push($('.answer-count', $el).text());
        });

        let ctx = $('canvas', questionEl)[0].getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: counts,
                    backgroundColor: colors
                }],
                labels: titles
            }
        });

    });

});