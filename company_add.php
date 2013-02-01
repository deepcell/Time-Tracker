<?php
require("config.class.php");
require_once("mysqldb.class.php");
require_once("time.php");
session_start();



/*
 * ACTION SAVE COMPANY DATA
 *
 */
if ($_GET['action'] == "save") 
{
	// sanitize data   mysql_real_escape_string()	
	$responsible = htmlspecialchars(stripslashes($_POST['responsible']));
	$name = htmlspecialchars(stripslashes($_POST['name']));
	$unique_code = md5(date("Y-m-d H:i:s"));

	// query
	$q = "INSERT INTO `company` (`id`, `name`, `responsible`, `unique_code`) VALUES (NULL, '$name', '$responsible', '$unique_code')";

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
$idd = $_SESSION['ADDTIMEID'];		// Time Id come from session now
$comp = <<<eof
			<script type="text/javascript" src="js/functions.js"></script>
			<form action="{$_SERVER['PHP_SELF']}?action=save" method="post">
				<input type='hidden' name='timeid' value='$idd' />
				<table width='100%'>			
					<tr><td> Company Name </td></tr>
					<tr><td><input type="text" name="name" value="" /></td></tr>
					<tr><td> Responsible </td></tr>
					<tr><td><input type="text" name="responsible" value="" /></td></tr>
					<tr><td> &nbsp; </td>
					<tr><td> <input type='submit' value='Save' /> </td>
					</tr>
				</table>
			</form>
eof;


$bodyContent .= "<h1>Time Tracker</h1><h3>Company</h3>" . $comp;


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