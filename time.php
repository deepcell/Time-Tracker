<?php
require_once("config.class.php");
require_once("mysqldb.class.php");

class TimeGettersSetters {
    private $db;

    public function __construct() {
        $this->db = Mysqldb::getInstance();
        $this->db->switchDatabase('time');
    }

    /**
     * time methods
     */
    public function getNextTimeId () {
        $a_result = $this->db->query("select max(tid) + 1 as nextTimeId from time");
        $nextTid = $a_result[0]['nextTimeId'];
        if (intval($nextTid) > 0) {
            return $nextTid;
        }
        return false;
    }

    public function getCurrentTimeId () {
        $a_result = $this->db->query("select max(Tid) as currentTimeId from time");
        $currentTid = $a_result[0]['currentTimeId'];
        if (intval($currentTid) > 0) {
            return $currentTid;
        }
        return false;
    }

    public function getTime ($tid) {
        if (intval($tid) === 0) {
            return false;
        }
        $a_result = $this->db->query("select * from time where tid={$tid}");
        if (is_array($a_result) && count($a_result[0]) > 0) {
            return $a_result[0];
        }
        return false;
    }

    public function createNewTimeEntry ($table, $primaryKey) {
        $isSuccess = false;
        $cnt = 0;
        while (!$isSuccess) {
            if ($cnt === 25) {
                return false;
            }
            $nextTimeId = $this->getNextTimeId();
            $isSuccess = $this->db->query("insert into time ($primaryKey) values ({$nextTimeId})");
            if (!$isSuccess) {
                sleep(.5);
            }
            $cnt++;
        }
        return $nextTimeId;
    }

    public function setTimeTid ($tid, $tid) {
        if (!$this->getTimeTid($tid)) {
            return false;
        }
        if (intval($tid) > 0) {
            $this->db->query("update time set tid={$tid} where tid={$tid}");
            return true;
        }
        return false;
    }
    public function getTimeTid ($tid) {
        if (intval($tid) === 0) {
            return false;
        }
        $a_time = $this->getTime($tid);
        if (intval($a_time['tid']) > 0) {
            return (int)$a_time['tid'];
        }
        return false;
    }

    public function setTimeClockedin ($tid, $clockedIn) {
        if (!$this->getTimeTid($tid)) {
            return false;
        }
        if (intval($clockedIn) > 0) {
            $this->db->query("update time set clockedIn={$clockedIn} where tid={$tid}");
            return true;
        }
        return false;
    }
    public function getTimeClockedin ($tid) {
        if (intval($tid) === 0) {
            return false;
        }
        $a_time = $this->getTime($tid);
        if (intval($a_time['clockedIn']) > 0) {
            return (int)$a_time['clockedIn'];
        }
        return false;
    }

    public function setTimeClockedout ($tid, $clockedOut) {
        if (!$this->getTimeTid($tid)) {
            return false;
        }
        if (intval($clockedOut) > 0) {
            $this->db->query("update time set clockedOut={$clockedOut} where tid={$tid}");
            return true;
        }
        return false;
    }
    public function getTimeClockedout ($tid) {
        if (intval($tid) === 0) {
            return false;
        }
        $a_time = $this->getTime($tid);
        if (intval($a_time['clockedOut']) > 0) {
            return (int)$a_time['clockedOut'];
        }
        return false;
    }

    public function setTimeCreated ($tid, $created) {
        if (!$this->getTimeTid($tid)) {
            return false;
        }
        if (is_string($created) && $created != "") {
            $this->db->query("update time set created='" . $this->db->escapeQuery($created) . "' where tid={$tid}");
            return true;
        }
        return false;
    }
    public function getTimeCreated ($tid) {
        if (intval($tid) === 0) {
            return false;
        }
        $a_time = $this->getTime($tid);
        if (is_string($a_time['created']) && $a_time['created'] != "") {
            return (string)$a_time['created'];
        }
        return false;
    }

    public function setTimeTimestamp ($tid, $timestamp) {
        if (!$this->getTimeTid($tid)) {
            return false;
        }
        if (is_string($timestamp) && $timestamp != "") {
            $this->db->query("update time set timestamp='" . $this->db->escapeQuery($timestamp) . "' where tid={$tid}");
            return true;
        }
        return false;
    }
    public function getTimeTimestamp ($tid) {
        if (intval($tid) === 0) {
            return false;
        }
        $a_time = $this->getTime($tid);
        if (is_string($a_time['timestamp']) && $a_time['timestamp'] != "") {
            return (string)$a_time['timestamp'];
        }
        return false;
    }

    /**
     * Helper Functions
     */
    public function isClockedIn() {
        $a_results = $this->db->query("select tid, clockedIn, clockedOut, created from time where clockedIn > 0 and clockedOut = 0");
        if (!$a_results) {
            return false;
        }
        if (!is_array($a_results[0]) || count($a_results[0]) === 0) {
            return false; 
        }
        if ((int)$a_results[0]['clockedIn'] > 0 && (int)$a_results[0]['clockedOut'] === 0) {
            return (int)$a_results[0]['tid'];
        }
        return false;
    }

    public function clockIn() {
        if ($this->isClockedIn()) {
            return false;
        }
        $curdate = date("Y-m-d H:i:s");
        $curunixtime = (int)date("U");
        if ($this->db->query("insert into time (clockedIn, created) values ({$curunixtime}, '{$curdate}')")) {
            return true;
        }
        return false;
    }

    public function clockOut() {
        $tid = $this->isClockedIn();
        if (!$tid) {
            return false;
        }
        $curunixtime = (int)date("U");
        $this->db->query("update time set clockedOut = {$curunixtime} where tid={$tid}");
        return true;
    }

    public function getTotalTimeForDay ($date) {
        if (!is_string($date) && !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date)) {
            return false;
        }
        $a_results = $this->db->query("select clockedIn, clockedOut from time where created like '{$date}%' and clockedIn > 0");
        if (!$a_results) {
            return (int)0;
        }
        if (!is_array($a_results[0]) || count($a_results[0]) === 0) {
            return (int)0;
        }
        $total = 0;
        foreach ($a_results as $k=>$row) {
            $clockedIn = (int)$row['clockedIn'];
            $clockedOut = (int)$row['clockedOut'];
            $curunixtime = (int)date("U");
            if ($clockedOut === 0) {
                $clockedOut = $curunixtime;
            }
            $total += $clockedOut - $clockedIn;
        }
        return $total;
    }

    public function getTotalTimeForCurrentWeek() {
        $a_dates = $this->getDatesOfDaysForCurrentWeek();
        if (!is_array($a_dates) || count($a_dates) === 0) {
            return false;
        }
        $total = 0;
        foreach ($a_dates as $day=>$date) {
            $total += $this->getTotalTimeForDay($date); 
        }
        return (int)$total;
    }

    public function getDatesOfDaysForCurrentWeek($date = null) {
        $a_dates = array();
        if ($date == null) {
            $curday = (int)date("N");
        } else {
            $curday = (int)date("N", strtotime($date));
        }
        // monday
        if ($curday === 1) {
            $a_dates['monday'] = date("Y-m-d", strtotime("today"));
            $a_dates['tuesday'] = date("Y-m-d", strtotime("next tuesday"));
            $a_dates['wednesday'] = date("Y-m-d", strtotime("next wednesday"));
            $a_dates['thursday'] = date("Y-m-d", strtotime("next thursday"));
            $a_dates['friday'] = date("Y-m-d", strtotime("next friday"));
            $a_dates['saturday'] = date("Y-m-d", strtotime("next saturday"));
            $a_dates['sunday'] = date("Y-m-d", strtotime("next sunday"));
        }
        // tuesday
        if ($curday === 2) {
            $a_dates['monday'] = date("Y-m-d", strtotime("last monday"));
            $a_dates['tuesday'] = date("Y-m-d", strtotime("today"));
            $a_dates['wednesday'] = date("Y-m-d", strtotime("next wednesday"));
            $a_dates['thursday'] = date("Y-m-d", strtotime("next thursday"));
            $a_dates['friday'] = date("Y-m-d", strtotime("next friday"));
            $a_dates['saturday'] = date("Y-m-d", strtotime("next saturday"));
            $a_dates['sunday'] = date("Y-m-d", strtotime("next sunday"));
        }
        // wednesday
        if ($curday === 3) {
            $a_dates['monday'] = date("Y-m-d", strtotime("last monday"));
            $a_dates['tuesday'] = date("Y-m-d", strtotime("last tuesday"));
            $a_dates['wednesday'] = date("Y-m-d", strtotime("today"));
            $a_dates['thursday'] = date("Y-m-d", strtotime("next thursday"));
            $a_dates['friday'] = date("Y-m-d", strtotime("next friday"));
            $a_dates['saturday'] = date("Y-m-d", strtotime("next saturday"));
            $a_dates['sunday'] = date("Y-m-d", strtotime("next sunday"));
        }
        // thursday
        if ($curday === 4) {
            $a_dates['monday'] = date("Y-m-d", strtotime("last monday"));
            $a_dates['tuesday'] = date("Y-m-d", strtotime("last tuesday"));
            $a_dates['wednesday'] = date("Y-m-d", strtotime("last wednesday"));
            $a_dates['thursday'] = date("Y-m-d", strtotime("today"));
            $a_dates['friday'] = date("Y-m-d", strtotime("next friday"));
            $a_dates['saturday'] = date("Y-m-d", strtotime("next saturday"));
            $a_dates['sunday'] = date("Y-m-d", strtotime("next sunday"));
        }
        // friday
        if ($curday === 5) {
            $a_dates['monday'] = date("Y-m-d", strtotime("last monday"));
            $a_dates['tuesday'] = date("Y-m-d", strtotime("last tuesday"));
            $a_dates['wednesday'] = date("Y-m-d", strtotime("last wednesday"));
            $a_dates['thursday'] = date("Y-m-d", strtotime("last thursday"));
            $a_dates['friday'] = date("Y-m-d", strtotime("today"));
            $a_dates['saturday'] = date("Y-m-d", strtotime("next saturday"));
            $a_dates['sunday'] = date("Y-m-d", strtotime("next sunday"));
        }
        // saturday
        if ($curday === 6) {
            $a_dates['monday'] = date("Y-m-d", strtotime("last monday"));
            $a_dates['tuesday'] = date("Y-m-d", strtotime("last tuesday"));
            $a_dates['wednesday'] = date("Y-m-d", strtotime("last wednesday"));
            $a_dates['thursday'] = date("Y-m-d", strtotime("last thursday"));
            $a_dates['friday'] = date("Y-m-d", strtotime("last friday"));
            $a_dates['saturday'] = date("Y-m-d", strtotime("today"));
            $a_dates['sunday'] = date("Y-m-d", strtotime("next sunday"));
        }
        // sunday
        if ($curday === 0) {
            $a_dates['monday'] = date("Y-m-d", strtotime("last monday"));
            $a_dates['tuesday'] = date("Y-m-d", strtotime("last tuesday"));
            $a_dates['wednesday'] = date("Y-m-d", strtotime("last wednesday"));
            $a_dates['thursday'] = date("Y-m-d", strtotime("last thursday"));
            $a_dates['friday'] = date("Y-m-d", strtotime("last friday"));
            $a_dates['saturday'] = date("Y-m-d", strtotime("last saturday"));
            $a_dates['sunday'] = date("Y-m-d", strtotime("today"));
        }
        return $a_dates;
    }

    public function convertUnixTimeToHours ($unixtime) {
        if ($unixtime >= 0) {
            return (float)round(($unixtime / 60) / 60, 2);
        }
        return false;
    }

    public function convertUnixTimeToMinutes ($unixtime) {
        if ($unixtime >= 0) {
            return (float)round($unixtime / 60, 2);
        }
        return false;
    }

    public function getAllDates () {
        $a_results = $this->db->query("select tid, created from time where clockedIn > 0 and clockedOut > 0 order by created desc");
        $a_dates = array();
        foreach ($a_results as $k=>$row) {
            $date = preg_replace("/^([0-9]{4}-[0-9]{2}-[0-9]{2}).*$/", "\${1}", $row['created']);
            if (!in_array($date, $a_dates)) {
                $a_dates[] = $date;
            }
        }
        return $a_dates;
    }

    public function getHoursWorkedForEachDay () {
        $a_dates = $this->getAllDates();
        if (!is_array($a_dates) || count($a_dates) === 0) {
            return false;
        }
        $a_hoursWorked = array();
        foreach ($a_dates as $k=>$date) {
            $a_hoursWorked[$date] = $this->getTotalTimeForDay($date);
        }
        return $a_hoursWorked;
    }

	/* added in 4 feb 2012 by Fernando costa */
    public function createNewBillingEntry () 
	{
        $curdate = date("Y-m-d H:i:s");
        if ($this->db->query("INSERT INTO billing (billable_rates, time, amount, datetime) VALUES ('$_SESSION[BILLID]', '$_SESSION[HOURS]', '$_SESSION[AMOUNT]', '$curdate')")) 
		{	echo "OK";
            return true;
        }
		echo "FUDEU!";
        return false;
    }

}
