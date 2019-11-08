<template>
    <div>
        <strong>Action</strong>
        <choose activeType="action"></choose>

        <actions :target="node"></actions>
    </div>
</template>

<script>
    import actions from '../components/actions.vue';
    import choose from './choose.vue';

    export default {
        data: function () {
            return {
                node: {
                    value:  {
                        key:   this.$store.state.activeNode.key   ? this.$store.state.activeNode.key : null,
                        value: this.$store.state.activeNode.value ? this.$store.state.activeNode.value : null,
                        type:  'action'
                    },
                    update: this.updateNode
                }
            };
        },
        methods: {
            updateNode: function(node) {
                this.$store.commit('updateNode', { node });
            }
        },
        components: {
            actions,
            choose
        }
    }
</script>
