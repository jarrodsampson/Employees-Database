<?php

/**
 * Login API
 *
 * Supporting POST
 *
 * @author Jarrod Sampson
 * @copyright 2015 Planlodge
 *
 */

class LoginAccessClass extends AccessObject
{
    
    public function __construct()
    {
    	// CORS headers to allow certain methods
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Content-type:application/json;charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        parent::__construct();
    }
    
    private function databaseConnection()
    {
        // check database connections
        $conn       = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        $connection = true;
        
        if ($conn) {
            $connection = true;
        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
        
        return $conn;
    }
    

    public function postLog()
    {
        // check database connections
        $conn = $this->databaseConnection();
        
        if ($conn) {
            
            $username= trim($_POST['username']);
            $password= trim($_POST['password']);
            $queries = new Queries;

            $password = sha1($password);
            
            
           
                $sqlQuery = $queries->loginUser($username, $password);
                
                $result = mysqli_query($conn, $sqlQuery);
                if (mysqli_num_rows($result) > 0) {
                    echo json_encode(array(
                        'response' => 1
                    ));
                } else {
                    echo json_encode(array(
                        'response' => 0
                    ));
                }

        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
    }
    
  
}


?>