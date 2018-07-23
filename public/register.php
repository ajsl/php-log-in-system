<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<link rel="stylesheet" type="text/css" href="css/register.css">
</head>
	<body><section class = "wrapper">
	<?php
	/*

	steps:
	-------
	*/

	// ----------Database Creation ------------

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
		};

	//empty inputs for the form handling. 

	$password = "";
	$email = "";
	$error = false; // assume no problem;
	$error2 = false; //assume no problem with the database
	$result = false;
	$error_message = []; //empty array to store any error messages.
	$error_message2 = []; //empty array to store any database error messages.
	$error_post = ""; //will store the error messages relating to the user inputs
	$error_post2 = ""; // Will store the error messages relating to the database
	$success = "";

	if ($_POST) {

		$password = $_POST['password'];
		$email = $_POST['email'];
		$activation = "";

		//3)Check user input
		// --------------------
		//check the user has entered a password 
		if ("" == $password) {
			$error = true;
			array_push($error_message, "ERROR - Please enter an password<br>");
		}

		// check the user has entered an email
		if ("" == $email) {
			$error = true;
			array_push($error_message, "ERROR - Please enter an email<br>");
		}elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			//check the email is valid
		}else{
			$error = true;
			array_push($error_message, "ERROR - Please enter a valid email");
		}

		// check the user has entered a password of at least 8 charecters and with uppper and lower case plus a numnber and a symbol. 
		if(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
					//if the query passes
			}else{
				//if the password is weak 
				$error = true;
				array_push($error_message, "Password weak, must be at least 8 charecters long; contain upper and lowercase letters, at least one number and a symbol");
			}
		
		

		 
		if ($error == true) {
			foreach ($error_message AS $value) {
				$error_post = $error_post . "<br>" .$value;
			}

			/*4)IF user input ok then create an activation code

		- Requirements? 
			*unique per user. 
			*hard to guess should be 10 digits or letters long. 
		- How to generate? 
			*long random number 
			*alphanumeric!
			*/ 

			// if both the email and password are valid create an activation code.

		}else {
			$activation = uuidv4(); // unique string creator function. 

			// sanitise the user inputs
			$clean_email = mysqli_real_escape_string($db_connection, $email);

			$clean_password = mysqli_real_escape_string($db_connection, $password);

			$clean_activation_code = mysqli_real_escape_string($db_connection, $activation);


			
			$datetime = date("Y-m-d H:i:s");

			// query to get the emails from the database
			$email_query = "SELECT * FROM `user` WHERE `email` = '$clean_email';";

			//run the email query
			$email_result = mysqli_query($db_connection, $email_query);

			if($email_result){//if the query runs 
				// if there are any rows containing a matching email? 
				if (mysqli_num_rows($email_result) > 0) {
						
					$error2 = true;
					array_push($error_message2, "Email allready used please go to the <a href=\"log_in.php\">log in</a> page or use a different email");
					
				}else{  		
					
					//hashing the password for security
					$password_hash = password_hash( $clean_password, PASSWORD_BCRYPT );

					// 5)Save in database (email, password, activation code, creation time, status = pending)
				
					$query = "INSERT INTO `user` (`email`, `password`, `activation code`, `date created`, `password-verify`) VALUES ('$clean_email', '$password_hash', '$clean_activation_code', '$datetime', 'NA');";
						
					
					// run the query in the $query variable through the DB connection. 
					$result = mysqli_query($db_connection, $query);
					
					if ($result){
						//query ran ok

						if(mysqli_affected_rows($db_connection) == 1){
							//query stored at least 1 row of data 
							$success = "<div class='success'><h5>Congratulations</h5><br><p>We have sent you an activation email to you email account. </p><p>Please open the email and follow the link to finish setting up your account.</p></div>";

							//query work now we send an email. 
							$link = "http://192.168.33.10/activate.php?code=".$clean_activation_code;

							$headers = "From: Dev Me <team@example.com>\r\n";
							$headers .= "Reply-To: Help <help@example.com>\r\n";
							$headers .= "MIME-Version: 1.0\r\n";
							$headers .= "Content-Type: text/html;\r\n";

							$subject = 'Please activate your account';
							$message = "<p>Hello, </p>";
							$message .= "<p>Click <a href=".$link.">this link</a> to activate your accoutn</p>";
							$message .= "<p>Many Thanks</p>";
							$message .= "<p>James</p>";


							if (mail($email, $subject, $message, $headers)){

							}else{
								$error2 = true;
								array_push($error_message2, "Email failed to send");
							};




						}else{
							$error2 = true;
							array_push($error_message2, "ERROR - somthing went wrong with the database"); 
						};

					}else{

						//query didn't run
						$error2 = true;
						array_push($error_message2, "ERROR - somthing went wrong with the database");
					};
				};	

			};
		}	
	}	
	//run through each error message and print them on the page. 
	if ($error == false && $error2 == true) {
			foreach ($error_message2 AS $value) {
				$error_post2 = $error_post . "<br>" .$value;
			}
	};		

	//2)PHP form handling
	

	
			
	
	//6)This if the database query works then send email

	//7)If we send the email succesfully then send the success message

	//8)If any step goes wrong then we can send them an error message. 

	//function to validate the password. 
	// function strong_password($pass){

	// 	if(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $pass){

	// 	}else{
	// 		$error = true;
	// 		array_push($error_message, "Password weak, must contain; upper and lowercase letters, at least one number and symbol");
	// 	};
		    
	// }

	// funtion to create a unique id. 
	function uuidv4(){

    return implode('-', [
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(2)),
        bin2hex(chr((ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
        bin2hex(chr((ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
        bin2hex(random_bytes(6))
    ]);
	}

	?> 


	
		<header class="button_box">
			<div  class="button_box">
				<h1 class="heading">Register</h1>
			</div>	
			<div class="button_box">
				<p class = "heading error"><?php echo $error_post2 ?></p>
				<p class = "heading error"><?php echo $error_post ?></p>
				
				
			</div>
			</header>
		<?php 
		if ($result == true){
			 echo $success;
			 }else{?>
		
			<form class = "form" action="" method="post">
				<div class="form_input">
					<b>Email:</b><input class = "feild" type="text" name="email" placeholder="Email@email.com" value = "<?php echo $email ?>">
				</div>
				<div class="form_input">
					<b>Password:</b><input class = "feild" type="password" name="password" placeholder="Password">
				</div>

				<div class="form_button" >
					<input class = "button" type="submit" value="Create Account">
				</div>
			</form>
		<?php }; ?>
	</section></body>
</html>