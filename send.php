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

	//TODO: REMOVE	
	$commFile = "/home/python/server/commandTransfer";
	
	if (isset($_REQUEST["global"]) && $_REQUEST["global"] !== "" // global variable
		&& isset($_REQUEST["value"]) && $_REQUEST["value"] !== "") {
		$stmt = $db->prepare("UPDATE Global_Settings
			SET V_VALUE=:value
			WHERE VARIABLE=:global");
		$stmt->bindValue(':value', $_REQUEST["value"]);
		$stmt->bindValue(':global', $_REQUEST["global"]);
		$stmt->execute();
		return;
	}
	
	if (isset($_REQUEST["deviceid"]) && $_REQUEST["deviceid"] !== ""  // rename a davice	
		&& isset($_REQUEST["name"]) && $_REQUEST["name"] !== "") {
		$stmt = $db->prepare("UPDATE Device_Name
			SET DEVICE_NAME=:name
			WHERE DEVICE_ID=:deviceid");
		$stmt->bindValue(':name', $_REQUEST["name"]);
		$stmt->bindValue(':deviceid', $_REQUEST["deviceid"]);
		$stmt->execute();
		return;
	}

	if (isset($_REQUEST["deviceid"]) && $_REQUEST["deviceid"] !== ""  // rename a davice variable
		&& isset($_REQUEST["valid"]) && $_REQUEST["valid"] !== ""
		&& isset($_REQUEST["name"]) && $_REQUEST["name"] !== "") {
		$stmt = $db->prepare("UPDATE Device_Value
			SET VALUE_NAME=:name
			WHERE DEVICE_ID=:deviceid AND VALUE_ID=:valid");
		$stmt->bindValue(':name', $_REQUEST["name"]);
		$stmt->bindValue(':deviceid', $_REQUEST["deviceid"]);
		$stmt->bindValue(':valid', $_REQUEST["valid"]);
		$stmt->execute();
		return;
	}
	
	if (isset($_REQUEST["deviceid"]) && $_REQUEST["deviceid"] !== ""  // delete device
		&& isset($_REQUEST["delete"])) {
		$stmt = $db->prepare("DELETE FROM `Device_Name` WHERE DEVICE_ID=:deviceid");
		$stmt->bindValue(':deviceid', $_REQUEST["deviceid"]);
		$stmt->execute();
		$stmt = $db->prepare("DELETE FROM `Device_Value` WHERE DEVICE_ID=:deviceid");
		$stmt->bindValue(':deviceid', $_REQUEST["deviceid"]);
		$stmt->execute();		
		return;
	}
	 
	if (isset($_REQUEST["deviceid"]) && $_REQUEST["deviceid"] !== ""  // power
		&& isset($_REQUEST["power"]) && $_REQUEST["power"] !== "") {
		$deviceid=$_REQUEST["deviceid"];
		$power=$_REQUEST["power"];
		if ($power < 0 || $power > 100) return;
		$stmt = $db->prepare("UPDATE  Device_Switch
			SET POWER_STATUS=:power
			WHERE DEVICE_ID=:deviceid");
		$stmt->bindValue(':deviceid', $deviceid);
		$stmt->bindValue(':power', $power);
		$stmt->execute();	
		
		//TODO: REMOVE
		
		$sql = "SELECT DIMMABLE FROM `Device_Switch` WHERE DEVICE_ID=$deviceid";
		$data = $db->query($sql);
		$array = $data->fetchAll(PDO::FETCH_COLUMN);
		$dimmable=$array[0];
		
		$value="0";
		if ($dimmable == 1) {
			if ($power == 0) {
				$value="OFFDIM";
			} else if ($power == 100) {
				$value="ONDIM";
			} else {
				$value=$power;
			}
		} else {
			if ($power == 0) {
				$value="OFF";
			} else if ($power == 100) {
				$value="ON";
			} else {
				$value=$power;
			}
		}
		file_put_contents ($commFile, "P" . $deviceid . "\x05" . $value );		
		return;
	}
	
	if (isset($_REQUEST["deviceid"]) && $_REQUEST["deviceid"] !== ""  // set dimmable
		&& isset($_REQUEST["dimmable"]) && $_REQUEST["dimmable"] !== "") {
		$dimmable=$_REQUEST["dimmable"];
		if ($dimmable != 0 && $dimmable != 1) return;		
		$stmt = $db->prepare("UPDATE  Device_Switch
			SET DIMMABLE=:dimmable
			WHERE DEVICE_ID=:deviceid");
		$stmt->bindValue(':deviceid', $_REQUEST["deviceid"]);
		$stmt->bindValue(':dimmable', $dimmable);
		$stmt->execute();	
		return;
	}
	
	if (isset($_REQUEST["deviceid"]) && $_REQUEST["deviceid"] !== ""  // set switcher
		&& isset($_REQUEST["switcher"]) && $_REQUEST["switcher"] !== "") {
		$switcher=$_REQUEST["switcher"];
		
		$stmt = $db->prepare("SELECT `V_VALUE` FROM `Global_Settings` WHERE `VARIABLE`='MAX_SWITCHES'");
		$stmt->execute();	
		$array = $stmt->fetchAll(PDO::FETCH_COLUMN);
		$max_switches=$array[0];
		
		if ($switcher > $max_switches) return;
		$stmt = $db->prepare("UPDATE  Device_Switch
			SET SWITCHER_ID=:switcher
			WHERE DEVICE_ID=:deviceid");
		$stmt->bindValue(':deviceid', $_REQUEST["deviceid"]);
		$stmt->bindValue(':switcher', $switcher);
		$stmt->execute();		
		return;
	}	
	
	if (isset($_REQUEST["deviceid"]) && $_REQUEST["deviceid"] !== ""  // set value
		&& isset($_REQUEST["valid"]) && $_REQUEST["valid"] !== ""
		&& isset($_REQUEST["value"]) && $_REQUEST["value"] !== "") {
		$deviceid=$_REQUEST["deviceid"];
		$valid=$_REQUEST["valid"];
		$stmt = $db->prepare("SELECT `POS_VALUE`, `VALUE_TYPE` FROM `Device_Value` WHERE `DEVICE_ID`=:deviceid AND `VALUE_ID`=:valid");
		$stmt->bindValue(':deviceid', $deviceid);
		$stmt->bindValue(':valid', $valid);
		$stmt->execute();	
		$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$posvalues=$array[0]["POS_VALUE"];
		$valtype=$array[0]["VALUE_TYPE"];
		
		//check input
		$value=$_REQUEST["value"];
		$posvalues=json_decode($posvalues);
		if ($valtype == "COLLECTION") {
			if (!in_array( $value , $posvalues)) return;
		} else if ($valtype == "SLIDER") {
			if ($posvalues[0] > $value || $posvalues[1] < $value) return;
		} else if ($valtype == "COLOR") {
			    $value = ltrim($value, '#');
					if (!ctype_xdigit($value) || strlen($value) != 6 )return;
		}
		
		if (isset($_REQUEST["db"])) {
			$stmt = $db->prepare("UPDATE  Device_Value
				SET VALUE=:value
				WHERE DEVICE_ID=:deviceid AND `VALUE_ID`=:valid");
			$stmt->bindValue(':deviceid', $deviceid);
			$stmt->bindValue(':valid', $valid);
			$stmt->bindValue(':value', $value);
			$stmt->execute();
		}
		
		if (isset($_REQUEST["client"])) {			
			$stmt = $db->prepare("SELECT `DEVICE_ADRESS` FROM `Device_Name` WHERE `DEVICE_ID`=:deviceid");
			$stmt->bindValue(':deviceid', $deviceid);
			$stmt->execute();	
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$deviceadress=$array[0]["DEVICE_ADRESS"];
			echo ($deviceadress);
			
			if (filter_var($deviceadress, FILTER_FLAG_IPV4)) {
				$r = file_get_contents('http://'.$deviceadress.'/'.$valid.'='.$value);
			} else if (strlen($deviceadress) == 5) { //TODO remove!!!!!
				echo ("old adress");
				$VALUE_SEPARATOR = "\x08";
				$sql = "SELECT VALUE_TYPE FROM Device_Value WHERE DEVICE_ID=$deviceid AND VALUE_ID='$valid'";
				$data = $db->query($sql);
				$array = $data->fetchAll(PDO::FETCH_ASSOC);
				$val_type = $array[0]['VALUE_TYPE'];
				
				if ($valtype == "COLLECTION") {					
					$value=$value.'#';
					$first=true;
					foreach ($posvalues as $tmp_val) {
						if (!$first) $value=$value.'|';
						$value=$value.$tmp_val;
						$first=false;
					} 
				} else if ($valtype == "SLIDER") {
					$value=$value.'#'.$posvalues[0].'|'.$posvalues[1].'|100';
				}
				echo("N" . $deviceadress . "\x05C" . $valid . $val_type .
					$VALUE_SEPARATOR . $value);
				file_put_contents ($commFile, "N" . $deviceadress . "\x05C" . $valid . $val_type .
					$VALUE_SEPARATOR . $value);
					
				
			} else {
				echo ("no adress");
			}
		}		
		return;
	}
	
?>