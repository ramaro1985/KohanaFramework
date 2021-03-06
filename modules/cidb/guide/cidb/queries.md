#Running Queries

##Queries

###$this->db->query()

To submit a query, use the following function:
    
    $this->db->query('YOUR QUERY HERE');

The query() function returns a database result object when "read" type queries are run, which you
can use to show your results. When "write" type queries are run it simply returns `TRUE` or `FALSE`
depending on success or failure. When retrieving data you will typically assign the query to your
own variable, like this:

    $query = $this->db->query('YOUR QUERY HERE');

###$this->db->simple_query()

This is a simplified version of the `$this->db->query()` function. It ONLY returns `TRUE/FALSE`
on success or failure. It DOES NOT return a database result set, nor does it set the query timer, 
or compile bind data, or store your query for debugging. It simply lets you submit a query. 
Most users will rarely use this function.

##Adding Database prefixes manually

If you have configured a database prefix and would like to add it in manually for, you can use 
the following.

    $this->db->dbprefix('tablename');
    // outputs prefix_tablename
    
##Protecting identifiers

In many databases it is advisable to protect table and field names - for example with backticks 
in MySQL. Active Record queries are automatically protected, however if you need to manually 
protect an identifier you can use:

    $this->db->protect_identifiers('table_name');

This function will also add a table prefix to your table, assuming you have a prefix specified 
in your database config file. To enable the prefixing set TRUE (boolen) via the second parameter:

    $this->db->protect_identifiers('table_name', TRUE);
    
##Escaping Queries

It's a very good security practice to escape your data before submitting it into your database. 
__Database Module__ has three methods that help you do this:

&nbsp;&nbsp;&nbsp;__1-__ `$this->db->escape()` This function determines the data type so that it can escape only string 
data. It also automatically adds single quotes around the data so you don't have to:

~~~ 
    $sql = "INSERT INTO table (title) VALUES(".$this->db->escape($title).")";
~~~

&nbsp;&nbsp;&nbsp;__2-__ `$this->db->escape_str()` This function escapes the data passed to it, regardless of type. 
    Most of the time you'll use the above function rather than this one. Use the function like this:

~~~    
    $sql = "INSERT INTO table (title) VALUES('".$this->db->escape_str($title)."')";
~~~

&nbsp;&nbsp;&nbsp;__3-__ `$this->db->escape_like_str()` This method should be used when strings are to be used in `LIKE`
conditions so that `LIKE` wildcards ('%', '_') in the string are also properly escaped. 

~~~
    $search = '20% raise';
    $sql = "SELECT id FROM table WHERE column LIKE '%".$this->db->escape_like_str($search)."%'";
~~~

##Query Bindings

Bindings enable you to simplify your query syntax by letting the system put the queries together
for you. Consider the following example:

    $sql = "SELECT * FROM some_table WHERE id = ? AND status = ? AND author = ?";

    $this->db->query($sql, array(3, 'live', 'Rick'));

The question marks in the query are automatically replaced with the values in the array in the 
second parameter of the query function.

[!!] The secondary benefit of using binds is that the values are automatically escaped, producing safer
queries. You don't have to remember to manually escape data; the engine does it automatically 
for you.