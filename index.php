<!-- Add jQuery library -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>

<!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript" src="jQuery/lib/jquery.mousewheel-3.0.6.pack.js"></script>

<!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="jQuery/source/jquery.fancybox.js"></script>
<link rel="stylesheet" type="text/css" href="jQuery/source/jquery.fancybox.css" media="screen" />

<!-- Add Button helper (this is optional) -->
<link rel="stylesheet" type="text/css" href="jQuery/source/helpers/jquery.fancybox-buttons.css?v=2.0.4" />
<script type="text/javascript" src="jQuery/source/helpers/jquery.fancybox-buttons.js?v=2.0.4"></script>

<!-- Add Thumbnail helper (this is optional) -->
<link rel="stylesheet" type="text/css" href="jQuery/source/helpers/jquery.fancybox-thumbs.css?v=2.0.4" />
<script type="text/javascript" src="jQuery/source/helpers/jquery.fancybox-thumbs.js?v=2.0.4"></script>

<script type="text/javascript">
	$(document).ready(function() {
		/*
		 * Open manually
		 */
		$("#fancybox-manual-a").click(function() {
			$.fancybox.open({
				href : 'billable_rates.php', 
				maxWidth	: 600,
				maxHeight	: 500,
				fitToView	: false,
				width		: '100%',
				height		: '100%',
				autoSize	: false,
				closeClick	: false,
				openEffect	: 'none',
				closeEffect	: 'none',
				type : 'iframe',
				padding : 5
			});
		});
		$("#fancybox-manual-b").click(function() {
			$.fancybox.open({
				href : 'company_add.php', 
				maxWidth	: 600,
				maxHeight	: 500,
				fitToView	: false,
				width		: '100%',
				height		: '100%',
				autoSize	: false,
				closeClick	: false,
				openEffect	: 'none',
				closeEffect	: 'none',
				type : 'iframe',
				padding : 5
			});
		});
	});
</script>
<style type="text/css">
	.fancybox-custom .fancybox-outer {
		box-shadow: 0 0 50px #222;
	}
</style>

<?php
require("config.class.php");
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

    // Unset some sessions here after clocked out. Unset the session here.
    unset($_SESSION['ADDTIMEID']);
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
	$timeid 		= $time->getCurrentTimeId ();
	$arr 			= $time->getTime ($timeid);
	$cnt 			= 0;
	$weekNumber 		= null;
	$a_weeksTotal 		= array();
	$arrayCount 		= count($a_allHoursWorked) - 1;
	// Add time id in the session, instead of pass in the url parameter
	session_start();
	$_SESSION['ADDTIMEID'] 	= $timeid;

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

	//$db = mysql_connect('localhost','root','') or die("Database error");
	//mysql_select_db('time', $db);

	// Query billable rates for this time tracker based in the registered client
 	$result 	= mysql_query("SELECT * FROM billable_rates WHERE timeid=$timeid");
	$row 		= mysql_fetch_array($result);
	$billable_id 	= $row['id'];
	$workspace 	= $row['workspace'];
	$rate 		= $row['rate'];
	$currency 	= $row['currency'];

	// Start session to use with billing, need to be unset when clocked out (unset variables session when call time->createNewBillingEntry()).
	session_start();
	$_SESSION['BILLID'] 	= $billable_id;
	$_SESSION['WORKSPACE'] 	= $workspace;
	$_SESSION['RATE'] 	= $rate;
	$_SESSION['CURRENCY'] 	= $currency;

	$result2 		= mysql_query("SELECT created, timestamp FROM time WHERE tid=$timeid");
	$row2 			= mysql_fetch_array($result2);
	$timestamp  		= $row2['timestamp'];
	$created  		= $row2['created'];


	$tt 			= $time->convertUnixTimeToHours($time->getTotalTimeForDay($created)) . " hours (" . $time->convertUnixTimeToMinutes($time->getTotalTimeForDay($created)) . " min)";
	$hours 			= $time->convertUnixTimeToHours($time->getTotalTimeForDay($created));
	$_SESSION['HOURS'] 	= $hours;
	$Today 			= $time->convertUnixTimeToHours($time->getTotalTimeForDay($created));
	$amount			= ($rate * $Today);
	$_SESSION['AMOUNT'] 	= $amount;


	$billing .= "<tr><td>" . $workspace . "</td>
				 <td>" . $rate .' '. $currency . "</td> 
				 <td>" . $tt . "</td>
				 <td>" . $amount .' '. $currency . "</td></tr>";
}

$bodyContent .= "<h1>Time Tracker</h1>" . $timeForm . "<h3>Recent times worked</h3>" . $todaysTime . $thisWeeksTime . "<h3>All times worked</h3>" . $allTime . "<h3>Billing <a id='fancybox-manual-a' href='javascript:;'><img src='images/edit.png' title='Bill for' alt='Bill for' ></a>  <a id='fancybox-manual-b' href='javascript:;'><img src='images/add.png' title='Register Company' alt='Register Company' ></a> </h3> " . $billing;


$html = <<<eof
<html>
<head>
<meta http-equiv="refresh" content="120;url={$_SERVER['PHP_SELF']}" />
<title>{$title} $timeWorkedToday hrs - Time</title>
<style type="text/css">
/* CSS goes here */
/* The css variable below is populated via ssi/css.php if you need dynamic css. Throughly test in needed browsers. */

body {
    font-family:sans-serif;
    font-size:100%;
    //background-color:#93021C;

	/* fit the background perfectly */
	background: url(images/tahiti.png) no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
	/* fit the background perfectly */

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
//    margin-bottom:16px;
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