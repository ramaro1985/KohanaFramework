Ext.ns('Auth.Admin');

Auth.Admin.Roles = Ext.extend(Ext.grid.GridPanel, {

      ownerModule: null,
      desktop: null,
      formulario: null,
      
      constructor: function(config) {
        config = config || {};

        this.ownerModule = config.ownerModule;
        this.desktop = this.ownerModule.app.getDesktop();
        
        var store = new Ext.data.JsonStore({
            url : '/administracion/listar_roles',
            root : 'data',
            totalProperty : 'total',
            fields:['role_id', 'name', 'description', 'parent_id']
           
           
        });
        store.load({params:{start : 0, limit : 20}});
        
        var sm = new Ext.grid.CheckboxSelectionModel();
         
        var cm = new Ext.grid.ColumnModel([
             
            new Ext.grid.RowNumberer(),
            sm,
            {
                header: 'Nombre',
                dataIndex: 'name',
                sortable: true ,
                width: 120
            }, {
                id: 'descripcion',
                header: 'Descripci&oacute;n',
                dataIndex: 'description',
                sortable: true ,
                width: 200
            }, {
                header: 'Padre',
                dataIndex: 'parent_id',
                sortable: true ,
                renderer: function(val)
                          {
                             if(val == 0)
                               return 'System';  
                          },
                width: 120
        }]);

        this.getSelectionModel().singleSelect = true;

        var bbar = new Ext.PagingToolbar({
            displayInfo: true,
            displayMsg: 'Mostrar rol {0} - {1} de {2}',
            emptyMsg: 'No existe rol para mostrar',
            pageSize: 10,
            store: store
        });
            
        Ext.applyIf(config, {
            id: 'auth-admin-groups',
            border: false,
            autoWidth: true,
            closable: true,
            autoExpandColumn: 'descripcion',
            clicksToEdit: 1,
            loadMask: true,
            stripeRows: true,
            cm: cm,
            sm: sm,
            store: store,
            bbar: bbar,
            tbar: [{
                handler: function() 
                          {
                             this.CreateWindows('Añadir Rol', 1);
                          },
                disabled: this.ownerModule.app.isAllowedTo('administracion', 'insertar_rol') ? false : true,
                iconCls: 'auth-admin-group-addrol-icon',
                scope: this,
                id: 'addrol',
                text: this.ownerModule.locale.nuevo,
                tooltip: 'Añadir nuevo rol'
            }, '-', {
                disabled: true,
                iconCls: 'auth-admin-group-delrol-icon',
                scope: this,
                id: 'delrol',
                text:  this.ownerModule.locale.delet,
                tooltip: 'Eliminar rol',
                handler: function()
                         {
                           Ext.MessageBox.confirm('Confirmaci&oacute;n','Estas seguro que quieres eliminar el rol seleccionado',
                               function(btn)
                               {
                                   eliminarrol(btn);
                               }) 
                         }
            }, '-', {
                disabled: true,
                iconCls: 'auth-admin-group-editrol-icon',
                scope: this,
                text:  this.ownerModule.locale.edit,
                id: 'modrol',
                tooltip:'Editar rol',
                handler: function()
                         {
                            this.CreateWindows('Editar', 2);
                         }
                
            }, '-', {
                disabled: true,
                iconCls: 'auth-admin-permissions-icon',
                scope: this,
                text:  this.ownerModule.locale.permissions,
                id: 'privilegios',
                tooltip:'Asignar privilegios ',
                handler: function() 
                         {
                            this.CreateWindowsPermissions('Asignar Privilegios');
                         }
                
            }],
           title: 'Roles',
           viewConfig: {
                emptyText: 'No existe rol para mostrar...'
           }
        });

        Auth.Admin.Roles.superclass.constructor.call(this, config);


        this.CreateWindowsPermissions = function(title) 
                                        {
                                          var tree = new Ext.tree.TreePanel({
                                              id: 'tree',
                                              title: 'Privilegios',
                                              height: 320,
                                              width: 200,
                                              useArrows: true,
                                              cls: 'pref-card pref-check-tree',
                                              autoScroll: true,
                                              animate: true,
                                              containerScroll: true,
                                              rootVisible: false,
                                              frame: true,
                                              root: {
                                                    nodeType: 'async'
                                              },
                                              dataUrl: loadTree(),
                                              listeners: {
                                                              'checkchange': {fn: onCheckChange, scope: this}
                                                         }
                                          });
                                          tree.getRootNode().expand();
            
                                          var panel = new Ext.Panel({
                                              layout: 'fit',
                                              items: [tree],
                                              bbar: [ '->',
                                                     {
                                                        disabled: true,
                                                        text: this.ownerModule.locale.save,
                                                        id: 'salvar',
                                                        iconCls: 'auth-admin-group-saverol-icon',
                                                        border: true,
                                                        handler: function()
                                                                  {
                                                                      savePermissions();
                                                                      var s = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelections();
                                                                      if(s.lenght == 0)
                                                                      {
                                                                          if(this.ownerModule.app.isAllowedTo('administracion', 'insertar_privilegios'))
                                                                              Ext.getCmp('privilegios').disable();
                                                                          if(this.ownerModule.app.isAllowedTo('administracion', 'eliminar_rol'))
                                                                              Ext.getCmp('delrol').disable();
                                                                          if(this.ownerModule.app.isAllowedTo('administracion', 'editar_rol'))
                                                                              Ext.getCmp('modrol').disable();
                                                                              
                                                                      }
                                                                  }
                                                      }, {
                                                        text: this.ownerModule.locale.cancel,
                                                        iconCls: 'auth-admin-group-delrol-icon',
                                                        border: true,
                                                        handler: function() 
                                                                 {
                                                                    var s = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelections();
                                                                    if(s.lenght == 0)
                                                                    {
                                                                         if(this.ownerModule.app.isAllowedTo('administracion', 'insertar_privilegios'))
                                                                              Ext.getCmp('privilegios').disable();
                                                                          if(this.ownerModule.app.isAllowedTo('administracion', 'eliminar_rol'))
                                                                              Ext.getCmp('delrol').disable();
                                                                          if(this.ownerModule.app.isAllowedTo('administracion', 'editar_rol'))
                                                                              Ext.getCmp('modrol').disable();
                                                                    }
                                                                    win.close();
                                                                 }
                                                    }]
                                          });
                                          win = new Ext.Window({
                                                layout: 'fit',
                                                height: 400,
                                                width: 400,
                                                title: title,
                                                items: panel,
                                                closeAction: 'close',
                                                modal: true
                                          });
                                          win.show('privilegios');
           

                                        }

        this.CreateWindows = function(title, op) 
                             {
                                this.formulariorol = new Ext.FormPanel({
                                     labelWidth: 120,
                                     frame: true,
                                     id: 'formulariorol',
                                     bodyStyle: 'padding:5px 5px 0',
                                     items: [{
                                        xtype: 'fieldset',
                                        title:'Datos del Rol',
                                        collapsible: false,
                                        autoHeight: true,
                                        defaults: {width: 210},
                                        defaultType: 'textfield',
                                        items: [{
                                            fieldLabel: 'Rol',
                                            name: 'name',
                                            vtype: 'alpha',
                                            allowBlank: false,
                                            blankText: 'Campo requerido.',
                                            id: 'name'
                                        }, {
                                            fieldLabel: 'Descripción',
                                            xtype: 'textarea',
                                            id: 'descripcion'
                                        }]
                                    }],
                                    buttons: [{
                                        text: this.ownerModule.locale.save,
                                        iconCls: 'auth-admin-group-saverol-icon',
                                        handler: function()
                                                 {
                                                    salvarDatos(op);
                                                 }
                                    }, {
                                        text: this.ownerModule.locale.cancel,
                                        iconCls: 'auth-admin-group-delrol-icon',
                                        handler: function() 
                                                 {
                                                    var s = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelections();
                                                    if(s.lenght == 0)
                                                    {
                                                        Ext.getCmp('delrol').setDisabled(true);
                                                        Ext.getCmp('modrol').setDisabled(true);
                                                        Ext.getCmp('privilegios').setDisabled(true);
                                                    }
                                                    win.close();
                                                 }
                                    }]
                                });
                                win = new Ext.Window({
                                      layout: 'fit',
                                      height: 220,
                                      width: 400,
                                      title: title,
                                      items: this.formulariorol,
                                      closeAction: 'close',
                                      modal: true
                                });
                                win.show((op == 1) ? 'addrol' : 'modrol');
                                if (op == 2)
                                {
                                    var record = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelected();
                                    this.formulariorol.getForm().loadRecord(record);
                                    Ext.getCmp('descripcion').setRawValue(record.data.description)
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
    
    
     
        function eliminarrol(btn)
        {
           if(btn === 'yes')
           {
              var s = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelections()
              var roles_ids = [];
              for(var i = 0; i < s.length; i++)
              {
                  roles_ids[i] = s[i].data.role_id;
              }
              roles_ids = Ext.encode(roles_ids);
              Ext.Msg.wait('Eliminando  roles', 'Por favor espere ...');
              Ext.Ajax.request({
                  url: '/administracion/eliminar_rol',
                  params: {'roles_ids': roles_ids},
                  method: 'POST',
                  
                  success:function(o)
                          {
                             if(o && o.responseText && Ext.decode(o.responseText).success)
                             {
                                 Ext.Msg.show({title: 'Correcto',
                                      msg: 'Rol (es) eliminado (s) correctamente.',
                                      icon: Ext.MessageBox.INFO,
                                      buttons: Ext.Msg.OK
                                 });
                                 store.load();
                                 Ext.getCmp('delrol').setDisabled(true);
                                 Ext.getCmp('modrol').setDisabled(true);
                                 Ext.getCmp('privilegios').setDisabled(true);
                             }
                             else
                             {
                                Ext.Msg.show({title: 'Error',
                                   msg: 'Existen  usuarios con el rol seleccionado asignado. Debe desasignar los roles a los usuarios.',
                                   icon: Ext.MessageBox.ERROR,
                                   buttons: Ext.Msg.OK
                                });  
                                 store.load();
                             }
                         },
                  failure: function()
                           {
                              Ext.Msg.show({title: 'Error',
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
            var formulario = Ext.getCmp('formulariorol');
            var flag = false;
            if(op == 1)
            {
                if(formulario.getForm().isValid())
                    flag = true;
            }
            else 
            {
                if(op == 2)
                {
                   if(formulario.getForm().isDirty())
                      if(formulario.getForm().isValid())
                           flag = true;
                }
            }   
            if(flag)
            {
                if(op == 1)
                  Ext.Msg.wait('Insertando el Rol', 'Por favor espere ...');
                else
                  Ext.Msg.wait('Modificando el Rol', 'Por favor espere ...');      
                formulario.getForm().submit({
                url: (op == 1) ? '/administracion/insertar_rol' : '/administracion/editar_rol',
               // waitMsg: (op == 1) ? 'Insertando el Rol' : 'Modificando el Rol',
                params: {
                    rol_id : (op == 2) ? Ext.getCmp('auth-admin-groups').getSelectionModel().getSelected().data.role_id : 'nada'
                },
                
                success: function(f, action)
                         {
                            Ext.MessageBox.alert('Correcto', action.result.msg, function(btn){
                                if(btn === 'ok')
                                {
                                   Ext.getCmp('delrol').setDisabled(true);
                                   Ext.getCmp('modrol').setDisabled(true);
                                   Ext.getCmp('privilegios').setDisabled(true);
                                }
                            });
                            formulario.getForm().reset();
                            store.load();
                            if(op == 2)  win.close();
                         },
                failure: function(f, action)
                         {
                             Ext.MessageBox.alert('Error', action.result.msg, function(btn){
                                 if(btn === 'ok')
                                 {
                                    if(op == 2)
                                    {
                                         Ext.getCmp('delrol').setDisabled(true);
                                         Ext.getCmp('modrol').setDisabled(true);
                                         Ext.getCmp('privilegios').setDisabled(true);
                                    }
                                       
                                 }
                                 win.close();
                             });
                             formulario.getForm().reset();
                         }
                
              
                });
            }
        }
        
        
        var modules = [];
        function retrieveChecked()
        {
                modules = []
                var controller;
                var actions = [];
                var permissions = [];
                var node;
                var childrenctrl = [];
                var l = 0;
                var m = 0;
                var selnode = Ext.getCmp('tree').getChecked();
                for(var i = 0; i < selnode.length; i++)
                {
                   node = selnode[i];
                   var depth = node.getDepth();
                   if(node.parentNode.attributes.checked == false)
                   {
                       Ext.MessageBox.alert('Alerta','Debe seleccionar el elemento que contiene el elemento marcado'); 
                       return null;
                   }
                   else
                   {
                       if(depth == 1)
                       {
                          if(node.hasChildNodes())
                          {
                             childrenctrl = node.attributes.children;
                             for(var g = 0; g < childrenctrl.length; g++)
                             {
                                var count = 0;
                                if(childrenctrl[g].checked == true)
                                {
                                   controller = childrenctrl[g].text;
                                   controller = controller.substring(8);
                                   var child = childrenctrl[g].children;
                                   actions = [];
                                   var k = 0;
                                   for(var f = 0; f < child.length; f++)
                                   {
                                       if(child[f].checked == true)
                                       {
                                           actions[k] = child[f].text;
                                           k++;
                                       }
                                   }
                                   if(k > 0)
                                   {
                                      permissions[m] = [controller, actions];
                                      m++; 
                                      count++;
                                   }
                                }
                                
                             } 
                             childrenctrl = [];
                             if(count > 0)
                             {
                               var mod = node.text;
                               mod = mod.substring(7);
                               modules[l] = mod;
                               l++;  
                             }
                          }
                        }
                    }
               }
              return  Ext.encode(permissions); 
        }

    
       function savePermissions()
        {
            var role_id = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelected().data.role_id;
            var privilegios = retrieveChecked();
            if(privilegios != null && modules != '')
            {
                Ext.Msg.wait('Insertando Privilegios', 'Por favor espere ...');
                Ext.Ajax.request({
                   url: '/administracion/insertar_privilegios',
                   params: {'privilegios': privilegios, 'role_id': role_id, 'modules': Ext.encode(modules)},
                   method: 'POST',
                   
                   success: function(action)
                            {
                                Ext.Msg.show({title: 'Correcto',
                                   msg: 'Privilegios asignados correctamente.',
                                   icon: Ext.MessageBox.INFO,
                                   buttons: Ext.Msg.OK
                                });
                                var s = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelections();
                                if(s.lenght == 0)
                                {
                                   Ext.getCmp('delrol').disable();
                                   Ext.getCmp('modrol').disable();
                                   Ext.getCmp('privilegios').disable();
                                }
                                win.close();
                            }
              });
            }
           
        }
     
       function loadTree()
       {
            var role_id = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelected().data.role_id;
            return '/administracion/listar_privilegios?role_id='+role_id+'';
       }
     
     
    
       function onCheckChange(node, checked)
       {
           if(checked)
           {
              node.getUI().addClass('complete');
              if(node.isLeaf())
              {
                  if(ownerModule.app.isAllowedTo('administracion', 'insertar_privilegios'))
                     Ext.getCmp('salvar').enable();
              }
           }
           else
           {
             node.getUI().removeClass('complete');
             var count = 0;
             var selnode = Ext.getCmp('tree').getChecked();
             for(var i = 0; i < selnode.length; i++)
             {
                if(selnode[i].isLeaf())
                {
                   count++; 
                }
             }
             if(count >= 1)
                 if(ownerModule.app.isAllowedTo('administracion', 'insertar_privilegios'))
                    Ext.getCmp('salvar').enable();
             else
                  Ext.getCmp('salvar').disable();
           }
	    node.ownerTree.selModel.select(node);
          
       }
       var ownerModule = this.ownerModule;
       this.getSelectionModel().on({
           'rowselect': function(sm, rowIndex, record) 
                        {
                            var s = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelections();
                            if(s.length == 1)
                            {
                                if(ownerModule.app.isAllowedTo('administracion', 'editar_rol'))
                                    Ext.getCmp('modrol').enable();
                                if(ownerModule.app.isAllowedTo('administracion', 'eliminar_rol'))
                                    Ext.getCmp('delrol').enable();
                                if(ownerModule.app.isAllowedTo('administracion', 'listar_privilegios'))
                                    Ext.getCmp('privilegios').enable();
                            }
                            else
                            { 
                                if(ownerModule.app.isAllowedTo('administracion', 'eliminar_rol'))
                                    Ext.getCmp('delrol').enable();
                                
                                    Ext.getCmp('modrol').disable();
                                    Ext.getCmp('privilegios').disable();
                            }
            
                        },
        'rowdeselect': function(sm, rowIndex, record) 
                        {
                                var s = Ext.getCmp('auth-admin-groups').getSelectionModel().getSelections();
                                if(s.length > 0)
                                {
                                    if(ownerModule.app.isAllowedTo('administracion', 'eliminar_rol'))
                                       Ext.getCmp('delrol').enable();
                                    if(s.length == 1)
                                    {
                                        if(ownerModule.app.isAllowedTo('administracion', 'editar_rol'))
                                            Ext.getCmp('modrol').enable(); 
                                        if(ownerModule.app.isAllowedTo('administracion', 'listar_privilegios'))
                                            Ext.getCmp('privilegios').enable();
                                    }
                                }
                                else
                                {
                                    if(s.length == 0)
                                    {
                                      Ext.getCmp('delrol').disable();
                                      Ext.getCmp('modrol').disable();
                                      Ext.getCmp('privilegios').disable();
                                    }
                                }
                        }
        });
    }

  });
