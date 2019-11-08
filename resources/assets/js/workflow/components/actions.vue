<template>
    <div>
        <hr>
        <div>Choose action</div>
        <ul class="list">
            <li>
                <select v-model="item.key" class="form-control" @change="prepareValuesKey()">
                    <option v-for="action in actions" :value="action.key" v-text="action.title"></option>
                </select>

                <div v-if="getActionByKey(item.key).inputs" v-for="(input, j) in getActionByKey(item.key).inputs">
                    <select v-if="input.type == 'select'" v-model="item.value" :placeholder="item.placeholder" class="form-control" :name="input.name">
                        <option v-for="opt in input.options" :value="opt" v-text="opt.title"></option>
                    </select>

                    <input v-if="input.type == 'text'" type="text" v-model="input.value" class="form-control">
                    <input v-if="input.type == 'number'" type="number" v-model="item.value" class="form-control">
                    <input v-if="input.type == 'date'" type="text" v-model="input.value" class="form-control">
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    import _ from 'lodash';

    export default {
        props: {
            target: {
                type: Object
            }
        },
        data: function() {
            return {
                actions: window.configuration.actions,
                updateNode: this.target.update,
                item: this.target.value
            }
        },
        watch: {
            item: {
                handler: function(newValue) {
                    this.updateNode(newValue);
                },
                deep: true
            }
        },
        methods: {
            getActionOptionsByKey(key, name) {
                return _.find(this.getActionByKey(key).inputs, { name });
            },

            getActionByKey(key) {
                return _.find(this.actions, { key }) || {};
            },

            prepareValuesKey() {
                if (typeof this.item.value == 'undefined') {
                    this.item.value = null;
                }
            }
        }
    }
</script>
