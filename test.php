<?php 
require("config.class.php");
require_once("mysqldb.class.php");
require_once("time.php");
$time = new TimeGettersSetters();



/*
 * Company Billing
 */
$val = $time->getCompanyData($_GET['action']);


if ($val == "") 
{
	die("No Data");
}



/*
 * INTERFACE
 *
 */
$html = <<<eof
<html>
<head>
<meta http-equiv="refresh" content="60;url={$_SERVER['PHP_SELF']}?action={$_GET['action']}" />
<title>{$val[0]['name']} - TimeTracker</title>
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
    width:450px;
    text-align:left;
    margin-left:auto;
    margin-right:auto;
    margin-top:0px;
    margin-bottom:16px;
    padding:10px;
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
eof;

echo '<div id="container"><pre>';
//print_r( $val );
foreach ($val as $v) {

	$h = $time->convertUnixTimeToHours($time->getTotalTimeForDay($v[created]));
	$m = $time->convertUnixTimeToMinutes($time->getTotalTimeForDay($v[created]));
	

	echo '
	<table width=100%>
		<tr><td width=40%><b>Name</b></td><td>'.$v[name].'</td></tr>
		<tr><td><b>Responsible</b></td><td>'.$v[responsible].'</td></tr>
		<tr><td><b>Rate</b></td><td>'.$v[rate].'</td></tr>
		<tr><td><b>Currency</b></td><td>'.$v[currency].'</td></tr>
		<tr><td><b>Payment Status</b></td><td>'.$v[status].'</td></tr>
		<tr><td><b>Date Created</b></td><td>'.$v[created].'</td></tr>
		<tr><td><b>Hour</b></td><td>'.$h.'</td></tr>
		<tr><td><b>Minutes</b></td><td>'.$m.'</td></tr>
	</table><hr />';
}

echo '
<table width=100% >
	<tr><td width=40%><b>Total Unpaid:</b></td><td>' . number_format($v[total], 2, ',', '.') .' '. $v[currency] . '</td></tr>
</table>';
echo '</pre></div>';


$html2 = <<<eof
</body>
</html>
eof;

print($html);
print($html2);