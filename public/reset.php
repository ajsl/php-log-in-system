<!DOCTYPE html>
<html>
<head>
	<title>Reset Password</title>

	<link rel="stylesheet" type="text/css" href="css/register.css">

</head>
<body>

</body>
</html>

<body><section class = "wrapper">

<?php 

/* 
Page to allow the user to reset their password
	Considerations 
	- check there is an activation code in the url
	- clean the activation code 
	- get the email address and clean it 
	- check the url matches the email
	- check the 2 passwords match 
	- clean the password and hash it 
	- update the password feild in the database
*/	

//connect to the database 
$db_server = "localhost";
$db_username = "root";
$db_password = "root";
$db_database = "scotchbox";

// Create connection
$db_connection = new mysqli($db_server, $db_username, $db_password, $db_database);

// Check connection
if ($db_connection->connect_error) {
    die("Connection failed: " . $db_connection->connect_error);
}

//set up defaults 
$error = false; 
$error_message = [];
$error_post = "";
$activate_post = "";
$email = "";
$result1 = false;


if($_POST) {

$email = $_POST['email'];
$password = $_POST['password'];
$password2 = $_POST['password2'];


// check user input
	//CHECK THE URL TO SEE IF THERE IS A VALID ACTIVATION CODE IF NOT GO TO A BLANK LOG IN PAGE.
	//-------------------------------------------------------------------------------------------
	// function to get the name of the page in the url.
	// function cur_age() {
	// 	return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	// }

	// echo "The current page name is ".curPageName();
	 
	$uri = $_SERVER['REQUEST_URI']; // Get the page name in the url. 
			
	if($uri != "/reset.php"){

		

		//validate the user data
		$activation = $_GET['code']; // use $_GET['code'] to access the data in the url;

		// -is the code set is it an empty string? 
		if($activation == ""){
			$error = true;
			array_push($error_message, "<h3 class= \"error\" >ERROR - your activation code is missing, please follow the link in your email</h3>");
		}else{

			//clean the activation code, passowrd and email

			$clean_email = mysqli_real_escape_string($db_connection, $email);

			$clean_password = mysqli_real_escape_string($db_connection, $password);
			$clean_password2 = mysqli_real_escape_string($db_connection, $password2);

			$clean_activation_code = mysqli_real_escape_string($db_connection, $activation);

			// build an activation query to match with the user.  

			$query = "SELECT * FROM `user` WHERE `password-verify` = '$clean_activation_code';";

			// run the query in the DB. 

			$result = mysqli_query($db_connection, $query);

			if($result){

				//query ran
				if(mysqli_num_rows($result) == 1){

					while($row = mysqli_fetch_assoc($result)){

						//check the activation code and inputed email match

						if($row['email'] == $clean_email){

							//check the passwords are strong 

							if(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
							//if the password is strong - check the passwords match 
								if ($clean_password == $clean_password2){
									// hash the password 

									$password_hash = password_hash( $clean_password, PASSWORD_BCRYPT );
									//get the ID
									$id = $row['id'];
									//update the password onthe database 
									//create a query

									$query = "UPDATE `user` SET `password` = '$password_hash' WHERE `id` = '$id';";

									$result1 = mysqli_query($db_connection, $query);

									//check the query ran
									if($result1){
										//query ran
										$result1 = true;
										$activate_post = "<div class=\"button_box\"><h3 class=\"activate success\">Your password has been changed successfully please follow the link below and go to the log in page</h3><div class=\"log_in_link feild form_input\">
											<p>Now <a href=\"log_in.php\">log in</a></p></div>
											";

									}else{
										$error = true;
										array_push($error_message, "<h3 class= \"error\">ERROR - could not connect to the database</h3>");
									}


								}else{
									$error = true;
									array_push($error_message, "<h3 class= \"error\">Your passwords don't match, please try again</h3>");
								}
							}else{
								//if the password is weak 
								$error = true;
								array_push($error_message, "<h3 class= \"error\">Password weak, must be at least 8 charecters long; contain upper and lowercase letters, at least one number and a symbol</h3>");
								}

							

								// hash the password 

								//update the password onthe database 

							
						}else{
							//email doesn't match"
							$error =true;
							array_push($error_message, "<h3 class= \"error\">ERROR - Your email dosen't match our records please try again</h3>");
						}
					}	
				}	
			}else{
				$error = true;
				array_push($error_message, "<h3 class= \"error\">ERROR - Unable to conect to the database</h3>");
			}

		}

		

		
	}else{
		//invalid acrtivation code -- not the correct place. 
		$error = true;
		array_push($error_message, "<h3 class= \"error\" >ERROR - there is a problem with your activation code, please follow the link in your email</h3>");
	}
}	

$error_post = "";


if ($error == true) {

	foreach ($error_message AS $value) {
		$error_post = $error_post . "<br>" .$value;
	}
}		
			
?>


	
		<header class="button_box">
			<div  class="button_box">
				<h1 class="heading">Reset your password</h1>
			</div>	
			<div class="button_box">
				<?php echo $error_post; ?>
				
				
			</div>

			<?php
			if($result1 == true){
				echo $activate_post;
			}else { ?>
		</header>
		<form class = "form" action="" method="post">
			
			<div class="form_input">
				<b>Email:</b><input class = "feild" type="text" name="email" placeholder="Email@email.com" value="<?php echo $email ?>" >
			</div>
			<div class="form_input">
				<b>New Password:</b><input class = "feild" type="password" name="password" placeholder="Password">
			</div>
			<div class="form_input">
				<b>New Password:</b><input class = "feild" type="password" name="password2" placeholder="Please re-enter your password">
			</div>

			<div class="form_button" >
				<input class = "button" type="submit" value="Create Account">
			</div>
		</form>

		<?php } ?>
		
		
	</section></body>
</html>
</html>