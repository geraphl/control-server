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
			$where_stat = "WHERE VARIABLE='" . $_REQUEST["global"] . "'";
		}		
		$sql = "SELECT * FROM `Global_Settings` " . $where_stat;
		$data = $db->query($sql);
		$array = $data->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
		echo json_encode($array);
		return;
	}
	
	if (isset($_REQUEST["device"])) {
		$where_stat = "";
		if ($_REQUEST["device"] !== "") {
			$where_stat = "WHERE dn.DEVICE_ID='" . $_REQUEST["device"] . "'";
		}	
		$sql = "SELECT dn.DEVICE_ID, DEVICE_NAME, ds.SWITCHER_ID, DIMMABLE, DEVICE_LAST_ON, DEVICE_LAST_OF, IF( DEVICE_LAST_ON < DEVICE_LAST_OF OR 
				DEVICE_LAST_ON < DATE_SUB(NOW(), INTERVAL $TIME_DIF_FOR_OFFLINE MINUTE) ,'OFF','ON') AS STATUS 
				FROM `Device_Name` AS dn 
				LEFT JOIN `Device_Switch` AS ds ON ds.DEVICE_ID=dn.DEVICE_ID
				LEFT JOIN `Switch_Dimmable` AS sd ON ds.SWITCHER_ID=sd.SWITCHER_ID
				$where_stat";
		$data = $db->query($sql);
		$array = $data->fetchAll(PDO::FETCH_ASSOC);			
		echo json_encode($array);
		return;
	}
	
	if (isset($_REQUEST["vals"]) && $_REQUEST["vals"] !== "") {
		$d_id = $_REQUEST["vals"];		
		$sql = "SELECT * FROM `Device_Value` WHERE DEVICE_ID='$d_id'";
		$data = $db->query($sql);
		$tmp_val = $data->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
		echo json_encode($tmp_val);
		return;
	}
	
?>