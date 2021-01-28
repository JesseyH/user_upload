<?php
    
require 'user.php';
require 'database.php';
    
//short options for clo
$shortopts = "u:";     // postgres user option
$shortopts .= "p:";     // postgres password option
$shortopts .= "h:";     // postgres host option

$longopts  = array (
    "file:",            // filename option
    "create_table",     // creates the user table
    "dry_run",          // runs all the parts of program but doesn't insert into db
    "help"              // lists all the options with details
);

$options = getopt($shortopts, $longopts);
    
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

if ((isset($options['p']))) {
    if (strlen($options['p']) === 0) {
        $db_pass = $options['p'];
    } else if (strpos($options['p'][0], "-") === 0) {
        echo "database password cannot start with a dash!\n";
    } else {
        $db_pass = $options['p'];
    }
}

if ((isset($options['h'])) && strpos($options['h'][0], "-") === 0){
    echo "database host cannot start with a dash!\n";
} else {
    $db_host = $options['h'];
}

//main code 
if (isset($options['help'])) {
    echo "help commands\n";
} else if (isset($options['create_table'])) {
    if ($dbSet) {
        $database = new Database_connect($db_user, $db_pass, $db_host);
        $database->createTable();
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
    if (isset($options['file'])) {
    if ($dbSet || isset($options['dry_run'])) {
        
        if ($dbSet) {
            $database = new Database_connect($db_user, $db_pass, $db_host);
            
        } else {
            echo "we are set to dry run, so no data will be inserted into the table.\n";
        }
        //go through the file and extract information line by line
        $arr = $database->query("SELECT to_regclass('" . $database->TABLE_NAME . "');");
        
        // Check if the table exists
        if ($arr[0]['to_regclass'] == $database-> TABLE_NAME) {

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
                        $error = "ERROR! the following row was rejected as the email is not valid:\n" .
                        $user->fname . " " . $user->lname . " " . $user->email . "\n";
                        fwrite(STDOUT, $error);
                    } else if (isset($options['dry_run'])) {
                        print("INSERT INTO TABLE: " . $user->fname . " " . $user->lname . " " . $user->email . "\n");
                    } else {
                        $database->insertRow($user->fname, $user->lname, $user->email);
                    }
                    
                    //echo $user->fname . " " . $user->lname . " " . $user->email . " " . $user->valid . "\n";
                    
                } else {
                    //this row does not contain 3 rows and is likely corrupted
                    //throw away this row
                    $error = "ERROR! the following row was rejected as it has too much or too little data:\n";
                    for ($i = 0; $i < $num; $i++) {
                        $error .= $data[$i] . " ";
                    }
                    $error .= "\n ";
                    fwrite(STDOUT, $error);
                }
            }
            fclose($file);
        } else {
            echo "Unable to open file '" . $filename . "' !\n" .
                "Check user_upload.php --help for more information\n";
        }
        } else {
            echo "-the table does not exist run --create_table first\n" .
                "Use user_upload.php --help for more information.\n";
        }
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
            echo "Set the above options correctly or use --dry_run to avoid db paramaters\n" .
                "Use user_upload.php --help for more information.\n";
    }
    } else {
        echo "--file must be set to get data from a csv file\n" .
            "Use user_upload.php --help for more information.\n";
    }
}
