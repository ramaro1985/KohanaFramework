Ext.ns('Auth.Admin');
Auth.Admin.Nav = function(ownerModule){
    this.ownerModule = ownerModule;
    this.locale = this.ownerModule.locale;

    var data = {
        items: [
            { cls: 'auth-admin-members-icon', id: 'viewMembers', label: 'Usuarios' },
            { cls: 'auth-admin-groups-icon', id: 'viewGroups', label: 'Roles' }/*,
            { cls: 'auth-admin-privileges-icon', id: 'viewPrivileges', label: 'Privileges' }*/
        ]
    };

    var dataView = new Ext.DataView({
        itemSelector: 'div.thumb-wrap',
        listeners: {
            click: function(dataView, index, node){
                var r = dataView.getRecord(node);

                if(r && r.id){
                    var action = r.id;
                    this.ownerModule[action]();
                }
            },
            scope: this
        },
        overClass: 'x-view-over',
        singleSelect: true,
        store: new Ext.data.JsonStore({
            data: data,
            fields: [ 'cls', 'id', 'label' ],
            idProperty: 'id',
            root: 'items'
        }),
        tpl: new Ext.XTemplate(
            '<tpl for=".">',
                '<div class="thumb-wrap" id="{id}">',
                    '<div class="thumb {cls}"></div>',
                        '<span>{label}</span>',
                    '</div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        )
    });

    Auth.Admin.Nav.superclass.constructor.call(this, {
        autoScroll: true,
        cls: 'auth-admin-nav',
        items: dataView,
        title: 'Inicio'
    });
};

Ext.extend(Auth.Admin.Nav, Ext.Panel);
