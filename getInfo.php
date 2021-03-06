<?php
/*
 * Mode 1: 
 *  device(number): The device to get the information
 *  name: The name of the variable
 * 		if no name is specified JUST the last logon and logoff are shown
 * 		if name is "ALL" get all names of this device
 *			if no device is specified, get all devices
 * Mode 2:
 *  global: the global value to return
 * 		if global = "ALL" all global variables are returned
 */

	include "constants.php";

	if (isset($_REQUEST["device"])) {
		$device = $_REQUEST["device"]; } else { $device = "";}
	if (isset($_REQUEST["name"])) {
		$name = $_REQUEST["name"]; } else { $name = "";}
	if (isset($_REQUEST["global"])) {
		$global = $_REQUEST["global"]; } else { $global = "";}
	
	if ($global !== "") {
		if ($global == "ALL") {
			$where_stat = "";
		} else {
			$where_stat = "WHERE VARIABLE='" . $global . "'";
		}
		
		$sql = "SELECT * FROM `Global_Settings` " . $where_stat;
		$data = $db->query($sql);
		$array = $data->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
		echo json_encode($array);
		
	} else { //not global
		
		$where_dev = "";
		if ($device !== "") {
			$where_dev = "WHERE DEVICE_ID=$device";
		}
		$sql = "SELECT DEVICE_ID, DEVICE_NAME, DEVICE_LAST_ON, DEVICE_LAST_OF,
			IF( DEVICE_LAST_ON < DEVICE_LAST_OF OR 
				DEVICE_LAST_ON < DATE_SUB(NOW(), INTERVAL $TIME_DIF_FOR_OFFLINE MINUTE) ,'OFF','ON') AS STATUS 
			FROM `Device_Name` $where_dev";
		//echo ($sql + "<br>");
		
		$data = $db->query($sql);	
		if ($name !== "") {
			$array = array();
			foreach ($data->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$d_id = $row['DEVICE_ID'];
				if ($name != "ALL") {
					$where_vals = "AND VALUE_NAME='$name'";			
				} else {
					$where_vals = "";
				}
				
				$sql = "SELECT * FROM `Device_Value` WHERE DEVICE_ID='$d_id' $where_vals";
				$add_data = $db->query($sql);
				if ($add_data != false) {
					$tmp_val = $add_data->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
					if (sizeof($tmp_val) > 0) {
						$row['VALUES'] = $tmp_val[$d_id];
					} else {
						$row['VALUES'] = "";
					}
				}
				$array[] = $row;			
			}
		} else {		
			$array = $data->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
		}
		echo json_encode($array);
	}
?>