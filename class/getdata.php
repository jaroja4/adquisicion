<?php 
$servername = "localhost";
$username = "jason";
$password = "123456";
$dbname = "xlabs";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";


$variable = $_GET["tmp"];

if ($variable != "algo") 
{ 
    echo "Contenido recibido: " . $variable; 
} 

$sql = 'INSERT INTO variables (id, idDispositivo, tipo, valor)
 VALUES (UUID(), "af76a1ba-be2b-11e8-b101-c85b76da12f5", "temp", "' . $variable . '");';

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


$conn = null;

?>