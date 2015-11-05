<?php

class Queries
{
    public function fetchAllFrameworks()
    {
        $query = "SELECT * FROM gugho_test";
        
        return $query;
    }
    
    public function fetchFrameworksSearch($querySearch)
    {
        $query = "SELECT * FROM gugho_test WHERE FirstName LIKE '%$querySearch%' OR LastName LIKE '%$querySearch%' OR ID LIKE '%$querySearch%' LIMIT 10";
        
        return $query;
    }
    
    public function newFrameworkAddition($id, $birthDate, $firstName, $lastName, $gender, $hireDate)
    {
        $query = "INSERT INTO gugho_test VALUES('$id','$birthDate','$firstName','$lastName','$gender', '$hireDate')";
        
        return $query;
    }
    
    public function deleteFrameworkQuery($id)
    {
        $query = "DELETE FROM gugho_test WHERE ID = '$id'";
        
        return $query;
    }
    
    public function updateFrameworkQuery($id, $birthDate, $firstName, $lastName, $gender, $hireDate)
    {
        $query = "UPDATE gugho_test SET BirthDate = '$birthDate', FirstName = '$firstName', LastName = '$lastName', Gender = '$gender', HireDate = '$hireDate' WHERE ID = '$id'";
        
        return $query;
    }

    public function checkFrameworkIfExistsById($id)
    {
        $query = "SELECT * FROM gugho_test WHERE ID = '$id'";
        
        return $query;
    }

    public function loginUser($username, $password)
    {
        $query = "SELECT * FROM SampleUsers WHERE username = '$username' AND password = '$password'";
        
        return $query;

    }
}


?>