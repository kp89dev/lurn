<template>
    <div>
        <p class="title"> Workflow Settings </p>
        <hr>
        <label class="col-md-12">Name: <span class="required">* </span></label>
        <input type="text" v-model="name">
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                name: this.$store.state.details.name
            }
        },
        watch: {
            name: function(newValue) {
                this.$store.commit('setName', newValue);
            }
        }
    }
</script>
