Ext.ns('Extop.Preferences');

Extop.Preferences.Shortcuts = Ext.extend(Ext.Panel, {
    constructor : function(config){
        // constructor pre-processing
        config = config || {};

        this.ownerModule = config.ownerModule;

        // this config
        Ext.applyIf(config, {
            border: false
            , buttons: [
                {
                    disabled: this.ownerModule.app.isAllowedTo('preferencias', 'save') ? false : true
                    , handler: onSave
                    , scope: this
                    , text: this.ownerModule.locale.save
                }
                , {
                    handler: onClose
                    , scope: this
                    , text: this.ownerModule.locale.close
                }
            ]
            , cls: 'pref-card'
            , items: [
                {
                    bodyStyle: 'padding:0 5px 0 0'
                    , border: false
                    , html: 'Selected applications will be available as a desktop Shortcut.  Don\'t forget to click the \'Save\' button.'
                    , region: 'center'
                    , xtype: 'panel'
                }
                , new Extop.Preferences.CheckTree({
                    launcherKey: 'shortcut',
                    listeners: {
                        'checkchange': { fn: onCheckChange, scope: this }
                    }
                    , ownerModule: config.ownerModule
                    , region: 'west'
                })
            ]
            , layout: 'border'
            //, title: this.ownerModule.locale.title.shortcuts
        });

        Extop.Preferences.Shortcuts.superclass.constructor.apply(this, [config]);
        // constructor post-processing

        function onClose(){
            this.ownerModule.win.close();
        }

        function onSave(){
            this.buttons[0].disable();
            this.ownerModule.save({
                item: 'shortcut'
                , callback: function(){
                    this.buttons[0].enable();
                }
                , callbackScope: this
                , data: Ext.encode(this.ownerModule.app.getDesktop().getLauncher('shortcut'))
            });
        }

        function onCheckChange(node, checked){
            if(node.leaf && node.id){
                if(checked){
                    this.ownerModule.app.getDesktop().addShortcut(node.id, true);
                }else{
                    this.ownerModule.app.getDesktop().removeShortcut(node.id, true);
                }
            }
        }
    }
});