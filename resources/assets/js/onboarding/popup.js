import Scenario from './components/Scenario';

new Vue({
    el: '#onboarding',
    data: onboarding,

    computed: {
        completed () {
            let completed = 0;

            for (let i = 0; i < this.scenarios.length; i++) {
                completed += this.scenarios[i].done ? 1 : 0;
            }

            return completed ? Math.round(completed / this.scenarios.length * 100) : 0;
        },
    },

    components: {
        Scenario,
    },
});
