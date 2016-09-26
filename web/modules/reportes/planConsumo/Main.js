Ext.ns('Reportes.PlanConsumo');
Reportes.PlanConsumo.Main= Ext.extend(Ext.app.Module, {
    id: 'reportes-planConsumo',
    type: 'reportes/planConsumo',
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
            title: 'Reporte por Plan de Consumo',
            width: w,
            height: h,
            iconCls: 'planConsumo',
            animCollapse: false,
            items:[new Reportes.PlanConsumo.GestionPlanConsumo({
                ownerModule: this, 
                id: 'Reportes.PlanConsumo.GestionPlanConsumo'
            })]
        });
        this.win.show();
    }
});