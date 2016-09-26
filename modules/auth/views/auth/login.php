<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>KoDExT Login</title>
        
        <!-- Ext Library -->
        <link type="text/css" href="/libraries/ext-3.3.1/resources/css/ext-all-notheme.css" rel="stylesheet" />
        <link id="theme" rel="stylesheet" type="text/css" href="/libraries/ext-3.3.1/resources/css/xtheme-blue.css" />
        <link id="theme-addon" rel="stylesheet" type="text/css" href="/modules/extop/resources/x-blue.css" />
        <script type="text/javascript" src="/libraries/ext-3.3.1/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="/libraries/ext-3.3.1/ext-all.js"></script>
        
        <!-- Login -->
        <link rel="stylesheet" type="text/css" href="/modules/auth/login/resources/css/login.css" />
        <script type="text/javascript" src="/modules/auth/login/login.js"></script>
    </head>

    <body>
        <div id="qo-panel">
        	<img alt="" src="/libraries/ext-3.3.1/resources/images/default/s.gif" class="qo-logo qo-abs-position" />
        	
        	<div class="qo-benefits qo-abs-position">
        		<p>A familiar desktop environment where you can access all your web applications in a single web page</p>
        		<p>Change the theme, wallpaper and colors to your liking</p>
                <p>For optimal experience use 1024x768 screen resolution at least.</p>
        	</div>
        	
        	<img alt="" src="/libraries/ext-3.3.1/resources/images/default/s.gif" class="qo-screenshot qo-abs-position" />
        	
        	<span class="qo-supported qo-abs-position">
        		<b>Supported Browsers</b><br />
        		<a href="http://www.mozilla.org/download.html" target="_blank">Firefox 2+</a><br />
        		<a href="http://www.microsoft.com/windows/downloads/ie/getitnow.mspx" target="_blank">Internet Explorer 7+</a><br />
        		<a href="http://www.opera.com/download/" target="_blank">Opera 9+</a><br />
        		<a href="http://www.apple.com/safari/download/" target="_blank">Safari 2+</a>
        	</span>
        	
            <a href="http://kohanaphp.com/" target="_blank">
                <img alt="" src="/libraries/ext-3.3.1/resources/images/default/s.gif" class="qo-kohana-logo qo-abs-position" />
            </a>
            
        	<a href="http://www.extjs.com/" target="_blank">
                <img alt="" src="/libraries/ext-3.3.1/resources/images/default/s.gif" class="qo-extjs-logo qo-abs-position" />
            </a>
            
        	<span class="qo-library qo-abs-position">
        	   built with <a href="http://kohanaphp.com/" target="_blank">Kohana</a> 
               and <a href="http://www.extjs.com/" target="_blank">Ext JS</a> 
               Frameworks.
        	</span>
        	
        	<label id="field1-label" class="qo-abs-position" accesskey="e" for="field1"><span class="key">U</span>sername</label>
        	<input class="qo-abs-position" type="text" name="field1" id="field1" value="anonymous" />
        	
        	<label id="field2-label" class="qo-abs-position" accesskey="p" for="field2"><span class="key">P</span>assword</label>
        	<input class="qo-abs-position" type="password" name="field2" id="field2" value="anonymous" />
        	
            <label id="field3-label" class="qo-abs-position" accesskey="g" for="field3" style="display: none;"><span class="key">G</span>roup</label>
        	<select class="qo-abs-position" name="field3" id="field3" style="display: none;"></select>
        	
        	<input id="submitBtn" class="qo-submit qo-abs-position" type="image" src="/libraries/ext-3.3.1/resources/images/default/s.gif" />
        </div>

    </body>
</html>