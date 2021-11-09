<?php 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header("Access-Control-Allow-Headers: access");
header("Content-Type: application/json; charset=UTF-8");

include 'database.php';
// echo $GET['type']; //returns type of animal user selected

//default state is to show all pets
$sql = 'SELECT pet_name, animal_type, age FROM animals';

// if ($_GET['type'] == 'cat') {
//     $sql = "SELECT pet_name, animal_type, age FROM animals WHERE animal_type = 'cat' ";

// } elseif ($_GET['type'] == 'dog') {
//     $sql = "SELECT pet_name, animal_type, age FROM animals WHERE animal_type = 'dog' ";
// }

// query from database
$results = mysqli_query($connection, $sql);

//fetch results as an assoc array
// $animals = mysqli_fetch_all($results, MYSQLI_ASSOC);
// print_r($animals);

// // //create empty array
$empty_array = array();

// // //assign each animal array in $animals to $json_array
while ($row = mysqli_fetch_all($results, MYSQLI_ASSOC)) {
    $empty_array[] = $row;
}

// print_r($empty_array);


$json_array = array();
// // adding variable to json data
$json_array['Pets'] = $empty_array[0];

//to let browser know its json data
header('Content-Type: application/json');
echo json_encode($json_array, JSON_PRETTY_PRINT);

// // //format to json and assign json to $animals_json variable
// $animals_json = json_encode($json_array);
// echo $animals_json;
// echo 'test';
// //to decode, true will return an associative array, false will return objects. 

// //free results
// mysqli_free_result($results);

// //close database connection
// mysqli_close($connection);
?>


