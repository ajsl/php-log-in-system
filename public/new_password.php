<!DOCTYPE html>
<html>
<head>
	<title>New Password</title>
	<link rel="stylesheet" type="text/css" href="css/register.css">
</head>



	<?php
		/*
		User inputs their email address and is sent a link to reset their password
		*/


		//- connect to the database

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

		//- store email and password vartiables 

		$email = "";
		$error = false;
		$error_message = [];
		$success = "";
		$result = false;
		$match = false; 
		$result1 = false;

		if($_POST){

			$email = $_POST['email'];
			

			if($email == ""){

				$error = true;
				array_push($error_message, "<h3 class=\"error\">Please enter your email</h3>");

			}else{	


				//- clean their email adresss 

				$clean_email = mysqli_real_escape_string($db_connection, $email);

				//- check their email is on the database 

				$query = "SELECT * FROM `user` WHERE `email` = '$clean_email';";

				//run the query
				$result = mysqli_query($db_connection, $query);

				if($result){
					//if the query runs
				}else{
					//if the query doesn't run 
					$error = true;
					array_push($error_message, "<h3 class=\"error\">Unable to connect with the database</h3>");
				}

				//run the email query
				$email = mysqli_query($db_connection, $query);

				if($email){//if the query runs 
				// if there are any rows containing a matching email? 
					if (mysqli_num_rows($email) == 1 ) {
						// does the email match a record.

						//create an activation code
						$activation = uuidv4(); // unique string creator function. 

						//clean the activation code
						$clean_activation_code = mysqli_real_escape_string($db_connection, $activation);

						//query to add the password-verify code
						$query = "UPDATE `user` SET `password-verify` = '$clean_activation_code' WHERE `email` = '$clean_email';";

						//run the query
						$result = mysqli_query($db_connection, $query);

						if($result){
							//query ran send email 
							//query work now we send an email. 
							$link = "http://192.168.33.10/reset.php?code=".$clean_activation_code;

							

							$headers = "From: Dev Me <team@example.com>\r\n";
							$headers .= "Reply-To: Help <help@example.com>\r\n";
							$headers .= "MIME-Version: 1.0\r\n";
							$headers .= "Content-Type: text/html;\r\n";

							$subject = 'Reset password link';
							$message = "<p>Hello, </p>";
							$message .= "<p>Please Click <a href=".$link.">this link</a> to reset your password</p>";
							$message .= "<p>Many Thanks</p>";
							$message .= "<p>James</p>";

							if (mail($clean_email, $subject, $message, $headers)){
								$result1 = true;
								$success = '<h2 class="success">Check your email for a password reset link</h2>';

							}else{
								$error = true;
								array_push($error_message, "<h3 class=\"error\">Email failed to send</h3>");
							};

							
						}else{
							$error = true;
							array_push($error_message, "<h3 class=\"error\">ERROR - couldn't connect with the database</h3>");
						}

						
					
					}else{
						$error = true;
						$match = true;
						array_push($error_message, "<h3 class=\"error\">Email dosn't match our records please try again</h3>");
					}
				}
				//- create a unique code
				
				//- compose an email

				//- send email

			} //else (if($email == ""){)
				

		} //if($_POST)	

		$error_post = "";

		if ($error == true) {

			foreach ($error_message AS $value) {
				$error_post = $error_post . "<br>" .$value;
			}
		}	

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

<body><section class="wrapper">
	<header class="button_box">
			<div  class="button_box">
				<h1 class="heading">Password reset</h1>
				
			</div>	
			<div class="button_box">
				<?php echo $error_post ?>
				
			</div>	
	</header>
			<?php 
			if($result1 == true){
				
				echo "<div class=\"button_box\">$success</div>";
			}else { ?>

			<p>Enter your email address below and we will send you a new link to reset your password</p>
			
			
		

		<form class = "form" action="" method="post">
			<div class="form_input">
				<b>Email:</b><input class = "feild" type="text" name="email" placeholder="Email@email.com" value="" >
			</div>
		
			<div class="form_button" >
				<input class = "button" type="submit" value="Submit">
			</div>
		</form>
		<?php }; ?>
</section></body>
</html>