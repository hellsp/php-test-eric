<?php

header('Content-Type: application/xhtml+xml; charset=utf-8');

session_start();

require_once "pdo.php";

	   //return true if email is valid
function checkmail($email){
	
	if(filter_var($email, FILTER_VALIDATE_EMAIL)){
		return true;
	} else {
		return false;
	}
}

 //Flash Message
if (issst($_SESSION['error']) ){
	echo('<p style="color: red;">' . htmlentities($_SESSION['error'])."</p >\n");
	unset($_SESSION['error']);
}

    //Flash Message
if (issst($_SESSION['success'])){
	echo('<p style="color: green;">' . htmlentities($_SESSION['success'])."</p >\n");
	unset($_SESSION['success']);
}


 // Check to see if we have some POST data, if we do process it
if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['retype'])){
	
	unset($_SESSION['email']);
	
	$checkemail = checkemail($_POST['email']);
	
	if ($checkemail === false){
		$_SESSION["error"] = "Invalid email.";
		header("Location: accounts.php");
		return;
	} else if (strlen($_POST['password']) < 5){
		$_SESSION["error"] = "Invalid password. Password must be at least 5 characters";
		header("Location: accounts.php");
		return;
	} else {
		
		if ($_POST['password'] === $_POST['retype']) {
			
			$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
			
			$sql = "INSERT INTO accounts (email, password) VALUES (:email, :password)";
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':email', $_POST['email']);
			$sth->bindParam(':password', $password);
			
			try{
				$sth->execute();
				$_SESSION["success"] = "New account created successfully";
				header("Location: login.php");
				return;
			} catch(PDOException $e) {
				error_log("Account Creation Failed: " . $e->getMessage());
				$_SESSION["error"] = "Account creation failed";
				header("Location: accounts.php");
				return;
			}
		}
		else{
			$_SESSION["error"] = "Passwords Do Not Match.";
			header("Location: accounts.php");
			return;
		}
	}
}

$dbh = null;

?>



<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta charset="utf-8" />
	<title> Accounts </title>
	<link rel="stylesheet" href="styles.css" />
	<script src="accounts.js"></script>
</head>
<body>
	
	<h1>User Accounts</h1>
	
	<nav>
		<a href=" ">Index</a >
		<a href="login.php">Login</a >
	</nav>
	
	<section id="create">
		
		<hr />
		
		<h2> Create New Account </h2>
		
		<form method="post">
			<label for="email">Email</label>
			<input type="text" id="email" name="email" /><br />
			<label for="password">Password</label>
			<input type="password" id="password" name="password" /><br />
			<label for="retype">Re-type Password</label>
			<input type="password" id="retype" name="retype" /><br />
			<input type="submit" onclick="return validateAccount();" name="submit" value="Submit" />
		</form>
		
		<p id="js_validation_message"></p >
		
	</section>
</body>
</html>
