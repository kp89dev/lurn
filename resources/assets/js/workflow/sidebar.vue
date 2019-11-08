<template>
    <div>
        <p class="node-title" ></p>

        <component :is="currentType" :node="node"></component>
    </div>
</template>

<script>
    import goal from './types/goal.vue'
    import enroll from './types/enroll.vue';
    import ifelse from './types/ifelse.vue';
    import action from './types/action.vue';
    import choose from './types/choose.vue';
    import delay from './types/delay.vue';

    export default {
        computed: {
                currentType: function() { return this.$store.state.activeNode.type ? this.$store.state.activeNode.type : 'choose' },
                node: function() { return this.$store.getters.getActiveNode }
        },
        components: {
          goal,
          enroll,
          action,
          choose,
          delay,
          ifelse
        }
    }
</script>
