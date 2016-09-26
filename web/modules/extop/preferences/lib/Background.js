Ext.ns('Extop.Preferences');

Extop.Preferences.Background = Ext.extend(Ext.Panel, {
	
	constructor: function(config){
		config = config || {};
		
		this.ownerModule = config.ownerModule;
		var desktop = this.ownerModule.app.getDesktop();
        
        var store = new Ext.data.JsonStore({
            id:'wallpapers-dataview-store',
            proxy: new Ext.data.HttpProxy({
                method: 'POST',
                url: '/preferencias/load_wallpapers'
            }),
            root: 'data',
            fields: ['wallpaper_id', 'name', 'thumbnail', 'file']
        });
        
        this.dataView = new Ext.DataView({
            store: store,
            region: 'center',
            tpl  : new Ext.XTemplate(
                '<div id="items">',
                    '<ul>',
                        '<tpl for=".">',
                            '<li class="items">',
                                '<img width="107" height="67" src="{thumbnail}" />',
                                '<strong>{name}</strong>',
                            '</li>',
                        '</tpl>',
                    '</ul>',
                '</div>'
            ),
            id: 'wallpapers-items',
            itemSelector: 'li.items',
            overClass   : 'item-hover',
            singleSelect: true,
            autoScroll  : true,
            listeners: {
                click: function(dataView, index){
                    var r = dataView.getStore().getAt(index).data;
                    var config = {
                        id: r.wallpaper_id,
                        name: r.name,
                        file: r.file
                    };
                    desktop.setWallpaper(config);
                },
                scope: this
            }
        });
		
		var wpp = this.ownerModule.app.getDesktop().getBackground().wallpaperPosition;
        var tileRadio = createRadio('tile', wpp == 'tile' ? true : false, 90, 40);
        var centerRadio = createRadio('center', wpp == 'center' ? true : false, 200, 40);
        
        var position = new Ext.FormPanel({
            border: false,
            height: 100,
            items: [
                {x: 15, y: 15, xtype: 'label', text: this.ownerModule.locale.wallpaper_position },
                { border: false, x: 15, y: 40,
                    items: 
                    	{border: false, 
                            html: '<img class="pref-bg-pos-tile" src="'+Ext.BLANK_IMAGE_URL+'" width="64" height="44" border="0" alt="" />'
                        }
                },
                tileRadio,
                { border: false, x: 125, y: 40,
                    items: 
                    	{border: false, 
                            html: '<img class="pref-bg-pos-center" src="'+Ext.BLANK_IMAGE_URL+'" width="64" height="44" border="0" alt="" />'
                        }
                },
                centerRadio,
                {x: 252, y: 15, xtype: 'label', text: this.ownerModule.locale.background_color },
                {
                    border: false
                    , items: new Ext.Button({
                        iconCls: 'pref-bg-color-icon'
                  , handler: onChangeBgColor
                        , scope: this
                        //, text: this.ownerModule.locale.button.backgroundColor.text
                    })
                    , x: 253
                    , y: 40
                }
                
            ],
            layout: 'absolute',
            region: 'south',
            split: false
        });
		
		// this config
        Ext.applyIf(config, {
            border: false,
            buttons: [
                {
                    disabled: this.ownerModule.app.isAllowedTo('preferencias', 'save') ? false : true,
                    handler: onSave,
                    scope: this,
                    text: this.ownerModule.locale.save
                },{
                    handler: onClose,
                    scope: this,
                    text: this.ownerModule.locale.close
                }
            ],
            cls: 'pref-card',
            items: [this.dataView, position],
            layout: 'border'//,
            //title: this.ownerModule.locale.background
        });
        
        Extop.Preferences.Background.superclass.constructor.apply(this, [config]);
    
        function createRadio (value, checked, x, y){
            if(value){
                var radio = new Ext.form.Radio({
                    name: 'position',
                    inputValue: value,
                    checked: checked,
                    x: x,
                    y: y
                });
                radio.on('check', togglePosition, radio);
                return radio;
            }
            return null;
        }
        
        function onChangeBgColor(){
            var hex = desktop.getBackground().color;
            var dialog = new Ext.ux.ColorDialog({
                closeAction: 'close',
                iconCls: 'pref-bg-color-icon',
                listeners: {
                    'cancel': { fn: onColorCancel.createDelegate(this, [hex]), scope: this },
                    'select': { fn: onColorSelect, scope: this, buffer: 350 },
                    'update': { fn: onColorSelect, scope: this, buffer: 350 }
                },
                manager: desktop.getManager(),
                resizable: false,
                title: 'Pick A Background Color',
                modal: true,
                plugins: new Ext.ux.ModalNotice() 
            });

            dialog.show(hex);
        }
        
        function onColorSelect(p, hex){
            desktop.setBackgroundColor(hex);
        }
        
        function onColorCancel(hex){
            desktop.setBackgroundColor(hex);
        }
        
        function onClose(){
            this.ownerModule.win.close();
        }
        
        function onSave(){
            var background = desktop.getBackground();
			var data = {
                color: background.backgroundcolor,
                wallpaperId: background.wallpaper.id,
                wallpaperPosition: background.wallpaperPosition
            };
            this.buttons[0].disable();
            
	    	this.ownerModule.save({
                item: 'background',
	    		callback: function(){
	    			this.buttons[0].enable();
	    		},
                callbackScope: this,
                data: Ext.encode(data)
	    	});
        }
        
        function togglePosition (field, checked){
            if(checked === true){
                desktop.setWallpaperPosition(field.inputValue);
            }
        }
	},
	
	afterRender : function(){
        Extop.Preferences.Background.superclass.afterRender.call(this);
        
        this.on('show', this.loadDataView, this, {single: true});
    },
    
    loadDataView: function(){
    	this.dataView.getStore().load();
    }
});