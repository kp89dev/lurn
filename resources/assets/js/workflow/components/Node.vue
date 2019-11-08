<template>
    <div>
        <div class="node" :class="{ first: item.type == 'enroll', active }" @click="select()">
            <i class="fa fa-times" v-if="item.type != 'enroll'" @click="remove()"></i>
            <i class="fa node-icon" :class="{
                'fa-play':      item.type == 'enroll',
                'fa-share-alt': item.type == 'ifelse',
                'fa-clock-o':   item.type == 'delay',
                'fa-rocket':    item.type == 'action',
                'fa-flag':      item.type == 'goal',
                'fa-question':   item.type == 'choose'}"></i>

            <div class="title" v-text="title"></div>

            <div class="content" v-if="item.description" v-text="item.description"></div>

            <div class="content" v-if="item.conditions && item.conditions.length">
                <p class="label">Condition{{ item.conditions.length == 1 ? '' : 's' }}</p>
                <ul class="conditions">
                    <li v-for="condition in item.conditions" :class="condition.type">
                        <div>
                            <span v-if="condition.key" v-text="getConditionByKey(condition.key).title"></span>

                            <strong v-if="condition.values && condition.values.length" v-for="value in condition.values">
                                <span v-if="value.title" v-text="value.title"></span> <span v-else v-text="value.value"></span>
                            </strong>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="content" v-if="item.type == 'delay'">
                <p>Wait <strong v-text="item.value.delay"></strong> <strong v-text="item.value.delayUnit"></strong></p>
            </div>

            <div class="content" v-if="item.type == 'action' && item.key">
                <div>
                    <span v-text="getActionByKey(item.key).title"></span><br/>
                    <strong v-if="item.value" v-text="item.value.title"></strong>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import AddNode from './AddNode.vue';

    export default {
        props: ['item', 'id'],
        computed: {
            active() {
                return this.id == this.$store.state.activeNodeIdx;
            },
            title () {
                switch (this.item.type) {
                    case 'enroll': return 'Enrollment';
                    case 'ifelse': return 'If / Then';
                    case 'delay':  return 'Delay / Wait';
                    case 'action': return 'Action';
                }
            }
        },

        methods: {
            remove () {
                this.$store.commit('removeNode', { id: this.id });
            },

            select () {
                this.$emit('select');
            },

            getConditionByKey(key) {
                return _.find(window.configuration.conditions, {key}) || {};
            },

            getActionByKey(key) {
                return _.find(window.configuration.actions, {key}) || {};
            }
        },

        components: {
            AddNode
        }
    }
</script>
