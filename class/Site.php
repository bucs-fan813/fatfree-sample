<?php
//namespace DB\Utils;
use \DB\SQL\Schema;
//use PDO;

class Site extends Controller
{

    /** @var \DB\SQL */
    protected $db;

    /** @var \BASE */
    //protected $f3;
    
    protected $db_schema;
    
    protected $db_driver;
    
    protected $db_port;
    
    protected $db_hostname;
    
    protected $db_username;
    
    protected $db_password;

    function __construct($schema)
    {
        $this->f3 = \Base::instance();
        //Set database defaults
        $this->db_schema = $schema;
        $this->db_driver = 'mysql';
        $this->db_port = '3306';
        $this->db_hostname = 'localhost';
        $this->db_username = 'root';
        $this->db_password = '';
        $this->db;
        $this->db_prefix = '';
        //$this->db = parent::__construct($db);
    }
    
    function get_db_driver() {
        return $this->db_driver;
    }

    function set_db_driver($driver) {
        $this->db_driver = $driver;
    }
    
    function get_db_port() {
        return $this->db_port;
    }
    
    function set_db_port($port) {
        $this->db_port = $port;
    }

    function get_db_hostname() {
        return $this->db_hostname;
    }
    
    function set_db_hostname($hostname) {
        $this->db_hostname = $hostname;
    }
    
    function get_db_username() {
        return $this->db_username;
    }
    
    function set_db_username($username) {
        $this->db_username = $username;
    }
    
    function get_db_password() {
        return $this->db_password;
    }
    
    function set_db_password($password) {
        $this->db_password = $password;
    }

    function get_db() {
        return $this->db;
    }
    
    function set_db($db) {
        $this->db = $db;
    }
    
    function databaseReady($options = array()) {
        foreach ($options as $key => $o)
            $this->$key = $o;

                    try {
                        $conn = new PDO("{$this->db_driver}:host={$this->db_hostname};port={$this->db_port}", $this->db_username, $this->db_password);
                        // set the PDO error mode to exception
                        $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        //echo "Connected successfully<br>";
            
                        $sql = "CREATE DATABASE IF NOT EXISTS {$this->db_schema}";
            
                        // use exec() because no results are returned
                        $conn->exec($sql);
                        //echo "Database ready<br>";
                    }
                    catch(PDOException $e)
                    {
                        echo "Connection failed: " . $e->getMessage() . "<br>";
                        return false;
                    }
                    finally {
                        $conn = null;
                    }
                    return true;
            
    }
//     function databaseReady($schema, $driver= 'mysql', $port = '3306', $hostname = 'localhost', $username ='root', $password = '') {
//         // Create connection
//         // NOTE: Use PDO instead of vendor specific instances like mysqli_connect() or oci_connect()

//         try {
//             $conn = new PDO("{$driver}:host={$hostname};port={$port}", $username, $password);
//             // set the PDO error mode to exception
//             $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//             //echo "Connected successfully<br>";
                        
//             $sql = "CREATE DATABASE IF NOT EXISTS {$schema}";
            
//             // use exec() because no results are returned
//             $conn->exec($sql);
//             //echo "Database ready<br>";
//         }
//         catch(PDOException $e)
//         {
//             echo "Connection failed: " . $e->getMessage() . "<br>";    
//             return false;
//         }
//         finally {
//             $conn = null;
//         }
//         return true;
//     }
    
    
    function schemaReady($schema) {
        $f3 = $this->f3;
        //Get the base tables from the F3 config
        $tables = $schema['tables'];
        //$prefix = $schema['prefix'];
        $this->prefix = $schema['prefix'];
        //For each table that is enabled (TRUE) in the config then create its table in the DB using the f3-schema-builder API
        //TODO: Test if this drops or alters tables
        $db = $f3->get('DB');
        $schema_builder = new \DB\SQL\Schema( $db );        
        foreach ($tables as $key => $value)
            if ($value == true && !in_array($this->prefix . $key,$schema_builder->getTables()))
            {
                SITE::createTable($key);
            }
        return true;
    }
    
    static function createTable($table_name) {
        //https://github.com/ikkez/f3-schema-builder
        $f3 = \Base::instance();
        
        $db = $f3->get('DB');
        $prefix = $f3->get('schema.prefix');
        $schema = new \DB\SQL\Schema( $db );
        $table = $schema->createTable($prefix . $table_name);
        
        
        switch ($table_name) {
            case 'user':
                $table->addColumn('name')->type(Schema::DT_VARCHAR128)->nullable(false);
                $table->addColumn('mail')->type(Schema::DT_VARCHAR128)->nullable(false);
                $table->addColumn('pass')->type(Schema::DT_VARCHAR128)->nullable(false);
                $table->addColumn('created')->type(Schema::DT_INT4)->nullable(false);
                $table->addColumn('access')->type(Schema::DT_INT4);
                $table->addColumn('login')->type(Schema::DT_INT4);
                $table->addColumn('status')->type(Schema::DT_BOOLEAN)->nullable(false)->defaults(0);
                $table->primary(array('id', 'mail'));
                break;
            case 'node':
                $table->addColumn('type')->type(Schema::DT_VARCHAR128)->nullable(false);
                $table->addColumn('title')->type(Schema::DT_VARCHAR256)->nullable(false);
                $table->addColumn('uid')->type(Schema::DT_INT4)->nullable(false);
                $table->addColumn('status')->type(Schema::DT_BOOLEAN)->nullable(false)->defaults(0);
                $table->addColumn('created')->type(Schema::DT_INT4)->nullable(false);
                $table->addColumn('changed')->type(Schema::DT_INT4);
                $table->addColumn('sticky')->type(Schema::DT_INT4);
                $table->primary(array('id'));
                break;
        }
        $table->build();

    }
}