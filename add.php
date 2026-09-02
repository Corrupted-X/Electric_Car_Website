<?php

	require('header.php');
	
	if(isset($_POST['addButton']))
		addEV();
	else
		displayForm();
		
	require('footer.php');
	
//-------------------------------------------------------------------------

function displayForm()
{
	echo <<<'FORMBLOCK'
	<form method="POST" action="add.php">
	<table>
		<tr>
			<th><label for="name">Model: </label></th>
			<th><label for="years">Years produced: </label></th>
			<th><label for="range">Range: </label></th>
		</tr>
		<tr>
			<td><input type="text" id="name" name="name" required maxlength="64" placeholder="name of EV" autocomplete="off"></td>
			<td><input type="text" id="years" name="years" required maxlength="9" placeholder="1975-2002" pattern="^[0-9]{4}-$|^[0-9]{4}-[0-9]{4}$" autocomplete="off"></td>
			<td><input type="numeric" id="range" name="range" required maxlength="5" placeholder="999" pattern="^[0-9]{1,5}$" autocomplete="off"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td style="float:right;"> <input type="submit" name="addButton" value="Add EV"></td>
		</tr>
	</table>
	</form>
FORMBLOCK;
}
//-------------------------------------------------------------------------

function addEV()
{
	//get data from form
	$name = $_POST['name'];
	$years = $_POST['years'];
	$range = $_POST['range'];
	
	//validate data
	$name = trim($name);
	$name = filter_var($name, FILTER_VALIDATE_REGEXP, 
		array("options"=>array("regexp"=>"/^[0-9a-zA-Z!-\.]{1,64}$/" )));
		
	$years = trim($years);
	$years = filter_var($years, FILTER_VALIDATE_REGEXP,
		array("options"=>array("regexp" =>"/^[0-9]{4}-$|^[0-9]{4}-[0-9]{4}$/")));
		
	$range = filter_var($range, FILTER_VALIDATE_INT, 
		array("options"=>array("min_val"=>"1", "max_val"=>"99999")));
	
	
	if($name != false && $years != false && $range != false)
	{
		
		//make database connection (handle)
		require("credentials.php");
		$db = mysqli_connect($hostname, $username, $password, $database);
		if(mysqli_connect_errno())
			die("Unable to connect to database " . mysqli_connect_error());

		//create prepared statement
		$query = mysqli_prepare($db, "INSERT INTO cars (name, productionYears, miles) VALUES(?, ?, ?)");
		
		//bind the parameters
		mysqli_stmt_bind_param($query, "ssi", $name, $years, $range); //ssi denotes the data types: s = string, i = integer
		
		//execute the query & display result (success or fail)
		if(mysqli_stmt_execute($query))
			echo <<<'SUCCESS'
				<div clas="center">
					<h2>Success record added</h2>
				</div>
SUCCESS;
		else
			echo <<<'FAIL'
				div clas="center">
					<h2>Error, record not added</h2>
				</div>
FAIL;
	} //If data okay
	else
	{
		echo <<<'FAILBLCOK'
		<div class="center">
			<h2>An error occurred. Unable to add record</h2>
		</div>
FAILBLCOK;
	}
	//You should close the query and the database
	mysqli_stmt_close($query);
	mysqli_close($db);
}
?>