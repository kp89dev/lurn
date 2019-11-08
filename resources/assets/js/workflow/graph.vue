<template>
    <div class="graph">
        <div v-for="(node, i) in nodes" :key="node.type + i">
            <node @select="selectNode(nodes[i], i)" :item="node" :id="i"></node>

            <div v-if="node.type != 'ifelse'">
                <add-node :id="i+1"></add-node>
            </div>

            <div class="row" v-if="node.type == 'ifelse'">
                <div class="col col-md-6">
                    <div v-for="(nnode, j) in node.nodes_false">
                        <add-node :id="id(i, 'nodes_false', j)"></add-node>
                        <node @select="selectNode(nnode, i, 'nodes_false', j)" :item="nnode" :id="id(i, 'nodes_false', j)" :key="nnode.type+i+j"></node>
                    </div>

                    <add-node :id="id(i, 'nodes_false', node.nodes_false.length)" ></add-node>
                </div>
                <div class="col col-md-6">
                    <div v-for="(nnode, j) in node.nodes_true">
                        <add-node :id="id(i, 'nodes_true', j)"></add-node>
                        <node @select="selectNode(nnode, i, 'nodes_true', j)" :item="nnode" :id="id(i, 'nodes_true', j)" :key="nnode.type+i+j"></node>
                    </div>

                    <add-node :id="id(i, 'nodes_true', node.nodes_true.length)"></add-node>
                </div>
            </div>

        </div>
    </div>
</template>

<script type="text/babel">
    import Node from './components/Node.vue';
    import AddNode from './components/AddNode.vue';

    export default {
        data: function() {
            return {
                nodes: this.$store.state.nodes
            }
        },
        methods: {
            selectNode (node , ...args) {
                if (typeof node == 'undefined') {
                    return; // nothing to do, node was removed
                }

                this.activeNodeId = args.join('.');
                this.$store.commit('setActiveNode', node);
                this.$store.commit('setActiveNodeIdx', this.activeNodeId);
            },

            id: function(...args) {
                return args.join('.');
            }
        },
        components: {
            Node,
            AddNode
        }
    }
</script>
