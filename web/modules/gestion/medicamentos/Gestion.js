Ext.ns('Gestion.Medicamentos');
Gestion.Medicamentos.Gestion= Ext.extend(Ext.app.Module, {
    id: 'gestion',
    type: 'gestion/medicamentos',
    init: function () {},
    createWindow: function () {
        var obj = this.app.getDesktop();
        var w = parseInt(obj.getWinWidth() * 1);
        var h = parseInt(obj.getWinHeight() * 1);
        if(h > 800){
            h = 800;
        }
        //---------Columna Izquierda completa-------// 
        var store_laboratorios = new Ext.data.JsonStore({  
            url: 'gestion/nombre_laboratorio',  
            root: 'datos',
            autoLoad:true,
            fields: ['id_lab','laboratorio']  
        });
        var store_productos = new Ext.data.JsonStore({  
            url: 'gestion/nombre_productos',  
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
            url: 'gestion/nombre_formula',  
            root: 'datos',
            fields: ['id_formula','tipo_formula','predeterminada','id_prod_terminado','cant_productos']  
        });
        this.insertar_formula = function(){
            var text_field_formula = new Ext.form.TextField({
                fieldLabel: 'Nombre f\u00f3rmula',
                name: 'formula_name',
                width : 180,
                allowBlank: false,
                blankText: 'Inserte nombre.'
            });
            var cant_productos = new Ext.form.NumberField({
                fieldLabel: 'Cant. Productos',
                name: 'cant_productos',
                width : 180,
                allowBlank: false,
                blankText: 'Inserte cantidad productos.'
            });
            var check_predeterminada= new Ext.form.Checkbox({
                fieldLabel: 'Predeterminada',
                name: 'predeterminada',
                width : 180                
            });
            var agregar_formula = new Ext.FormPanel({  
                bodyStyle:'padding:10px',
                items:[text_field_formula,cant_productos,check_predeterminada]
            });
            this.win_formula = new Ext.Window({  
                title: 'A\u00f1adir F\u00f3rmula',
                buttonAlign: 'center',
                modal: true,
                items:[agregar_formula],
                buttons: [{
                    text:'Aceptar',
                    handler:function(){
                        agregar_formula.getForm().submit(
                        {
                            url:'gestion/insertar_formula',
                            method:'POST',
                            success:function(f, action){
                                Ext.MessageBox.alert('Correcto', action.result.msg, function(){});
                                agregar_formula.getForm().reset();
                                store_formulas.reload();
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
                
            });  
            this.win_formula.show(); 
        };
        this.eliminar_formula = function(){
            var combo_formula = new Ext.form.ComboBox({
                fieldLabel: 'Nombre f\u00f3rmula',
                name: 'formula_name',
                store:store_formulas,
                width : 180,
                allowBlank: false,
                mode:'remote',
                blankText: 'Seleccion nombre.',
                displayField:'tipo_formula',
                editable: true,
                triggerAction:'all'
            });
            var eliminar_formula = new Ext.FormPanel({  
                bodyStyle:'padding:10px',
                items:[combo_formula]
            });
            this.win_formula = new Ext.Window({  
                title: 'Eliminar F\u00f3rmula',
                buttonAlign: 'center',
                modal: true,
                items:[eliminar_formula],
                buttons: [{
                    text:'Aceptar',
                    handler:function(){
                        eliminar_formula.getForm().submit(
                        {
                            url:'gestion/eliminar_formula',
                            method:'POST',
                            success:function(f, action){
                                Ext.MessageBox.alert('Correcto', action.result.msg, function(){});
                                eliminar_formula.getForm().reset();
                                store_formulas.reload();
                                store_formulas_mp.removeAll();
                                tool_bar_mp.disable();
                            }
                        });
                    }
                },{
                    text:'Cancelar',
                    handler:function(){
                        eliminar_formula.getForm().reset();
                    }
                }]
                
            });  
            this.win_formula.show(); 
        };
        this.edit_formula = function(){
            var combo_formula = new Ext.form.ComboBox({
                fieldLabel: 'Nombre f\u00f3rmula',
                name: 'formula_name',
                store:store_formulas,
                width : 180,
                allowBlank: false,
                mode:'remote',
                blankText: 'Seleccion nombre.',
                valueField:'id_formula',
                displayField:'tipo_formula',
                editable: true,
                triggerAction:'all',
                listeners:{
                    'select':function(combo_formula,rec,idx){
                        this.id_formula = combo_formula.getValue();
                        var id_formula = new Ext.form.NumberField({
                            name:'id_formula',
                            hidden:true
                        });
                        var nombre_formula = new Ext.form.TextField({
                            fieldLabel: 'Nombre f\u00f3rmula',
                            name:'tipo_formula',
                            allowBlank: false,
                            blankText: 'Inserte nombre.'
                        });
                        var cant_productos = new Ext.form.NumberField({
                            fieldLabel: 'Cant. Productos',
                            name:'cant_productos',
                            allowBlank: false,
                            blankText: 'Inserte Cant. Productos.'
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
                                    url:'gestion/editar_formula',
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
                            items:[id_formula,nombre_formula,cant_productos]
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
                            url : 'gestion/obj_formula',
                            params:{
                                id_formula: combo_formula.getValue()
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
                }
            });
            var edit_formula = new Ext.FormPanel({  
                bodyStyle:'padding:10px',
                items:[combo_formula]
            });
            this.win_formula = new Ext.Window({  
                title: 'Seleccione F\u00f3rmula',
                buttonAlign: 'center',
                modal: true,
                items:[edit_formula]
            });  
            this.win_formula.show(); 
        }; 
        var tool_bar_formulas= new Ext.Toolbar({
            disabled:true,
            items: [{
                xtype: 'tbbutton',
                text: 'A\u00f1adir F\u00f3rmula',
                iconCls: 'add',
                handler:this.insertar_formula
            },{
                xtype: 'tbbutton',
                text: 'Eliminar F\u00f3rmula',
                iconCls: 'delete',
                handler:this.eliminar_formula
            },{
                xtype: 'tbbutton',
                text: 'Editar F\u00f3rmula',
                iconCls: 'edit',
                handler:this.edit_formula
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
        this.formulas = new Ext.grid.GridPanel({
            store:store_formulas,
            stripeRows: true,
            height:120,
            autoScroll:true,
            columnLines:true,
            listeners : {
                rowdblclick : doble_clik_fomulas
            },
            columns: [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id_formula',
                hidden:true                       
            },{
                header: 'Nombre de F\u00f3rmula',
                dataIndex: 'tipo_formula',
                width: (w/4)-5
            },{
                header: 'Predeterminada',
                dataIndex: 'predeterminada',
                width: (w/8)-15
            },{
                header: 'Cant. de Productos',
                dataIndex: 'cant_productos',
                width: (w/8)-16
            }]
        });
        //---------End Formulas-----//
        //---------Materias Primas-----//
        var store_formulas_mp = new Ext.data.JsonStore({  
            url: 'gestion/materias_primas_formula',  
            root: 'data',
            fields: ['id_materia_prima','id_formula','nombre_producto_mp','indice_consumo'],
            Property:'id_materia_prima',
            totalProperty : 'total',
            id_formula:'id_formula'
        });
        this.insertar_mp = function(){
            var store_materias_primas = new Ext.data.JsonStore({  
                url: 'gestion/nombre_materias_primas',  
                root: 'datos',
                fields: ['id_producto','nombre'],
                autoLoad:true
            });
            var materias_primas= new Ext.form.ComboBox({
                store:store_materias_primas,
                fieldLabel: 'Nombre MP',
                name:'materia_prima',
                width : 200,
                allowBlank: false,
                blankText: 'Seleccion nombre.',
                mode:'local',
                displayField:'nombre',
                editable: true,
                triggerAction:'all'
            });
            var indice_consumo = new Ext.form.NumberField({
                fieldLabel: 'Indice Consumo',
                width : 85,
                name:'indice_consumo',
                allowBlank: false,
                blankText: 'Inserte indice Consumo.'
            });
            var content_insertar_materia_prima = new Ext.FormPanel({
                name:'insertar_mp',
                title:'Datos de la  materia prima',
                border:false,
                bodyStyle:'padding:10px',
                items:[materias_primas,indice_consumo]
            });
            this.win_mp = new Ext.Window({  
                title: 'A\u00f1adir Materia Prima a la F\u00f3rmula',
                buttonAlign: 'center',
                modal: true,
                width : 350,
                items:[content_insertar_materia_prima],
                buttons: [{
                    text:'Aceptar',
                    handler:function(){
                        content_insertar_materia_prima.getForm().submit(
                        {
                            url:'gestion/insertar_materia_prima',
                            method:'POST',
                            success:function(f, action){
                                Ext.MessageBox.alert('Correcto', action.result.msg, function(){});
                                content_insertar_materia_prima.getForm().reset();
                                store_formulas_mp.reload();
                            },
                            failure:function(f, action){
                                Ext.MessageBox.alert('Error', action.result.msg, function(){});
                                content_insertar_materia_prima.getForm().reset();
                            }
                        });
                    }
                },{
                    text:'Cancel',
                    handler:function(){
                        content_insertar_materia_prima.getForm().reset();
                    }
                }]
                
            });  
            this.win_mp.show(); 
        };
        this.eliminar_mp = function(){
            var materias_primas= new Ext.form.ComboBox({
                store:store_formulas_mp,
                fieldLabel: 'Nombre MP',
                name:'materia_prima',
                width : 200,
                allowBlank: false,
                blankText: 'Seleccion nombre.',
                mode:'local',
                displayField:'nombre_producto_mp',
                editable: true,
                triggerAction:'all'
            });
            var conten_materia_prima = new Ext.form.FieldSet({
                border:false,
                items:[materias_primas]
            });
            var content_eliminar_materia_prima = new Ext.FormPanel({
                name:'insertar_mp',
                title:'Seleccione  materia prima',
                border:false,
                items:[conten_materia_prima]
            });
            this.win_formula = new Ext.Window({  
                title: 'Eliminar Materia Prima de la F\u00f3rmula',
                buttonAlign: 'center',
                modal: true,
                width : 450,
                items:[content_eliminar_materia_prima],
                buttons: [{
                    text:'Aceptar',
                    handler:function(){
                        content_eliminar_materia_prima.getForm().submit(
                        {
                            url:'gestion/eliminar_materia_prima',
                            method:'POST',
                            success:function(f, action){
                                Ext.MessageBox.alert('Correcto', action.result.msg, function(){});
                                content_eliminar_materia_prima.getForm().reset();
                                store_formulas_mp.reload();
                            },
                            failure:function(f, action){
                                Ext.MessageBox.alert('Error', action.result.msg, function(){});
                                content_eliminar_materia_prima.getForm().reset();
                            }
                        });
                    }
                },{
                    text:'Cancel',
                    handler:function(){
                        content_eliminar_materia_prima.getForm().reset();
                    }
                }]
                
            });  
            this.win_formula.show(); 
        };
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
                    url : 'gestion/editar_datos_mp',  
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
                text: 'A\u00f1adir Materia Prima',
                iconCls: 'add',
                handler:this.insertar_mp
            },{
                xtype: 'tbbutton',
                text: 'Eliminar Materia Prima',
                iconCls: 'delete',
                handler:this.eliminar_mp
            },{
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
                dataIndex: 'nombre_producto_mp',
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
        this.win = obj.createWindow({
            id: this.id + '-win',
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
                items:[tool_bar_formulas,this.formulas,tool_bar_mp,formula_materia,this.paginado_mp]     
            }]
        });
        this.win.show();
    }
});