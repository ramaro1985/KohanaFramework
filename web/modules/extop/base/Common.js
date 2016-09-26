Ext.BLANK_IMAGE_URL = '/libraries/ext-3.3.1/resources/images/default/s.gif';
Ext.QuickTips.init();

Ext.Ajax.on('requestcomplete', function(conn, response, options){
    var responseText = response.responseText;
    var responseXML = response.responseXML;
    if (responseText && !responseXML){
        var o = Ext.decode(responseText);
        if(o.msg != undefined)
        {
            Kodext.App.getDesktop().showNotification({
                title: o.msg.title || ((o.success == true)?Kodext.App.locale.common.information:Kodext.App.locale.common.error),
                iconCls: (o.success == true)?'information-icon':'error-icon',
                html: o.msg.html,
                width: 250
            });
        }
    }
});

Ext.Ajax.on('requestexception', function(conn, response, options){
    var responseText = response.responseText;
    var responseXML = response.responseXML;
    
    if (response.status == 403)
    {
        window.location = '/auth/login';
    }
    else
    {
        var o = Ext.decode(responseText);
        if(response.status == 401)
        {
           if (responseText && !responseXML)
           {
                if(o.html != undefined)
                {
                   Ext.Msg.show({title: 'Seguridad',
                       msg: 'Usted no posee los permisos necesarios.',
                       icon: Ext.MessageBox.ERROR,
                       buttons: Ext.Msg.OK
                   }); 
                }
           }  
        }
        else
        {
            if (responseText && !responseXML)
            {
                if(o.html != undefined)
                {
                    Kodext.App.getDesktop().showNotification({
                        title: Kodext.App.locale.common.error + ' ' + response.status,
                        iconCls: 'error-icon',
                        html: o.html,
                        width: 250
                    });
                }
            } 
        }
       
    }
});