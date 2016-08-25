<?php
ini_set('display_errors', 'On');
include "constants.php";
include "colorPicker/picker.php";
?>
<html>
<head>
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="/img/colormap.gif">
	<link rel="stylesheet" href="style.css" type="text/css" />
	<!--<script type="text/javascript" src="jscolor/jscolor.js"></script>-->
	<script type="text/javascript" src="jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="script.js"></script>
	
	<?php
		getCpickerScript();
	?>

	<title>Control Page</title>
</head>
<body>
<div class="korpus">

<span id="optionsButton" class="c_button">&clubs;</span>
<h1>Control Page</h1>

<span id="set_screen">
	<h2>Einstellungen</h2>
	<div class="set_cont">
		<div class="set_elem">
		Zeit bis offline (in Min.): <br>
		 <input type="range" id="TIME_TO_OFF" class="slider" min="1" max="999" step="1" value="<?php echo $TIME_DIF_FOR_OFFLINE;?>"
		 oninput="start_timer(); document.getElementById('sliderNTIME_TO_OFF').innerHTML=this.value;" 
		 onchange="Transmit_Global('TIME_TO_OFF'); document.getElementById('sliderNTIME_TO_OFF').innerHTML=this.value;"/>
		<span class="slider_number" id="sliderNTIME_TO_OFF"><?php echo $TIME_DIF_FOR_OFFLINE;?></span>
		</div>
		<div class="set_elem">
		Daemon Status: <select id="DAEMON_STATUS" oninput="Transmit_Global('DAEMON_STATUS');" onchange="Transmit_Global('DAEMON_STATUS');">
		<?php
			$parts = explode("#", $DAEMON_STATUS);
				$selected = $parts[0];
				$list = explode("|", $parts[1]);
				foreach ($list as $entry) {
					$sel = "";
					if ($selected == $entry) $sel = " selected=\"selected\"";				
					echo "<option value=\"$entry#$parts[1]\"$sel>$entry</option>";				
				}
		?>
		</select></div>
		<div class="set_elem">
			Aktualisierungsinterval WebIf (in Sek.):<br>
				<input type="range" id="WEB_REFRESH" class="slider" min="2" max="180" step="1" value="<?php echo $WEB_REFRESH;?>"
			 oninput="start_timer(); document.getElementById('sliderNWEB_REFRESH').innerHTML=this.value;" 
			 onchange="Transmit_Global('WEB_REFRESH'); document.getElementById('sliderNWEB_REFRESH').innerHTML=this.value;"/>
			<span class="slider_number" id="sliderNWEB_REFRESH"><?php echo $WEB_REFRESH;?></span>
		</div>
		<div class="set_elem">
			Editiermodus:
			<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox checkbox" id="edit_mode" onchange="onchange_edit_mode();">			
		</div>
		<div class="set_elem">
			Funk-ID 1:
			<input type="text" class="eckbox" id="FUNK_ID_1" value="<?php echo $FUNK_ID_1;?>" onchange="Transmit_Global('FUNK_ID_1');">	
		</div>
		<div class="set_elem">
			Funk-ID 2:
			<input type="text" class="eckbox" id="FUNK_ID_2" value="<?php echo $FUNK_ID_2;?>" onchange="Transmit_Global('FUNK_ID_2');">		
		</div>
		<div class="set_elem">
			Handy Mac:
			<input type="text" class="eckbox" id="HANDY_MAC" value="<?php echo $HANDY_MAC;?>" onchange="Transmit_Global('HANDY_MAC');">		
		</div>
		<div class="set_elem">
			Handy Abfrage (in Min.):
			<input type="range" id="HANDY_TIMER" class="slider" min="0" max="60" step="1" value="<?php echo $HANDY_TIMER;?>"
				oninput="start_timer(); document.getElementById('sliderHANDY_TIMER').innerHTML=this.value;" 
				onchange="Transmit_Global('HANDY_TIMER'); document.getElementById('sliderHANDY_TIMER').innerHTML=this.value;"/>
			<span class="slider_number" id="sliderHANDY_TIMER"><?php echo $HANDY_TIMER;?></span>
		</div>
	</div>

  <button type="button" id="ok_button_set">Ok</button>
</span>
<span id="info"> </span>

<?php
$sql = "SELECT dn.DEVICE_ID, dn.DEVICE_NAME, IFNULL(dn.DEVICE_ADRESS,'') AS DEVICE_ADRESS, 
	IF( DEVICE_LAST_ON < DEVICE_LAST_OF OR 
		DEVICE_LAST_ON < DATE_SUB(NOW(), INTERVAL $TIME_DIF_FOR_OFFLINE MINUTE) ,'OFF','ON') AS STATUS
	FROM `Device_Name` AS dn ";
foreach ($db->query($sql) as $row) {
	$d_id = $row['DEVICE_ID'];
	$device_adress = $row['DEVICE_ADRESS'];
	$status = $row['STATUS'] == "OFF" ? "d_off" : "d_on";
	$status = $device_adress == "" ? "d_unknown" : $status;
	$click_string = $device_adress == "" ? "" : "clickable_container";
		
	$withspoiler = true;
	//do the next query here for need of spoiler
	$sql = "SELECT * FROM `Device_Value` WHERE DEVICE_ID='$d_id' ORDER BY VALUE_ID";
	$data = $db->query($sql);
	if (count($data->fetchAll(PDO::FETCH_ASSOC)) == 0) $withspoiler = false;	
	
	// is dimmable
	$sql_dim = "SELECT sd.DIMMABLE AS DIMMABLE 
		FROM `Switch_Dimmable` AS sd, Device_Switch as ds 
		WHERE sd.SWITCHER_ID=ds.SWITCHER_ID AND ds.DEVICE_ID=$d_id";
	$data_dim = $db->query($sql_dim);
	$dimmable = False;
	if (count($data_dim->fetchAll(PDO::FETCH_ASSOC)) > 0) $dimmable = True;
	$dim_string = "";
	if ($dimmable) $dim_string = "DIM";
	
	echo "<span id='total_$d_id' class=\"dev_container device_container_$d_id $click_string\">";
	echo "<span class='del_button EDIT_ELEMENT' onclick='delete_dev(\"$d_id\");'>x</span>";
	echo "<h2 id=\"dev_$d_id\" class=\"$status\">";
	echo "<span class='button_container'>";
	echo "<div><span class=\"off_button power_button c_button\" onclick=\"Transmit_Value($d_id, 'POWER', 'OFF$dim_string');\"></span>";
	echo "<span class=\"on_button power_button c_button\" onclick=\"Transmit_Value($d_id, 'POWER', 'ON$dim_string');\"></span></div>";
	if ($dimmable) {	
		echo "<div><span class=\"dim_button power_button c_button\" onclick=\"Transmit_Value($d_id, 'POWER', 'DIMOFF');\">-</span>";	
		echo "<span class=\"dim_button power_button c_button\" onclick=\"Transmit_Value($d_id, 'POWER', 'DIMON');\">+</span></div>";
	}
	echo "</span>";
	
	echo "<span class=\"d_name\" spoil=\"" . $d_id . "\">" . $d_id . ". ";
	echo "<span class='device_name' dev_id='$d_id'>" . $row['DEVICE_NAME'] . 
		"</span><span class='change_dn EDIT_ELEMENT' dev_id='$d_id'>change</span><span class='change_dn_a' dev_id='$d_id'>x</span></span>";
	echo "</h2>";
	if (!$withspoiler) {
		echo "</span>";		
		continue;
	}
	echo "<div class=\"spoiler spoil" . $d_id . "\">";

	foreach ($db->query($sql) as $row) {
		$type = $row['VALUE_TYPE'];
		$value = $row['VALUE'];
		$value_id = $row['VALUE_ID'];
		$value_name = $row['VALUE_NAME'];
		$identify_val = $d_id . "+" . $value_id;
		echo "<h3><span id='" . $identify_val . "' class='value_name_label'>" . $value_name . 
			"</span><span id='CBut" . $identify_val . "' class='change_vn EDIT_ELEMENT' dval_id='$identify_val' d_id='" . $d_id . "' this_val_name_id='" . $value_id . 
			"'>change</span>
			<span id='aBut" . $identify_val . "'  class='change_vn_a' dval_id='$identify_val'>x</span>:</h3>";
		$id_name = urlencode($d_id . $value_name);
		switch ($type) {
    
		case "COLLECTION":
		  ?>					
			<select id="<?php echo $id_name;?>" 
				onchange="Transmit_Value(<?php echo $d_id;?>, <?php echo "'$value_name'";?>, 
					this.value);" >	//Slide#Slide|Fix|More		 document.getElementById('<?php echo $id_name;?>')
			<?php
			$parts = explode("#", $value);
			$selected = $parts[0];
			$list = explode("|", $parts[1]);
			foreach ($list as $entry) {
				$sel = "";
				if ($selected == $entry) $sel = " selected=\"selected\"";				
				echo "<option value=\"$entry#$parts[1]\"$sel>$entry</option>";				
			}			
		  ?>
			</select>			
  
			<?php
			break;		
		
    case "COLOR":
		  ?>
		<!--	<input value="<?php echo $value;?>" id="<?php echo $id_name;?>" onchange="refresh_color('<?php echo $id_name;?>')"
				class="color {onImmediateChange:'Transmit_Value(<?php echo $d_id;?>, <?php echo "\'$value_name\'";?>,this.toString().toUpperCase());'}">
			-->
			<?php
			getCpicker($d_id, $value_name, $id_name, $value); 
      break;		
    case "SLIDER": //value: init-val#min#max#step
			$parts = explode("#", $value);
			$value = $parts[0];
			$list = explode("|", $parts[1]);
			$min_val = $list[0];
			$max_val = $list[1];
			$setp_size = $list[2]; //($max_val - $min_val) / 100;
		  ?>			
				<input type="range" id="<?php echo $id_name;?>" class="slider" min="<?php echo $min_val;?>" 
					max="<?php echo $max_val;?>" step="<?php echo $setp_size;?>" value="<?php echo $value;?>"
					 oninput="showValue('<?php echo $d_id;?>', '<?php echo $id_name;?>', '<?php echo $value_name;?>', 
						this.value + '<?php echo "#" . $min_val . "|" . $max_val . "|" . $setp_size;?>')" 
					 onchange="transValue('<?php echo $d_id;?>', '<?php echo $id_name;?>', '<?php echo $value_name;?>', this.value + '<?php
							echo "#" . $min_val . "|" . $max_val . "|" . $setp_size;?>')"/>
				<span class="slider_number" id="sliderN<?php echo $id_name;?>"><?php echo $value;?></span>
				<!--transValue-->
				<?php 
        break;
    default:
		?>
				<input type="text" id="<?php echo $id_name;?>" value="<?php echo $value;?>" onchange="setValue(this.value)"/>
				<script type="text/javascript">
				function setValue(newValue)
				{
					Transmit_Value(<?php echo $d_id;?>, <?php echo "'$value_name'";?>, newValue);
				}
				</script>
				<?php
		}	
	}
	echo "</div></span>";
}

?>
</div>
</body></html>
