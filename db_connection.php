<?php
error_reporting(E_ALL);
ini_set('display_errors',1);


//database connection details
$servername = 'localhost';
$username = 'root';
$password = '12345';
$dbname = 'leadsManagement';

//create a connection to the database
$conn = new mysqli ($servername,$username,$password,$dbname);


//checks kama connection inafanya kazi 

if($conn -> connect_error){
    die("Connection failed:" . $conn->connect_error);
}
?>