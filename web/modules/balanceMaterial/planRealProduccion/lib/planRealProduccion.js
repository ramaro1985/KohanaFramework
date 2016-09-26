/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.ns('BalanceMaterial.PlanRealProduccion');
BalanceMaterial.PlanRealProduccion.Reporte = Ext.extend(Ext.Panel, {
    ownerModule : null,
    desktop : null,
    formulario : null,
    constructor : function(config) {
        config = config || {};
        this.ownerModule = config.ownerModule;
        this.desktop = this.ownerModule.app.getDesktop();
        this.actions = {
            'Reporte' : function(ownerModule){
                ownerModule.viewCard('Reporte');
            }
        };
        var w = parseInt(this.desktop.getWinWidth() * 1);
        var h = parseInt(this.desktop.getWinHeight() * 1);
        if(h > 800){
            h = 800;
        }
//------------------filtros para la busqueda--------------------------------

        var store_laboratorios = new Ext.data.JsonStore({  
            url: 'planRealProduccion/listar_laboratorio',  
            root: 'data',
            autoLoad:true,
            fields: ['id_lab','laboratorio']  
        });
        
        var store_formFarmaceutica = new Ext.data.JsonStore({  
            url: 'planRealProduccion/listar_formFarmaceutica',  
            root: 'data',
            autoLoad:true,
            fields: ['id_lab','laboratorio']  
        });
        
        var laboratorios= new Ext.form.ComboBox({
            width : 250,
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
            emptyText: 'Seleccione laboratorio...'
        });
        
        var formFarmaceutica= new Ext.form.ComboBox({
            width : 250,
            name:'laboratorios',
            fieldLabel:'Laboratorios',
            store:store_formFarmaceutica,
            mode: 'local',
            valueField:'id_lab',
            displayField:'laboratorio',
            editable: true,
            triggerAction:'all',
            allowBlank: false,
            blankText: 'Seleccione Forma Farmaceutica.',
            emptyText: 'Seleccione Forma Farmaceutica ...'
        });
        
        var button = new Ext.Button({
            text: 'Buscar',
            iconCls: 'search'
        });
        
        var tbar_lab_formFarm = new Ext.Toolbar({
            items: [laboratorios,' ',formFarmaceutica,' ',button]
        });
        
// -----------------------grid Productos------------------------------------------------    

            
        var store_productos = new Ext.data.JsonStore({  
            url: 'planRealProduccion/listar_productos',  
            root: 'data',
            totalProperty : 'total',
            fields: ['codigo','nombre','UM','planReal','laboratorio','almacen','total']  
        });
        store_productos.load({params:{start : 0, limit : 20}});
        
        var cm_productos = new Ext.grid.ColumnModel([
             
            new Ext.grid.RowNumberer(),
            {
                header: 'Cod',
                dataIndex: 'codigo',
                sortable: true ,
                width: 90
            }, {
                id: 'nombre',
                header: 'Nombre',
                dataIndex: 'nombre',
                sortable: true ,
                width: 105
            }, {
                header: 'UM',
                dataIndex: 'UM',
                sortable: true,
                width: 50
            },{
                header: 'P.Real',
                dataIndex: 'planReal',
                sortable: true,
                width: 90
            },{
                header: 'LAB',
                dataIndex: 'laboratorio',
                sortable: true,
                width: 100
            },{
                header: 'DROG',
                dataIndex: 'almacen',
                sortable: true,
                width: 100
            },{
                header: 'E.Total',
                dataIndex: 'total',
                sortable: true,
                width: 80
              }
        ]);
        
        var bbar_productos = new Ext.PagingToolbar({
            displayInfo: true,
            displayMsg: 'Mostrar productos {0} - {1} de {2}',
            emptyMsg: 'No existe productos para mostrar',
            pageSize: 10,
            store: store_productos
        });
        
        var tbar_productos = new Ext.Toolbar({
            items:['->' ,{
                    text: 'Buscar:', 
                    xtype: 'tbtext'
                   },{
                    xtype: 'textfield',
                    id: 'sproducto',
                    emptyText: 'Producto',
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
                 }]
        });
        
        var grid_productos = new Ext.grid.GridPanel({
            id: 'grid-productos',
            title: 'Listado de Productos',
            region: 'west',
            border: true,
            width: w/2,
            height: h/2,
            closable: true,
            clicksToEdit: 1,
            loadMask: true,
            stripeRows: true,
            cm: cm_productos,
            store: store_productos,
           // bbar: bbar_productos,
            tbar: tbar_productos
        });
        
// -----------------------grid Materias Primas------------------------------------------------    

            
        var store_mp = new Ext.data.JsonStore({  
            url: 'planRealProduccion/listar_materias_primas',  
            root: 'data',
            totalProperty : 'total',
            fields: ['codigo','nombre','UM','laboratorio','almacen','Etotal']  
        });
        store_mp.load({params:{start : 0, limit : 20}});
        
        var cm_mp = new Ext.grid.ColumnModel([
             
            new Ext.grid.RowNumberer(),
            {
                header: 'Cod',
                dataIndex: 'codigo',
                sortable: true ,
                width: 100
            }, {
                id: 'nombre',
                header: 'Nombre',
                dataIndex: 'nombre',
                sortable: true ,
                width: 150
            }, {
                header: 'UM',
                dataIndex: 'UM',
                sortable: true,
                width: 50
            },{
                header: 'LAB',
                dataIndex: 'laboratorio',
                sortable: true,
                width: 100
            },{
                header: 'Farmacuba',
                dataIndex: 'almacen',
                sortable: true,
                width: 100
            },{
                header: 'E.Total',
                dataIndex: 'Etotal',
                sortable: true,
                width: 110
              }
        ]);
        
        var bbar_mp = new Ext.PagingToolbar({
            displayInfo: true,
            displayMsg: 'Mostrar materias primas {0} - {1} de {2}',
            emptyMsg: 'No existe materias primas para mostrar',
            pageSize: 10,
            store: store_mp
        });
        
        var tbar_mp = new Ext.Toolbar({
            items:['->' ,{
                    text: 'Buscar:', 
                    xtype: 'tbtext'
                   },{
                    xtype: 'textfield',
                    id: 'smp',
                    emptyText: 'Materia Prima',
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
                 }]
        });
        
        var grid_mp = new Ext.grid.GridPanel({
            id: 'grid-mp',
            title: 'Listado de Materias Primas',
            region: 'center',
            border: true,
            width: w/2,
            height: h/2,
            closable: true,
            clicksToEdit: 1,
            loadMask: true,
            stripeRows: true,
            cm: cm_mp,
            store: store_mp,
            //bbar: bbar_mp,
            tbar: tbar_mp
        });
        
// -----------------------Reporte x Materias Primas------------------------------------------------    

            
        var store_x_mp = new Ext.data.JsonStore({  
            url: 'planRealProduccion/reporte_materias_primas',  
            root: 'data',
            totalProperty : 'total',
            fields: ['codigo','nombre','UM','cant_utilizar','laboratorio','almacen','Etotal','total_final']  
        });
        store_x_mp.load({params:{start : 0, limit : 20}});
        
        var cm_x_mp = new Ext.grid.ColumnModel([
             
            new Ext.grid.RowNumberer(),
            {
                header: 'Cod',
                dataIndex: 'codigo',
                sortable: true ,
                width: 100
            }, {
                id: 'nombre',
                header: 'Nombre',
                dataIndex: 'nombre',
                sortable: true ,
                width: 100
            }, {
                header: 'UM',
                dataIndex: 'UM',
                sortable: true,
                width: 100
            },{
                header: 'Cant. a Utilizar',
                dataIndex: 'cant_utilizar',
                sortable: true,
                width: 100
            },{
                header: 'LAB',
                dataIndex: 'laboratorio',
                sortable: true,
                width: 100
            },{
                header: 'Farmacuba',
                dataIndex: 'almacen',
                sortable: true,
                width: 100
            },{
                header: 'E.Total',
                dataIndex: 'Etotal',
                sortable: true,
                width: 100
            },{
                header: 'Total Final',
                dataIndex: 'total_final',
                sortable: true,
                width: 100
              }
        ]);
        
        var bbar_x_mp = new Ext.PagingToolbar({
            displayInfo: true,
            displayMsg: 'Mostrar materias primas {0} - {1} de {2}',
            emptyMsg: 'No existe materias primas para mostrar',
            pageSize: 10,
            store: store_x_mp
        });
        
      
// -----------------------Reporte x Productos------------------------------------------------    

            
        var store_x_productos = new Ext.data.JsonStore({  
            url: 'planRealProduccion/reporte__productos',  
            root: 'data',
            totalProperty : 'total',
            fields: ['codigo','nombre','UM','cant_elab','laboratorio','almacen','Etotal']  
        });
        store_x_productos.load({params:{start : 0, limit : 20}});
        
        var cm_x_productos = new Ext.grid.ColumnModel([
             
            new Ext.grid.RowNumberer(),
            {
                header: 'Cod',
                dataIndex: 'codigo',
                sortable: true ,
                width: 120
            }, {
                id: 'nombre',
                header: 'Nombre',
                dataIndex: 'nombre',
                sortable: true ,
                width: 200
            }, {
                header: 'UM',
                dataIndex: 'UM',
                sortable: true,
                width: 120
            },{
                header: 'Cant Elab',
                dataIndex: 'cant_elab',
                sortable: true,
                width: 120
            },{
                header: 'P.Real',
                dataIndex: 'planReal',
                sortable: true,
                width: 120
            },{
                header: 'LAB',
                dataIndex: 'laboratorio',
                sortable: true,
                width: 200
            },{
                header: 'DROG',
                dataIndex: 'almacen',
                sortable: true,
                width: 200
            },{
                header: 'E.Total',
                dataIndex: 'total',
                sortable: true,
                width: 120
              }
        ]);
        
        var bbar_x_productos = new Ext.PagingToolbar({
            displayInfo: true,
            displayMsg: 'Mostrar productos {0} - {1} de {2}',
            emptyMsg: 'No existe productos para mostrar',
            pageSize: 10,
            store: store_x_productos
        });
        
//-------------------grid Reportes----------------------------------------

        var grid_reportes = new Ext.grid.GridPanel({
            id: 'grid-reportes',
            title: 'Reporte de ...',
            region: 'south',
            border: false,
            autoWidth: true,
            height: h/2,
            closable: true,
            clicksToEdit: 1,
            loadMask: true,
            stripeRows: true,
            cm: cm_x_productos,
            store: store_x_productos,
            bbar: bbar_x_productos
        }); 
        
//------------------panel contenedor de los grid-------------------------

        var panel = new Ext.Panel({
            width: w,
            height: h,
            title: 'Laboratorios:',
            layout: 'border',
            items:[grid_productos, grid_mp, grid_reportes]
        });
        
        Ext.applyIf(config, {
           // width: w,
           // height: h,
            layout:'fit',
            animCollapse: false,
            tbar: tbar_lab_formFarm,
            items:[panel]
        });
        
        BalanceMaterial.PlanRealProduccion.Reporte.superclass.constructor.apply(this, [config]);
    }
});




