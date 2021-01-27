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
        $this->dbconn = pg_connect("host=" .  $host . " port=5432 user=" . $user . " password=" . $pass) or die('Could not connect: ' . pg_last_error());
    }
    
    function createTable() {
        
    }
}


$database = new Database_connect("jessey", "", "localhost");


