Ext.ns('BalanceMaterial.FormulaMaestra');
BalanceMaterial.FormulaMaestra.Main= Ext.extend(Ext.app.Module, {
    id: 'balance-material',
    type: 'balance-material/Formula-maestra',
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
            title: 'F\u00f3rmula Maestra',
            width: w,
            height: h,
            animCollapse: false,
            items:[new BalanceMaterial.FormulaMaestra.GestionFormulaMaestra({
                ownerModule: this, 
                id: 'BalanceMaterial.FormulaMaestra.GestionFormulaMaestra'
            })]
        });
        this.win.show();
    }
});