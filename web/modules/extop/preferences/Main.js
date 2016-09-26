Ext.ns('Extop.Preferences');

Extop.Preferences.Main = Ext.extend(Ext.app.Module, {
    id: 'extop-preferences',
    type: 'extop/preferences',
    
    cardHistory: [ 'pref-win-card-1' ],

    init: function () {
    },

    createWindow: function () {
    	var d = this.app.getDesktop();
        this.win = d.getWindow(this.id + '-win');
        var h = parseInt(d.getWinHeight() * 0.9);
        var w = parseInt(d.getWinWidth() * 0.9);
        if(h > 480){ h = 480; }
        if(w > 640){ w = 640; }
        
        if (this.win){
        	this.win.setSize(w, h);
        }
        else{     	
        	this.contentPanel = new Ext.Panel({
        		activeItem: 0,
                border: false,
        		layout: 'card',
        		items: [
                    new Extop.Preferences.Nav({
                        ownerModule: this
                        , id: 'pref-win-card-1'
                    }),
                    new Extop.Preferences.QuickStart({
                		ownerModule: this
                		, id: 'pref-win-card-2'
                	}),
                    new Extop.Preferences.Appearance({
                		ownerModule: this
                		, id: 'pref-win-card-3'
                	}),
                    new Extop.Preferences.Background({
                        ownerModule: this
                        , id: 'pref-win-card-4'
                    }),
                    new Extop.Preferences.AutoRun({
                		ownerModule: this
                		, id: 'pref-win-card-5'
                	}),
                    new Extop.Preferences.Shortcuts({
                		ownerModule: this
                		, id: 'pref-win-card-6'
                	})
                    
                ],
        		tbar: [
                    {
                        disabled: true,
                        handler: this.navHandler.createDelegate(this, [-1]),
                        id: 'back',
                        scope: this,
                        text: this.locale.back
                    }
                    , {
                        disabled: true,
                        handler: this.navHandler.createDelegate(this, [1]),
                        id: 'next',
                        scope: this,
                        text: this.locale.next
                    }
                ]
        	});
        	
        	this.win = d.createWindow({
        		id: this.id + '-win',
        		title: this.locale.preferences,
        		iconCls: 'app-pref-icon',
        		layout: 'fit',
        		width: w,
        		height: h,
                constrainHeader: true,
                animCollapse: false,
                shim: false,
                taskbuttonTooltip: '<b>Preferences Main Window</b><br />Set desktop preferences',
                items: this.contentPanel
            });
            
            this.layout = this.contentPanel.getLayout();
        }
        
        this.win.show();
    },
    
    handleButtonState : function(){
        var cards = this.cardHistory;
        var activeId = this.layout.activeItem.id;
        var items = this.contentPanel.getTopToolbar().items;
        var back = items.get(0);
        var next = items.get(1);
        
        for(var i = 0, len = cards.length; i < len; i++){
            if(cards[i] === activeId){
                if(i <= 0){
                    back.disable();
                    next.enable();
                }else if(i >= (len-1)){
                    back.enable();
                    next.disable();
                }else{
                    back.enable();
                    next.enable();
                }
                break;
            }
        }
    },
    
    navHandler : function(index){
        var cards = this.cardHistory;
        var activeId = this.layout.activeItem.id;
        var nextId;
        
        for(var i = 0, len = cards.length; i < len; i++){
            if(cards[i] === activeId){
                nextId = cards[i+index];
                break;
            }
        }
        
        this.layout.setActiveItem(nextId);
        this.handleButtonState();
    },
    
    save : function(params){
    	var desktop = this.app.getDesktop();
    	var notifyWin = desktop.showNotification({
			html: 'this.locale.notification.saving.msg'
			, title: 'this.locale.notification.saving.title'
		});
	    var callback = params.callback || null;
		var callbackScope = params.callbackScope || this;
		
        Ext.Ajax.request({
			url: '/preferencias/save',
            method: 'POST',
			params: {
                'item': params.item,
                'data': params.data
                
			},
			success: function(o){
				if(o && o.responseText && Ext.decode(o.responseText).success){
					saveComplete('this.locale.notification.saved.title', 'this.locale.notification.saved.msg');
				}else{
					saveComplete('this.locale.error.title', 'this.locale.error.msg');
				}
			},
			failure: function(){
				saveComplete('this.locale.connection.lost.title', 'this.locale.connection.lost.msg');
			},
			scope: this
		});
		
		function saveComplete(title, msg){
			notifyWin.setIconClass('icon-done');
			notifyWin.setTitle(title);
			notifyWin.setMessage(msg);
			desktop.hideNotification(notifyWin);
			
			if(callback){
				callback.call(callbackScope);
			}
		}
	},
    
    viewCard : function(card){
        this.layout.setActiveItem(card);
        var h = this.cardHistory;
        if(h.length > 1){ h.pop(); }
        h.push(card);
        this.handleButtonState();
   }
});