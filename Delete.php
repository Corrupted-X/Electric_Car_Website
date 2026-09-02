<?php

	require('header.php');
	
	if(isset($_POST['deleteButton']))
		deleteList();
	else
		displayList();
	
	require('footer.php');

//------------------------------------------------------------------------------------------------

function displayList()
{
	$background = 0;
	
	echo <<<HTMLBLOCK
	<form method="POST" action="Delete.php">
		<table>
			<tr>
				<th>Delete</th>
				<th>Name</th>
				<th>Production Years</th>
				<th>Range</th>
			</tr>
HTMLBLOCK;

	require('credentials.php');
	$db = mysqli_connect($hostname, $username, $password, $database);
	if(mysqli_connect_errno())
		die("Unable to connect to database " . mysqli_connect_error());
	
	$cars = mysqli_query($db, 'SELECT name,productionYears,miles,ID FROM cars ORDER BY name');
	if(!$cars) // FIX: added $
		die("Query Failed " . mysqli_error($db));
	
	//iterate over row set, display each row and alternate the background between light and dark
	while($row = mysqli_fetch_array($cars))
	{
		$name  = $row[0];
		$years = $row[1];
		$range = $row[2];
		$ID    = $row[3];
		
		if($background++ % 2 == 0) // FIX: corrected alternating logic
			echo "		<tr style=\"background-color: white\">\n";
		else
			echo "		<tr style=\"background-color: lightgrey\">\n";
		
		echo <<<TABLEDATA
			<td><input type="checkbox" id="carid[]" name="carid[]" value="$ID"></td>
			<td>$name</td>
			<td>$years</td>
			<td>$range</td>
		</tr>

TABLEDATA;
	}
	
	//delete button
	echo <<<FORMBLOCK
	</table>
	<p>
	<input type="submit" name="deleteButton" value="Delete Selected">
	</p>
	</form>
FORMBLOCK;
	
	//close database
	mysqli_close($db);
}

//---------------------------------------------------------------------------------

function deleteList()
{
	$result = 0;
	//connect to db
	require('credentials.php');
	$db = mysqli_connect($hostname, $username, $password, $database);
	if(mysqli_connect_errno())
		die("Unable to connect to database " . mysqli_connect_error());
	
	//Grab form data
	$delete = $_POST['carid'];
	
	//Build query template
	$query = mysqli_prepare($db, 'DELETE FROM cars WHERE ID=?');
	
	//iterate over the form data 
		//bind
		//execute
		//increment counter on success
	foreach($delete as $index => $recordID) //delete: array, $index: key, recordID: value
	{
		$recordID = filter_var($recordID, FILTER_VALIDATE_INT, array("options"=>array("min_range"=>0)));
		if($recordID)
		{
			mysqli_bind_param($query, "i", $recordID); //binds it to our prepare stmt
			if(mysqli_stmt_execute($query))
				$result++;
		}
	}

	//display the results
if ($result > 0)
{
    echo <<<SUCCESSBLOCK
<div class="center">
    <h2>Success! $result records deleted</h2>
</div>
SUCCESSBLOCK;
}
else
{
    echo <<<FAILBLOCK
<div class="center">
    <h2>An error occurred. $result records deleted</h2>
</div>
FAILBLOCK;
}
	
	mysqli_stmt_close($query);
	mysqli_close($db);
}

?>