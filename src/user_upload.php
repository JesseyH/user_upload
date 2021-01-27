<?php
    
require 'user.php';
require 'database.php';
    
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

$options = getopt($shortopts, $longopts);
var_dump($options);

    
// placing options for db credentials into variables
if ((isset($options['u'])) && (isset($options['p'])) && (isset($options['u']))) {
    $dbSet = true;
}
    
//ensure that the options are set with valid parameters
if ((isset($options['u'])) && strpos($options['u'][0], "-") === 0){
    echo "database user cannot start with a dash!\n";
} else {
    $db_user = $options['u'];
}

if ((isset($options['p'])) && strpos($options['p'][0], "-") === 0){
    echo "database password cannot start with a dash!\n";
} else {
    $db_pass = $options['p'];
}

if ((isset($options['h'])) && strpos($options['h'][0], "-") === 0){
    echo "database host cannot start with a dash!\n";
} else {
    $db_host = $options['h'];
}

if (isset($options['help'])) {
    echo "help commands\n";
} else if (isset($options['create_table'])) {
    if ($dbSet) {
        echo "We're only going to run the table create\n";
    } else {
        if (!isset($db_user)) {
            echo "database user is not set!\n";
        }
        if (!isset($db_pass)) {
            echo "database password is not set!\n";
        }
        if (!isset($db_host)) {
            echo "database host is not set!\n";
        }
            echo "Use user_upload.php --help for more information.\n";
    }
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
                
                //echo $fname . " " . $lname . " " . $email . "\n";
                
                $user = new User($fname, $lname, $email);
                
                if ($user->valid === false) {
                    $error = "ERROR! the following row was rejected:\n" .
                    $user->fname . " " . $user->lname . " " . $user->email . "\n";
                    fwrite(STDOUT, $error);
                }
                
                //echo $user->fname . " " . $user->lname . " " . $user->email . " " . $user->valid . "\n";
                
                //TODO: insert arrray 'row' into table
            } else {
                //this row does not contain 3 rows and is likely corrupted
                //throw away this row
            }
        }
        fclose($file);
    } else {
        echo "Unable to open file '" . $filename . "' !\n" .
            "Check user_upload.php --help for more information\n";
    }
}

