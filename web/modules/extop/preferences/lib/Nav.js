Ext.ns('Extop.Preferences');

Extop.Preferences.Nav = Ext.extend(Ext.Panel, {
    constructor : function(config){
      // constructor pre-processing
        config = config || {};
        
        this.ownerModule = config.ownerModule;
        this.locale = this.ownerModule.locale; 
        
        this.actions = {
            'viewQuickstart' : function(ownerModule){
                ownerModule.viewCard('pref-win-card-2');
            },
            'viewAppearance' : function(ownerModule){
                ownerModule.viewCard('pref-win-card-3');
            },
            'viewWallpapers' : function(ownerModule){
                ownerModule.viewCard('pref-win-card-4');
            },
            'viewAutoRun' : function(ownerModule){
                ownerModule.viewCard('pref-win-card-5');
            },
            'viewShortcuts' : function(ownerModule){
                ownerModule.viewCard('pref-win-card-6');
            }
        };
        
      // this config
        Ext.applyIf(config, {
            autoScroll: true,
            bodyStyle: 'padding:15px',
            border: false
        });
        
        Extop.Preferences.Nav.superclass.constructor.apply(this, [config]);
      // constructor post-processing
    },
    
   // overrides
    
    afterRender : function(){
    var tpl = new Ext.XTemplate(
         '<ul class="pref-nav-list">'
         , '<tpl for=".">'
            , '<li><div>'
               , '<div class="prev-link-item-icon"><img src="'+Ext.BLANK_IMAGE_URL+'" class="{cls}"/></div>'
               , '<div class="prev-link-item-txt"><a id="{id}" href="#">{text}</a><br />{description}</div>'
               , '<div class="x-clear"></div>'
            , '</div></li>'
         , '</tpl>'
         , '</ul>'
    );
    tpl.overwrite(this.body, Ext.decode(this.locale.nav));
    
      this.body.on({
         'mousedown': {
            fn: this.doAction
            , scope: this
            , delegate: 'a'
         }
         , 'click': {
            fn: Ext.emptyFn
            , scope: null
            , delegate: 'a'
            , preventDefault: true
         }
      });
        
      Extop.Preferences.Nav.superclass.afterRender.call(this); // do sizing calcs last
   },

   // added methods

    doAction : function(e, t){
        e.stopEvent();
        this.actions[t.id](this.ownerModule);  // pass ownerModule for scope
    }
});