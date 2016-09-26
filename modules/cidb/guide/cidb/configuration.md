#Configuration

Database module has a config file that lets you store your database connection values 
(username, password, database name, etc.). The default database.php config file is located at:
    
    'MODPATH/database/cofig'
    
Make your own config file into
    
    'APPPATH/config'

prevents updates may overrides configuration settings.

The config settings are stored in a multi-dimensional array with this prototype:

    return array (
        'active_group' => 'default',
        'active_record' => TRUE,
        
    	'default' => array(
    		'dbdriver'    => 'database_engine',
    		'hostname'    => 'hostname',
    		'database'    => 'database_name',
    		'username'    => 'user',
    		'password'    => 'password',
    		'pconnect'    => FALSE,
    		'dbprefix'    => '',
    		'char_set'    => 'charset',
            'dbcollat'    => 'collation',
    		'cache_on'    => FALSE,
            'cachedir'    => '',
    		'autoinit'    => TRUE,
            'stricton'    => FALSE,
            'db_debug'    => TRUE,
            'swap_pre'    => '',      
    	),
    );
    
The reason we use a multi-dimensional array rather than a more simple one is to permit you to
optionally store multiple sets of connection values. If, for example, you run multiple environments
(development, production, test, etc.) under a single installation, you can set up a connection group
for each, then switch between groups as needed.

To globally tell the system to use that group you would set this parameter located in 
the config array:

    return array (
        'active_group' => 'default',
        ...
    }
    
[!!] The group name is arbitrary. It can be anything you want. By default we've used the word 
"__default__" for the primary connection, but it too can be renamed to something more relevant to 
your project.

##Active Record

The Active Record Class is globally enabled or disabled by setting the 
__active_record__ parameter in the database configuration array to `TRUE/FALSE` (boolean). 
If you are not using the active record class, setting it to `FALSE` will utilize fewer resources 
when the database classes are initialized.

    return array (
        ...
        'active_record' => TRUE,
        ...
    }

[!!] Some Database classes such as [Session_Database] require Active Records be enabled to 
access certain functionality.

##Explanation of Values:

* __hostname__ - The hostname of your database server. Often this is "localhost".
* __username__ - The username used to connect to the database.
* __password__ - The password used to connect to the database.
* __database__ - The name of the database you want to connect to.
* __dbdriver__ - The database type. ie: `mysql`, `pgsql`, `odbc`, etc. Must be specified in lower case.
* __dbprefix__ - An optional table prefix which will added to the table name when running Active Record queries. This permits multiple CodeIgniter installations to share one database.
* __pconnect__ - `TRUE/FALSE` (boolean) - Whether to use a persistent connection.
* __db_debug__ - `TRUE/FALSE` (boolean) - Whether database errors should be displayed.
* __cache_on__ - `TRUE/FALSE` (boolean) - Whether database query caching is enabled, see also Database Caching Class.
* __cachedir__ - The absolute server path to your database query cache directory.
* __char_set__ - The character set used in communicating with the database.
* __dbcollat__ - The character collation used in communicating with the database.

[!!] For MySQL and MySQLi databases, this setting is only used as a backup if your server is 
running PHP < 5.2.3 or MySQL < 5.0.7 (and in table creation queries made with [Db_Forge]). 
There is an incompatibility in PHP with `mysql_real_escape_string()` which can make your site 
vulnerable to SQL injection if you are using a multi-byte character set and are running versions 
lower than these. Sites using __Latin-1__ or __UTF-8__ database character set and collation 
are unaffected.

* __swap_pre__ - A default table prefix that should be swapped with dbprefix. This is useful for distributed applications where you might run manually written queries, and need the prefix to still be customizable by the end user.
* __autoinit__ - Whether or not to automatically initialize the database.
* __stricton__ - `TRUE/FALSE` (boolean) - Whether to force "Strict Mode" connections, good for ensuring strict SQL while developing an application.
* __port__ - The database port number. To use this value you have to add a line to the database config array.

~~~
return array (
    ...        
    'default' => array(
        ...
        'port'    => 3306,
        ...   
    ),
);
~~~

[!!] Depending on what database platform you are using (MySQL, PgSQL, etc.) not all values will 
be needed. For example, when using SQLite you will not need to supply a username or password, 
and the database name will be the path to your database file. The information above assumes you 
are using MySQL.