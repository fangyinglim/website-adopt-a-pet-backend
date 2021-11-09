<?php 

include 'database.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header("Access-Control-Allow-Headers: access");
header("Content-Type: application/json; charset=UTF-8");

//initialise variables
$usernameFromUser = $passwordFromUser = '';
$errors = array('username' => '', 'password'=> ''); // better practice
$wrongLogin = '';
$POSTrequest = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $POSTrequest = 'POST request successful';
} else {
    $POSTrequest = 'POST request unsuccessful';
}


$data = json_decode(file_get_contents("php://input"), true);
// echo 'form data:';
// print_r($data); // returns assoc array
// print_r($_POST); //returns object as well
$usernameFromUser = $data['username'];
$passwordFromUser = $data['pw'];

// echo 'username:' . $usernameFromUser;
// echo 'password:' . $passwordFromUser;

//checking if fields are left empty:
if (empty($usernameFromUser)) {
    // echo 'username is empty';
    $errors['username'] = 'Username is empty';
}

if (empty($passwordFromUser)) {
    // echo 'password is empty';
    $errors['password'] = 'Password is empty';

}





// grab data from database based on user id that user keyed in, ? is a placeholder
$sql = 'SELECT username, pw FROM users WHERE username = ?';

//creating a prepared statement to make it more secure:
//initialising stmt, and returns an object for use with mysqli_stmt_prepare
$stmt = mysqli_stmt_init($connection); 

//preparing the prepared $stmt with $url
//check if there are any errors with the sql statement, returns true or false
if (mysqli_stmt_prepare($stmt, $sql)) {
    
    //bind $username variable input by user to $stmt as a string, replace ? with $username
    mysqli_stmt_bind_param($stmt, "s", $usernameFromUser);
    
    //run parameters inside database, returns true or false only
    if(mysqli_stmt_execute($stmt)) {
        
        //returns false on failure, returns mysqli_result object if true
        $resultData = mysqli_stmt_get_result($stmt);
        
        // print_r($resultData);
        
        //fetch data from database
        $row = mysqli_fetch_assoc($resultData);
        // echo 'from database data: ';
        // print_r($row);

        if($row['pw'] === $passwordFromUser && $row['username'] === $usernameFromUser) {
            if(empty($row['pw']) && empty($row['username'])) {
                $wrongLogin ='';
            } else {
                $wrongLogin = 'Username and Password matches';

            }
        } else {
            $wrongLogin = 'Username and Password do not match';
        }
        
    
    } 

    else {
        echo 'statement failed';
    }
}
$dataArray = array(
    "Connection" =>"$connStatus",
    "POST" => "$POSTrequest",
    "usernameFromUser" => "$usernameFromUser",
    "passwordFromUser" => "$passwordFromUser",
    "usernameFromDB" => "$row[username]",
    "passwordFromDB" => "$row[pw]",
    "usernameError" => "$errors[username]",
    "passwordError" => "$errors[password]",
    "loginError" => "$wrongLogin"
);

//to let browser know its json data
header('Content-Type: application/json');
print_r(json_encode($dataArray, JSON_PRETTY_PRINT));



// // Close statement
// // mysqli_stmt_close($stmt);

// //     }

