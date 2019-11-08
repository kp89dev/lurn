export const updateGoal = (state, payload) => state.goal = payload;
export const updateNode = (state, payload) => {
    state.activeNode     = payload.node;
    state.activeNodeType = payload.node.type;

    if (typeof state.activeNodeIdx == 'number' || state.activeNodeIdx.indexOf('.') == -1) {
        state.nodes[state.activeNodeIdx] = payload.node;
    } else {
        _.dropRight(state.activeNodeIdx.split('.')) //remove last array value
        //dynamically get into nodes and assign the new value
        //eg. state.nodes[1]['nodes_false'][1] = payload.node
        .reduce((prev, curr) => prev[curr], state.nodes)
        [state.activeNodeIdx.split('.').pop()] = payload.node;
    }
    state.nodes.splice();
};

export const setActiveNode = (state, payload) => {
    state.activeNode = payload;
    state.activeNodeType = payload.type;
};

export const setActiveNodeIdx = (state, payload) => {
    state.activeNodeIdx = payload;
};

export const setName = (state, payload) => {
    state.details.name = payload;
};

export const addEmptyNode =  (state, payload) => {
    state.activeNodeIdx  = payload.id;
    state.activeNode     = payload.node;
    state.activeNodeType = payload.node.type;

    if (typeof payload.id == 'number') {
        state.nodes.splice(payload.id, 0, payload.node);
    } else {
        _.dropRight(payload.id.split('.')) //remove last array value
        //dynamically get into nodes and assign the new value
        //eg. state.nodes[1]['nodes_false'][1] = payload.node
        .reduce((prev, curr) => prev[curr], state.nodes)
        //add element at specified position
        .splice(payload.id.split('.').pop(), 0, payload.node);
    }
};

export const removeNode = (state, payload) => {
    if (typeof payload.id == 'number' || payload.id.indexOf('.') == -1) {
        let prevNode = state.nodes[payload.id-1];
        state.activeNode     = prevNode;
        state.activeNodeIdx  = payload.id-1;
        state.activeNodeType = prevNode.type;

        state.nodes.splice(payload.id, 1);
    } else {
        let idArray = payload.id.split('.');
        let lastElem = idArray.pop();
        if (lastElem == 0) { //last element on an if branch
            state.activeNode     = state.nodes[idArray[0]];
            state.activeNodeIdx  = idArray[0];
            state.activeNodeType = state.nodes[idArray[0]].type;
        } else {
            idArray.push(lastElem-1);
            let prevNodeIdx  = idArray.join('.');
            let prevNodeSameBranch = idArray.reduce((prev, curr) => prev[curr], state.nodes);

            state.activeNodeIdx  = prevNodeIdx;
            state.activeNode     = prevNodeSameBranch;
            state.activeNodeType = prevNodeSameBranch.type;
        }

        _.dropRight(payload.id.split('.')) //remove last array value
        //dynamically get into nodes and assign the new value
        //eg. state.nodes[1]['nodes_false'][1] = payload.node
        .reduce((prev, curr) => prev[curr], state.nodes)
        //add element at specified position
        .splice(payload.id.split('.').pop(), 1); //finally remove the element
    }
};
