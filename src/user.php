<?php

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
        $this->fname = str_replace('/\s/', '', preg_replace("/[^A-Za-z0-9']/", '', $this->fname));
        $this->fname = str_replace("'", "''", $this->fname);
        $this->fname = ucfirst(strtolower($this->fname));
        
        $this->lname = str_replace('/\s/', '', preg_replace("/[^A-Za-z0-9']/", '', $this->lname));
        $this->lname = str_replace("'", "''", $this->lname);
        $this->lname = ucfirst(strtolower($this->lname));
        
        // remove white spaces from emails and lowercase for all letters
        $this->email = str_replace('/\s/', '', $this->email);
        $this->email = str_replace(' ', '', $this->email);
        $this->email = str_replace("'", "''", $this->email);
        $this->email = strtolower($this->email);
        
    }
}
