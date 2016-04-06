//   (c) 2007-2016 by Kajona, www.kajona.de
//       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
//       $Id$

if (typeof KAJONA === "undefined") {
    alert('load kajona.js before!');
}

KAJONA.kajonatree = {
    helper: {},
    contextmenu: {},
    conditionalselect: {}
};

/**
 * Object to initilaze a JsTree
 */
KAJONA.kajonatree.jstree = function () {

    var treeContext = this;

    this.loadNodeDataUrl = null;
    this.rootNodeSystemid = null;
    this.treeConfig = null;//@see class \Kajona\System\System\SystemJSTreeConfig for structure
    this.treeId = null;
    this.treeviewExpanders  = null;

    /**
     * Moves nodes below another node.
     * Triggers a relaod of the page after node was moved
     *
     * @param node
     * @param node_parent
     * @param node_position
     * @param more
     * @returns {boolean}
     */
    function moveNode(node, node_parent, node_position, more) {
        //node moved
        var strNodeId = node.id,
         strNewParentId = node_parent.id;

        //save new parent to backend
        KAJONA.admin.ajax.genericAjaxCall("system", "setPrevid", strNodeId+"&prevId="+strNewParentId, function() {
            location.reload();
        });
        return true;
    }

    /**
     * Checks if a node can be dropped to a certain place in the tree
     *
     * @param node
     * @param node_parent
     * @param node_position
     * @param more
     * @returns {boolean}
     */
    function checkMoveNode (node, node_parent, node_position, more) {
        var targetNode = more.ref,
         strDragId = node.id,
         strTargetId = targetNode.id,
         strInsertPosition = more.pos; //"b"=>before, "a"=>after, "i"=inside

        //only insert are allowed, no ordering
        if (strInsertPosition !== "i") {
            return false;
        }

        //dragged node already direct childnode of target?
        var arrTargetChildren = targetNode.children;
        if($.inArray(strDragId, arrTargetChildren) > -1){
            return false;
        }

        //dragged node is parent of target?
        var arrTargetParents = targetNode.parents;
        if($.inArray(strDragId, arrTargetParents) > -1){
            return false;//TODO maybe not needed, already check by jstree it self
        }

        //dragged node same as target node?
        if(strDragId == strTargetId) {
            return false;//TODO maybe not needed, already check by jstree it self
        }

        return true;
    }


    /**
     * Callback used for dragging elements from the list to the tree
     *
     * @param e
     * @returns {*}
     */
    this.listDnd = function(e) {
        var strSystemId = $(this).closest("tr").data("systemid");
        var strTitle = $(this).closest("tr").find(".title").text();

        //Check if there a jstree instance (there should only one)
        var jsTree = $.jstree.reference('#'+treeContext.treeId);

        //create basic node
        var objNode =   {
            id : strSystemId,
            text: strTitle
        };

        //if a jstree instanse exists try to find a node for it
        if(jsTree != null) {
            var treeNode = jsTree.get_node(strSystemId);
            if(treeNode != false) {
                objNode = treeNode;
            }
        }

        var objData = {
            'jstree' : true,
            'obj' : $(this),
            'nodes' : [
                objNode
            ]
        };
        var event = e;
        var strHtml = '<div id="jstree-dnd" class="jstree-default"><i class="jstree-icon jstree-er"></i>' + strTitle + '</div>';//drag container
        return $.vakata.dnd.start(event, objData, strHtml);
    };


    /**
     * Initializes the jstree
     */
    this.initTree = function () {

        /* Create config object*/
        var jsTreeObj = {
            'core' : {
                /**
                 *
                 * @param operation operation can be 'create_node', 'rename_node', 'delete_node', 'move_node' or 'copy_node'
                 * @param node the selected node
                 * @param node_parent
                 * @param node_position
                 * @param more on dnd => more is the hovered node
                 * @returns {boolean}
                 */
                'check_callback' : function (operation, node, node_parent, node_position, more) {
                    // operation can be 'create_node', 'rename_node', 'delete_node', 'move_node' or 'copy_node'
                    // in case of 'rename_node' node_position is filled with the new node name

                    if(operation === 'move_node') {
                        //check when dragging
                        if(more.dnd) {
                            return checkMoveNode(node, node_parent, node_position, more);
                        }
                        else {
                            return moveNode(node, node_parent, node_position, more);
                        }
                    }

                    if(operation === 'create_node') {
                        return true;//Check for assignment tree
                    }

                    return false;
                },
                'expand_selected_onload': true,
                'data': {
                    'url': function (node) {
                        return treeContext.loadNodeDataUrl;
                    },
                    'data': function (node) {
                        var data = {};
                        if (node.id === "#") {
                            data.systemid = treeContext.rootNodeSystemid;
                            data.jstree_initialtoggling = treeContext.treeviewExpanders;
                        }
                        else {
                            data.systemid = node.id;
                        }
                        return data;
                    }
                },
                'themes': {
                    "url": false,
                    "icons": false
                },
                'animation' : false
            },
            'dnd': {
                'check_while_dragging' : true
            },
            'types': {
            },
            'contextmenu': {
            },
            'conditionalselect': KAJONA.kajonatree.conditionalselect.handleConditionalSelect,

            'plugins': ['conditionalselect']
        };

        /* Extend Js Tree Object due to jsTreeConfig*/
        if(this.treeConfig.checkbox) {
            jsTreeObj.plugins.push('checkbox');
        }
        if(this.treeConfig.dnd) {
            jsTreeObj.plugins.push('dnd');
        }
        if(this.treeConfig.types) {
            jsTreeObj.plugins.push('types');
            jsTreeObj.types = this.treeConfig.types;
        }
        if(this.treeConfig.contextmenu) {
            jsTreeObj.plugins.push('contextmenu');
            jsTreeObj.contextmenu.items = this.treeConfig.contextmenu.items;
            jsTreeObj.contextmenu.show_at_node = false;
        }

        /* Create the tree */
        var $jsTree = $('#'+this.treeId).jstree(jsTreeObj);

        /*Register events*/
        $jsTree
            .on("show_contextmenu.jstree", function(objNode, x, y) {
                //initialze properties when context menu is shown
                KAJONA.util.lang.initializeProperties($('.jstree-contextmenu'));
            });

        //4. init jstree draggable for lists
        $('td.treedrag.jstree-listdraggable').on('mousedown', this.listDnd);
    };
};


/**
 * Get the current tree instance
 *
 * @returns {*}
 */
KAJONA.kajonatree.helper.getTreeInstance = function() {
    var treeId = $('.treeDiv').first()[0].id;
    return $.jstree.reference('#' + treeId);

};

/**
 *  Creates the contextmenu
 *
 * @param o - the node
 * @param cb - callback function
 */
KAJONA.kajonatree.contextmenu.createDefaultContextMenu = function(o, cb) {
    var objItems =  {
        "expand_all": {
            "label": "<span data-lang-property=\"system:commons_tree_contextmenu_loadallsubnodes\"></span>",
            "action": KAJONA.kajonatree.contextmenu.openAllNodes,
            "icon":"fa fa-sitemap"
        }
    };

    return objItems;
};


/**
 *  Each time a node should be select, this method is being fired via the conditionalselect plugin.
 *  Handles conitional select events.
 *
 * @param objNode - the node to be selected
 * @param event - the event being fired
 *
 */
KAJONA.kajonatree.conditionalselect.handleConditionalSelect = function (objNode, event) {

    //hanlde on click events
    if(event.type == "click") {

        //if node contains a_attr with href -> relaod page
        if(objNode.a_attr) {
            if(objNode.a_attr.href) {
                document.location.href = objNode.a_attr.href;//Document reload
            }
        }
    }

    return true;
};

/**
 * Function to open all nodes via the contextmenu
 *
 * @param data
 */
KAJONA.kajonatree.contextmenu.openAllNodes = function(data) {
    var objTreeInstance = $.jstree.reference(data.reference),
        objNode = objTreeInstance.get_node(data.reference);
    objTreeInstance.open_all(objNode);
};