<?php

/**
 * A Framework API
 *
 * Supporting GET, POST, PUT, and DELETE
 *
 * @author Jarrod Sampson
 * @copyright 2015 Planlodge
 *
 */

class EmployeeAccessClass extends AccessObject
{
    
    public function __construct()
    {
    	// CORS headers to allow certain methods
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
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
    
    /*
	 * Loop through the data
	 * for GET functions
	 */
    private function dataQuery($conn, $sqlQueryObject, $format, $version)
    {
        if ($version == "v1")
        {
            $result      = mysqli_query($conn, $sqlQueryObject);
            $numberCount = mysqli_num_rows($result);
            
            if ($numberCount > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    
                    $BirthDate   = $row['BirthDate'];
                    $FirstName   = $row['FirstName'];
                    $LastName    = $row['LastName'];
                    $Gender      = $row['Gender'];
                    $ID          = $row['ID'];
                    $HireDate    = $row['HireDate'];

                    
                    $employees[] = array(
                        'ID' => $ID,
                        'BirthDate' => $BirthDate,
                        'FirstName' => $FirstName,
                        'LastName' => $LastName,
                        'Gender' => $Gender,
                        'HireDate'  => $HireDate
                    );
                }
                // 
                //  This will create the json associated with the query
                //
                if ($format == "json")
                {
                    $output = json_encode(array(
                        'items' => $numberCount,
                        'data' => $employees
                    ), JSON_PRETTY_PRINT);
                    
                    return $output;
                }
                // 
                //  This will create the xml associated with the query
                //
                else if ($format == "xml")
                {

                    $xml = new SimpleXMLElement('<xml/>');

                    foreach ($employees as $employee) {
                        $track = $xml->addChild('employee');
                        $track->addChild('ID', $employee['ID']);
                        $track->addChild('BirthDate', $employee['BirthDate']);
                        $track->addChild('FirstName', $employee['FirstName']);
                        $track->addChild('LastName', $employee['LastName']);
                        $track->addChild('Gender', $employee['Gender']);
                        $track->addChild('HireDate', $employee['HireDate']);
                    }

                    Header('Content-type: text/xml');
                    return ($xml->asXML());
                }
                
                
            } else {
                $output = json_encode(array(
                    'data' => 'No Framework Found'
                ), JSON_PRETTY_PRINT);
                
                return $output;
            }
        }
        else
        {

            //
            //   ncorrect Version found
            //
            $output = json_encode(array(
                'Version' => 'Please Select a released version of this API.',
                'Issue' => $version . ' is not defined.'
            ), JSON_PRETTY_PRINT);

            return $output;

        }

    }
    
    public function getRequestParam()
    {
        
        if (isset($_GET['query']) && (isset($_GET['format']) && (isset($_GET['version'])))){

            $query = strtolower($_GET['query']);
            $format = $_GET['format'];
            $version = $_GET['version'];
            
            // check database connections
            $conn = $this->databaseConnection();
            
            if ($conn) {
                
                $queries  = new Queries;
                $sqlQuery = $queries->fetchFrameworksSearch($query);
                
                echo $this->dataQuery($conn, $sqlQuery, $format, $version);
                
            } else {
                $connection = false;
                echo "There was an error, please contact web administrator.";
                return false;
            }
            
            
            
        } 
        else if (isset($_GET['format']) && (isset($_GET['version'])))
        {
        	$format = $_GET['format'];
            $version = $_GET['version'];
            
            // check database connections
            $conn = $this->databaseConnection();
            
            if ($conn) {
                
                $queries  = new Queries;
                $sqlQuery = $queries->fetchAllFrameworks();
                
                
                echo $this->dataQuery($conn, $sqlQuery, $format, $version);
                
                
            } else {
                $connection = false;
                echo "There was an error, please contact web administrator.";
                return false;
            }
        }
        
    }
    
    public function postFramework()
    {
        // check database connections
        $conn = $this->databaseConnection();
        
        if ($conn) {
            
            $id   = trim($_POST['ID']);
            $birthDate   = trim($_POST['BirthDate']);
            $firstName    = trim($_POST['FirstName']);
            $lastName        = trim($_POST['LastName']);
            $gender = trim($_POST['Gender']);
            $hireDate = trim($_POST['HireDate']);
            
            $queries = new Queries;
            
            $sqlCheck    = $queries->checkFrameworkIfExistsById($id);
            $checkResult = mysqli_query($conn, $sqlCheck);
            if (mysqli_num_rows($checkResult) > 0) {
                echo json_encode(array(
                    'error' => $id . ' already Exists.'
                ));
            } else {
                $sqlQuery = $queries->newFrameworkAddition($id, $birthDate, $firstName, $lastName, $gender, $hireDate);
                
                $result = mysqli_query($conn, $sqlQuery);
                if ($result) {
                    echo json_encode(array(
                        'success' => 'Created New Entry.'
                    ));
                } else {
                    echo json_encode(array(
                        'error' => 'Unable to Add Request.'
                    ));
                }
            }
            
            
            
        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
    }
    
    public function deleteFramework()
    {
        // check database connections
        $conn = $this->databaseConnection();
        
        parse_str(file_get_contents("php://input"), $post_vars);
        $id = $post_vars['ID'];
        
        if ($conn) {
            
            $queries     = new Queries;
            $sqlCheck    = $queries->checkFrameworkIfExistsById($id);
            $checkResult = mysqli_query($conn, $sqlCheck);
            if (mysqli_num_rows($checkResult) <= 0) {
                
                echo json_encode(array(
                    'error' => $id . ' does not exist.'
                ));
            } else {
                $sqlQuery = $queries->deleteFrameworkQuery($id);
                $result   = mysqli_query($conn, $sqlQuery);
                if ($result) {
                    echo json_encode(array(
                        'success' => 'Deleted ' . $id
                    ));
                } else {
                    echo json_encode(array(
                        'error' => 'Unable to Delete ' . $id
                    ));
                }
            }
            
        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
        
        
    }
    
    public function updateFramework()
    {
        // check database connections
        $conn = $this->databaseConnection();
        
        parse_str(file_get_contents("php://input"), $post_vars);
        $id          = $post_vars['ID'];
        $birthDate   = trim($post_vars['BirthDate']);
        $firstName    = trim($post_vars['FirstName']);
        $lastName        = trim($post_vars['LastName']);
        $gender = trim($post_vars['Gender']);
        $hireDate = trim($post_vars['HireDate']);
        
        if ($conn) {
            
            $queries     = new Queries;
            $sqlCheck    = $queries->checkFrameworkIfExistsById($id);
            $checkResult = mysqli_query($conn, $sqlCheck);
            if (mysqli_num_rows($checkResult) <= 0) {
                echo json_encode(array(
                    'error' => $id . ' does not exist.'
                ));
            } else {
                $sqlQuery = $queries->updateFrameworkQuery($id, $birthDate, $firstName, $lastName, $gender, $hireDate);
                
                $result = mysqli_query($conn, $sqlQuery);
                if ($result) {
                    echo json_encode(array(
                        'success' => 'Updated ' . $id
                    ));
                } else {
                    echo json_encode(array(
                        'error' => 'Unable to Update ' . $id
                    ));
                }
            }
            
            
        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
        
        
    }
}


?>