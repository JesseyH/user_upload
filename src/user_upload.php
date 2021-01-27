<?php
//short options for clo
$shortopts .= "u:";     // postgres user option
$shortopts .= "p:";     // postgres password option
$shortopts .= "h:";     // postgres host option

$longopts  = array (
    "file:",            // filename option
    "create_table",     // creates the user table
    "dry_run",          // runs all the parts of program but doesn't insert into db
    "help"              // lists all the options with details
);
    
class User
{
    public $fname;
    public $lname;
    public $email;
    public $valid;
    
    function __construct($fname, $lname, $email) {
        $this->fname = $fname;
        $this->lname = $lname;
        $this->email = $email;
        //think about redordering this (don't waste time sanitizing garbage emails)
        $this->sanitize();
        $this->valid = $this->validate();
    }
    
    /*
     *
     */
    private function validate() {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            //email is valid so we will clean the remaining values
            return true;
        } else {
            //email is not valid so we set $valid to false
            return false;
        }
    }
    
    /*
     *
     */
    private function sanitize() {
        // remove non letters and white spaces from names and have only the leading letter capitalized
        $this->fname = str_replace('/\s+/', '', preg_replace('/\PL/u', '', $this->fname));
        $this->fname = ucfirst(strtolower($this->fname));
        
        $this->lname = str_replace('/\s+/', '', preg_replace('/\PL/u', '', $this->lname));
        $this->lname = ucfirst(strtolower($this->lname));
        
        // remove white spaces from emails and lowercase for all letters
        $this->email = str_replace('/\s+/', '', $this->email);
        $this->email = strtolower($this->email);
        
    }
}

$options = getopt($shortopts, $longopts);

var_dump($options);

if (isset($options['create_table'])) {
    echo "We're only going to run the table create\n";
} else if (isset($options['help'])) {
    
} else {
    //go through the file and extract information line by line
    $filename = $options['file'];
    if (($file = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($file, 0, ",")) !== FALSE) {
            //check how many columns are in this row
            $num = count($data);
            //ensure there are 3 columns
            if ($num == 3) {
                $fname = $data[0];
                $lname = $data[1];
                $email = $data[2];
                
                echo $fname . " " . $lname . " " . $email . "\n";
                
                $user = new User($fname, $lname, $email);

                echo $user->fname . " " . $user->lname . " " . $user->email . " " . $user->valid . "\n";
                
                //TODO: insert data into array
                
                //TODO: insert arrray 'row' into table
            } else {
                //this row does not contain 3 rows and is likely corrupted
                //throw away this row
            }
        }
        fclose($file);
    } else {
        echo "Unable to open file '" . $filename . "' !\nCheck --help for assistance\n";
    }
    
}

