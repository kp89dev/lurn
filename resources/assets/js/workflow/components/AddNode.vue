<template>
    <div>
        <div class="add" @click="add()">
            <i class="fa fa-plus"></i>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['id'],

        methods: {
            add () {
                let node = {
                    type: 'choose',
                    key: '',
                    inputs:[],
                    values: [],
                    conditions: [],
                    value: null
                };

                this.$store.commit('addEmptyNode', {node, id: this.id})
            }
        }
    }
</script>
