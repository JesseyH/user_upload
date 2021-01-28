<?php

class Database_connect
{
    public $TABLE_NAME = "users";
    
    public $user;
    public $pass;
    public $host;
    public $dbconn;
    
    function __construct($user, $pass, $host) {
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
        $this->dbconn = pg_connect("host=" .  $host . " port=5432 user=" . $user . " password=" . $pass) or die("Could not connect!\n");
        
    }
    
    function createTable() {
        $arr = $this->query("SELECT to_regclass('" . $this->TABLE_NAME . "');");
        
        // Check if the table exists
        if ($arr[0]['to_regclass'] == $this->TABLE_NAME) {
            //if the table exists prompt the user on whether or not they'd like to recreate the table
            print("The table '" . $this->TABLE_NAME . "' already exists, would you like to recreate it? y/n:\n");
            
            do {
                $stdin = fopen('php://stdin', 'r');
                $response = fgetc($stdin);
                if ($response == 'n') {
                    print("Aborted!\n");
                    break;
                } else if ($response == 'y') {
                    print("Recreating table '" . $this->TABLE_NAME . "'...\n");
                    
                    $query = "DROP TABLE IF EXISTS " . $this->TABLE_NAME . ";";
                    $this->query($query);
                    
                    $query = "CREATE TABLE IF NOT EXISTS " . $this->TABLE_NAME . " (" .
                        "id serial PRIMARY KEY," .
                        "name character varying(255) NOT NULL," .
                        "surname character varying(255) NOT NULL," .
                        "email character varying(255) NOT NULL UNIQUE);";
                    $this->query($query);
                    
                    print("table '" . $this->TABLE_NAME . "' has been recreated\n");
                    break;
                }
                print("please press either 'y' or 'n' on your keyboard then press enter.\n");
            } while (true);
        } else {
            //if the table does not exist simply recreate the table
            print("Creating table '" . $this->TABLE_NAME . "'...\n");
            
            $query = "CREATE TABLE IF NOT EXISTS " . $this->TABLE_NAME . " (" .
                "id serial PRIMARY KEY," .
                "name character varying(255) NOT NULL," .
                "surname character varying(255) NOT NULL," .
                "email character varying(255) NOT NULL UNIQUE);";
            
            $this->query($query);
            
            print("table '" . $this->TABLE_NAME . "' has been created\n");
        }
    }
    
    function insertRow($fname, $lname, $email) {
        
        $arr = $this->query("SELECT to_regclass('" . $this->TABLE_NAME . "');");
        
        // Check if the table exists
        if ($arr[0]['to_regclass'] == $this->TABLE_NAME) {
            //if the table exists add the row to the table
            
            $arr = $this->query("SELECT EXISTS(SELECT 1 FROM " . $this->TABLE_NAME ." WHERE email = '" . $email . "')");
            if ($arr[0]['exists'] == "t") {
                print("ERROR! the following row was rejected, the email already exists in the table: \n" .
                      $fname . " " . $lname . " " . $email . "\n");
            } else {
                $query = "INSERT INTO " . $this->TABLE_NAME . "(name, surname, email) " .
                        "VALUES('" . $fname . "','" . $lname . "','" . $email . "');";
                $this->query($query);
            }
        } else {
            print("The table does not exist! Please create the table and try again!\n" .
                  "Use user_upload.php --help for more information.\n");
        }
    }
    
    function query($q) {
        $result = pg_query($this->dbconn, $q);
        if ($result) {
            return pg_fetch_all($result);
        }
    }
}

//$database = new Database_connect("jessey", "", "localhost");
//$database->createTable();
