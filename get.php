<?php
/*
 * global Mode:
 *  blank: all global Variables
 * 	with name if global variable: only this variable
 * device Mode:
 *	blank: list all devices
 * 	with device id: list only this device
 * vals Mode:
 *	with device id: get all values for this device
 */

	include "constants.php";
	
	
	
	if (isset($_REQUEST["global"])) {
		$where_stat = "";
		if ($_REQUEST["global"] !== "") {
			$where_stat = "WHERE VARIABLE=:variab";
		}
		$stmt = $db->prepare("SELECT * FROM `Global_Settings` $where_stat");
		$stmt->bindValue(':variab', $_REQUEST["global"]);
		$stmt->execute();	
		$array = $stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
		echo json_encode($array);
		return;
	}
	
	if (isset($_REQUEST["device"])) {
		$where_stat = "";
		if ($_REQUEST["device"] !== "") {
			$where_stat = "WHERE dn.DEVICE_ID=:device";
		}		
		$stmt = $db->prepare("SELECT dn.DEVICE_ID, DEVICE_NAME, ds.SWITCHER_ID, DIMMABLE, POWER_STATUS, DEVICE_LAST_ON, DEVICE_LAST_OF, IF( DEVICE_LAST_ON < DEVICE_LAST_OF OR 
				DEVICE_LAST_ON < DATE_SUB(NOW(), INTERVAL :TIME_DIF_FOR_OFFLINE MINUTE) ,'OFF','ON') AS STATUS 
				FROM `Device_Name` AS dn 
				LEFT JOIN `Device_Switch` AS ds ON ds.DEVICE_ID=dn.DEVICE_ID
				$where_stat");
		$stmt->bindValue(':device', $_REQUEST["device"]);
		$stmt->bindValue(':TIME_DIF_FOR_OFFLINE', $TIME_DIF_FOR_OFFLINE);
		$stmt->execute();
		$array = $stmt->fetchAll(PDO::FETCH_ASSOC);			
		echo json_encode($array);
		return;
	}
	
	if (isset($_REQUEST["vals"]) && $_REQUEST["vals"] !== "") {
		$stmt = $db->prepare("SELECT * FROM `Device_Value` WHERE DEVICE_ID=:d_id");
		$stmt->bindValue(':d_id', $_REQUEST["vals"]);
		$stmt->execute();		
		$tmp_val = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
		echo json_encode($tmp_val);
		return;
	}
	
?>