<template>
    <div>
        <strong>Enrollment</strong>
        <conditions :target="node"></conditions>
    </div>
</template>

<script>
    import conditions from '../components/conditions.vue';

    export default {
        data: function () {
            return {
                node: {
                    value: this.$store.state.nodes[0],
                    update: this.updateNode
                }
            };
        },
        components: {
            conditions
        },
        methods: {
            updateNode: function(node) {
                this.$store.commit('updateNode', { node, offset: 0});
            }
        }
    }
</script>
