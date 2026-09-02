//Appointments JavaScript

function studentEmail(email)
{
	let txt = document.getElementById(email).value;

	if (txt.length < 1)
		return email + " Cannot be Blank";
	
	if (!/[a-zA-Z]+@una\.edu$/.test(txt))
		return "Invalid Email";
		
	return "";
}

function ApptTime(record)
{
	let ndx = document.getElementById(record).selectedIndex;

	if (ndx == 0 || ndx == -1)
		return "No " + record + " option selected\n";
	else
		return "";
}


function ValidateE()
{
	let msg ="";
	
	msg += studentEmail("email");
	
	if(msg.length > 0)
	{
		alert(msg);
		return false;
	}
	
	return true;
}

function Validate()
{
	let msg ="";
	
	msg += studentEmail("email");
	msg += ApptTime("record");
	
	if(msg.length > 0)
	{
		alert(msg);
		return false;
	}
	
	return true;
}