<?php
require_once("config.class.php");
require_once("mysqldb.class.php");
require_once("time.php");


$time = new TimeGettersSetters();
$bodyContent = "";



/*
 * Actions
 */

if ($_GET['action'] == "clockIn") 
{
    $time->clockIn();
    header("Location:{$_SERVER['PHP_SELF']}"); exit();
} 
else if ($_GET['action'] == "clockOut") 
{
	session_start();
	$time->createNewBillingEntry();
	$time->clockOut();
    	header("Location:{$_SERVER['PHP_SELF']}"); exit();
}



/*
 * Render page
 */

if ($time->isClockedIn()) 
{
    $title 	= "IN";
    $action 	= "clockOut";
    $buttons 	= "<input disabled='disabled' type='submit' value='Clock in' /> <input type='submit' value='Clock out' />";
} 
else 
{
    $title 	= "OUT";
    $action 	= "clockIn";
    $buttons 	= "<input type='submit' value='Clock in' /> <input disabled='disabled' type='submit' value='Clock out' />";
}

$timeForm = <<<eof
<form action="{$_SERVER['PHP_SELF']}?action={$action}" method="post">
{$buttons}
</form>
eof;

$billing = "<table width='100%'><tr bgcolor='#efefef'><td><b>Company</b></td><td><b>Rate</b></td><td><b>Time</b></td><td><b>Amount</b></td></tr>";


$timeWorkedToday = $time->convertUnixTimeToHours($time->getTotalTimeForDay(date("Y-m-d")));
$todaysTime 	 = "<p><strong>Today:</strong> " . $time->convertUnixTimeToHours($time->getTotalTimeForDay(date("Y-m-d"))) . " hours (" . $time->convertUnixTimeToMinutes($time->getTotalTimeForDay(date("Y-m-d"))) . " minutes)</p>";
$thisWeeksTime 	 = "<p><strong>This week:</strong> " . $time->convertUnixTimeToHours($time->getTotalTimeForCurrentWeek()) . " hours (" . $time->convertUnixTimeToMinutes($time->getTotalTimeForCurrentWeek()) . " minutes)</p>";

$allTime 		= "";
$a_allHoursWorked 	= $time->getHoursWorkedForEachDay();
if ($a_allHoursWorked) 
{
	// get last time ID	
	$timeid 	= $time->getCurrentTimeId ();
	$arr 		= $time->getTime ($timeid);
	$cnt 		= 0;
	$weekNumber 	= null;
	$a_weeksTotal 	= array();
	$arrayCount 	= count($a_allHoursWorked) - 1;

	foreach ($a_allHoursWorked as $date=>$secondsWorked) 
	{
        	$weekNumber = date("W", strtotime($date));
		if ($cnt === 0) 
		{
		    # starts a new week total
		    $a_weeksTotal[$weekNumber] = $time->getTotalTimeForDay($date);
		} 
		else 
		{
			if ($weekNumber === $previousWeekNumber) 
			{
				# keep adding time to this weeks total
				$a_weeksTotal[$weekNumber] += $time->getTotalTimeForDay($date);
			} 
			else if ($weekNumber !== $previousWeekNumber || $cnt === $arrayCount) 
			{
                		$allTime .= "<p class='weekHeading'><strong>Week({$previousWeekNumber}) Total:</strong> " . $time->convertUnixTimetoHours($a_weeksTotal[$previousWeekNumber]) . " hours (" . $time->convertUnixTimeToMinutes($a_weeksTotal[$previousWeekNumber]) . " minutes)</p>";
				# starts a new week total
                		$a_weeksTotal[$weekNumber] = $time->getTotalTimeForDay($date);
			}
        	}

        	$allTime .= "<p><strong>{$date}:</strong> " . $time->convertUnixTimeToHours($secondsWorked) . " hours (" . $time->convertUnixTimeToMinutes($secondsWorked) . " minutes)</p>";
        	$previousWeekNumber = $weekNumber;
        	# This if block catches the very first week entered.

		if ($weekNumber !== $previousWeekNumber || $cnt === $arrayCount) 
		{
			$allTime .= "<p class='weekHeading'><strong>Week({$previousWeekNumber}) Total:</strong> " . $time->convertUnixTimetoHours($a_weeksTotal[$previousWeekNumber]) . " hours (" . $time->convertUnixTimeToMinutes($a_weeksTotal[$previousWeekNumber]) . " minutes)</p>";
			# starts a new week total
			$a_weeksTotal[$weekNumber] = $time->getTotalTimeForDay($date);
		}

        $cnt++;
	}

	// Query billable rates for this time tracker based in the registered client
	$db = mysql_connect('localhost','root','') or die("Database error");
	mysql_select_db('time', $db);

 	$q 		= "SELECT * FROM billable_rates WHERE timeid=$timeid";
	$result 	= mysql_query($q);
	$row 		= mysql_fetch_array($result);
	$billable_id 	= $row['id'];
	$workspace 	= $row['workspace'];
	$rate 		= $row['rate'];
	$currency 	= $row['currency'];


	/* Grava valores na sessão para usar em billing, iso deve ser apagado na chamada da função em  time->createNewBillingEntry()  */
	session_start();
	$_SESSION['BILLID'] 	= $billable_id;
	$_SESSION['WORKSPACE'] 	= $workspace;
	$_SESSION['RATE'] 		= $rate;
	$_SESSION['CURRENCY'] 	= $currency;


	$q2 			= "SELECT created, timestamp FROM time WHERE tid=$timeid";
	$result2 		= mysql_query($q2);
	$row2 			= mysql_fetch_array($result2);
	$timestamp  	= $row2['timestamp'];
	$created  		= $row2['created'];


	$tt = $time->convertUnixTimeToHours($time->getTotalTimeForDay($created)) . " hours (" . $time->convertUnixTimeToMinutes($time->getTotalTimeForDay($created)) . " min)";
	$hours 				= $time->convertUnixTimeToHours($time->getTotalTimeForDay($created));
	$_SESSION['HOURS'] 	= $hours;
	$Today 				= $time->convertUnixTimeToHours($time->getTotalTimeForDay($created));
	$amount				= ($rate * $Today);
	$_SESSION['AMOUNT'] = $amount;


	$billing .= "<tr><td>" . $workspace . "</td>
				 <td>" . $rate .' '. $currency . "</td> 
				 <td>" . $tt . "</td>
				 <td>" . $amount .' '. $currency . "</td></tr>";
}

$bodyContent .= "<h1>Time Tracker</h1>" . $timeForm . "<h3>Recent times worked</h3>" . $todaysTime . $thisWeeksTime . "<h3>All times worked</h3>" . $allTime . "<h3>Billing <a target='_blank' href='billable_rates.php?id=$timeid'><img src='edit.png'></a></h3>" . $billing;


$html = <<<eof
<html>
<head>
<meta http-equiv="refresh" content="15;url={$_SERVER['PHP_SELF']}" />
<title>{$title} $timeWorkedToday hrs - Time</title>
<style type="text/css">
/* CSS goes here */
/* The css variable below is populated via ssi/css.php if you need dynamic css. Throughly test in needed browsers. */

body {
    font-family:sans-serif;
    font-size:100%;
    background-color:#93021C;

	/* fit the background image perfectly */
	background: url(tahiti.png) no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
	/* fit the background image perfectly */

    text-align:center;
    }
li {
    margin-bottom:8px;
    }
a, a:link {
    color:maroon;
    }
a:hover {
    color:navy;
    text-decoration:none;
    }
div#container {
    width:500px;
    text-align:left;
    margin-left:auto;
    margin-right:auto;
    margin-top:32px;
    margin-bottom:16px;
    padding:16px;
    background-color:white;
    -moz-border-radius:16px;
    -moz-box-shadow: 0px 0px 16px #000;
    -webkit-box-shadow: 0px 0px 16px #000;
    -o-box-shadow: 0px 0px 16px #000;
    box-shadow: 0px 0px 16px #000;
    }
.mono {
    font-family:monospace;
    }

h1, h2, h3, h4 {
    margin:8px 4px;
    padding:4px 8px;
    background-color:#dfdfdf;
    -moz-border-radius:8px;
    -moz-box-shadow: 0px 0px 4px #000;
    -webkit-box-shadow: 0px 0px 4px #000;
    -o-box-shadow: 0px 0px 4px #000;
    box-shadow: 0px 0px 4px #000;
}

.DISTweekHeading {
    background-color:#eeeeee;
    -moz-border-radius:8px;
    -moz-box-shadow: 0px 0px 4px #000;
    -webkit-box-shadow: 0px 0px 4px #000;
    -o-box-shadow: 0px 0px 4px #000;
    box-shadow: 0px 0px 4px #000;
}
.weekHeading {
    background-color:white;
    -moz-border-radius-bottomleft:8px;
    -moz-border-radius-bottomright:8px;
    -moz-box-shadow: 0px 4px 2px gray;
    -webkit-box-shadow: 0px 4px 2px gray;
    -o-box-shadow: 0px 4px 2px gray;
    box-shadow: 0px 4px 2px gray;
}

form, p {
    margin:4px 4px;
    padding:4px 4px;
}

li {
    list-style-type:none;
}
</style>
</head>
<body>
<div id="container">
{$bodyContent}
</div>
</body>
</html>
eof;

print($html);
