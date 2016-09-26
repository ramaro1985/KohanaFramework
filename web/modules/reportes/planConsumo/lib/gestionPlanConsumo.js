Ext.ns('Reportes.PlanConsumo');
Reportes.PlanConsumo.GestionPlanConsumo = Ext.extend(Ext.Panel, {
    ownerModule : null,
    desktop : null,
    formulario : null,
    constructor : function(config) {
        config = config || {};
        this.ownerModule = config.ownerModule;
        var ownerModule = this.ownerModule;
        this.desktop = this.ownerModule.app.getDesktop();
        this.actions = {
            'GestionPlanConsumo' : function(ownerModule){
                ownerModule.viewCard('PlanConsumo');
            }
        };
        var w = parseInt(this.desktop.getWinWidth() * 1);
        var h = parseInt(this.desktop.getWinHeight() * 1);
        if(h > 800){
            h = 800;
        }
        //---------------Primera fila------------------//
        this.store_laboratorios = new Ext.data.JsonStore({  
            url: 'formulaMaestra/nombre_laboratorio',  
            root: 'datos',
            autoLoad:true,
            fields: ['id_lab','laboratorio']  
        });
        var laboratorios= new Ext.form.ComboBox({
            width : w/2-150,
            name:'laboratorios',
            fieldLabel:'Laboratorios',
            store:this.store_laboratorios,
            mode: 'local',
            valueField:'id_lab',
            displayField:'laboratorio',
            editable: true,
            triggerAction:'all',
            allowBlank: false,
            blankText: 'Seleccione Laboratorio.',
            emptyText: 'Seleccione Laboratorio...'
        });
        this.store_formaFarmaceutica = new Ext.data.JsonStore({  
            url: 'planProduccion/nombre_formaFarmaceutica',  
            root: 'datos',
            autoLoad:true,
            fields: ['id_linea','linea']  
        });
        var formaFarmaceutica = new Ext.form.ComboBox({
            width : w/2-150,
            name:'formaFarmaceutica',
            fieldLabel:'Forma Farmace\u00fatica',
            store:this.store_formaFarmaceutica,
            mode: 'local',
            valueField:'id_linea',
            displayField:'linea',
            editable: true,
            triggerAction:'all',
            emptyText: 'Seleccione Forma Farmace\u00fatica...'
        });
        this.buscar = new Ext.Button({
            text: 'BUSCAR',
            border:false,
            handler: function(){
                if(laboratorios.getRawValue()!=''){
                    store_productos.removeAll();
                    store_productos_combo.removeAll();
                    store_materias_primas.removeAll();
                    store_materias_primas_combo.removeAll();
                    store_materias_primas_productos.removeAll();
                    store_cantidad_mp_nec.removeAll();
                    var title_materias_primas_productos= 'Reportes de materias primas del producto:';
                    materias_primas_productos.setTitle(title_materias_primas_productos)
                    
                    store_productos.load({
                        params:{
                            id_laboratorio:laboratorios.getValue(),
                            formaFarmaceutica:formaFarmaceutica.getValue()
                        }
                    });
                    store_productos_combo.load({
                        params:{
                            id_laboratorio:laboratorios.getValue(),
                            formaFarmaceutica:formaFarmaceutica.getValue()
                        }
                    });
                    store_materias_primas.load({
                        params:{
                            id_laboratorio:laboratorios.getValue(),
                            formaFarmaceutica:formaFarmaceutica.getValue()
                        }
                    });
                    store_materias_primas_combo.load({
                        params:{
                            id_laboratorio:laboratorios.getValue(),
                            formaFarmaceutica:formaFarmaceutica.getValue()
                        }
                    });
                    var title= 'Laboratorio:'+' '+laboratorios.getRawValue();
                    content_Grid.setTitle(title);
                }
            }
        });
        var content_filtros = new Ext.FormPanel({
            layout:'column',
            border:false,
            defaults: {
                border: false,
                bodyStyle: 'padding:5px'
            },
            items:[{
                    columnWidth:.4,
                    items:[laboratorios]
                },{
                    columnWidth:.4,
                    items:[formaFarmaceutica]
                },{
                    columnWidth:.2,
                    items:[this.buscar]
                }]
        })  
        //----------End primera fila-----------//
        //----------Segunda fila---------------//
        //----------Columna derecha------------//
        var store_productos = new Ext.data.JsonStore({  
            url: 'planProduccion/nombre_productos',  
            root: 'data',
            fields: ['codigo','id_producto','nombre','descripcion','um','plan_prod_anual','cant_lab','cant_almacen','existencia_total'],
            Property:'id_producto'
        });
        var store_productos_combo = new Ext.data.JsonStore({  
            url: 'planProduccion/productos_lab',  
            root: 'data',
            fields: ['codigo','id_producto','nombre','descripcion'],
            Property:'id_producto'
        });
        function descripcion(val, x, store_productos){
            return '<b>'+val+'</b><br>'+store_productos.data.descripcion;
        }
        var combo_productos = new Ext.form.ComboBox({
            mode: 'local',
            store:store_productos_combo,
            displayField:'nombre',
            valueField:'id_producto',
            editable: true,
            triggerAction:'all',
            emptyText: 'Seleccione Producto...',
            listeners:{
                'select':function(cmb,rec,idx){
                    store_productos.load({
                        params:{
                            id_laboratorio:laboratorios.getValue(),
                            formaFarmaceutica:formaFarmaceutica.getValue(),
                            id_producto:this.getValue()                                  
                        }
                    });
                }
            }
        });
        var doble_clik_productos= function(productos, rowIndex) {
            var record = productos.getStore().getAt(rowIndex);
            store_materias_primas_productos.removeAll();
            materias_primas_productos.reconfigure(store_materias_primas_productos, cm)
            store_materias_primas_productos.load({
                params:{
                    id_producto: record.get('id_producto'),
                    id_laboratorio:laboratorios.getValue()
                }                
            });
            var title_materias_primas_productos= 'Reportes de materias primas del producto:'+' '+record.get('nombre');
            materias_primas_productos.setTitle(title_materias_primas_productos),
            form.setVisible(),
            materias_primas_productos.setHeight(h/2-35)
        };  
        var productos = new Ext.grid.GridPanel({
            store:store_productos,
            height:h/2-60,
            stripeRows: true,
            forceFit: true,
            autoScroll:true,
            columns: [
                new Ext.grid.RowNumberer(),
                {
                    dataIndex: 'id_producto',
                    hidden:true
                },{
                    header: 'Cod',
                    dataIndex: 'codigo',
                    width: (w/2)/7-10              
                },{
                    header: 'Nombre',
                    dataIndex: 'nombre',
                    width: (w/2)/7+40  ,
                    renderer: descripcion
                },{
                    header: 'UM',
                    dataIndex: 'um',
                    width:(w/2)/7-40              
                },{
                    header: 'P.Anual',
                    dataIndex: 'plan_prod_anual',
                    width:(w/2)/7               
                },{
                    header: 'LAB',
                    dataIndex: 'cant_lab',
                    width:(w/2)/7-10              
                },{
                    header: 'DROG',
                    dataIndex: 'cant_almacen',
                    width:(w/2)/7-10               
                },{
                    header: 'ETotal',
                    dataIndex: 'existencia_total',
                    width:(w/2)/7               
                }],
            listeners : {
                rowdblclick : doble_clik_productos
            },
            tbar:new Ext.Toolbar({
                items:[{
                        xtype:'tbtext',
                        text:'Listado de Productos:'
                    },'',combo_productos]
            })
        });
        //-----------End Columna Derecha-------------//
        //-----------Columna Izquierda---------------//
        var store_materias_primas = new Ext.data.JsonStore({  
            url: 'planProduccion/nombre_materias_primas',  
            root: 'data',
            fields: ['codigo','id_producto','nombre','descripcion','um','cant_lab','cant_almacen','existencia_total'],
            Property:'id_producto'
        });
        var store_materias_primas_combo = new Ext.data.JsonStore({  
            url: 'planProduccion/materias_primas_lab',  
            root: 'data',
            fields: ['codigo','id_producto','nombre','descripcion'],
            Property:'id_producto'
        });
        function descripcion_1(val, x, store_productos){
            return '<b>'+val+'</b><br>'+store_productos.data.descripcion;
        }
        var combo_materias_primas = new Ext.form.ComboBox({
            mode: 'local',
            store:store_materias_primas_combo,
            displayField:'nombre',
            valueField:'id_producto',
            editable: true,
            triggerAction:'all',
            emptyText: 'Seleccione Materia Prima...',
            listeners:{
                'select':function(cmb,rec,idx){
                    store_materias_primas.load({
                        params:{
                            id_laboratorio:laboratorios.getValue(),
                            formaFarmaceutica:formaFarmaceutica.getValue(),
                            id_producto:this.getValue()                                  
                        }
                    });
                }
            }
        });
        var doble_clik_mp= function(materias_primas, rowIndex) {
            var record = materias_primas.getStore().getAt(rowIndex);
            store_cantidad_mp_nec.removeAll();
            materias_primas_productos.reconfigure(store_cantidad_mp_nec, cm_mp)
            store_cantidad_mp_nec.load({
                params:{
                    id_producto: record.get('id_producto'),
                    id_laboratorio:laboratorios.getValue()
                }                
            });
            var title_materias_primas_productos= 'Reportes de Productos de la materia prima:'+' '+record.get('nombre');
            materias_primas_productos.setTitle(title_materias_primas_productos),
            materias_primas_productos.setHeight(h/2-60),
            form.setVisible('show'),
            form.getForm().load()
        };
        var materias_primas = new Ext.grid.GridPanel({
            store:store_materias_primas,
            height:h/2-60,
            stripeRows: true,
            forceFit: true,
            autoScroll:true,
            columns: [
                new Ext.grid.RowNumberer(),
                {
                    dataIndex: 'id_producto',
                    hidden:true
                },{
                    header: 'Cod',
                    dataIndex: 'codigo',
                    width: (w/2)/6-11             
                },{
                    header: 'Nombre',
                    dataIndex: 'nombre',
                    width: (w/2)/6+40  ,
                    renderer: descripcion_1
                },{
                    header: 'UM',
                    dataIndex: 'um',
                    width:(w/2)/6-40              
                },{
                    header: 'LAB',
                    dataIndex: 'cant_lab',
                    width:(w/2)/6-11              
                },{
                    header: 'Farmacuba',
                    dataIndex: 'cant_almacen',
                    width:(w/2)/6 -10             
                },{
                    header: 'ETotal',
                    dataIndex: 'existencia_total',
                    width:(w/2)/6              
                }],
            listeners : {
                rowdblclick : doble_clik_mp
            },
            tbar:new Ext.Toolbar({
                items:[{
                        xtype:'tbtext',
                        text:'Listado de Materias Primas:'
                    },'',combo_materias_primas]
            })
        });
        //-----------End Columna Derecha-------------//
        //-----------Tercera Fila--------------------//
        
        function totalfinal(val)
        {
            if(val > 0 && val < 100){
                 return '<span style="color:yellow;">' + val + '</span>';
            }
            if(val > 0){
                return '<span style="color:blue;">' + val + '</span>';
                
            }else if(val < 0){
                return '<span style="color:red;">' + val + '</span>';
            }
            return val;
        }
        
        var store_materias_primas_productos = new Ext.data.JsonStore({  
            url: 'planProduccion/materias_primas_producto',  
            root: 'data',
            fields: ['codigo','id_producto','nombre','descripcion','um','cant_necesaria','cant_lab','cant_almacen','existencia_total','total_fina']
        });
        var store_cantidad_mp_nec = new Ext.data.JsonStore({  
            url: 'planProduccion/cantidad_mp_nec',  
            root: 'data',
            fields: ['codigo','id_producto','nombre','descripcion','um','cant_elab','plan_prod_anual','cant_lab','cant_almacen','existencia_total']
        });
        function descripcion(val, x, store_productos){
            return '<b>'+val+'</b><br>'+store_productos.data.descripcion;
        }
        var cm = new Ext.grid.ColumnModel([
            {
                dataIndex: 'id_producto',
                hidden:true
            },{
                header: 'Cod',
                dataIndex: 'codigo',
                width: w/8-10              
            },{
                header: 'Nombre',
                dataIndex: 'nombre',
                width: w/8+60  ,
                renderer: descripcion
            },{
                header: 'UM',
                dataIndex: 'um',
                width:w/8-20             
            },{
                header: 'Cant',
                dataIndex: 'cant_necesaria',
                width:w/8-10              
            },{
                header: 'LAB',
                dataIndex: 'cant_lab',
                width:w/8-10              
            },{
                header: 'Farmacuba',
                dataIndex: 'cant_almacen',
                width:w/8-10                
            },{
                header: 'ETotal',
                dataIndex: 'existencia_total',
                width:w/8-10               
            },{
                header: 'Total Final',
                dataIndex: 'total_fina',
                renderer: totalfinal,
                width:w/8-10               
            }
        ]);
        var cm_mp = new Ext.grid.ColumnModel([
            {
                dataIndex: 'id_producto',
                hidden:true
            },{
                header: 'Cod',
                dataIndex: 'codigo',
                width: w/8-10              
            },{
                header: 'Nombre',
                dataIndex: 'nombre',
                width: w/8+60  ,
                renderer: descripcion
            },{
                header: 'UM',
                dataIndex: 'um',
                width:w/8-20             
            },{
                header: 'CantElab',
                dataIndex: 'cant_elab',
                width:w/8-10              
            },{
                header: 'PAnual',
                dataIndex: 'plan_prod_anual',
                width:w/8-10              
            },{
                header: 'LAB',
                dataIndex: 'cant_lab',
                width:w/8-10              
            },{
                header: 'DROG',
                dataIndex: 'cant_almacen',
                width:w/8-10                
            },{
                header: 'ETotal',
                dataIndex: 'existencia_total',
                width:w/8-10               
            }]);
        var materias_primas_productos = new Ext.grid.GridPanel({
            store:store_materias_primas_productos,
            height:h/2-35,
            title:'Reportes de materias primas del producto:',
            width:w-10,
            stripeRows: true,
            forceFit: true,
            autoScroll:true,
            cm:cm
        });
        //-----------End Tercera Fila----------------//
        var content_Grid = new Ext.FormPanel({
            layout:'column',
            border:false,
            title:'Laboratorio:',
            defaults: {
                border: false
            },
            items:[{
                    columnWidth:.5,
                    items:[productos]
                },{
                    columnWidth:.5,
                    items:[materias_primas]
                }]
        }) 
        
        var form = new Ext.form.FormPanel({
            url: 'planProduccion/total',
            border:false,
            hidden : true,
            width:400,
            items:{xtype: 'textfield',
                    name: 'total',
                    id: 'total',
                    fieldLabel: 'Cant.Total',
                    width: 100,
                    labelWidth: 180}
        });
        
        Ext.applyIf(config, {
            width: w,
            height: h,
            animCollapse: false,
            items:[content_filtros,content_Grid,materias_primas_productos,form]
        });
        Reportes.PlanConsumo.GestionPlanConsumo.superclass.constructor.apply(this, [config]);
    }
});

