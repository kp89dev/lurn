import { store } from './store/store';
import graph from './graph.vue';
import sidebar from './sidebar.vue';
import settings from './settings.vue';
import workflowService from './services/workflow';

new Vue({
    el: '#workflows',
    store,
    components: {
        graph,
        sidebar,
        settings
    },
    data: {
        savingWorkflow: false,
        workflowError: null,
        workflowSuccess: null
    },
    methods: {
        setGoalAsActiveNode() {
            this.$store.commit('setActiveNode', this.$store.state.goal);
            this.$store.commit('setActiveNodeIdx', null);
        },
        setSettings() {
            this.$store.commit('setActiveNodeIdx', 'settings');
        },
        saveWorkflow() {
            this.savingWorkflow = true;
            this.workflowError = null;
            this.workflowSuccess = null;
            let self = this;

            workflowService.save({
                id: window.workflow_details.id,
                name: window.workflow_details.name,
                goal:  this.$store.state.goal,
                workflow: this.$store.state.nodes,
                details: this.$store.state.details
            }).then(function(response){
                window.workflow_details.id = response.id;
                self.workflowError = null;
                self.workflowSuccess = true;
            }).catch(function(response) {

                if (response.ok) {
                    self.workflowError = null;
                    self.workflowSuccess = true;
                } else {
                    self.workflowError = Object.values(response.body)[0][0];
                }
            }).finally(function(){
                self.savingWorkflow = false;
            });
        }
    },
    computed: {
        activeGoal() {
            return this.$store.state.activeNodeIdx == null
        },
        activeSettings() {
            return this.$store.state.activeNodeIdx == 'settings'
        }
    }
});

