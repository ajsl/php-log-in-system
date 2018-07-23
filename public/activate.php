<!DOCTYPE html>
<html>
<head>
	<title>Confirmation</title>
	<link rel="stylesheet" type="text/css" href="css/register.css">
</head>
<body><section class="wrapper">
	<?php
	/*

	Activation confirmation page 
	- connect to the database 
	- get the activation code from the url 
	- check that it is there 
	- clean the code 
	- build a query to find the row in the data base
	- check that the application is unique
	- check the status - if it's allready active present the link 
	- update the status of the user to active 
	- print any errors to the page 
	- if no erros print a confirmation message and present the link


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
		$clean_activation_code = "";
		$link = "";
		
	//validate the user data
		

		// -is the code set is it an empty string? 
		
		// check the url 
		$uri = $_SERVER['REQUEST_URI']; // Get the page name in the url. 

		if($uri != "/activate.php"){





			$activation = $_GET['code']; // use $_GET['code'] to access the data in the url;
			
			if($activation == ""){
				
			$error = true;
			array_push($error_message, "<h3 class= \"error\" >ERROR - your activation code is missing</h3>");
			};
			//clean the activation code

			$clean_activation_code = mysqli_real_escape_string($db_connection, $activation);

			$link = "http://192.168.33.10/log_in.php?code=".$clean_activation_code;
			// print error messages to the page. 
			

			// build an activation query to match with the user.  

			$query = "SELECT * FROM `user` WHERE `activation code` = '$clean_activation_code';";

			

			
			// run the query in the DB. 
			$result = mysqli_query($db_connection, $query);


			//check the query ran
			if($result){
				
			}else {//if the query didn't run we get an error. 
				$error = true; 
				array_push($error_message, "<h3 class = \"error\" >ERROR - There is a problem with the database, the query didn't run</h3>");
			}


			if (mysqli_num_rows($result) == 1){ // make sure the code only matched with one user. 

				while($row = mysqli_fetch_assoc($result)){
					if ($row['account status'] == 'pending') {
						
						//get the id number 
						$id = $row['id'];

						//query to update the status
						$update = "UPDATE `user` SET `account status` = 'active' WHERE `id` = '$id';";

						// run the update query to change from pending to active. 
						$result2 = mysqli_query($db_connection, $update); 

							if ($result2){
								
							}else{
								$error = true;
								array_push($error_message, "<h3 class = \"error\">Failed to update account status</h3>");
							}
						
					}elseif ($row['account status'] != 'pending'){
						$error = true;
						array_push($error_message, "<h3 class = \"error\">Your account has already been activated<br>Please click the link below and go to the log in page</h3>");

						array_push($error_message, "<div class=\"log_in_link form_input\">
						<p>Now <a href='$link'>log in</a></p></div>");
					
					}
				}
			}elseif(mysqli_num_rows($result) != 1){
				$error = true; 
				array_push($error_message, "<h3 class=\"error\">ERROR - Activation code allready in use");

			}	
		}	
		// link variable to concatinate the activation code in order to allow pre population of the email feild on the log in page. 
			$link = "http://192.168.33.10/log_in.php?code=".$clean_activation_code;
		// print error messages to the page. 
		if ($error == true) {

			foreach ($error_message AS $value) {
				$error_post = $error_post . "<br>" .$value;
			}
		}else{ // if there are no errors - give them a success message and promt the user to click the link. 

			$activate_post = "<h3 class=\"activate success\">Your account has been activated, follow the link below to log in</h3><div class=\"log_in_link form_input\">
			<p>Now <a href='$link'>log in</a></p>
		</div>";

	}
	
	?>

	<header>
		<h1>Activate</h1>
	</header>
	<section>
		<?php echo $error_post ?>
		<div><?php echo $activate_post ?></div>
		
	</section>

</section></body>
</html>



