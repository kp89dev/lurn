<template>
    <div>
        <strong>Goal</strong>
        <conditions :target="node"></conditions>
    </div>
</template>

<script>
    import conditions from '../components/conditions.vue';

    export default {
        computed: {
            node: function () {
                return {
                    value: this.$store.state.goal,
                    update: this.updateGoal
                }
            }
        },
        components: {
            conditions
        },
        methods: {
            updateGoal: function(goal) {
                this.$store.commit('updateGoal', goal);
            }
        },
        created: function() {
            this.$store.activeNodeIdx = null;
        }
    }
</script>
