Ext.ns('Extop.Demo');

Extop.Demo.AccordionWindow = Ext.extend(Ext.app.Module, {
     id: 'demo-accordion',
     type: 'demo/accordion',
     
     /**
     * Initialize this module.
     * This function is called at startup (page load/refresh).
     */
     init : function(){
     
     },
	
	/**
	 * Create this modules window here.
     */
    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('acc-win');

        if(!win){
            win = desktop.createWindow({
                id: 'acc-win',
                title: 'Accordion Window',
                width: 250,
                height: 400,
                iconCls: 'app-accordion-icon',
                shim: false,
                animCollapse: false,
                constrainHeader: true,
                maximizable: false,
                taskbuttonTooltip: '<b>Accordion Window</b><br />A window with an accordion layout,',

                tbar:[{
                    tooltip: '<b>Rich Tooltips</b><br />Let your users know what they can do!',
                    iconCls: 'connect-icon'
                },'-',{
                    tooltip: 'Add a new user',
                    iconCls: 'user_add-icon'
                },' ',{
                    tooltip: 'Remove the selected user',
                    iconCls: 'user_delete-icon'
                }],

                layout: 'accordion',
                layoutConfig: {
                    animate: false
                },

                items: [
                    new Ext.tree.TreePanel({
                        border: false,
                        id: 'im-tree',
                        title: 'Online Users',
                        loader: new Ext.tree.TreeLoader(),
                        rootVisible: false,
                        lines: false,
                        autoScroll: true,
                        useArrows: true,
                        tools: [{
                            id: 'refresh',
                            on: {
                                click: function(){
                                    var tree = Ext.getCmp('im-tree');
                                    tree.body.mask('Loading...', 'x-mask-loading');
                                    tree.root.reload();
                                    tree.root.collapse(true, false);
                                    setTimeout(function(){ // mimic a server call
                                        tree.body.unmask();
                                        tree.root.expand(true, true);
                                    }, 1000);
                                }
                            }
                        }],
                        root: new Ext.tree.AsyncTreeNode({
                            text: 'Hidden Root',
                            children: [{
                                text: 'Friends',
                                iconCls: 'group-icon',
                                expanded: true,
                                children: [{
                                    text: 'Jack',
                                    iconCls: 'user-icon',
                                    leaf: true
                                },{
                                    text:'Brian',
                                    iconCls:'user-icon',
                                    leaf:true
                                },{
                                    text:'Jon',
                                    iconCls:'user-icon',
                                    leaf:true
                                }]
                            },{
                                text: 'Family',
                                iconCls: 'group_link-icon',
                                expanded: true,
                                children: [{
                                    text: 'Kelly',
                                    iconCls: 'user_female-icon',
                                    leaf: true
                                },{
                                    text: 'Sara',
                                    iconCls: 'user_female-icon',
                                    leaf: true
                                },{
                                    text: 'Zack',
                                    iconCls: 'user_green-icon',
                                    leaf: true
                                },{
                                    text: 'John',
                                    iconCls: 'user_orange-icon',
                                    leaf: true
                                }]
                            }]
                        })
                    }),{
                        border: false,
                        title: 'Settings',
                        html: '<p>Something useful would be in here.</p>',
                        autoScroll: true
                    },{
                        border: false,
                        title: 'Even More Stuff',
                        html: '<p>Something useful would be in here.</p>'
                    },{
                        border: false,
                        title: 'My Stuff',
                        html: '<p>Something useful would be in here.</p>'
                    }
                ]
            });
        }
        win.show();
    }
});