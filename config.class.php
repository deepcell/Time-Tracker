<?php

/*
 * Configuration file for Time-Tracker
 * 
 */

class Config 
{
	private static $singleton;
	public function __construct () 
	{
		// MySQL
		$this->mysqlServer   = "localhost";
		$this->mysqlPort     = "";
		$this->mysqlUsername = "root";
		$this->mysqlPassword = "";
		$this->mysqlDatabase = "time";
	}
    	public static function getInstance () 
	{
		if (is_null(self::$singleton)) 
		{
			self::$singleton = new config();
		}
        	return self::$singleton;
    	}
}
?>
