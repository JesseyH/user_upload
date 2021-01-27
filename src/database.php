<?php

class Database_connect
{
    public $user;
    public $pass;
    public $host;
    public $dbconn;
    
    function __construct($user, $pass, $host) {
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
        $this->dbconn = pg_connect("host=" .  $host . " port=5432 user=" . $user . " password=" . $pass)
            or die('Could not connect: ' . pg_last_error());
    }
    
    function createTable() {
        $arr = $this->query("SELECT to_regclass('users');");
        
        // Check if the table exists
        if ($arr[0]['to_regclass'] == "users") {
            //if the table exists prompt the user on whether or not they'd like to recreate the table
            print("The table 'users' already exists, would you like to recreate it? y/n:\n");
            
            do {
                $stdin = fopen('php://stdin', 'r');
                $response = fgetc($stdin);
                if ($response == 'n') {
                    print("Aborted!\n");
                    break;
                } else if ($response == 'y') {
                    print("Recreating table 'users'...\n");
                    
                    $query = "DROP TABLE IF EXISTS users;";
                    $this->query($query);
                    
                    $query = "CREATE TABLE IF NOT EXISTS users (" .
                        "id serial PRIMARY KEY," .
                        "name character varying(255) NOT NULL," .
                        "surname character varying(255) NOT NULL," .
                        "email character varying(255) NOT NULL UNIQUE);";
                    $this->query($query);
                    
                    print("table 'users' has been recreated\n");
                    break;
                }
                print("please press either 'y' or 'n' on your keyboard then press enter.\n");
            } while (true);
        } else {
            //if the table does not exist simply recreate the table
            print("Creating table 'users'...\n");
            
            $query = "CREATE TABLE IF NOT EXISTS users (" .
                "id serial PRIMARY KEY," .
                "name character varying(255) NOT NULL," .
                "surname character varying(255) NOT NULL," .
                "email character varying(255) NOT NULL UNIQUE);";
            
            $this->query($query);
            
            print("table 'users' has been created\n");
        }
        
        
    }
    
    private function query($q) {
        $result = pg_query($this->dbconn, $q);
        return pg_fetch_all($result);
    }
}

//$database = new Database_connect("jessey", "", "localhost");
//$database->createTable();
