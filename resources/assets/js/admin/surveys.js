$(() => {

    window.toBeRemoved = {
        questions: [],
        answers: []
    };

    /**
     * Events.
     */
    let addQuestion = function (e) {
        if (e) { 
            e.preventDefault();
        }

        $('.question-list').append($('#question-template').html());

        $('input[type="checkbox"]').bootstrapSwitch();

        Sortable.create($('.answer-list', $('.question-item').last())[0], {
            handle: '.answer-handle',
            animation: 150
        });
    };

    let removeQuestion = function (e) {
        e.preventDefault();

        let $parent = $(this).parents('.question-item').first();

        let id = $('input[name="question_id[]"]', $parent).val();

        if (id !== '') {
            window.toBeRemoved.questions.push(id);
        }

        $parent.remove();
    };

    let addAnswer = function (e) {
        e.preventDefault();

        window.x = $(this);

        $(this).parents('.panel').children('.answer-list').first().append($('#answer-template').html());

        $('input[type="checkbox"]').bootstrapSwitch();
    };

    let removeAnswer = function (e) {
        e.preventDefault();

        let $parent = $(this).parents('.answer-item').first();

        let id = $('input[name="answer_id[]"]', $parent).val();

        if (id !== '') {
            window.toBeRemoved.answers.push(id);
        }

        console.log($parent);

        $parent.remove();
    };

    let submitForm = function () {
        let data = {
            questions: [],
            delete: window.toBeRemoved
        };

        $('.question-item').each((questionIdx, questionEl) => {
            let question = {
                id: $('input[name="question_id[]"]', questionEl).val() || null,
                title: $('input[name="question_title[]"]', questionEl).val(),
                enabled: $('input[name="question_enabled[]"]', questionEl).is(':checked'),
                answer_choice: $('select[name="question_answer_choice[]"]', questionEl).val(),
                order: questionIdx,
                answers: []
            };

            $('.answer-item', questionEl).each((answerIdx, answerEl) => {
                question.answers.push({
                    id: $('input[name="answer_id[]"]', answerEl).val() || null,
                    title: $('input[name="answer_title[]"]', answerEl).val(),
                    enabled: $('input[name="answer_enabled[]"]', answerEl).is(':checked'),
                    order: answerIdx,
                });
            });

            data.questions.push(question);
        });

        $('input[name="question_data"]').val(JSON.stringify(data));
    };

    /**
     * Attach events.
     */
    $(document).on('click', '.add-question', addQuestion);
    $(document).on('click', '.remove-question', removeQuestion);
    $(document).on('click', '.add-answer', addAnswer);
    $(document).on('click', '.remove-answer', removeAnswer);
    $('form').on('submit', submitForm);

    if (! $('.question-item').length) {
        addQuestion();
    }

    Sortable.create(document.querySelector('.question-list'), {
        handle: '.question-handle',
        animation: 150
    });

    Sortable.create(document.querySelector('.answer-list'), {
        handle: '.answer-handle',
        animation: 150
    });

    $('.input-daterange').datepicker({autoclose: true, todayBtn: 'linked'});
});