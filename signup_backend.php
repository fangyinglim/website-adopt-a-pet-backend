<?php 

include 'database.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header("Access-Control-Allow-Headers: access");
header("Content-Type: application/json; charset=UTF-8");

//initialise variables
$usernameFromUserForSignup = $passwordFromUserForSignup = $emailFromUserForSignup = '';
$errors = array('username' => '', 'password'=>'', 'email' => ''); // better practice
$POSTrequest = '';
$signupStatus = '';
$SQLIerror = '';
$userTaken;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $POSTrequest = 'POST request successful';
} else {
    $POSTrequest = 'POST request unsuccessful';
}


$data = json_decode(file_get_contents("php://input"), true);
// print_r($data); // returns assoc array
// print_r($_POST); //returns object as well
$usernameFromUserForSignup = $data['username'];
$passwordFromUserForSignup= $data['pw'];
$emailFromUserForSignup = $data['email'];


//checking if fields are left empty:
if (empty($usernameFromUserForSignup)) {
    // echo 'username is empty';
    $errors['username'] = 'Username is empty';
}
if (empty($passwordFromUserForSignup)) {
    // echo 'password is empty';
    $errors['password'] = 'Password is empty';
}
if (empty($emailFromUserForSignup)) {
    // echo 'email is empty';
    $errors['email'] = 'Email is empty';
} elseif (!filter_var($emailFromUserForSignup, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Not a valid email address';
    }


//check if username or email is taken 
// if post request is successful and there are no errors in username, pw and email fields, then:
if ($POSTrequest == 'POST request successful' && $errors['email'] == '' && 
    $errors['username'] == '' && $errors['password'] == '') {

    $username = mysqli_real_escape_string($connection, $usernameFromUserForSignup);
    $password = mysqli_real_escape_string($connection, $passwordFromUserForSignup);
    $email = mysqli_real_escape_string($connection, $emailFromUserForSignup);


    $sql = "SELECT username FROM users WHERE username = ?";

    //creating a prepared statement to make it more secure:
    //initialising stmt, and returns an object for use with mysqli_stmt_prepare
    $stmt = mysqli_stmt_init($connection); 

    //preparing the prepared $stmt with $url
    //check if there are any errors with the sql statement, returns true or false
    if (mysqli_stmt_prepare($stmt, $sql)) {
    
    //bind $username variable input by user to $stmt as a string, replace ? with $username
    mysqli_stmt_bind_param($stmt, "s", $username);
    
    //run parameters inside database, returns true or false only
        if(mysqli_stmt_execute($stmt)) {
            
            //returns false on failure, returns mysqli_result object if true
            $resultData = mysqli_stmt_get_result($stmt);
            
            // print_r($resultData);
            
            //fetch data from database
            $row = mysqli_fetch_assoc($resultData);
            // echo 'from database data: ';
            // print_r($row);
           
           
            //check if username is taken
            if($row['username'] == $username) {
                $userTaken = true;
                $signupStatus = 'Please try again, username is taken';

            //if username is not taken, then proceed to post data to database 
            } else {
                $userTaken = false;

                $DBusername = mysqli_real_escape_string($connection, $usernameFromUserForSignup);
                $DBpassword = mysqli_real_escape_string($connection, $passwordFromUserForSignup);
                $DBemail = mysqli_real_escape_string($connection, $emailFromUserForSignup);


                $sql = "INSERT INTO users (username, pw, email) 
                VALUES ('".$DBusername."', '".$DBpassword."', '".$DBemail."')";
    

                if(mysqli_query($connection, $sql)) {
                $signupStatus = 'New User created';
                $SQLIerror = mysqli_error($connection);

                } else {
                    $signupStatus = 'Please try again, something went wrong';
                    $SQLIerror = mysqli_error($connection);
                }
            }
        }
    }

}





// returns an object as response to frontend
$dataArray = array(
    "Connection" =>"$connStatus",
    "POST" => "$POSTrequest",
    "usernameFromUserForSignup" => "$usernameFromUserForSignup",
    "passwordFromUserForSignup" => "$passwordFromUserForSignup",
    "emailFromUserForSignup" => "$emailFromUserForSignup",
    "usernameFromDB" => " $DBusername",
    "passwordFromDB" => "$DBpassword",
    "emailFromDB" => "$DBemail",
    "usernameError" => "$errors[username]",
    "passwordError" => "$errors[password]",
    "emailError" => "$errors[email]",
    "signupError" => "$signupStatus",
    "SQLError" => "$SQLIerror"
);

//to let browser know its json data
header('Content-Type: application/json');
print_r(json_encode($dataArray, JSON_PRETTY_PRINT));





























//archived

// //POST to database
// if ($POSTrequest == 'POST request successful' && $errors['email'] == '' && 
//     $errors['username'] == '' && $errors['password'] == '') {

//     $DBusername = mysqli_real_escape_string($connection, $usernameFromUserForSignup);
//     $DBpassword = mysqli_real_escape_string($connection, $passwordFromUserForSignup);
//     $DBemail = mysqli_real_escape_string($connection, $emailFromUserForSignup);


//     $sql = "INSERT INTO users (username, pw, email) 
//     VALUES ('".$DBusername."', '".$DBpassword."', '".$DBemail."')";
    

//     if(mysqli_query($connection, $sql)) {
//      $signupStatus = 'New User created';
//        $SQLIerror = mysqli_error($connection);

//     } else {
//         $signupStatus = 'Please try again';
//           $SQLIerror = mysqli_error($connection);
//     }

// }