	var timer;
	
	function refresh_color(id) {
		document.getElementById(id).color.fromString(document.getElementById(id).value);
	}
	
	//transmit
	
	function	Transmit_Value(d_id, name, value, sync, mode) {	
		console.log("Transmit: " + d_id + "." + name + "=" + value);
		stop_timer();
		sync = typeof sync !== 'undefined' ? sync : true;
		mode = typeof mode !== 'undefined' ? "&mode=" + mode : "";
		var xmlhttp = new XMLHttpRequest();
		var req = "transmit.php?device=" + d_id + "&name=" + encodeURIComponent(name) + 
			"&value=" + encodeURIComponent(value) + mode;
		//req = encodeURIComponent(req);
		//console.log("Transmitting: " + req);
		xmlhttp.open("POST", req, false); //POST  sync
		xmlhttp.send();
		start_timer();
	}
	
	//transmit global variable
	function Transmit_Global(v_id, sync) {
		start_timer();
		console.log("transmit global: " + v_id)
		sync = typeof sync !== 'undefined' ? sync : false;
		var value = document.getElementById(v_id).value;
		var xmlhttp = new XMLHttpRequest();
		var req = "transmit.php?global=" + v_id + "&value=" + encodeURIComponent(value);
		//req = encodeURIComponent(req); //encodeURI
		xmlhttp.open("POST", req, sync);
		xmlhttp.send();
	}
	
	//change name
	function Transmit_name(d_id, new_name, name) {
		name = typeof name !== 'undefined' &&  name != 'undefined' ? "&name=" + encodeURIComponent(name) : "";		
		var xmlhttp = new XMLHttpRequest();
		var req = "transmit.php?device=" + d_id + "&newName=" + encodeURIComponent(new_name) + name;
		console.log(req);
		xmlhttp.open("POST", req, true);
		xmlhttp.send();
		start_timer();
	}
	
	function delete_dev(thi_id) {
		var answ = confirm("Wirklic löschen?");
		if (answ) {
			document.getElementById("total_" + thi_id).style.display = "none";
			var xmlhttp = new XMLHttpRequest();
			var req = "transmit.php?device=" + thi_id + "&name=delete";
			console.log(req);
			xmlhttp.open("POST", req, false);
			xmlhttp.send();
			start_timer();
		}
	}	
	
	//get datetime 
	function getDateTime() {
		var currentdate = new Date(); 
		var datetime = currentdate.getFullYear() + "-" +
									+ (currentdate.getMonth()+1) + "-" +
									+	currentdate.getDate() + " "
									+ currentdate.getHours() + ":"  
									+ currentdate.getMinutes() + ":" 
									+ currentdate.getSeconds();
		return datetime;
	}
	
	//load

	function Refresh_value(el_id, new_value) {
		var val_id = encodeURI(el_id).replace(/%20/g,'+');
		var el = document.getElementById(val_id);
		if (el == null) return;			
		if (el_id == "DAEMON_STATUS") { // HNADLE SERVER - check time
			var prefix = "OFF";
			if (new_value != "" && 
			(new Date(new_value)).getTime() > (new Date(getDateTime())).getTime() - 60000)  // adding one minute
					prefix = "ON";						
			new_value = prefix + "#OFF|ON";
		} 
		if (el.getAttribute("type")=="range") {
			new_value = (new_value + "").split("#")[0];
		}
		//console.log("Refresh Value: " + el_id + " Old value: " + el.value + " New value: " + new_value);		
		if (el.value != new_value) {
			console.log(el_id + "  old value:-" + el.value + "-  new_value: -" + new_value + "-");
			$("input[id='" + val_id + "']").effect("pulsate", [], 500);
			el.value = new_value;
			el.onchange();
		}		
	}

	function Refresh_device(device_id, status) {
		var stat = status == "OFF" ? "d_off" : "d_on";
		var el_id = "dev_" + device_id;
		//console.log("refresh device: " + el_id);
		var el = document.getElementById(el_id);
		if (el != null && el.className != stat && el.className != "d_unknown") {
			//console.log(el.className);
			el.className = stat;
			$("#" + el_id).effect("pulsate", [], 500);
		}		
	}

	var counter = 0;
	var xmlhttp = new XMLHttpRequest();
	var xmlhttpGlobal = new XMLHttpRequest();
	function got_response_global() {
		if (xmlhttpGlobal.readyState == 4 && xmlhttpGlobal.status == 200) {
			try {
				var response = xmlhttpGlobal.responseText;
				//console.log("global-response: " + response);
				respnonse = JSON.parse(response, true);
			}catch(e) { 
				console.log(e); 
				console.log("abort global");
				return;
				}
			for (var i in respnonse) {
				var val = respnonse[i];
				Refresh_value(i, val);
			}
			xmlhttpGlobal = new XMLHttpRequest();			
		}
	}
	function got_response() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			try {
				var response = xmlhttp.responseText;
				//console.log("val-response: " + response);
				var info = "";
				respnonse = JSON.parse(response, true);
			}catch(e) { 
				console.log(e);
				console.log("abort normal");
				return;
			}
			
			info += counter + "-Parted: <br>";
			for (var i in respnonse) {
				var dev = respnonse[i];
				for (var value_label in dev) {
					var dev_value = dev[value_label];
					info += value_label + ": ";
					if (value_label === "VALUES") {
						for(var value_set_label in dev_value) {
							var value_set = dev_value[value_set_label];
							info += "{ " + value_set_label + ": ";	
							for(var set_value_label in value_set) {	
								var set_value = value_set[set_value_label];
								info += set_value_label + "=" + set_value + " ";	
							}							
							Refresh_value(dev["DEVICE_ID"] + value_set["VALUE_NAME"], value_set["VALUE"]);
							info += "}";						
						}
					} else {
						info += dev_value;
					}					
					info += " ";
				}
				Refresh_device(dev["DEVICE_ID"], dev["STATUS"]);
				info += "<br><br>";
			}

			//document.getElementById("info").innerHTML = info;
			//console.log(info);
			counter++;
			xmlhttp = new XMLHttpRequest();
		}
	}
	function sendGetInfo() {
		xmlhttp.onreadystatechange = got_response;
		xmlhttp.open("POST", "getInfo.php?name=ALL", true);
		xmlhttp.send();
		xmlhttpGlobal.onreadystatechange = got_response_global;
		xmlhttpGlobal.open("POST", "getInfo.php?global=ALL", true);
		xmlhttpGlobal.send();		
	}	
	
	function stop_timer() {		
    clearInterval(timer);
	}
	
	function start_timer() {
    clearInterval(timer);
		var delay_refresh = document.getElementById("WEB_REFRESH").value;
		timer = setInterval(function () {sendGetInfo()}, parseInt(delay_refresh) * 1000);	
	}
	
	//slider functions
	function showValue(d_id, id_n, v_name, newValue)
	{
		document.getElementById("sliderN" + id_n).innerHTML=newValue.split("#")[0];		
		Transmit_Value(d_id, v_name, newValue);
	}
	function transValue(d_id, id_n, v_name, newValue)
	{
		document.getElementById("sliderN" + id_n).innerHTML=newValue.split("#")[0];
		Transmit_Value(d_id, v_name, newValue, false);
	}
	
	//global edit mode
	function onchange_edit_mode()
	{
		if (document.getElementById('edit_mode').checked) {
			$(".EDIT_ELEMENT").show();
		} else {
			$(".EDIT_ELEMENT").hide();			
		}	
	}

  $(function() {
		//rename device 
		$(".change_dn").click(function(event) { //changing text
			event.stopPropagation();
			var did = $(this).attr("dev_id");
			var el = $("#dev_" + did).find(".device_name");
			
			if ($(this).hasClass("save_dn")) {				
				var text = el.val();
				//save 
				Transmit_name(did, text);
				
				var input = $("<span class='device_name' dev_id='" + did + "' />").html(text);
				$(this).html("change");
				$("#dev_" + did).find(".change_dn_a").hide();	
			} else {
				var text = el.html();
				var input = $("<input oldval='" + text + "' class='device_name dn_input' dev_id='" + did + "' />").val(text);
				$(this).html("save");	
				$("#dev_" + did).find(".change_dn_a").show();		
			}
			
      el.replaceWith(input);			
			$(".dn_input").click(function(event) { //stop spoiler for input text
				event.stopPropagation();
			});
			$(this).toggleClass("save_dn");
		});
		$(".change_dn_a").click(function(event) { //abort text
			event.stopPropagation();
			var did = $(this).attr("dev_id");
			var el = $("#dev_" + did).find(".device_name");
			var text = el.attr("oldval");
			var input = $("<span class='device_name' dev_id='" + did + "' />").html(text);
			
			$("#dev_" + did).find(".change_dn").html("change");	
			$(this).hide();	
      el.replaceWith(input);
			$("#dev_" + did).find(".change_dn").toggleClass("save_dn");
		});
					
		//rename value name
		$(".change_vn").click(function(event) { //changing text
			var did = $(this).attr("dval_id");
			var el = $("#" + did.replace( /(:|\.|\[|\]|,|\-|\+)/g, "\\$1" ));
			
			if ($(this).hasClass("save_dn")) {	// change to normal mode and save	
				var text = el.val();
				//save 
				var dev_id = $(this).attr("d_id");
				Transmit_name(dev_id, text, el.attr('oldval'));
				
				var input = $("<span id='" + did + "' class='value_name_label' dev_id='" + did + "' />").html(text);
				$(this).html("change");
				$("#aBut" + did.replace( /(:|\.|\[|\]|,|\+)/g, "\\$1" )).hide();	
			} else { //change to edit mode
				var text = el.html();
				var input = $("<input id='" + did + "' oldval='" + text + 
					"' class='value_name_label vn_input' dev_id='" + did + "' />").val(text);
				$(this).html("save");	
				$("#aBut" + did.replace( /(:|\.|\[|\]|,|\+)/g, "\\$1" )).show();		
			}
			
      el.replaceWith(input);			
			$(".dn_input").click(function(event) { //stop spoiler for input text
				event.stopPropagation();
			});
			$(this).toggleClass("save_dn");
		});
		$(".change_vn_a").click(function(event) { //abort text
			event.stopPropagation();
			var did = $(this).attr("dval_id");
			var el = $("#" + did.replace( /(:|\.|\[|\]|,|\-|\+)/g, "\\$1" ));
			
			var text = el.attr("oldval");
			var input = $("<span class='value_name_label' dev_id='" + did + "' />").html(text);
			
			$(this).hide();	
      el.replaceWith(input);
			$("#CBut" + did.replace( /(:|\.|\[|\]|,|\+)/g, "\\$1" )).html("change");
			$("#CBut" + did.replace( /(:|\.|\[|\]|,|\+)/g, "\\$1" )).toggleClass("save_dn");
		});
					
		
		// options menu
		$("#optionsButton, #ok_button_set").on('click', function(){
			$("#set_screen").slideToggle();
		});		
		$( document ).ready(function() {
			sendGetInfo();
			start_timer();
		});		
		
		$(".d_name").click(function(){
			var num = $(this).attr( "spoil" );
			console.log("spoil: " + num);
			$(".spoil" + num).slideToggle();
		});
	});
	