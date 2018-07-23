<!DOCTYPE html>
<html>
<head>
	<title>Account</title>
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
		$log_out = "";
		$error_post = "";
		$log_out_link ="Log out";
		
		if (isset($_SESSION['logged_in'])){
			if ('YES' == $_SESSION['logged_in']){
				echo "welcome to your account";
			}else{
				$error = true; 
				array_push($error_message, "<div class=\"error\"><h1>You are not logged in please go to the <a href=\"log_in.php\">login page</a></h1></div>");
			}
		}else{
			$error = true; 
			array_push($error_message, "<div class=\"error\"><h1>You are not logged in please go to the <a href=\"log_in.php\">login page</a></h1></div>");
			$log_out = '';
			$log_out_link = '';
		}

		if ($error == true) {

			foreach ($error_message AS $value) {
				$error_post = $error_post . "<br>" .$value;
			}
		}	
	?>	

<body><section class = "wrapper">

	
		<header class="button_box">
			<div  class="button_box">
				<h1 class="heading">Account	</h1>
			</div>	
			<div class="button_box">
				<?php echo $error_post; ?>
				
			</div>
			<div>
			    <?php

				    
			    	if(isset($_GET['logout'])) {
				    session_unset();
				    $log_out = "<div class= \"log_out\"><h2>You have logged out successfully</h2><h2>Click <a href=\"log_in.php\">here</a> to got back to the log in page</h2></div>";

				    $log_out_link = "";



					}else{


					}

			    
			    
			    ?>

				<a href="?logout"><?php echo $log_out_link ?></a>
		<section>
			<?php echo $log_out ?>
		</section>	    

	</section></body>
</html>