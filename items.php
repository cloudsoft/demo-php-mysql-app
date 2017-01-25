<?php
$servername = 127.0.0.1;
$username = "${MYSQL_USERNAME}";
$password = "${MYSQL_PASSWORD}";

$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!$conn->select_db('test')) {
  die("Couldn't select database test");
}

$createTable = "CREATE TABLE IF NOT EXISTS `items` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `name` varchar(255) default '',
    `description` varchar(255) default '',
    PRIMARY KEY  (`ID`)
)";

if(!$conn->query($createTable)){
    die("Table creation failed: (" . $conn->errno . ") " . $conn->error);
}

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
  case 'POST':
    echo json_encode(handlePost($conn));
    break;
  case 'GET':
    echo json_encode(handleGet($conn));
    break;
  default:
    break;
}

function handleGet($conn) {
  if ($result = $conn->query("SELECT name, description FROM items")) {
    if($result) {
      while ($row = $result->fetch_object()){
        $items_arr[] = $row;
      }
    }
    $result->close();
    return $items_arr;
  }
  return array();
}

function handlePost($conn) {
  $name = $conn->real_escape_string($_POST['name']);
  $description = $conn->real_escape_string($_POST['description']);
  $conn->query("INSERT into items (name, description) VALUES ('$name', '$description')");
  if (!empty($conn->error)) {
    echo $conn->error;
  }
  return array('name' => $name, 'description' => $description);
}

?>
