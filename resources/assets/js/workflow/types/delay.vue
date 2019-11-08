<template>
    <div>
        <strong>Delay</strong>
        <choose activeType="delay"></choose>
        <hr>
        <div>Choose settings</div>
        <p><input type="number" min="1" v-model="activeNode.value.delay"></p>
        <p>
            <select v-model="activeNode.value.delayUnit" class="form-control">
                <option value="minutes">Minutes</option>
                <option value="hours">Hours</option>
                <option value="days">Days</option>
                <option value="weeks">Weeks</option>
                <option value="months">Months</option>
            </select>
        </p>
    </div>
</template>

<script>
    import choose from './choose.vue';

    export default {
        computed: {
            activeNode() { return this.$store.state.activeNode }
        },
        components: {
            choose
        }
    }
</script>
