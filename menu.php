<?php
	require('header.php');
	
echo <<<HTMLBLOCK
<table>
    <tr>
        <td>&bullet; <a href="listing.php">Show EVs</a></td>
    </tr>
    <tr>
        <td>&bullet; <a href="add.php">Add EVs</a></td>
    </tr>
    <tr>
        <td>&bullet; <a href="update.php">Edit EVs</a></td>
    </tr>
    <tr>
        <td>&bullet; <a href="Delete.php">Delete EVs</a></td>
    </tr>
</table>
HTMLBLOCK;

	
	
	require('footer.php');
	//database password named ev: 
	/*
		do show tables
		describe cars
		SELECT * FROM cars (This grabs everything out of the cols)
	*/
?>