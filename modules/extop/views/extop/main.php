<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es-Es" xml:lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="PRAGMA" content="NO-CACHE" />
    <meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
    <meta http-equiv="EXPIRES" content="-1" />
    
    <title>KoDExT</title>
    
    <!-- EXT JS LIBRARY -->
    <link type="text/css" href="/libraries/ext-3.3.1/resources/css/ext-all-notheme.css" rel="stylesheet" />
    <script type="text/javascript" src="/libraries/ext-3.3.1/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="/libraries/ext-3.3.1/ext-all.js"></script>
    
    <!-- DESKTOP CSS -->
    <link type="text/css" href="/modules/extop/resources/x-desktop.css" rel="stylesheet" />
    <link type="text/css" href="/modules/extop/resources/css/x-icons.css" rel="stylesheet" />
    
    <link type="text/css" href="/modules/common/famfam/resources/css/fam_silk-icons.css" rel="stylesheet" />
    
    <!-- CORE -->
    <!-- In a production environment these would be minified into one file -->
    <script type="text/javascript" src="/modules/extop/base/Common.js"></script>
    <script type="text/javascript" src="/modules/extop/base/App.js"></script>
    <script type="text/javascript" src="/modules/extop/base/Desktop.js"></script>
    <script type="text/javascript" src="/modules/extop/base/Module.js"></script>
    <script type="text/javascript" src="/modules/extop/base/Notification.js"></script>
    <script type="text/javascript" src="/modules/extop/base/Shortcut.js"></script>
    <script type="text/javascript" src="/modules/extop/base/StartMenu.js"></script>
    <script type="text/javascript" src="/modules/extop/base/TaskBar.js"></script>
    
    <!-- 
        This dynamic code will load all the modules the member has 
        access to and setup the desktop 
    -->
    <?php
    echo ("
        <script type=\"text/javascript\">
            /*
             * Kodext Desktop 1.1
             * Based on qWikiOffice Desktop, further details below.
             * Copyright(c) 2008-2011, Rafael Ernesto Espinosa Santiesteban
             * 
             * qWikiOffice Desktop 1.0
             * Copyright(c) 2007-2010, Murdock Technologies, Inc.
             * licensing@qwikioffice.com
             *
             * http://www.qwikioffice.com/license
             */
            
            Ext.ns('Ext.ux', 'Ext.plugin', 'Kodext');
            
            Kodext.App = new Ext.app.App({
                init : function(){},
               
               locale: ".json_encode($extop->locale).",
            
               /**
                * The member's name and group name for this session.
                */
               memberInfo: ".json_encode($extop->member_info).",
            
               /**
                * An array of the module definitions.
                * The definitions are used until the module is loaded on demand.
                */
               modules: ".json_encode($extop->modules).",
            
               /**
                 * The list of all privileges.
                 */
               privileges:".json_encode($extop->list_privileges).",
            
               /**
               /**
                 * The members privileges.
                 */
               privileges:".json_encode($extop->privileges).",
            
               /**
                * The desktop config object.
                */
                desktopConfig: {
                    appearance: ".json_encode($extop->config->appearance).",
                    background: ".json_encode($extop->config->background).",
                    launchers: ".json_encode($extop->config->launchers).",
                    taskbarConfig: {
                        buttonScale: 'large',
                        position: '".$extop->config->appearance->taskbarPosition."',
                        quickstartConfig: {
                            width: 120
                       },
                       startButtonConfig: {
                            iconCls: 'icon-qwikioffice',
                            text: '".$extop->locale['extop']['start_menu_label']."',
                       },
                       startMenuConfig: {
                            iconCls: 'icon-user-48',
                            title: '" . $extop->member_info->name . " " . $extop->member_info->lastname ."',
                            width: 320
                       }
                   },
                    logoutConfig: {
                        handler: function(){window.location = \"/auth/logout\";},
                        iconCls: 'icon-logout',
                        text: '".$extop->locale['common']['logout']."'
                    }
                }
            });
        </script>        
    "); 
    ?>
</head>
<body scroll="no"></body>
</html>