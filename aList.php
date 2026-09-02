<?php

	require('header.php');
	displayList();
	require('footer.php');

//-----Display List------------------------------------------------------------------------------

function displayList()
{
    $background = 0;

    echo <<<HTMLBLOCK
	<div class="centerTable">
    <table>
	<caption>Available Appointments</caption>
        <tr>
            <th>Start Time</th>
            <th>End Time</th>
            <th>SID</th>
        </tr>
HTMLBLOCK;
	
	require('credentials.php');
	$db = mysqli_connect($hostname, $username, $password, $database);
	
	if (mysqli_connect_errno())
		die("Unable to connect to database " . mysqli_connect_error());

    $appts = mysqli_query($db, 'select * from appts where SID is null order by start');

    if (!$appts) //Logic Case: If Query Fails
		die("Query failed " . mysqli_error($db));
	
	if(mysqli_num_rows($appts) == 0)
		echo "<tr><td colspan='3'>Sorry, No Appointments are Available</td></tr>";
	else
	{
		while ($row = mysqli_fetch_array($appts)) //Logic Case: Create Row IF There is Appts
		{
			$PK    		= $row[0]; 
			$StartTime 	= $row[1];
			$EndTime   	= $row[2];
			$SID   		= $row[3];
			
			$Start 	= date("n/j/y g:i A", strtotime($StartTime));
			$End 	= date("n/j/y g:i A", strtotime($EndTime));
			
			if ($background++ % 2 == 0)
				echo "<tr class='lightRow'>\n";
			else
				echo "<tr class='darkRow'>\n";
			
			 echo <<<TABLEDATA
				<td style="color: black">$Start</td>
				<td style="color: black">$End</td>
				<td style="color: black">$SID</td>
			</tr>
	TABLEDATA;
		}
	}	
	echo "</table> </div>";
    mysqli_close($db); //Close Database
}


?>