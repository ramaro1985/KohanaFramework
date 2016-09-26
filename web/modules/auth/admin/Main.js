Ext.ns('Auth.Admin');
Auth.Admin.Main = Ext.extend(Ext.app.Module, {
    id: 'auth-admin',
    type: 'auth/admin',

    defaults: {
        winHeight: 600, 
        winWidth: 800
    },
    tabPanel: null,
    win: null,

    init: function () {

    },

    createWindow: function() {
        var desktop = this.app.getDesktop();
        this.win = desktop.getWindow(this.id + '-win');
        var h = parseInt(desktop.getWinHeight() * 1);
        var w = parseInt(desktop.getWinWidth() * 1);
        /*if(h > this.defaults.winHeight){
            h = this.defaults.winHeight;
        }
        if(w > this.defaults.winWidth){
            w = this.defaults.winWidth;
        }*/
        if(h > 800){
            h = 800;
        }
        var winWidth = w;
        var winHeight = h;

        if (!this.win){
            this.tabPanel = new Ext.TabPanel({
                activeTab:0,
                border: false,
                items: new Auth.Admin.Nav(this)
            });

        this.win = desktop.createWindow({
            title: this.locale.auth_admin,
            animCollapse: false,
            constrainHeader: true,
            id: this.id + '-win',
            height: winHeight,
            width: winWidth,
            iconCls: 'app-admin-icon',
            layout: 'fit',
            shim: false,
            taskbuttonTooltip: 'this.locale.taskbuttonTooltip',
            items: [this.tabPanel]
            });
        }

        this.win.show();
    },
    openTab : function(tab){
        if(tab){
            this.tabPanel.add(tab);
        }
        this.tabPanel.setActiveTab(tab);
    },
    viewGroups : function(){
        var tab = this.tabPanel.getItem('auth-admin-groups');
        if(!tab){
            tab = new Auth.Admin.Roles({
                ownerModule: this
            });
            this.openTab(tab);
        }else{
            this.tabPanel.setActiveTab(tab);
        }
    },
    viewMembers : function(){
        var tab = this.tabPanel.getItem('auth-admin-members');
        if(!tab){
            tab = new Auth.Admin.Usuarios({
                ownerModule: this
            });
            this.openTab(tab);
        }else{
            this.tabPanel.setActiveTab(tab);
        }
    },
  
    viewSignups : function(){
        var tab = this.tabPanel.getItem('auth-admin-signups');
        if(!tab){
            tab = new QoDesk.QoAdmin.Signups(this);
            this.openTab(tab);
        }else{
            this.tabPanel.setActiveTab(tab);
        }
    }
});
