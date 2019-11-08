<template>
    <div>
        <hr>
        <div>Choose settings</div>
        <ul class="list">
            <li v-for="(item, i) in node.conditions" :class="{ orclass: item.type == 'or'}">
                <i class="fa fa-close" @click="removeCondition(i)"></i>
                <select v-model="item.key" class="form-control" @change="prepareValuesKey(item)">
                    <option v-for="condition in conditions" :value="condition.key" v-text="condition.title"></option>
                </select>

                <div v-if="getConditionByKey(item.key).inputs" v-for="(input, j) in getConditionByKey(item.key).inputs">
                    <select v-if="input.type == 'select'" v-model="item.values[j]" class="form-control">
                        <option v-for="opt in input.options" :value="opt" v-text="opt.title"></option>
                    </select>

                    <input v-if="input.type == 'text'" type="text" v-model="input.values[j].value" class="form-control">
                    <input v-if="input.type == 'number'" type="number" v-model="item.values[j].value" class="form-control">
                    <input v-if="input.type == 'date'" type="text" v-model="input.values[j].value" class="form-control">
                </div>

                <div v-if="i == node.conditions.length-1" class="btn btn-default" @click="addAndNodeCondition()">
                    <i class="fa fa-plus"></i> AND
                </div>
            </li>

            <li class="add">
                <div v-if="node.conditions.length" class="btn btn-default" @click="addOrNodeCondition()">
                    <i class="fa fa-plus"></i> OR
                </div>
                <div v-if="! node.conditions.length" class="btn btn-default" @click="addAndNodeCondition()">
                    <i class="fa fa-plus"></i> ADD
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    import _ from 'lodash';

    export default {
        props: ['target'],
        data: function() {
            return {
                conditions: window.configuration.conditions,
                node: this.target.value,
                updateNode: this.target.update
            }
        },
        watch: {
            node: {
                handler: function(newValue) {
                    this.updateNode(newValue);
                },
                deep: true
            }
        },
        methods: {
            removeCondition (key) {
                this.node.conditions.splice(key, 1);
            },

            addAndNodeCondition () {
                this.node.conditions.push({type: 'and', key: "", inputs: [], values: []});
            },

            addOrNodeCondition() {
                this.node.conditions.push({type: 'or', key: "", inputs: [], values: []});
            },

            getConditionOptionsByKey(key, name) {
                return _.find(this.getConditionByKey(key).inputs, { name });
            },

            getConditionByKey(key) {
                return _.find(this.conditions, { key }) || {};
            },

            prepareValuesKey(item) {
                item.values =  [];
                item.inputs =  [];
                if (typeof this.getConditionByKey(item.key).inputs == 'undefined') {
                    return;
                }

                this.getConditionByKey(item.key).inputs.forEach(function(input){
                    item.inputs.push({name: input.name, type: input.type});
                    item.values.push({value: ''});
                })
            }
        }
    }
</script>
