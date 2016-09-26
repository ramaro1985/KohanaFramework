#Quick Start: Example Code

The following page contains example code showing how the database class is used. 
For complete details please read the individual pages describing each function.

##Initializing the Database Module

You must add the database module to [Kohana::modules] that sets the [Kohana::$_modules] property
into bootstrap.php file.

    Kohana::modules(array(
        ...
        'database'      => MODPATH.'database',
        ...
    ));
    
The models extends now [Kohana_Model_Database] class and the database classes are accessible through
`$this->db`, `$this->dbutil` and `$this->dbforge` inside model objects.

Also database classes can be loaded by this way:
    
    DB::load();
    DB::dbutil();
    DB::dbforge();
    
[!!] The DB::load() must be called first, else will get an error.

Then methods are callable through [DB::$database], [DB::$dbutil] and [DB::$dbforge] properties.

[!!] You can connect to multiple databases at the same time. See the connecting 
page for details.

##Standard Query With Multiple Results (Object Version)

    $query = $this->db->query('SELECT name, title, email FROM my_table');

    foreach ($query->result() as $row)
    {
        echo $row->title;
        echo $row->name;
        echo $row->email;
    }
    
    echo 'Total Results: ' . $query->num_rows(); 
    
The above `result()` function returns an array of objects. Example: `$row->title`

##Standard Query With Multiple Results (Array Version)

    $query = $this->db->query('SELECT name, title, email FROM my_table');

    foreach ($query->result_array() as $row)
    {
        echo $row['title'];
        echo $row['name'];
        echo $row['email'];
    }
    
The above `result_array()` function returns an array of standard array indexes. 
Example: `$row['title']`

##Testing for Results

If you run queries that might not produce a result, you are encouraged to test for a result first 
using the `num_rows()` function:

    $query = $this->db->query("YOUR QUERY");
    
    if ($query->num_rows() > 0)
    {
       foreach ($query->result() as $row)
       {
          echo $row->title;
          echo $row->name;
          echo $row->body;
       }
    } 
    
##Standard Query With Single Result

    $query = $this->db->query('SELECT name FROM my_table LIMIT 1');
    
    $row = $query->row();
    echo $row->name;

The above `row()` function returns an object. Example: `$row->name`

##Standard Query With Single Result (Array version)

    $query = $this->db->query('SELECT name FROM my_table LIMIT 1');
    
    $row = $query->row_array();
    echo $row['name'];

The above `row_array()` function returns an array. Example: `$row['name']`

##Standard Insert

    $sql = "INSERT INTO mytable (title, name)
            VALUES (".$this->db->escape($title).", ".$this->db->escape($name).")";
    
    $this->db->query($sql);
    
    echo $this->db->affected_rows();
    
##Active Record Query

The Active Record Pattern gives you a simplified means of retrieving data:

    $query = $this->db->get('table_name');
    
    foreach ($query->result() as $row)
    {
        echo $row->title;
    }

The above `get()` function retrieves all the results from the supplied table. 
The Active Record class contains a full compliment of functions for working with data.

##Active Record Insert

    $data = array(
                   'title' => $title,
                   'name' => $name,
                   'date' => $date
                );
    
    $this->db->insert('mytable', $data);

The `insert()` method above produces:

    "INSERT INTO mytable (title, name, date) VALUES ('{$title}', '{$name}', '{$date}')"