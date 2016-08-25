<?php
	/*
	 * device(number): Specifies the device_id
	 * name: the name of the variable to set
	 * value: the value to set
	 * type: is used if its a new value-set 
	 * global: the global variable name
	 * Mode 1: device + name + value --> to set value
	 * Mode 2: device + name + type + value --> create value-set
	 * Mode 3: global + value --> set global variable
	 * Mode 4: device + name="POWER" + value=["ON", "?"] --> set device status
	 */
	ini_set('display_errors', 'On');
	include "constants.php";
	$commFile = "/home/python/server/commandTransfer";
	$show_output = true;
	$VALUE_SEPARATOR = "\x08";

	if (isset($_REQUEST["device"])) {
		$device = $_REQUEST["device"]; } else { $device = "";}
	if (isset($_REQUEST["name"])) {
		$name = $_REQUEST["name"]; } else { $name = "";}
	if (isset($_REQUEST["value"])) {
		$value = $_REQUEST["value"]; } else { $value = "";}
	if (isset($_REQUEST["type"])) {
		$type = $_REQUEST["type"]; } else { $type = "";}
	if (isset($_REQUEST["global"])) {
		$global = $_REQUEST["global"]; } else { $global = "";}
	if (isset($_REQUEST["mode"])) {
		$mode = $_REQUEST["mode"]; } else { $mode = "both";}		
	if (isset($_REQUEST["newName"])) {
		$newName = $_REQUEST["newName"]; } else { $newName = "";}
	
	
	if ($newName != "" && $device != "" && $name !== "") { // rename a davice variable
		$sql = "UPDATE Device_Value
			SET VALUE_NAME='$newName'
			WHERE DEVICE_ID=$device AND VALUE_NAME='$name'";
		$db->query($sql);
		echo ("SQL-Query: " . $sql);

	
	} else if ($newName != "" && $device != "") { // rename a davice	
		$sql = "UPDATE Device_Name
			SET DEVICE_NAME='$newName'
			WHERE DEVICE_ID=$device";
		$db->query($sql);		
		
	} else if ($device !== "" && $name == "delete") { //delete device
		$sql = "DELETE FROM `Device_Name` WHERE DEVICE_ID = '$device'";
		$db->query($sql);
		$sql = "DELETE FROM `Device_Value` WHERE DEVICE_ID = '$device'";
		$db->query($sql);
		
	
	} else if ($device !== "" && $name !== "" && $value !== "") { //set new device variable
		$type_string = "";
		if ($type !== "") $type_string = "($type)";
		if ($show_output) echo "$device. $name$type_string: $value <br>";
		
		
		if ($name == "POWER") {
			/*$on = false;
			if ($value == "ON") $on = true; */			
			file_put_contents ($commFile, "P" . $device . "\x05" . $value );
			
			return;
		}
		if ($type == "") {
			if ($mode == "both" || $mode == "db")
			$sql = "UPDATE Device_Value
				SET VALUE='$value'
				WHERE DEVICE_ID=$device AND VALUE_NAME='$name' ";
			$db->query($sql);
			if ($mode == "both" || $mode == "device") {
				// get value id from valuename
				$sql = "SELECT VALUE_ID, VALUE_TYPE FROM Device_Value WHERE DEVICE_ID=$device AND VALUE_NAME='$name'";
				$data = $db->query($sql);
				$array = $data->fetchAll(PDO::FETCH_ASSOC);
				$val_ID = $array[0]['VALUE_ID'];
				$val_type = $array[0]['VALUE_TYPE'];
				$val_ID = sprintf("%'.02d", $val_ID);
				
				$sql = "SELECT DEVICE_ADRESS FROM Device_Name WHERE DEVICE_ID=$device";
				$data = $db->query($sql);
				$array = $data->fetchAll(PDO::FETCH_ASSOC);
				$rec = $array[0]['DEVICE_ADRESS'];
				
				echo ("valid: $val_ID-  Val_type:$val_type   rec: $rec <br>");
				//TODO Transmit via python
				file_put_contents ($commFile, "N" . $rec . "\x05C" . $val_ID . $val_type .
					$VALUE_SEPARATOR . $value );
					/*
				$t_command = "sudo /home/python/server/daemonScript.sh transmit 'C" . $val_ID . $val_type .
					$VALUE_SEPARATOR . $value . "' " . $rec  . ' > /dev/null 2>&1 &';
				exec($t_command, $out, $ret);
				$out = implode("<br>",$out);
				if ($show_output) echo "Output: <br> $out <br> Return: $ret";
				echo ("command: $t_command <br>"); */
			}
		} else {
			$sql = "INSERT INTO Device_Value
				(DEVICE_ID, VALUE_NAME, VALUE, VALUE_TYPE)
				VALUES ('$device', '$name', '$value', '$type')
				ON DUPLICATE KEY UPDATE
				DEVICE_ID='$device', VALUE_NAME='$name', VALUE='$value', VALUE_TYPE='$type'";
			$db->query($sql);
		}		
		if ($show_output) echo "Anfrage: $sql <br>";	
		
	} else if ($global !== "" && $value !== "") { // set new global variable
		if ($global == "DAEMON_STATUS") { // handling the server
			if (explode("#", $value)[0] == "ON") {
				if ($show_output) echo "Start Daemon<br>";
				//exec('python /home/python/server/realrf24/__init__.py', $out, $ret);
				exec('sudo /home/python/server/daemonScript.sh quickstart > /dev/null 2>&1 &', $out, $ret);
				//exec('python /home/python/server/serverDaemon.py start > /dev/null 2>&1 &', $out, $ret);
				$out = implode("<br>",$out);
				if ($show_output) echo "Output: <br> $out <br> Return: $ret";
			} else {
				if ($show_output) echo "Stop Daemon<br>";
				
				exec('sudo /home/python/server/daemonScript.sh stop > /dev/null 2>&1 &', $out, $ret);
				$out = implode("<br>",$out);
				if ($show_output) echo "Output: <br> $out <br> Return: $ret";
			}			
		} else {
			$sql = "UPDATE Global_Settings
				SET V_VALUE='$value'
				WHERE VARIABLE='$global' ";
			$db->query($sql);	
		}
	}
?>