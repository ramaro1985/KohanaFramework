Ext.ns('Reportes.PlanProduccion');
Reportes.PlanProduccion.Main= Ext.extend(Ext.app.Module, {
    id: 'reportes-planProduccion',
    type: 'reportes/planProduccion',
    init: function () {},
    createWindow: function () {
        var obj = this.app.getDesktop();
        var w = parseInt(obj.getWinWidth() * 1);
        var h = parseInt(obj.getWinHeight() * 1);
        if(h > 800){
            h = 800;
        }
        this.win = obj.createWindow({
            id: this.id + '-win',
            title: 'Reporte por Plan de Producci\u00f3n Real',
            width: w,
            height: h,
            iconCls: 'planReal',
            animCollapse: false,
            items:[new Reportes.PlanProduccion.GestionPlanProduccion({
                ownerModule: this,
                id: 'Reportes.PlanProduccion.GestionPlanProduccion'
            })]
        });
        this.win.show();
    }
});