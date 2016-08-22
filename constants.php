<?php

ini_set('display_errors', 'On');
	
/* Connect to an ODBC database using driver invocation */
$dsn = 'mysql:dbname=IoT;host=127.0.0.1';
$db_user = 'python';

try {
    $db = new PDO($dsn, $db_user);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
/*** Constants ***/
$TIME_DIF_FOR_OFFLINE = "15";
$DAEMON_STATUS = "";
$WEB_REFRESH = "3";


function get_seconds($date_time) 
{ 
	return ($date_time->y * 365 * 24 * 60 * 60) + 
				 ($date_time->m * 30 * 24 * 60 * 60) + 
				 ($date_time->d * 24 * 60 * 60) + 
				 ($date_time->h * 60 * 60) + 
				 ($date_time->i * 60) + 
				 $date_time->s; 
} 
			

$sql = "SELECT *
	FROM `Global_Settings`";
foreach ($db->query($sql) as $row){
	if ($row["VARIABLE"] == "TIME_TO_OFF")
		$TIME_DIF_FOR_OFFLINE = $row["V_VALUE"];
	if ($row["VARIABLE"] == "DAEMON_STATUS")
		$DAEMON_STATUS = $row["V_VALUE"];
	if ($row["VARIABLE"] == "WEB_REFRESH")
		$WEB_REFRESH = $row["V_VALUE"];	
	if ($row["VARIABLE"] == "FUNK_ID_1")
		$FUNK_ID_1 = $row["V_VALUE"];	
	if ($row["VARIABLE"] == "FUNK_ID_2")
		$FUNK_ID_2 = $row["V_VALUE"];	
	if ($row["VARIABLE"] == "HANDY_TIMER")
		$HANDY_TIMER = $row["V_VALUE"];	
	if ($row["VARIABLE"] == "HANDY_MAC")
		$HANDY_MAC = $row["V_VALUE"];	
}

$prefix_dae_state = "OFF"; 
$dif = (new DateTime($DAEMON_STATUS))->diff((new DateTime())); 
$dif = get_seconds($dif);
if ($DAEMON_STATUS != "" &&  $dif >  600000) $prefix_dae_state = "ON";
$DAEMON_STATUS = $prefix_dae_state . "#OFF|ON";

?>