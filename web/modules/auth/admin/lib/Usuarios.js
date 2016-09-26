Ext.ns('Auth.Admin');

Auth.Admin.Usuarios = Ext.extend(Ext.grid.EditorGridPanel, {

    ownerModule: null,
    desktop: null,
    
    constructor: function(config) {
        config = config || {};
        this.ownerModule = config.ownerModule;
        this.desktop = this.ownerModule.app.getDesktop();
      
      
        var store_usuarios = new Ext.data.JsonStore({
            url: 'administracion/listar_usuarios',
            root: "data",
            totalProperty: 'total',
            fields: ['user_id', 'username', 'email','checked',
            'lastlogin', 'created', 'modified', 'name', 'lastname', 'password_bd', 'activo']
             
        });
        store_usuarios.load({
            params:{
                start : 0, 
                limit : 20
            }
        });
        
        
    var store_usuarios_editar = new Ext.data.JsonStore({
        url: 'administracion/listar_roles_editar',
        root: "data",
        totalProperty: 'total',
        fields: ['role_id', 'name']
             
    });
    store_usuarios_editar.load({
        params:{
            user_id: 4
        }
    });
        
        
function cover_image(val,metaData)
{
    if(val=='activo')
    {
        metaData.css ='auth-admin-check-icon'; 
        return '';              
    }
}
       
       
var sm = new Ext.grid.CheckboxSelectionModel();
       
       
    var cm = new Ext.grid.ColumnModel(
       
        [ new Ext.grid.RowNumberer(),
        sm,
        {
            header: 'Usuario',
            dataIndex: 'username',
            width: 100
        }, {
            header: 'Correo',
            dataIndex: 'email',
            id: 'email',
            width: 120
        }, {
            header: 'Activo',
            dataIndex: 'activo',
            id: 'activo',
            renderer: cover_image,
            width: 50
        }, {
            header: 'Baneado',
            dataIndex: 'checked',
            scope: this,
            editor: {
                xtype: 'checkbox',
                scope: this,
                listeners: {
                    scope:this,
                    'change': function(Checkbox,checked,oldValue)
                    {
                        var record = Ext.getCmp('auth-admin-members').getSelectionModel().getSelected();
                        Ext.MessageBox.show({
                            title: (checked)? this.ownerModule.locale.userbanned : this.ownerModule.locale.infotitle,
                            msg: (checked)? this.ownerModule.locale.reason : this.ownerModule.locale.userunbanned,
                            width: 300,
                            buttons: Ext.MessageBox.OKCANCEL,
                            icon: (checked)?null:Ext.MessageBox.INFO,
                            multiline: checked,
                            scope: this,
                            fn: function(btnpresionado,reason)
                            {
                                if(btnpresionado != 'ok')
                                    record.reject();
                                else
                                {
                                    if((reason == '' && checked == false)|| (reason != '' && checked))
                                        setBanned(checked,record.data.user_id,reason,'this.ownerModule.locale');
                                    else
                                    {
                                        mostrarMensaje(3,'this.ownerModule.locale.notification.requerido.text');
                                        record.reject();
                                    }
                                }
                            }
                        });
                    }
                }
            },
            falseText: '<span style="color:green;">' + 'NO' + '</span>',
            menuDisabled: true,
            trueText: '<span style="color:red;">' + 'SI' + '</span>',
            checked: true,
            width: 60,
            xtype: 'booleancolumn'
        }, {
            header: 'Ultimo Acceso',
            dataIndex: 'lastlogin',
            width: 120
        }, {
            header: 'Creado',
            dataIndex: 'created',
            width: 120
        }, {
            header: 'Modificado',
            dataIndex: 'modified',
            width: 120
        }]);

    cm.defaultSortable = true;
    this.getSelectionModel().singleSelect = true;
    this.getSelectionModel().moveEditorOnEnter = false;
        
    var bbar = new Ext.PagingToolbar({
        displayInfo: true,
        displayMsg: 'Mostrar usuario {0} - {1} de {2}',
        emptyMsg: 'No hay usuarios para mostrar',
        pageSize: 10,
        store: store_usuarios
    });
        
        
    Ext.applyIf(config, {
        id: 'auth-admin-members',
        border: false,
        closable: true,
        clicksToEdit: 1,
        loadMask: true,
        stripeRows: true,
        autoExpandColumn: 'email',
        cm: cm,
        sm: sm,
        store: store_usuarios,
        bbar: bbar,
        tbar: [{
            handler: function() 

            {
                this.CreateWindows('Añadir Usuario', 1);
            },
            disabled: this.ownerModule.app.isAllowedTo('administracion', 'insertar_usuario') ? false : true,
            iconCls: 'auth-admin-group-addrol-icon',
            scope: this,
            id: 'addmember',
            tooltip: 'Añadir nuevo usuario',
            text: this.ownerModule.locale.nuevo
        },'-', {
            disabled: true,
            iconCls: 'auth-admin-group-delrol-icon',
            scope: this,
            id: 'delmember',
            text: this.ownerModule.locale.delet,
            tooltip: 'Eliminar usuario',
            handler: function()
            {
                Ext.MessageBox.confirm('Confirmaci&oacute;n','Estas seguro que quieres eliminar el usuario seleccionado',
                    function(btn)
                    {
                        eliminaruser(btn);
                    }) 
            }
        },'-', {
            disabled: true,
            iconCls: 'auth-admin-group-editrol-icon',
            scope: this,
            id: 'modmember',
            handler: function() 
            {
                this.CreateWindows('Editar Datos del Usuario', 2);
            },
            text: this.ownerModule.locale.edit,
            tooltip: 'Editar usuario'
        },'->' ,{
            text: 'Buscar por:', 
            xtype: 'tbtext'
        },{
            xtype: 'textfield',
            id: 'fname',
            enableKeyEvents: true,
            listeners: {
                keypress: function(textField, eventObject)
                {
                    if (eventObject.getKey() == eventObject.ENTER)
                    {
                        this.filter();
                    }
                },
                scope:this
            }
        }],
        title: 'Usuarios',
        viewConfig: {
            emptyText: 'No hay usuarios para mostrar',
            getRowClass: function(r)
            {
                var d = r.data;
                if (parseInt(d.active)) 
                {
                    return 'auth-admin-inactive';
                }
                return '';
            }
        }
    });

    Auth.Admin.Usuarios.superclass.constructor.call(this, config);

    this.CreateWindows = function(title, op)
    {
                                 
        /* var store = new Ext.data.JsonStore({
                                        url: '/administracion/listar_roles',
                                        root: 'data',
                                        totalProperty: 'total',
                                        fields:['role_id', 'name']


                                     });
                                    store.load({params:{start: 0, limit: 20}});

                                    var smrole = new Ext.grid.CheckboxSelectionModel();

                                    var cmrole = new Ext.grid.ColumnModel([
                                        smrole,
                                       {
                                         header: 'Roles',
                                         dataIndex: 'name',
                                         sortable: true ,
                                         width: 150
                                       }]);

                                     var gridRole = new Ext.grid.GridPanel({
                                         region:'east',
                                         id: 'grid-roles',
                                         cm: cmrole,
                                         sm: smrole,
                                         store: store,
                                         width: 180,
                                         stripeRows: true,
                                         loadMask: true
                                     });*/
                                     
        var formulariomember = new Ext.FormPanel({
            labelWidth: 120,
            region: 'center',
            frame: true,
            width: 400,
            id: 'formulariomember',
            bodyStyle: 'padding:5px 5px 0',
            items: [{
                xtype: 'fieldset',
                title: 'Datos del Usuario',
                collapsible: false,
                autoHeight: true,
                defaults: {
                    width: 210
                },
                defaultType: 'textfield',
                items: [{
                    fieldLabel: 'Nombre',
                    name: 'name',
                    vtype: 'alpha',
                    maxLength: 20,
                    allowBlank: false,
                    blankText: 'Campo requerido.',
                    maxLengthText: 'El tamaño máximo permitido es de 20.',
                    id: 'name'
                }, {
                    fieldLabel: 'Apellidos',
                    name: 'lastname',
                    vtype: 'alpha',
                    maxLength: 20,
                    allowBlank: false,
                    blankText: 'Campo requerido.',
                    maxLengthText: 'El tamaño máximo permitido es de 20.',
                    id : 'lastname'
                },{
                    fieldLabel: 'Usuario',
                    name: 'username',
                    vtype: 'alpha',
                    maxLength: 20,
                    allowBlank: false,
                    blankText: 'Campo requerido.',
                    maxLengthText: 'El tamaño máximo permitido es de 20.',
                    id: 'username'
                }, {
                    fieldLabel: 'Correo',
                    name: 'email',
                    blankText: 'Campo requerido.',
                    allowBlank: false,
                    vtype: 'email'
                }, {
                    fieldLabel: 'Contraseña',
                    hidden: (op == 2) ? true : false,
                    name: 'password',
                    allowBlank: (op == 1) ? false : true,
                    blankText: 'Campo requerido.',
                    inputType: 'password',
                    minLength: 8,
                    maxLength: 50,
                    maxLengthText: 'El tamaño máximo permitido es de 50.',
                    minLengthText: 'El tamaño mínimo permitido es de 8.',
                    id: 'password'
                }, {
                    fieldLabel: 'Confirmar Contraseña',
                    hidden: (op == 2) ? true : false,
                    name: 'cpassword',
                    vtype: 'password',
                    minLength: 8,
                    maxLength: 50,
                    allowBlank: (op == 1) ? false : true,
                    maxLengthText: 'El tamaño máximo permitido es de 50.',
                    minLengthText: 'El tamaño mínimo permitido es de 8.',
                    blankText: 'Campo requerido.',
                    id: 'cpassword',
                    initialPassField: 'password',
                    inputType: 'password'
                }]
            },{
                xtype: 'fieldset',
                checkboxToggle: true,
                hidden: (op == 1) ? true : false,
                title: 'Cambiar Contraseña',
                collapsed: true,
                autoHeight: true,
                defaults: {
                    width : 210
                },
                listeners: {
                    'collapse': function()
                    {
                        Ext.getCmp('epassword').reset();
                        Ext.getCmp('ecpassword').reset();
                        Ext.getCmp('ecpassword').allowBlank = true;
                        Ext.getCmp('epassword').allowBlank = true;
                    },
                    'expand': function()
                    {
                        Ext.getCmp('ecpassword').allowBlank = false;
                        Ext.getCmp('epassword').allowBlank = false;
                    }

                },
                defaultType: 'textfield',
                items: [{
                    fieldLabel: 'Nueva Contraseña',
                    name: 'epassword',
                    allowBlank: true,
                    blankText: 'Campo requerido.',
                    inputType: 'password',
                    minLength: 8,
                    maxLength: 50,
                    maxLengthText: 'El tamaño máximo permitido es de 50.',
                    minLengthText: 'El tamaño mínimo permitido es de 8.',
                    id: 'epassword'
                }, {
                    fieldLabel: 'Confirmar Contraseña',
                    name: 'ecpassword',
                    vtype: 'password',
                    minLength: 8,
                    maxLength: 50,
                    allowBlank: true,
                    maxLengthText: 'El tamaño máximo permitido es de 50.',
                    minLengthText: 'El tamaño mínimo permitido es de 8.',
                    blankText: 'Campo requerido.',
                    id: 'ecpassword',
                    initialPassField: 'epassword',
                    inputType: 'password'
                }

                ] 
            }]
        });

        var tree = new Ext.tree.TreePanel({
            region: 'east',
            id: 'tree',
            title: 'Roles',
            height: 320,
            width: 200,
            useArrows: true,
            lines: false,
            cls: 'pref-card pref-check-tree',
            autoScroll: true,
            animate: true,
            enableDD: false,
            containerScroll: true,
            rootVisible: false,
            frame: true,
            root: new Ext.tree.AsyncTreeNode(),
            loader: new Ext.tree.TreeLoader({
                dataUrl: (op == 1) ? '/administracion/mostrar_roles' : loadTree()
            }),
            listeners: {
                'checkchange': {
                    fn: onCheckChange, 
                    scope: this
                }
            }
        });

        tree.getRootNode().expand(true);

        var panel = new Ext.Panel({
            layout: 'border',
            items: [tree, formulariomember],
            bbar: [ '->',
            {
                text: this.ownerModule.locale.save,
                id: 'salvar',
                iconCls: 'auth-admin-group-saverol-icon',
                border: true,
                handler: function()
                {
                    salvarDatos(op);
                }
            }, {
                text: this.ownerModule.locale.cancel,
                iconCls: 'auth-admin-group-delrol-icon',
                border: true,
                handler: function() 
                {
                    var s = Ext.getCmp('auth-admin-members').getSelectionModel().getSelections();
                    if(s.lenght == 0)
                    {
                        Ext.getCmp('delmember').setDisabled(true);
                        Ext.getCmp('modmember').setDisabled(true);
                    }
                    win.close();
                }
            }]
        });

        win = new Ext.Window({
            layout: 'fit',
            height: (op == 1) ? 300 : 320,
            width: 600,
            title: title,
            items: [panel],
            closeAction: 'close',
            modal: true
        });
                                    
        win.show((op == 1) ? 'addmember' : 'modmember');
        if (op == 2)
        {
            var record = this.getSelectionModel().getSelected();
            formulariomember.getForm().loadRecord(record);
        }
    }

    this.on({
        'render': {
            fn: function() 
            {
                this.getStore().load();
            },
            scope: this,
            single: true
        }
    });
        
    function saveComplete(title, msg)
    {
        notifyWin.setIconClass('icon-done');
        notifyWin.setTitle(title);
        notifyWin.setMessage(msg);
        desktop.hideNotification(notifyWin);
        if(callback)
        {
            callback.call(callbackScope);
        }
    }
        
    function loadTree()
    {
        var user_id = Ext.getCmp('auth-admin-members').getSelectionModel().getSelected().data.user_id
        return '/administracion/mostrar_roles_actualizar?user_id='+user_id+'';
    }
       
     
    function setBanned(banned,iduser,reason,locale)
    {
        Ext.Ajax.request({
            url: '/administracion/banear',
            params: {
                banned : banned,
                iduser : iduser,
                reason : reason
            },
            callback : function(options, success, response) 
            {
                responseData = Ext.decode(response.responseText);
                if(responseData.codMsg == 1)
                {
                /* mostrarMensaje(1,locale.notification.satisfactorio.text,function(){
                                            Ext.getCmp('auth-admin-members').getStore().load();
                                            Ext.getCmp('auth-admin-members').getSelectionModel().fireEvent('rowdeselect');
                                        });*/
                }
            }
        });
    }
        

    function eliminaruser(btn)
    {
        if(btn === 'yes')
        {
            var s = Ext.getCmp('auth-admin-members').getSelectionModel().getSelections();
            var user_ids = [];
            for(var i = 0; i < s.length; i++)
            {
                user_ids[i] = s[i].data.user_id;
            }
            user_ids = Ext.encode(user_ids);
            Ext.Msg.wait('Eliminando  usuarios', 'Por favor espere ...');
            Ext.Ajax.request({
                url: '/administracion/eliminar_usuario',
                params: {
                    'user_id': user_ids
                },
                method: 'POST',
                 
                success:function(o)
                {
                    if(o && o.responseText && Ext.decode(o.responseText).success)
                    {
                        store_usuarios.load();
                        Ext.getCmp('delmember').setDisabled(true);
                        Ext.getCmp('modmember').setDisabled(true);
                        Ext.Msg.show({
                            title: 'Correcto',
                            msg: 'Usuario (s) eliminado (s) correctamente.',
                            icon: Ext.MessageBox.INFO,
                            buttons: Ext.Msg.OK
                        });
                    }
                    else
                    {
                        Ext.Msg.show({
                            title: 'Error',
                            msg: 'No se pudo eliminar los usuarios seleccionados.',
                            icon: Ext.MessageBox.ERROR,
                            buttons: Ext.Msg.OK
                        });  
                        store_usuarios.load();
                    }
                },
                failure:function()
                {
                    Ext.Msg.show({
                        title: 'Error',
                        msg: 'Se perdio la conección con el servidor',
                        icon: Ext.MessageBox.ERROR,
                        buttons: Ext.Msg.OK
                    });
                }
            });
                           
        }
    }
      
      
    function salvarDatos(op)
    {
        /*var desktop = this.ownerModule.app.getDesktop();
    	    var notifyWin = desktop.showNotification({
			html: 'this.locale.notification.saving.msg'
			, title: 'this.locale.notification.saving.title'
            });*/
            
        var formulario = Ext.getCmp('formulariomember');
        var s = Ext.getCmp('tree').getChecked();
        var flag = false;
        if(op == 1)
        {
            if(formulario.getForm().isValid())
            {
                if(s.length > 0)
                {
                    flag = true;
                }
                else
                    Ext.MessageBox.alert('Error', 'Debe asignarle al menos un rol al usuario', function(){});
            }
        }
        else 
        {
            if(op == 2)
            {
                if(formulario.getForm().isDirty())
                    if(formulario.getForm().isValid())
                    {
                        if(s.length > 0)
                        {
                            flag = true;
                        }
                        else
                            Ext.MessageBox.alert('Error', 'Debe asignarle al menos un rol al usuario', function(){});
                    }
            }
        }
        if(flag)
        {
            if(op == 1)
                Ext.Msg.wait('Insertando el Usuario', 'Por favor espere ...');
            else
                Ext.Msg.wait('Modificando el Usuario', 'Por favor espere ...');      
            formulario.getForm().submit({
                url: (op == 1) ? '/administracion/insertar_usuario' : '/administracion/editar_usuario',
                // waitMsg: (op == 1) ? 'Insertando el Usuario' : 'Modificando el Usuario',
                params: {
                    user_id: (op == 2) ? Ext.getCmp('auth-admin-members').getSelectionModel().getSelected().data.user_id : 'nada',
                    //password_bd : (op == 2) ? Ext.getCmp('auth-admin-members').getSelectionModel().getSelected().data.password_bd : 'nada',
                    checkeds: retrieveChecked()
                        
                },
                success:function(f, action)
                {
                    Ext.MessageBox.alert('Correcto', action.result.msg, function(btn){
                        if(btn === 'ok')
                        {
                            if(op == 2)
                            {
                                Ext.getCmp('delmember').setDisabled(true);
                                Ext.getCmp('modmember').setDisabled(true); 
                                win.close()
                            }
                                      
                        }
                    });
                    formulario.getForm().reset();
                    store_usuarios.load();
                    Ext.getCmp('tree').getRootNode().reload();
                },
                failure:function(f, action)
                {
                    Ext.MessageBox.alert('Error', action.result.msg, function(btn){
                        if(btn === 'ok')
                        {
                            if(op == 2)
                            {
                                Ext.getCmp('delmember').setDisabled(true);
                                Ext.getCmp('modmember').setDisabled(true); 
                            }

                        }
                        win.close();
                    });
                }
            });
        /*function saveComplete(title, msg){
			notifyWin.setIconClass('icon-done');
			notifyWin.setTitle(title);
			notifyWin.setMessage(msg);
			desktop.hideNotification(notifyWin);
		}*/
        }
    }
        
    function retrieveChecked()
    {
        var id = [];
        var i = 0;
        var selnode = Ext.getCmp('tree').getChecked();
        Ext.each(selnode, function(node){
            id[i] = node.id;
            i++;
        } );
        return Ext.encode(id);
    }
                
    /* function retrieveChecked()
        {
            var s = Ext.getCmp('grid-roles').getSelectionModel().getSelections();
            var roles_ids = [];
            for(var i = 0; i < s.length; i++)
            {
                roles_ids[i] = s[i].data.role_id;
            }
            roles_ids = Ext.encode(roles_ids);
            return roles_ids;
        }*/
        
        
    /*function checkHandler(item, checked){
            if(checked){
                var store = this.getStore();
                store.baseParams.filterField = item.key;
                searchFieldBtn.setText(item.text);
            }
        }*/
        
    function onCheckChange(node, checked)
    {
        if(checked)
        {
            node.getUI().addClass('complete');
        }
        else
        {
            node.getUI().removeClass('complete');
        }
        node.ownerTree.selModel.select(node);
    }
        
    Ext.apply(Ext.form.VTypes, {
        password: function(val, field) 
        {
            if (field.initialPassField)
            {
                var pwd = Ext.getCmp(field.initialPassField);
                return (val == pwd.getValue());
            }
            return true;
        },

        passwordText: 'La contraseña no coinciden',
            
        vpassword: function(val)
        {
            var pwd = Ext.getCmp('auth-admin-members').getSelectionModel().getSelected().data.password;
            if(pwd)
                return (val == pwd)
            else
                return true;
        },
        vpasswordText: 'La contraseña no coinciden 1' 
            
           
    });
        
    var ownerModule = this.ownerModule;
    this.getSelectionModel().on({
        'rowselect' : function(sm, rowIndex, record) 
        {
            var s = Ext.getCmp('auth-admin-members').getSelectionModel().getSelections();
            if(s.length == 1)
            {
                if(ownerModule.app.isAllowedTo('administracion', 'editar_usuario'))
                    Ext.getCmp('modmember').enable();
                                    
                if(ownerModule.app.isAllowedTo('administracion', 'eliminar_usuario'))
                    Ext.getCmp('delmember').enable();
                                   
            }
            else
            { 
                if(ownerModule.app.isAllowedTo('administracion', 'eliminar_usuario'))
                    Ext.getCmp('delmember').enable();
                                 
                Ext.getCmp('modmember').disable();

            }
        },
        'rowdeselect' : function(sm, rowIndex, record) 
        {
            var s = Ext.getCmp('auth-admin-members').getSelectionModel().getSelections();
            if(s.length > 0)
            {
                if(ownerModule.app.isAllowedTo('administracion', 'eliminar_usuario'))
                    Ext.getCmp('delmember').enable();
                if(s.length == 1)
                    if(ownerModule.app.isAllowedTo('administracion', 'editar_usuario'))
                        Ext.getCmp('modmember').enable();
            }
            else
            {
                if(s.length == 0)
                {
                    Ext.getCmp('delmember').disable();
                    Ext.getCmp('modmember').disable();
                }
            }
        }
            
    });
},
filter: function(){
    Ext.getCmp('auth-admin-members').getStore().reload({
        params: {
            fname:Ext.getCmp('fname').getValue(), 
            start:0,
            limit:20
        }
    });
}
});
