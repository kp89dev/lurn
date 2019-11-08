<template>
    <div>
        <strong>IF / Else</strong>
        <choose activeType="ifelse"></choose>
        <conditions :target="node"></conditions>
    </div>
</template>

<script>
    import conditions from '../components/conditions.vue';
    import choose from './choose.vue';

    export default {
        data: function () {
            return {
                node: {
                    value: this.$store.state.activeNode,
                    update: this.updateNode
                }
            };
        },
        methods: {
            updateNode: function(node) {
                if (! node.conditions ) {
                    node.conditions = [];
                }

                this.$store.commit('updateNode', { node });
            }
        },
        components: {
            conditions,
            choose
        }
    }
</script>
