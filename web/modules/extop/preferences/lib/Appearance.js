Ext.ns('Extop.Preferences');

Extop.Preferences.Appearance = Ext.extend(Ext.Panel, {
	constructor : function(config){
		// constructor pre-processing
		config = config || {};
		
		this.ownerModule = config.ownerModule;
		
		var desktop = this.ownerModule.app.getDesktop();
		
        var store = new Ext.data.JsonStore({
            id:'themes-dataview-store',
            proxy: new Ext.data.HttpProxy({
                method: 'POST',
                url: '/preferencias/load_themes'
            }),
            root: 'data',
            fields: ['theme_id', 'name', 'thumbnail', 'file']
        });
        
		this.dataView = new Ext.DataView({
            store: store,
            region: 'center',
            tpl  : new Ext.XTemplate(
                '<div id="items">',
                    '<ul>',
                        '<tpl for=".">',
                            '<li class="items">',
                                '<img src="/modules/extop/resources/images/{thumbnail}" />',
                                '<strong>{name}</strong>',
                            '</li>',
                        '</tpl>',
                    '</ul>',
                '</div>'
            ),
            id: 'themes-items',
            itemSelector: 'li.items',
            overClass   : 'item-hover',
            singleSelect: true,
            autoScroll  : true,
            listeners: {
                click: function(dataView, index){
                    var r = dataView.getStore().getAt(index).data;
                    var config = {
                        id: r.theme_id,
                        name: r.name,
                        file: r.file
                    };
                    desktop.setTheme(config);
                },
                scope: this
            }
        });
		
		this.slider = createSlider({
			handler: new Ext.util.DelayedTask(updateTransparency, this)
			, min: 0
			, max: 100
			, x: 15
			, y: 40
			, width: 100
		});
		
		var formPanel = new Ext.FormPanel({
			border: false
			, height: 70
			, items: [
				{x: 15, y: 15, xtype: 'label', text: 'this.ownerModule.locale.label.transparency' }
				, this.slider.slider
				, this.slider.display
            , {x: 215, y: 15, xtype: 'label', text: 'this.ownerModule.locale.label.fontColor' }
				, {
					border: false
					, items: new Ext.Button({
						iconCls: 'pref-font-color-icon'
						, handler: onChangeFontColor
						, scope: this
						//, text: this.ownerModule.locale.button.fontColor.text
					})
					, x: 215
					, y: 40
				}
			]
			, layout: 'absolute'
			, split: false
			, region: 'south'
		});
	
		// this config
		Ext.applyIf(config, {
			border: false
			, buttons: [
				{
					disabled: this.ownerModule.app.isAllowedTo('preferencias', 'save') ? false : true
					, handler: onSave
					, scope: this
					, text: 'this.ownerModule.locale.button.save.text'
				}
				, {
					handler: onClose
					, scope: this
					, text: 'this.ownerModule.locale.button.close.text'
				}
			]
			, cls: 'pref-card'
			, items: [this.dataView, formPanel]
			, layout: 'border'
			//, title: 'this.ownerModule.locale.title.appearance'
		});
		
		Extop.Preferences.Appearance.superclass.constructor.apply(this, [config]);
		// constructor post-processing
		
		function createSlider(config){
			var handler = config.handler, min = config.min, max = config.max
				, width = config.width || 100, x = config.x, y = config.y;
	
			var slider = new Ext.Slider({
				minValue: min
				, maxValue: max
				, width: width
				, x: x
				, y: y
			});
			
			var display =  new Ext.form.NumberField({
				cls: 'pref-percent-field'
				, enableKeyEvents: true
				, maxValue: max
				, minValue: min
				, width: 45
				, x: x + width + 15
				, y: y - 1
			});
				
			function sliderHandler(slider){
				var v = slider.getValue();
				display.setValue(v);
				handler.delay(100, null, null, [v]); // delayed task prevents IE bog
			}
			
			slider.on({
				'change': { fn: sliderHandler, scope: this }
				, 'drag': { fn: sliderHandler, scope: this }
			});
			
			display.on({
				'keyup': {
					fn: function(field){
						var v = field.getValue();
						if(v !== '' && !isNaN(v) && v >= field.minValue && v <= field.maxValue){
							slider.setValue(v);
						}
					}
					, buffer: 350
					, scope: this
				}
			});
	
			return { slider: slider, display: display }
		}
		
		function onClose(){
			this.ownerModule.win.close();
		}
		
		function onSave(){
			var appearance = this.ownerModule.app.getDesktop().getAppearance();
			var data = {
            fontColor: appearance.fontColor
            , themeId: appearance.theme.id
            , taskbarTransparency: appearance.taskbarTransparency
         };

			this.buttons[0].disable();
	    	this.ownerModule.save({
	    		method: 'saveAppearance'
	    		, callback: function(){
	    			this.buttons[0].enable();
	    		}
	    		, callbackScope: this
            , data: Ext.encode(data)
	    	});
		}

      function onSelectionChange(view, sel){
         if(sel.length > 0){
            var appearance = this.ownerModule.app.getDesktop().getAppearance();
				var cId = appearance.theme.id;
            var r = view.getRecord(sel[0]);
            var d = r.data;

            if(parseInt(cId) !== parseInt(r.id)){
               if(r && r.id && d.name && d.file){
                  desktop.setTheme({
                     id: r.id,
                     name: d.name,
                     file: d.file
                  });
               }
            }
         }
      }

      function onChangeFontColor(){
			var hex = this.ownerModule.app.getDesktop().getAppearance().fontColor;
         var dialog = new Ext.ux.ColorDialog({
				closeAction: 'close'
				, iconCls: 'pref-font-color-icon'
				, listeners: {
					'cancel': { fn: onFontColorCancel.createDelegate(this, [hex]), scope: this },
					'select': { fn: onFontColorSelect, scope: this, buffer: 350 },
               'update': { fn: onFontColorSelect, scope: this, buffer: 350 }
				}
				, manager: this.ownerModule.app.getDesktop().getManager()
				, resizable: false
				, title: 'Pick A Font Color'
				, modal: true
				, plugins: new Ext.ux.ModalNotice()
			});
			dialog.show(hex);
	    }

		function onFontColorSelect(p, hex){
			desktop.setFontColor(hex);
		}

		function onFontColorCancel(hex){
			desktop.setFontColor(hex);
		}

		function updateTransparency(v){
			desktop.setTaskbarTransparency(v);
		}
	}
	
	// overrides
	
	, afterRender : function(){
		Extop.Preferences.Appearance.superclass.afterRender.call(this);
		
		this.on('show', this.initAppearance, this, {single: true});
	}
	
	// added methods
	
	, initAppearance : function(){
		this.dataView.getStore().load();
		this.slider.slider.setValue(this.ownerModule.app.getDesktop().getAppearance().taskbarTransparency);
	}
});