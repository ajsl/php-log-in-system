<!DOCTYPE html>
<html>
<head>
	<title>Log in</title>
	<link rel="stylesheet" type="text/css" href="css/register.css">
</head>
<?php

		//connect to the database 

		$db_server = "localhost";
		$db_username = "root";
		$db_password = "root";
		$db_database = "scotchbox";

		session_start(); // start session

		// Create connection

		$db_connection = new mysqli($db_server, $db_username, $db_password, $db_database);

		// Check connection

		if ($db_connection->connect_error) {
		    die("Connection failed: " . $db_connection->connect_error);
		};

		//set up defaults 
		$error = false; 
		$error_message = [];
		$error_post = "";
		$email = "";
		$email = "";
		$password = "";

		//CHECK THE URL TO SEE IF THERE IS A VALID ACTIVATION CODE IF NOT GO TO A BLANK LOG IN PAGE.
		//-------------------------------------------------------------------------------------------
		// function to get the name of the page in the url.
		// function cur_age() {
		// 	return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
		// }

		// echo "The current page name is ".curPageName();
 
		$uri = $_SERVER['REQUEST_URI']; // Get the page name in the url. 
		
		if($uri != "/log_in.php"){

			// if the url has an activation code 
			$activation = $_GET['code'];

			 // use $_GET['code'] to access the data in the url;

			// clean the code
			$clean_activation_code = mysqli_real_escape_string($db_connection, $activation);

			// query to get the users data using the activation code and only if the account is active. 
			$query = "SELECT * FROM `user` WHERE `activation code` = '$clean_activation_code';";

			// run the query in the DB. 
			$result = mysqli_query($db_connection, $query);

			
				
			

			if (mysqli_num_rows($result) == 1){ // make sure the code only matched with one user. 

				while($row = mysqli_fetch_assoc($result)){
					if ($row['account status'] == 'active') {
						
						//get the email for the input value. 
						$email = $row['email'];

					}
				}
			}else{
				header("Location: /log_in.php");
			}

		

			// header("Location: /log_in.php");
		} //end of the if($uri != "/log_in.php")

		//Log in form
		//--------------

		if($_POST){

			//set values to variables 
			$password = $_POST['password'];
			$email = $_POST['email'];

			//check the fields have been filled

			if($email == "" || $password == ""){
				$error = true;
				array_push($error_message, "<h3 class=\"error\">To log in please enter your email and password</h3>");

			}else{
				 

				//clean the user inputs
				$clean_email = mysqli_real_escape_string($db_connection, $email);

				$clean_password = mysqli_real_escape_string($db_connection, $password);

				// create a query to access the database
				$query = "SELECT * FROM `user` WHERE `email` = '$clean_email';";

				//run the query
				$result = mysqli_query($db_connection, $query);

				if ($result){
					//if the query runs
					
				}else{
					//if the query doesn't run
					$error = true;
					array_push($error_message, "<h3 class= \"error\">ERROR - Unable to conect to the database</h3>");
				}

				// check to see we only have one row of data

				if (mysqli_num_rows($result) == 1) {
					while($row = mysqli_fetch_assoc($result)){
						
						$password_db = $row['password'];
						if (password_verify( $clean_password, $password_db )) {

							if ($row['account status'] == 'active') {

							//get the password from the database 
							
							$_SESSION['logged_in'] = 'YES';
							header("Location: /account.php");
							}else{//check to see that the user is active
							$error = true;
							array_push($error_message, "<h3 class= \"error\">ERROR - Account not active please check your email, you have been sent an activation link</h3>");
					}		
						}else{
							$error = true;
							array_push($error_message, "<h3 class= \"error\">Your email and/or password don't match, please try again</h3>" );
						}



						
				}
				}else{ // if the given password and the stored password don't match. 
					$error = true;
					array_push($error_message, "<h3 class= \"error\">ERROR - Your email and/or password don't match, please try again</h3>" );

				

			} // $_POST else





			//clean the user inputs
		}

		}

		if ($error == true) {

			foreach ($error_message AS $value) {
				$error_post = $error_post . "<br>" .$value;
			}
		}else{ // if there are no errors - give them a success message and promt the user to click the link. 

			// $activate_post = "<h3 class=\"activate success\">Your account has been activated, follow the link below to log in</h3><div class=\"log_in_link form_input\">
			// <p>Now <a href='$link'>log in</a></p></div>";

	}


	?>
<body><section class = "wrapper">

	
		<header class="button_box">
			<div  class="button_box">
				<h1 class="heading">Log In</h1>
			</div>	
			<div class="button_box">
				<?php echo $error_post; ?>
				
			</div>
		</header>
		<form class = "form" action="" method="post">
			<div class="form_input">
				<b>Email:</b><input class = "feild" type="text" name="email" placeholder="Email@email.com" value="<?php echo $email ?>" >
			</div>
			<div class="form_input">
				<b>Password:</b><input class = "feild" type="password" name="password" placeholder="Password">
			</div>

			<div class="form_button" >
				<input class = "button" type="submit" value="Log In">
			</div>
		</form>
		<div>
			<a href="new_password.php">forgoten you password?</a>
		</div>
		
	</section></body>
</html>