Ext.ns('BalanceMaterial.FormulaMaestra');
BalanceMaterial.FormulaMaestra.GestionFormulaMaestra = Ext.extend(Ext.Panel, {
    ownerModule : null,
    desktop : null,
    formulario : null,
    constructor : function(config) {
        config = config || {};
        this.ownerModule = config.ownerModule;
        this.desktop = this.ownerModule.app.getDesktop();
        this.actions = {
            'GestionFormulaMaestra' : function(ownerModule){
                ownerModule.viewCard('GestionFormulaMaestra');
            }
        };
        var w = parseInt(this.desktop.getWinWidth() * 1);
        var h = parseInt(this.desktop.getWinHeight() * 1);
        if(h > 800){
            h = 800;
        }
        //---------Columna Izquierda completa-------// 
        var store_laboratorios = new Ext.data.JsonStore({  
            url: 'formulaMaestra/nombre_laboratorio',  
            root: 'datos',
            autoLoad:true,
            fields: ['id_lab','laboratorio']  
        });
        var store_productos = new Ext.data.JsonStore({  
            url: 'formulaMaestra/nombre_productos',  
            root: 'data',
            fields: ['id_producto','nombre','descripcion'],
            Property:'id_producto',
            totalProperty : 'total'
        });
        function descripcion(val, x, store_productos){
            return '<b>'+val+'</b><br>'+store_productos.data.descripcion;
        }
        var laboratorios= new Ext.form.ComboBox({
            width : w/2-150,
            name:'laboratorios',
            fieldLabel:'Laboratorios',
            store:store_laboratorios,
            mode: 'local',
            valueField:'id_lab',
            displayField:'laboratorio',
            editable: true,
            triggerAction:'all',
            allowBlank: false,
            blankText: 'Seleccione laboratorio.',
            emptyText: 'Seleccione laboratorio...',
            listeners:{
                'select':function(cmb,rec,idx){
                    store_productos.load({
                        params:{
                            id_laboratorio:this.getValue(),
                            start : 0,
                            limit : 20
                        }
                    });
                    tool_bar_formulas.setDisabled('disable');
                    store_formulas.removeAll();
                    tool_bar_mp.setDisabled('disable');
                    store_formulas_mp.removeAll();
                }
            }
        });
        this.doble_clik_productos = function(productos, rowIndex) {
            var record = productos.getStore().getAt(rowIndex);
            store_formulas.load({
                params:{
                    id_producto: record.get('id_producto')
                }
            });
            tool_bar_formulas.setDisabled();
            tool_bar_mp.setDisabled('disable');
            store_formulas_mp.removeAll();
        };
        this.productos = new Ext.grid.GridPanel({
            store:store_productos,
            height:h-65,
            stripeRows: true,
            forceFit: true,
            autoScroll:true,
            listeners : {
                rowdblclick :this.doble_clik_productos
            },
            columns: [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id_producto',
                hidden:true
            },{
                header: 'Nombre del Producto',
                dataIndex: 'nombre',
                width: (w/2)-36,
                renderer: descripcion
            }],
            tbar:new Ext.Toolbar({
                items:[{
                    xtype:'tbtext',
                    text:'Laboratorios:'
                },'',laboratorios]
            })
        });
        this.paginado_productos = new Ext.PagingToolbar({
            store: store_productos, 
            displayInfo: true,
            displayMsg: '{0} - {1} de {2} Productos',
            emptyMsg: 'No hay productos a mostrar',
            pageSize: 20
        });
        this.paginado_productos.on('beforechange',function(bar,params){  
            params.id_laboratorio = laboratorios.getValue();  
        });
        //----------End Columna Izquierda----------//
        //----------Columna Derecha----------//
        //--------Formulas-------//
        var store_formulas = new Ext.data.JsonStore({  
            url: 'formulaMaestra/nombre_formula',  
            root: 'datos',
            fields: ['id_formula','tipo_formula','predeterminada','id_prod_terminado','cant_productos']  
        });
        this.insertar_formula = function(){
            //-----------Esto es para agregar formulas----------//  
            var store_formulas_1 = new Ext.data.JsonStore({  
                url: 'formulaMaestra/nombre_formula',  
                root: 'datos',
                fields: ['id_formula','tipo_formula']  
            });
            store_formulas_1.load();
            
            var store_formulas_mp_1 = new Ext.data.JsonStore({  
                url: 'formulaMaestra/materias_primas_formula_seleccionada_a_partir_de',  
                fields: ['id_materia_prima','nombre','indice_consumo']
            });
            var text_field_formula = new Ext.form.TextField({
                fieldLabel: 'Nombre f\u00f3rmula',
                name: 'formula_name',
                width : w/3-200,
                allowBlank: false,
                blankText: 'Inserte nombre.'
            });
            var cant_productos = new Ext.form.NumberField({
                fieldLabel: 'Cant. Productos',
                name: 'cant_productos',
                width : w/3-200,
                allowBlank: false,
                blankText: 'Inserte cantidad productos.'
            });
            var check_predeterminada= new Ext.form.Checkbox({
                fieldLabel: 'Predeterminada',
                name: 'predeterminada',
                width : w/3-200                
            });
            this.content_datos_formula = new Ext.form.FieldSet({
                border:true,
                title:'Datos de la F\u00f3rmula',
                items:[text_field_formula,cant_productos,check_predeterminada],
                buttonAlign: 'center',
                buttons: [{
                    text:'Aceptar',
                    handler:function(){
                        agregar_formula.getForm().submit(
                        {
                            url: 'formulaMaestra/insertar_formula',
                            method:'POST',
                            success:function(f, action){
                                Ext.MessageBox.alert('Correcto', action.result.msg, function(){});
                                agregar_formula.getForm().reset();
                                store_formulas.reload();
                                agregar_formula.setDisabled(true);
                                agregar_materia_prima.setDisabled(false);
                                anndir_formula.setDisabled(false);
                            },
                            failure:function(f, action){
                                Ext.MessageBox.alert('Error', action.result.msg, function(){});
                                agregar_formula.getForm().reset();
                            }
                        });
                    }
                },{
                    text:'Cancel',
                    handler:function(){
                        agregar_formula.getForm().reset();
                    }
                }]
            })
            var agregar_formula = new Ext.FormPanel({  
                border:false,
                height: h/2+165,
                items:[this.content_datos_formula]
            });
            this.numberField = new Ext.form.NumberField({
                allowBlank:false,
                blankText: 'Insrte dato.'
            });
            //---------------End agragar Formulas-------------//
            //--------------Agregar MP ----------------------//
            var store_materias_primas = new Ext.data.JsonStore({  
                url: 'formulaMaestra/nombre_materias_primas',  
                root: 'datos',
                fields: ['id_producto','nombre'],
                autoLoad:true
            });
            var content_insertar_materia_prima = new Ext.FormPanel({
                name:'insertar_mp',
                border:false,
                frame:false,
                layout:'fit',
                autoWidth : true,
                items:[{
                    xtype:'combo',    
                    store:store_materias_primas,
                    name:'materia_prima',
                    emptyText: 'Seleccione materia prima...',
                    mode:'local',
                    displayField:'nombre',
                    editable: true,
                    triggerAction:'all',
                    listeners:{
                        'select':function(materias_primas_nombres,rec,idx){
                            content_insertar_materia_prima.getForm().submit(
                            {
                                url: 'formulaMaestra/insertar_materia_prima_pivote',
                                method:'POST',
                                success:function(f, action){
                                    content_insertar_materia_prima.getForm().reset();
                                    store_formulas_mp_1.reload();
                                },
                                failure:function(f, action){
                                    content_insertar_materia_prima.getForm().reset();
                                }
                            });
                        }
                    }
                }]
            });
            //--------------End MP -------------------------//
            //---------------A partir de------------//
            var formulas= new Ext.form.ComboBox({
                width : w/3-150,
                name: 'id_fomula_add',
                store: store_formulas_1,
                mode: 'local',
                valueField:'id_formula',
                displayField:'tipo_formula',
                editable: true,
                triggerAction:'all',
                emptyText: 'Seleccione F\u00f3rmula...',
                listeners:{
                    'select':function(cmb,rec,idx){
                        store_formulas_mp_1.load({
                            params:{
                                id_formula_a_partir_de:this.getValue()
                            }
                        });
                    }
                }
            });
            //---------------End de a partir de------------//
            //---------Funcion Guardar Cambios------------------//
            this.guardar_cambios_1 = function(){
                var modified = materias_primas.getStore().getModifiedRecords();
                if(!Ext.isEmpty(modified)){  
                    var recordsToSend = [];  
                    Ext.each(modified, function(record) {
                        recordsToSend.push(Ext.apply({
                            id:record.id
                        },record.data));  
                    });  
                    materias_primas.el.mask('Guardando…', 'x-mask-loading');
                    materias_primas.stopEditing();  
                    recordsToSend = Ext.encode(recordsToSend);   
                    Ext.Ajax.request({
                        url: 'formulaMaestra/editar_datos_mp_formula',  
                        params :{
                            records : recordsToSend
                        },  
                        scope:this,  
                        success : function(response) {
                            materias_primas.el.unmask();  
                            materias_primas.getStore().commitChanges();
                            
                        }  
                    });  
                }  
            };
            //---------End Funcion Guardar Cambios------------------//
            //--------- Grid Materias Primas ---------------------//
            var cm = new Ext.grid.ColumnModel([
            {
                header: 'id_materiaprima',
                dataIndex: 'id_materiaprima',
                hidden:true                       
            },{
                header: 'Nombre de Materia Prima',
                dataIndex: 'nombre',
                width: w/2
            },{
                header: 'Ind. Consumo',
                dataIndex: 'indice_consumo',
                editor:this.numberField,
                width: w/4-115
            }
            ]);
            var materias_primas =new  Ext.grid.EditorGridPanel({
                store:store_formulas_mp_1,
                stripeRows: true,
                id:'prueba',
                forceFit : true,
                height: h/2+160,
                autoScroll:true,
                columnLines:true,
                border:true,
                cm : cm,
                sm:new Ext.grid.RowSelectionModel({
                    singleSelect:true
                }),
                tbar:new Ext.Toolbar({
                    items:[
                    {
                        xtype:'tbtext',
                        text:'A partir de:'
                    },'',formulas,
                    {
                        xtype: 'tbbutton',
                        text: 'Eliminar MP',
                        iconCls: 'delete',
                        handler : function(){
                            Ext.MessageBox.confirm('Confirmaci&oacute;n','Est\u00e1 seguro que quiere eliminar la MP seleccionada',
                                function(btn){
                                    if(btn === 'yes')
                                    {
                                        var s = Ext.getCmp('prueba').getSelectionModel().getSelections();
                                        Ext.Ajax.request({
                                            url: 'formulaMaestra/eliminar_materia_prima',
                                            method: 'POST',
                                            params: {
                                                'materia_prima': Ext.getCmp('prueba').getSelectionModel().getSelected().data.nombre                                              
                                            },
                                            success:function(action){
                                                store_formulas_mp_1.remove(s);
                                            }
                                        });
                           
                                    }
                                }) 
                        }
                    },{
                        xtype: 'tbbutton',
                        text: 'Guardar Cambios',
                        iconCls: 'save',
                        handler:this.guardar_cambios_1
                    }
                    ]
                })
            });
            var agregar_materia_prima= new Ext.FormPanel({
                bodyStyle:'padding:5px',
                border:false,
                disabled:true,
                height: h/2+165,
                items:[content_insertar_materia_prima,materias_primas]
            });
            //---------End Grid Materias Primas ---------------------//
            var anndir_formula = new Ext.Toolbar({
                disabled : true,
                items:[
                {
                    xtype: 'tbbutton',
                    text: 'Nueva F\u00f3rmula Maestra',
                    iconCls: 'add',
                    handler : function(){
                        store_formulas_mp_1.removeAll();
                        store_formulas_1.reload();
                        agregar_materia_prima.setDisabled(true);
                        agregar_formula.setDisabled(false);
                    }
                }]
            })
            this.win_formula = new Ext.Window({  
                title: 'F\u00f3rmula Maestra-Materias Primas',
                layout:'column',
                modal: true,
                width:w-25,
                height: h/2+275,
                tbar:anndir_formula,
                items:[{
                    title: 'A\u00f1adir F\u00f3rmula Maestra',
                    columnWidth:.3,
                    border:true,
                    bodyStyle:'padding:10px',
                    items:[agregar_formula]
                },{
                    title: 'Materias Primas de la F\u00f3rmula Maestra',
                    columnWidth:.7,
                    border:true,
                    bodyStyle:'padding:10px',
                    items:[agregar_materia_prima]
                }]
            });  
            this.win_formula.show(); 
        };
        var tool_bar_formulas= new Ext.Toolbar({
            disabled:true,
            items: [{
                xtype: 'tbbutton',
                text: 'A\u00f1adir F\u00f3rmula Maestra',
                iconCls: 'add',
                handler:this.insertar_formula
            },{
                xtype: 'tbbutton',
                text: 'Eliminar F\u00f3rmula Maestra',
                iconCls: 'delete',
                handler:function(){
                    Ext.MessageBox.confirm('Confirmaci&oacute;n','Est\u00e1 seguro que quiere eliminar la f\u00f3rmula seleccionada',
                        function(btn){
                            if(btn === 'yes')
                            {
                                var s = formulas.getSelectionModel().getSelections();
                                Ext.Ajax.request({
                                    url: 'formulaMaestra/eliminar_formula',
                                    method: 'POST',
                                    params: {
                                        'formula_name': formulas.getSelectionModel().getSelected().data.tipo_formula                                              
                                    },
                                    success:function(action){
                                        store_formulas.remove(s);
                                        store_formulas_mp.removeAll();
                                        tool_bar_mp.disable();
                                    }
                                });
                           
                            }
                        }) 
                }
            },{
                xtype: 'tbbutton',
                text: 'Editar F\u00f3rmula Maestra',
                iconCls: 'edit',
                handler:function(){
                    var id_formula = new Ext.form.NumberField({
                        name:'id_formula',
                        hidden:true,
                        value:formulas.getSelectionModel().getSelected().data.id_formula
                    });
                    var nombre_formula = new Ext.form.TextField({
                        fieldLabel: 'Nombre f\u00f3rmula',
                        name:'tipo_formula',
                        allowBlank: false,
                        blankText: 'Inserte nombre.'
                    });
                    var predeterminada = new Ext.form.Checkbox({
                        fieldLabel: 'Predeterminada',
                        name:'predeterminada',
                        id:'predeterminada'
                    });
                    var aceptar = new Ext.Button({
                        text:'Editar',
                        handler:function(){
                            content_editar_formula.getForm().submit(
                            {
                                url: 'formulaMaestra/editar_formula',
                                method:'POST',
                                success:function(f, action){
                                    Ext.MessageBox.alert('Correcto', action.result.msg, function(){});
                                    store_formulas.reload();
                                }
                            });
                                
                        }
                    });
                    var content_column_left = new Ext.form.FieldSet({
                        border:false,
                        items:[id_formula,nombre_formula]
                    });
                    var content_column_rhigt = new Ext.form.FieldSet({
                        border:false,
                        items:[predeterminada]
                    });
                    var content_botton = new Ext.form.FieldSet({
                        border:false,
                        items:[aceptar]
                    });

                    var content_editar_formula = new Ext.FormPanel({
                        name:'editar_formula',
                        title:'Datos de la f\u00f3rmula.',
                        items:[{
                            layout:'column',
                            items:[{
                                columnWidth:.5,
                                border:false,
                                bodyStyle:'padding:5px',
                                items:[content_column_left]
                            },{
                                columnWidth:.3,
                                border:false,
                                bodyStyle:'padding:5px',
                                items:[content_column_rhigt]
                            },{
                                columnWidth:.2,
                                border:false,
                                bodyStyle:'padding:5px',
                                items:[content_botton]
                            }]
                        }]
                    });
                    content_editar_formula.getForm().load({
                        url: 'formulaMaestra/obj_formula',
                        params:{
                            id_formula: formulas.getSelectionModel().getSelected().data.id_formula
                        },
                        success : function() {
                            content_editar_formula.el.unmask();
                        }
                    });
                    this.win_edit_fromula= new Ext.Window({  
                        title: 'Editar F\u00f3rmula',
                        width : 550,
                        modal: true,
                        items:[content_editar_formula]
                    });  
                    this.win_edit_fromula.show();
                }
            }]
        });
        var doble_clik_fomulas= function(formulas, rowIndex) {
            var record = formulas.getStore().getAt(rowIndex);
            store_formulas_mp.load({
                params:{
                    id_formula: record.get('id_formula'),
                    start : 0,
                    limit : 15
                }                
            });
            tool_bar_mp.setDisabled();
        };
        function cover_image(val,metaData){
            if(val=='on'){
                metaData.css ='ok'; 
                return ''               
            }
        } 
        var cm = new Ext.grid.ColumnModel([
        {
            header: 'id_formula',
            dataIndex: 'id_formula',
            hidden:true                       
        },{
            header: 'Nombre de F\u00f3rmula Maestra',
            dataIndex: 'tipo_formula',
            width: (w/4)+15
        },{
            header: 'Predeterminada',
            dataIndex: 'predeterminada',
            width: (w/8)-15,
            renderer:cover_image
        },{
            header: 'Cant. de Productos',
            dataIndex: 'cant_productos',
            width: (w/8)-16
        }
        ]);
        var formulas = new Ext.grid.GridPanel({
            store:store_formulas,
            stripeRows: true,
            height:120,
            autoScroll:true,
            columnLines:true,
            listeners : {
                rowdblclick : doble_clik_fomulas
            },
            cm:cm,
            sm:new Ext.grid.RowSelectionModel({
                singleSelect:true
            })
        });
        //---------End Formulas-----//
        //---------Materias Primas-----//
        var store_formulas_mp = new Ext.data.JsonStore({  
            url: 'formulaMaestra/materias_primas_formula',  
            root: 'data',
            fields: ['id_materia_prima','id_formula','nombre','indice_consumo'],
            Property:'id_materia_prima',
            totalProperty : 'total',
            id_formula:'id_formula'
        });
        this.guardar_cambios = function(){
            var modified = formula_materia.getStore().getModifiedRecords();
            
            if(!Ext.isEmpty(modified)){  
                var recordsToSend = [];  
                Ext.each(modified, function(record) {
                    recordsToSend.push(Ext.apply({
                        id:record.id
                    },record.data));  
                });  
                formula_materia.el.mask('Guardando…', 'x-mask-loading');
                formula_materia.stopEditing();  
                recordsToSend = Ext.encode(recordsToSend);   
                Ext.Ajax.request({
                    url: 'formulaMaestra/editar_datos_mp',  
                    params :{
                        records : recordsToSend
                    },  
                    scope:this,  
                    success : function(response) {
                        formula_materia.el.unmask();  
                        formula_materia.getStore().commitChanges();  
                    }  
                });  
            }  
        };
        var tool_bar_mp= new Ext.Toolbar({
            disabled:true,
            items: [{
                xtype: 'tbbutton',
                text: 'Guardar Cambios',
                iconCls: 'save',
                handler:this.guardar_cambios
            }]
        });
        this.numberField = new Ext.form.NumberField({
            allowBlank:false,
            blankText: 'Insrte dato.'
        });
        var formula_materia =new  Ext.grid.EditorGridPanel({
            store:store_formulas_mp,
            stripeRows: true,
            height: h-239,
            autoScroll:true,
            columnLines:true,
            border:true,
            columns: [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id_materia_prima',
                hidden:true                       
            },{
                header: 'Nombre de Materia Prima',
                dataIndex: 'nombre',
                width: (w/4)-5
            },{
                header: 'Indice de Consumo',
                dataIndex: 'indice_consumo',
                editor:this.numberField,
                width: (w/4)-31
            }]
        });
        this.paginado_mp = new Ext.PagingToolbar({
            store: store_formulas_mp, 
            displayInfo: true,
            displayMsg: '{0} - {1} de {2} MP',
            emptyMsg: 'No hay MP a mostrar',
            pageSize: 15
        });
        //---------End Materias Primas-----//
        //----------End Columna Derecha----------//
        Ext.applyIf(config, {
            title: 'Ralaci\u00f3n Laboratorios-Productos',
            width: w,
            height: h,
            layout:'column',
            animCollapse: false,
            items:[{
                columnWidth:.5,
                height: h-25,
                items:[this.productos,this.paginado_productos]
            },{
                columnWidth:.5,
                height:h-25,
                items:[tool_bar_formulas,formulas,tool_bar_mp,formula_materia,this.paginado_mp]     
            }]
        });
        BalanceMaterial.FormulaMaestra.GestionFormulaMaestra.superclass.constructor.apply(this, [config]);
    }
});

