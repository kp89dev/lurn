<template>
    <div>
        <hr>
        <p>Choose a type</p>
        <ul class="types">
            <li v-for="type in nodeTypes" @click="updateActiveNode(type.key)" :class="{ active: activeType == type.key }">
                <span v-text="type.title"></span>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ['activeType'],
        data: function() {
            return {
                nodeTypes: window.configuration.nodeTypes
            }
        },
        methods: {
            updateActiveNode: function(type) {

                let defaults = _.find(_.cloneDeep(window.configuration.nodeTypes), {key: type}).default;

                this.$store.commit('updateNode', {
                    node: defaults
                });
            }
        }
    }
</script>
