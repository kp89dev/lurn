import * as mutations from './mutations';
import * as getters from './getters';
import * as actions from './actions';

export const store = new Vuex.Store({
    state: {
        nodes: window.workflow,
        goal:  window.goal,
        activeNodeType: 'goal',
        activeNode: window.goal,
        activeNodeIdx: null,
        details: window.workflow_details
    },
    mutations,
    getters,
    actions
});
