<?php
require("config.class.php");
require_once("mysqldb.class.php");
require_once("time.php");
session_start();


/*
 * ACTION SAVE
 *
 */

if ($_GET['action'] == "save") 
{
	// sanitize data
	$workspace = htmlspecialchars(stripslashes($_POST['workspace']));
	$workspace = mysql_real_escape_string($workspace);
	$type = $_POST['type'];
	$rate = htmlspecialchars(stripslashes($_POST['rate']));
	$rate = mysql_real_escape_string($rate);
	$curr = htmlspecialchars(stripslashes($_POST['currency']));
	$curr = mysql_real_escape_string($curr);
	$timeid = $_POST['timeid'];

	// query
	$q = "INSERT INTO `billable_rates` (`id`, `workspace`, `type`, `rate`, `currency`, `timeid`) VALUES (NULL, '$workspace', '$type', '$rate', '$curr', '$timeid')";

	// insert data in db
	//$mydb = Mysqldb::getInstance("localhost", "", "root", "", "time");
	$mydb = Mysqldb::getInstance("", "", "", "", "");
	$a_results = $mydb->query($q);
	if (empty($a_results))
	{ 
		die("Error: Duplicated value is not allowed for Time ID."); 
	}
	else 
	{ 
		die( "Success: Data was inserted in the data base." );
	}

}



/*
 * INTERFACE
 *
 */

// Time Id come from session now
$idd = $_SESSION['ADDTIMEID'];
$billing = <<<eof
			<script type="text/javascript" src="js/functions.js"></script>

			<form action="{$_SERVER['PHP_SELF']}?action=save" method="post">
			<input type='hidden' name='timeid' value='$idd' />
			<table width='100%'>
			<tr><td> Workspace Name</td></tr>
			<tr><td> <input type='text' name='workspace' value='' /> </td></tr>
			<tr><td> <input type='radio' name='type' value='0' /> Not Billable
					 <input type='radio' name='type' value='1' /> Default Hourly Rate
					 <input type='radio' name='type' value='2' /> Fixed Amount Of
				</td></tr>
			<tr><td> Default Hourly Rate / Fixed Sum Of </td>
			<tr><td> 
				<input type="text" maxlength="10" size="10" name="rate" onkeypress="return(currencyFormat(this,'','.',event))" />
			    </td>
			<tr><td> Default Currency (BRL, USD, EUR, THB, etc..) </td>
			<tr><td> 
				<select name="currency" style="width:100px;">
					<option value="0" selected> Select </option> 
					<option value="BRL" > BRL - Brazilian Real </option> 
					<option value="USD" > USD - US Dollar </option> 
					<option value="EUR" > EUR - Euro </option> 
					<option value="THB" > THB - Thai Baht </option> 
					<option value="GBP" > GBP - British Pound </option> 
					<option value="AED" > AED - Emirati Dirham </option> 
					<option value="INR" > INR - Indian Rupee </option> 
					<option value="CHF" > CHF - Swiss Franc </option> 
					<option value="ILS" > ILS - Israeli Shekel </option> 
					<option value="CNY" > CNY - Chinese Yuan Renminbi  </option> 
					<option value="JPY" > JPY - Japanese Yen </option> 
					<option value="AUD" > AUD - Australian Dollar </option> 

				</select>
			     </td>
			<tr><td> &nbsp; </td>
			<tr><td> <input type='submit' value='Save' /> </td>
			</tr></table>

			</form>
eof;


$bodyContent .= "<h1>Time Tracker</h1><h3>Billing</h3>" . $billing;



$html = <<<eof
<html>
<head>
<meta http-equiv="refresh" content="60;url={$_SERVER['PHP_SELF']}" />
<title>{$title} $timeWorkedToday hrs - Time</title>
<style type="text/css">
/* CSS goes here */
/* The css variable below is populated via ssi/css.php if you need dynamic css. Throughly test in needed browsers. */


body {
    font-family:sans-serif;
    font-size:100%;
    background-color:#93021C;

        /* fit the background image perfectly */
        background: url(images/Black_Background.png) no-repeat center center fixed;
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
