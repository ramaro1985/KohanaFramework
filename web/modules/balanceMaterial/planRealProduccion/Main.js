/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.ns('BalanceMaterial.PlanRealProduccion');
BalanceMaterial.PlanRealProduccion.Main= Ext.extend(Ext.app.Module, {
    id: 'balanceMaterial-planReal',
    type: 'balanceMaterial/planReal',
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
            animCollapse: false,
            items:[new BalanceMaterial.PlanRealProduccion.Reporte({
                ownerModule: this 
               // id: 'BalanceMaterial.PlanRealProduccion.Reporte'
            })]
        });
        this.win.show();
    }
});


