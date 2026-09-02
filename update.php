<?php

 require('header.php');
 
 if(array_key_exists('editButton', $_POST))
	 processEdit();
 else if(array_key_exists('updateButton', $_POST))
	editform();
 else
	displayList();

 require('footer.php');


//---------------------------------------------------------
function displayList()
{
 //draw the form
 //query the database to get the car names and the primary key
 //submit button that sends the item to be edited
 
 echo <<<HTMLBLOCK
 <p>Select the EV to edit</p>
 <form method="POST" action="update.php">
	<select name="record">
HTMLBLOCK;
 
 require("credentials.php");
 $db = mysqli_connect($hostname, $username, $password, $database);
 if(mysqli_connect_errno())
	 die("Unable to connect to database " .mysqli_connect_error());
 
 $cars = mysqli_query($db, 'SELECT name,ID FROM cars ORDER BY name'); //sets names in ORDER
 if(!$cars)
	 die("Query failed ". mysqli_error($db));
 
 while($row = mysqli_fetch_array($cars))
 {
	 $name = $row[0];
	 $ID = $row[1];
	 
	 echo "		<option value=\"$ID\">$name</option>\n";
 }
	 echo <<<FORMBLOCK
	    </select>
		<p>
		   <input type="submit" name="updateButton" value="Update Select">
		</p>
	</form>
FORMBLOCK;

	mysqli_close($db);
}

//---------------------------------------------------------
function editForm()
{
	//Get values from POST
	$ID = $_POST['record'];
	
	//Vailidate values
	$ID = filter_var($ID, FILTER_VALIDATE_INT,
		array("options"=>array("min_range"=>0)));

	require('credentials.php');

	$db = mysqli_connect($hostname, $username, $password, $database);

	if(mysqli_connect_errno())
		die("Unable to connect to database " . mysqli_connect_error());
	
	//Prepare Statment & bind params
	$query = mysqli_prepare($db, 'SELECT name, productionYears, miles FROM cars WHERE ID=?');
	mysqli_stmt_bind_param($query, "i", $ID);
	
	//Display form
	if(mysqli_stmt_execute($query))
	{
		mysqli_stmt_bind_result($query, $name, $prodYears, $range);
		mysqli_stmt_fetch($query);

		echo <<<EDITFORM
<form method="POST" action="update.php">
	<table>
		<tr>
			<th><label for="model">Model:</label></th>
			<th><label for="years">Years Produced:</label></th>
			<th><label for="range">Range:</label></th>
		</tr>

		<tr>
			<td><input type="text" id="model" name="model" required maxlength="64" autocomplete="off" value="$name"></td>
			<td><input type="text" id="years" name="years" required maxlength="9" pattern="^[0-9]{4}-$|^[0-9]{4}-[0-9]{4}$" autocomplete="off" value="$prodYears"></td>
			<td><input type="number" id="range" name="range" required maxlength="5" pattern="^[0-9]{1,5}$" autocomplete="off" value="$range">
				<input type="hidden" id="record" name="record" value="$ID">
			</td>
		</tr>

		<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td style="float:right"><input type="submit" name="editButton" value="Update EV"></td>
		</tr>
	</table>
</form>
EDITFORM;
//xxx NOTE WE SHOULD USE PHP SESSION TO TRANSFER THE ID, NOT!! A HIDDEN FIELD
	}
	else
	{
		die("Query error " . mysqli_error($db));
	}

	mysqli_stmt_close($query);
	mysqli_close($db);
}

//---------------------------------------------------------
function processEdit()
{
    // get POST variables safely
    $name      = $_POST['model'];
    $prodYears = $_POST['years'];
    $range     = $_POST['range'];
    $ID        = $_POST['record'];

    // validate
    $name = trim($name);
    $name = filter_var($name, FILTER_VALIDATE_REGEXP, [
        "options" => ["regexp" => "/^[0-9a-zA-Z!\-\.]{1,64}$/"]
    ]);

    $prodYears = trim($prodYears);
    $prodYears = filter_var($prodYears, FILTER_VALIDATE_REGEXP, [
        "options" => ["regexp" => "/^[0-9]{4}-$|^[0-9]{4}-[0-9]{4}$/"]
    ]);

    $range = filter_var($range, FILTER_VALIDATE_INT, [
        "options" => ["min_range" => 0, "max_range" => 99999]
    ]);

    $ID = filter_var($ID, FILTER_VALIDATE_INT, [
        "options" => ["min_range" => 0]
    ]);

    // check validation results
    if ($name === false || $prodYears === false || $range === false || $ID === false) {
        die("Invalid input.");
    }

    // connect to database
    require('credentials.php'); // <-- fixed missing parenthesis

    $db = mysqli_connect($hostname, $username, $password, $database);
    if (mysqli_connect_errno()) {
        die("Unable to connect to database " . mysqli_connect_error());
    }

    // prepare query
    $query = mysqli_prepare($db, "UPDATE cars SET name=?, productionYears=?, miles=? WHERE ID=?");

    // bind params (s = string, i = int)
    mysqli_stmt_bind_param($query, "ssii", $name, $prodYears, $range, $ID);

    // execute
    if (mysqli_stmt_execute($query)) {
        echo '<div class="center"><h2>Edit Saved</h2></div>'; // <-- fixed quotes
    } else {
        echo '<div class="center"><h2>Unable to update record. Update aborted</h2></div>';
    }

    // close
    mysqli_stmt_close($query);
    mysqli_close($db);
}



?>