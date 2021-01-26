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
            //chec how many columns aer in this row
            $num = count($data);
            //ensure there are 3 columns
            if ($num == 3) {
                $fname = $data[0];
                $lname = $data[1];
                $email = $data[2];
                
                echo $fname . " " . $lname . " " . $email . "\n";
                
                //TODO: clean data here
                
                //TODO: insert data into array
                
                //TODO: insert row into table
            } else {
                //throw away this row
            }
        }
        fclose($file);
    } else {
        echo "Unable to open file '" . $filename . "' !\nCheck --help for assistance\n";
    }
    
}

