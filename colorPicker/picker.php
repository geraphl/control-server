

<?php 	
function getCpickerScript() {
?>

<script>

var mouse_over = false;
function clickColor(this_id, hex, seltop, selleft, html5) {
	document.getElementById(this_id).value = hex;
	document.getElementById(this_id).onchange();
	/*
	selleft = selleft - 108;
	
	document.getElementById("selectedhexagon" + this_id).style.top=seltop + "px";
	document.getElementById("selectedhexagon" + this_id).style.left=selleft + "px";
	document.getElementById("selectedhexagon" + this_id).style.visibility="visible";
	*/
}
function mouseOutMap(this_id) {	
	//console.log("mouse out");
	document.getElementById(this_id).style.backgroundColor = document.getElementById(this_id).value;
	mouse_over = false;
	document.body.style.cursor = "";
	start_timer();
	setTimeout(function() {
		if (!mouse_over) {
			document.getElementById(this_id).onchange();
		}
		},100);
	
	/*
	var value = document.getElementById(this_id).value;
	var d_id = document.getElementById(this_id).getAttribute("d_id");
	var name = document.getElementById(this_id).getAttribute("value_name"); */
}

function mouseOverColor(this_id, hex) {
	mouse_over = true;
	stop_timer();
	if (document.getElementById(this_id).style.backgroundColor == hex) return;
	document.getElementById(this_id).style.backgroundColor = hex;
	document.body.style.cursor = "pointer";
	
	var d_id = document.getElementById(this_id).getAttribute("d_id");
	var name = document.getElementById(this_id).getAttribute("value_name");
	Transmit_Value(d_id, name, hex, true, "device");
}

	
function rgb2hex(rgb) {
	rgb = rgb.match(/\d+/g);
	var red = rgb[0];
	var green = rgb[1];
	var blue = rgb[2];
	var rgb = blue | (green << 8) | (red << 16);
	return  (0x1000000 + rgb).toString(16).slice(1).toUpperCase();
}

function resize_to_large(yes) {
	
}
	
$( document ).ready(function() {
	// $(".colormap_img").each(function() {
		// width_large = (this.clientWidth > 300);
		// console.log("width_large: "+ width_large +" size: " + this.clientWidth);
	// });
	
	$("area").each(function() { //backup
		$(this).attr( "coords", function( i, val ) {
			this.setAttribute("coBak", val);
			})
		});
	resizedw();
	var width_large = $(window).width() > 1200;  


	function resizedw(){
		width_large = $(window).width() > 1200; 
		// $(".colormap_img").each(function() {
			// width_large = (this.clientWidth > 300);
		// });
		$("area").each(function() {
				$(this).attr( "coords", function( i, val ) {
					val = this.getAttribute("coBak");
					val = val.split(",");
					if (!width_large) {
						for (index = 0; index < val.length; ++index) {
								val[index] = val[index] * 2;
						}
					}
					return val.join(", ");
				});
		});
	}

	var doit;
	window.onresize = function(){
		clearTimeout(doit);
		doit = setTimeout(resizedw, 200);
	};
	
	 $("area").each(function() {
			// $(this).attr( "coords", function( i, val ) {
				// if (width_large) {
					// val = val.split(",");
					// for (index = 0; index < val.length; ++index) {
							// val[index] = val[index] * 2;
					// }
					// return val.join(", ");
				// }
			// })
		
		$(this).bind({ 'touchend' : function(event){ 		
			var touch = event.originalEvent.changedTouches[0]; 
			var el = $(document.elementFromPoint(touch.clientX, touch.clientY));
			var found_element = false;
			el.filter('area').each(function()  {
				$(this).trigger( "click" ); 
				found_element = true;
			});
			
			if (!found_element) { //out of bounds
				var p_id = $(this).parent().attr("picker_id");
				var rgbValue = document.getElementById(p_id).style.backgroundColor;
				var color = rgb2hex(rgbValue);
				document.getElementById(p_id).value = color;
				document.getElementById(p_id).onchange();
			}
			event.preventDefault();
			start_timer();
		} });
		var current_mouseover_obj;
		$(this).bind({ 'touchmove' : function(event){
			event.preventDefault();
			var touch = event.originalEvent.touches[0];  
			var el = $(document.elementFromPoint(touch.clientX, touch.clientY));
			
		var current_mouseover = true;
			el.filter('area').each(function()  {
				if (current_mouseover && current_mouseover_obj != $(this).attr("alt")) {
					$(this).trigger( "onmouseover" );
					console.log("touch on: " + $(this).attr("alt"));
					current_mouseover_obj = $(this).attr("alt");
				}
				current_mouseover = false;
				start_timer();
			});
		} });
	});
/*<area style="cursor:pointer" shape="poly" coords="63,0,72,4,72,15,63,19,54,15,54,4" 
onclick="clickColor('<?php echo $picker_id; ?>', &quot;003366&quot;,-199,54)" 
onmouseover="mouseOverColor('<?php echo $picker_id; ?>', &quot;003366&quot;)" alt="#003366"><ar
	*/
});

function onChangeColorPicker(d_id, name, idComp, value) {
	if (!/^[0-9A-F]{6}$/i.test(value)) {
		document.getElementById(idComp).style.border = "thick solid #FF0000";
		document.getElementById(idComp).style.backgroundColor = "#000";
		document.getElementById("selectedhexagon" + idComp).style.visibility="hidden";
		return;
	}
	document.getElementById(idComp).style.border = "";
	
	document.getElementById(idComp).style.backgroundColor = "#" + value;
	
	var this_colormap = $("#colormap" + idComp.replace( /(:|\.|\[|\]|,|\+)/g, "\\$1" )); 
		var area_element = this_colormap.find("area[alt=#" + value + "]");
		console.log("area element: " + typeof(area_element) + " length: " + area_element.length);
		var number_list =  "";
		area_element.attr( "coords", function( i, val ) {
			number_list = val;
		});
		if (number_list != "") {
			number_list = number_list.split(",");
			var xPos = parseInt(number_list[6]);
			var yPos = parseInt(number_list[1]);

			var image_map = document.getElementById("colorIMG" + idComp);
			xPos -= (image_map.clientWidth / 2);
			yPos -= image_map.clientHeight;

			document.getElementById("selectedhexagon" + idComp).style.top=yPos + "px";
			document.getElementById("selectedhexagon" + idComp).style.left=xPos + "px";
			document.getElementById("selectedhexagon" + idComp).style.visibility="visible";
		} else {
			console.log("hide");
			document.getElementById("selectedhexagon" + idComp).style.visibility="hidden";
		}
	Transmit_Value(d_id, name, value, false);
}
</script>

<?php
}
?>	

<?php 
function getCpicker($d_id, $value_name, $picker_id, $value) {
?>	

<span class="color_picker_block">
<img src="/img/colormap.gif" usemap="#colormap<?php echo $picker_id; ?>" id="colorIMG<?php echo $picker_id; ?>" class="colormap_img"><map id="colormap<?php echo $picker_id; ?>" picker_id="<?php echo $picker_id; ?>" class="color_map_cont" name="colormap<?php echo $picker_id; ?>" onmouseout="mouseOutMap('<?php echo $picker_id; ?>')"><area style="cursor:pointer" shape="poly" coords="63,0,72,4,72,15,63,19,54,15,54,4" onclick="clickColor('<?php echo $picker_id; ?>', &quot;003366&quot;,-199,54)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>', &quot;003366&quot;)" alt="#003366"><area style="cursor:pointer" shape="poly" coords="81,0,90,4,90,15,81,19,72,15,72,4" onclick="clickColor('<?php echo $picker_id; ?>', &quot;336699&quot;,-199,72)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;336699&quot;)" alt="#336699"><area style="cursor:pointer" shape="poly" coords="99,0,108,4,108,15,99,19,90,15,90,4" onclick="clickColor('<?php echo $picker_id; ?>',&quot;3366CC&quot;,-199,90)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;3366CC&quot;)" alt="#3366CC"><area style="cursor:pointer" shape="poly" coords="117,0,126,4,126,15,117,19,108,15,108,4" onclick="clickColor('<?php echo $picker_id; ?>',&quot;003399&quot;,-199,108)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;003399&quot;)" alt="#003399"><area style="cursor:pointer" shape="poly" coords="135,0,144,4,144,15,135,19,126,15,126,4" onclick="clickColor('<?php echo $picker_id; ?>',&quot;000099&quot;,-199,126)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;000099&quot;)" alt="#000099"><area style="cursor:pointer" shape="poly" coords="153,0,162,4,162,15,153,19,144,15,144,4" onclick="clickColor('<?php echo $picker_id; ?>',&quot;0000CC&quot;,-199,144)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;0000CC&quot;)" alt="#0000CC"><area style="cursor:pointer" shape="poly" coords="171,0,180,4,180,15,171,19,162,15,162,4" onclick="clickColor('<?php echo $picker_id; ?>',&quot;000066&quot;,-199,162)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;000066&quot;)" alt="#000066"><area style="cursor:pointer" shape="poly" coords="54,15,63,19,63,30,54,34,45,30,45,19" onclick="clickColor('<?php echo $picker_id; ?>',&quot;006666&quot;,-184,45)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;006666&quot;)" alt="#006666"><area style="cursor:pointer" shape="poly" coords="72,15,81,19,81,30,72,34,63,30,63,19" onclick="clickColor('<?php echo $picker_id; ?>',&quot;006699&quot;,-184,63)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;006699&quot;)" alt="#006699"><area style="cursor:pointer" shape="poly" coords="90,15,99,19,99,30,90,34,81,30,81,19" onclick="clickColor('<?php echo $picker_id; ?>',&quot;0099CC&quot;,-184,81)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;0099CC&quot;)" alt="#0099CC"><area style="cursor:pointer" shape="poly" coords="108,15,117,19,117,30,108,34,99,30,99,19" onclick="clickColor('<?php echo $picker_id; ?>',&quot;0066CC&quot;,-184,99)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;0066CC&quot;)" alt="#0066CC"><area style="cursor:pointer" shape="poly" coords="126,15,135,19,135,30,126,34,117,30,117,19" onclick="clickColor('<?php echo $picker_id; ?>',&quot;0033CC&quot;,-184,117)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;0033CC&quot;)" alt="#0033CC"><area style="cursor:pointer" shape="poly" coords="144,15,153,19,153,30,144,34,135,30,135,19" onclick="clickColor('<?php echo $picker_id; ?>',&quot;0000FF&quot;,-184,135)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;0000FF&quot;)" alt="#0000FF"><area style="cursor:pointer" shape="poly" coords="162,15,171,19,171,30,162,34,153,30,153,19" onclick="clickColor('<?php echo $picker_id; ?>',&quot;3333FF&quot;,-184,153)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;3333FF&quot;)" alt="#3333FF"><area style="cursor:pointer" shape="poly" coords="180,15,189,19,189,30,180,34,171,30,171,19" onclick="clickColor('<?php echo $picker_id; ?>',&quot;333399&quot;,-184,171)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;333399&quot;)" alt="#333399"><area style="cursor:pointer" shape="poly" coords="45,30,54,34,54,45,45,49,36,45,36,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;669999&quot;,-169,36)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;669999&quot;)" alt="#669999"><area style="cursor:pointer" shape="poly" coords="63,30,72,34,72,45,63,49,54,45,54,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;009999&quot;,-169,54)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;009999&quot;)" alt="#009999"><area style="cursor:pointer" shape="poly" coords="81,30,90,34,90,45,81,49,72,45,72,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;33CCCC&quot;,-169,72)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;33CCCC&quot;)" alt="#33CCCC"><area style="cursor:pointer" shape="poly" coords="99,30,108,34,108,45,99,49,90,45,90,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;00CCFF&quot;,-169,90)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;00CCFF&quot;)" alt="#00CCFF"><area style="cursor:pointer" shape="poly" coords="117,30,126,34,126,45,117,49,108,45,108,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;0099FF&quot;,-169,108)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;0099FF&quot;)" alt="#0099FF"><area style="cursor:pointer" shape="poly" coords="135,30,144,34,144,45,135,49,126,45,126,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;0066FF&quot;,-169,126)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;0066FF&quot;)" alt="#0066FF"><area style="cursor:pointer" shape="poly" coords="153,30,162,34,162,45,153,49,144,45,144,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;3366FF&quot;,-169,144)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;3366FF&quot;)" alt="#3366FF"><area style="cursor:pointer" shape="poly" coords="171,30,180,34,180,45,171,49,162,45,162,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;3333CC&quot;,-169,162)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;3333CC&quot;)" alt="#3333CC"><area style="cursor:pointer" shape="poly" coords="189,30,198,34,198,45,189,49,180,45,180,34" onclick="clickColor('<?php echo $picker_id; ?>',&quot;666699&quot;,-169,180)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;666699&quot;)" alt="#666699"><area style="cursor:pointer" shape="poly" coords="36,45,45,49,45,60,36,64,27,60,27,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;339966&quot;,-154,27)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;339966&quot;)" alt="#339966"><area style="cursor:pointer" shape="poly" coords="54,45,63,49,63,60,54,64,45,60,45,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;00CC99&quot;,-154,45)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;00CC99&quot;)" alt="#00CC99"><area style="cursor:pointer" shape="poly" coords="72,45,81,49,81,60,72,64,63,60,63,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;00FFCC&quot;,-154,63)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;00FFCC&quot;)" alt="#00FFCC"><area style="cursor:pointer" shape="poly" coords="90,45,99,49,99,60,90,64,81,60,81,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;00FFFF&quot;,-154,81)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;00FFFF&quot;)" alt="#00FFFF"><area style="cursor:pointer" shape="poly" coords="108,45,117,49,117,60,108,64,99,60,99,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;33CCFF&quot;,-154,99)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;33CCFF&quot;)" alt="#33CCFF"><area style="cursor:pointer" shape="poly" coords="126,45,135,49,135,60,126,64,117,60,117,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;3399FF&quot;,-154,117)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;3399FF&quot;)" alt="#3399FF"><area style="cursor:pointer" shape="poly" coords="144,45,153,49,153,60,144,64,135,60,135,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;6699FF&quot;,-154,135)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;6699FF&quot;)" alt="#6699FF"><area style="cursor:pointer" shape="poly" coords="162,45,171,49,171,60,162,64,153,60,153,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;6666FF&quot;,-154,153)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;6666FF&quot;)" alt="#6666FF"><area style="cursor:pointer" shape="poly" coords="180,45,189,49,189,60,180,64,171,60,171,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;6600FF&quot;,-154,171)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;6600FF&quot;)" alt="#6600FF"><area style="cursor:pointer" shape="poly" coords="198,45,207,49,207,60,198,64,189,60,189,49" onclick="clickColor('<?php echo $picker_id; ?>',&quot;6600CC&quot;,-154,189)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;6600CC&quot;)" alt="#6600CC"><area style="cursor:pointer" shape="poly" coords="27,60,36,64,36,75,27,79,18,75,18,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;339933&quot;,-139,18)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;339933&quot;)" alt="#339933"><area style="cursor:pointer" shape="poly" coords="45,60,54,64,54,75,45,79,36,75,36,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;00CC66&quot;,-139,36)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;00CC66&quot;)" alt="#00CC66"><area style="cursor:pointer" shape="poly" coords="63,60,72,64,72,75,63,79,54,75,54,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;00FF99&quot;,-139,54)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;00FF99&quot;)" alt="#00FF99"><area style="cursor:pointer" shape="poly" coords="81,60,90,64,90,75,81,79,72,75,72,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;66FFCC&quot;,-139,72)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;66FFCC&quot;)" alt="#66FFCC"><area style="cursor:pointer" shape="poly" coords="99,60,108,64,108,75,99,79,90,75,90,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;66FFFF&quot;,-139,90)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;66FFFF&quot;)" alt="#66FFFF"><area style="cursor:pointer" shape="poly" coords="117,60,126,64,126,75,117,79,108,75,108,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;66CCFF&quot;,-139,108)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;66CCFF&quot;)" alt="#66CCFF"><area style="cursor:pointer" shape="poly" coords="135,60,144,64,144,75,135,79,126,75,126,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;99CCFF&quot;,-139,126)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;99CCFF&quot;)" alt="#99CCFF"><area style="cursor:pointer" shape="poly" coords="153,60,162,64,162,75,153,79,144,75,144,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;9999FF&quot;,-139,144)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;9999FF&quot;)" alt="#9999FF"><area style="cursor:pointer" shape="poly" coords="171,60,180,64,180,75,171,79,162,75,162,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;9966FF&quot;,-139,162)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;9966FF&quot;)" alt="#9966FF"><area style="cursor:pointer" shape="poly" coords="189,60,198,64,198,75,189,79,180,75,180,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;9933FF&quot;,-139,180)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;9933FF&quot;)" alt="#9933FF"><area style="cursor:pointer" shape="poly" coords="207,60,216,64,216,75,207,79,198,75,198,64" onclick="clickColor('<?php echo $picker_id; ?>',&quot;9900FF&quot;,-139,198)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;9900FF&quot;)" alt="#9900FF"><area style="cursor:pointer" shape="poly" coords="18,75,27,79,27,90,18,94,9,90,9,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;006600&quot;,-124,9)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;006600&quot;)" alt="#006600"><area style="cursor:pointer" shape="poly" coords="36,75,45,79,45,90,36,94,27,90,27,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;00CC00&quot;,-124,27)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;00CC00&quot;)" alt="#00CC00"><area style="cursor:pointer" shape="poly" coords="54,75,63,79,63,90,54,94,45,90,45,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;00FF00&quot;,-124,45)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;00FF00&quot;)" alt="#00FF00"><area style="cursor:pointer" shape="poly" coords="72,75,81,79,81,90,72,94,63,90,63,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;66FF99&quot;,-124,63)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;66FF99&quot;)" alt="#66FF99"><area style="cursor:pointer" shape="poly" coords="90,75,99,79,99,90,90,94,81,90,81,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;99FFCC&quot;,-124,81)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;99FFCC&quot;)" alt="#99FFCC"><area style="cursor:pointer" shape="poly" coords="108,75,117,79,117,90,108,94,99,90,99,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CCFFFF&quot;,-124,99)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CCFFFF&quot;)" alt="#CCFFFF"><area style="cursor:pointer" shape="poly" coords="126,75,135,79,135,90,126,94,117,90,117,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CCCCFF&quot;,-124,117)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CCCCFF&quot;)" alt="#CCCCFF"><area style="cursor:pointer" shape="poly" coords="144,75,153,79,153,90,144,94,135,90,135,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC99FF&quot;,-124,135)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC99FF&quot;)" alt="#CC99FF"><area style="cursor:pointer" shape="poly" coords="162,75,171,79,171,90,162,94,153,90,153,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC66FF&quot;,-124,153)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC66FF&quot;)" alt="#CC66FF"><area style="cursor:pointer" shape="poly" coords="180,75,189,79,189,90,180,94,171,90,171,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC33FF&quot;,-124,171)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC33FF&quot;)" alt="#CC33FF"><area style="cursor:pointer" shape="poly" coords="198,75,207,79,207,90,198,94,189,90,189,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC00FF&quot;,-124,189)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC00FF&quot;)" alt="#CC00FF"><area style="cursor:pointer" shape="poly" coords="216,75,225,79,225,90,216,94,207,90,207,79" onclick="clickColor('<?php echo $picker_id; ?>',&quot;9900CC&quot;,-124,207)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;9900CC&quot;)" alt="#9900CC"><area style="cursor:pointer" shape="poly" coords="9,90,18,94,18,105,9,109,0,105,0,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;003300&quot;,-109,0)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;003300&quot;)" alt="#003300"><area style="cursor:pointer" shape="poly" coords="27,90,36,94,36,105,27,109,18,105,18,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;009933&quot;,-109,18)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;009933&quot;)" alt="#009933"><area style="cursor:pointer" shape="poly" coords="45,90,54,94,54,105,45,109,36,105,36,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;33CC33&quot;,-109,36)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;33CC33&quot;)" alt="#33CC33"><area style="cursor:pointer" shape="poly" coords="63,90,72,94,72,105,63,109,54,105,54,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;66FF66&quot;,-109,54)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;66FF66&quot;)" alt="#66FF66"><area style="cursor:pointer" shape="poly" coords="81,90,90,94,90,105,81,109,72,105,72,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;99FF99&quot;,-109,72)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;99FF99&quot;)" alt="#99FF99"><area style="cursor:pointer" shape="poly" coords="99,90,108,94,108,105,99,109,90,105,90,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CCFFCC&quot;,-109,90)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CCFFCC&quot;)" alt="#CCFFCC"><area style="cursor:pointer" shape="poly" coords="117,90,126,94,126,105,117,109,108,105,108,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFFFFF&quot;,-109,108)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFFFFF&quot;)" alt="#FFFFFF"><area style="cursor:pointer" shape="poly" coords="135,90,144,94,144,105,135,109,126,105,126,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFCCFF&quot;,-109,126)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFCCFF&quot;)" alt="#FFCCFF"><area style="cursor:pointer" shape="poly" coords="153,90,162,94,162,105,153,109,144,105,144,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF99FF&quot;,-109,144)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF99FF&quot;)" alt="#FF99FF"><area style="cursor:pointer" shape="poly" coords="171,90,180,94,180,105,171,109,162,105,162,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF66FF&quot;,-109,162)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF66FF&quot;)" alt="#FF66FF"><area style="cursor:pointer" shape="poly" coords="189,90,198,94,198,105,189,109,180,105,180,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF00FF&quot;,-109,180)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF00FF&quot;)" alt="#FF00FF"><area style="cursor:pointer" shape="poly" coords="207,90,216,94,216,105,207,109,198,105,198,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC00CC&quot;,-109,198)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC00CC&quot;)" alt="#CC00CC"><area style="cursor:pointer" shape="poly" coords="225,90,234,94,234,105,225,109,216,105,216,94" onclick="clickColor('<?php echo $picker_id; ?>',&quot;660066&quot;,-109,216)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;660066&quot;)" alt="#660066"><area style="cursor:pointer" shape="poly" coords="18,105,27,109,27,120,18,124,9,120,9,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;336600&quot;,-94,9)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;336600&quot;)" alt="#336600"><area style="cursor:pointer" shape="poly" coords="36,105,45,109,45,120,36,124,27,120,27,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;009900&quot;,-94,27)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;009900&quot;)" alt="#009900"><area style="cursor:pointer" shape="poly" coords="54,105,63,109,63,120,54,124,45,120,45,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;66FF33&quot;,-94,45)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;66FF33&quot;)" alt="#66FF33"><area style="cursor:pointer" shape="poly" coords="72,105,81,109,81,120,72,124,63,120,63,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;99FF66&quot;,-94,63)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;99FF66&quot;)" alt="#99FF66"><area style="cursor:pointer" shape="poly" coords="90,105,99,109,99,120,90,124,81,120,81,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CCFF99&quot;,-94,81)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CCFF99&quot;)" alt="#CCFF99"><area style="cursor:pointer" shape="poly" coords="108,105,117,109,117,120,108,124,99,120,99,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFFFCC&quot;,-94,99)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFFFCC&quot;)" alt="#FFFFCC"><area style="cursor:pointer" shape="poly" coords="126,105,135,109,135,120,126,124,117,120,117,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFCCCC&quot;,-94,117)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFCCCC&quot;)" alt="#FFCCCC"><area style="cursor:pointer" shape="poly" coords="144,105,153,109,153,120,144,124,135,120,135,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF99CC&quot;,-94,135)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF99CC&quot;)" alt="#FF99CC"><area style="cursor:pointer" shape="poly" coords="162,105,171,109,171,120,162,124,153,120,153,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF66CC&quot;,-94,153)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF66CC&quot;)" alt="#FF66CC"><area style="cursor:pointer" shape="poly" coords="180,105,189,109,189,120,180,124,171,120,171,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF33CC&quot;,-94,171)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF33CC&quot;)" alt="#FF33CC"><area style="cursor:pointer" shape="poly" coords="198,105,207,109,207,120,198,124,189,120,189,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC0099&quot;,-94,189)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC0099&quot;)" alt="#CC0099"><area style="cursor:pointer" shape="poly" coords="216,105,225,109,225,120,216,124,207,120,207,109" onclick="clickColor('<?php echo $picker_id; ?>',&quot;993399&quot;,-94,207)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;993399&quot;)" alt="#993399"><area style="cursor:pointer" shape="poly" coords="27,120,36,124,36,135,27,139,18,135,18,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;333300&quot;,-79,18)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;333300&quot;)" alt="#333300"><area style="cursor:pointer" shape="poly" coords="45,120,54,124,54,135,45,139,36,135,36,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;669900&quot;,-79,36)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;669900&quot;)" alt="#669900"><area style="cursor:pointer" shape="poly" coords="63,120,72,124,72,135,63,139,54,135,54,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;99FF33&quot;,-79,54)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;99FF33&quot;)" alt="#99FF33"><area style="cursor:pointer" shape="poly" coords="81,120,90,124,90,135,81,139,72,135,72,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CCFF66&quot;,-79,72)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CCFF66&quot;)" alt="#CCFF66"><area style="cursor:pointer" shape="poly" coords="99,120,108,124,108,135,99,139,90,135,90,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFFF99&quot;,-79,90)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFFF99&quot;)" alt="#FFFF99"><area style="cursor:pointer" shape="poly" coords="117,120,126,124,126,135,117,139,108,135,108,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFCC99&quot;,-79,108)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFCC99&quot;)" alt="#FFCC99"><area style="cursor:pointer" shape="poly" coords="135,120,144,124,144,135,135,139,126,135,126,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF9999&quot;,-79,126)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF9999&quot;)" alt="#FF9999"><area style="cursor:pointer" shape="poly" coords="153,120,162,124,162,135,153,139,144,135,144,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF6699&quot;,-79,144)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF6699&quot;)" alt="#FF6699"><area style="cursor:pointer" shape="poly" coords="171,120,180,124,180,135,171,139,162,135,162,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF3399&quot;,-79,162)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF3399&quot;)" alt="#FF3399"><area style="cursor:pointer" shape="poly" coords="189,120,198,124,198,135,189,139,180,135,180,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC3399&quot;,-79,180)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC3399&quot;)" alt="#CC3399"><area style="cursor:pointer" shape="poly" coords="207,120,216,124,216,135,207,139,198,135,198,124" onclick="clickColor('<?php echo $picker_id; ?>',&quot;990099&quot;,-79,198)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;990099&quot;)" alt="#990099"><area style="cursor:pointer" shape="poly" coords="36,135,45,139,45,150,36,154,27,150,27,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;666633&quot;,-64,27)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;666633&quot;)" alt="#666633"><area style="cursor:pointer" shape="poly" coords="54,135,63,139,63,150,54,154,45,150,45,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;99CC00&quot;,-64,45)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;99CC00&quot;)" alt="#99CC00"><area style="cursor:pointer" shape="poly" coords="72,135,81,139,81,150,72,154,63,150,63,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CCFF33&quot;,-64,63)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CCFF33&quot;)" alt="#CCFF33"><area style="cursor:pointer" shape="poly" coords="90,135,99,139,99,150,90,154,81,150,81,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFFF66&quot;,-64,81)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFFF66&quot;)" alt="#FFFF66"><area style="cursor:pointer" shape="poly" coords="108,135,117,139,117,150,108,154,99,150,99,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFCC66&quot;,-64,99)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFCC66&quot;)" alt="#FFCC66"><area style="cursor:pointer" shape="poly" coords="126,135,135,139,135,150,126,154,117,150,117,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF9966&quot;,-64,117)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF9966&quot;)" alt="#FF9966"><area style="cursor:pointer" shape="poly" coords="144,135,153,139,153,150,144,154,135,150,135,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF6666&quot;,-64,135)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF6666&quot;)" alt="#FF6666"><area style="cursor:pointer" shape="poly" coords="162,135,171,139,171,150,162,154,153,150,153,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF0066&quot;,-64,153)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF0066&quot;)" alt="#FF0066"><area style="cursor:pointer" shape="poly" coords="180,135,189,139,189,150,180,154,171,150,171,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC6699&quot;,-64,171)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC6699&quot;)" alt="#CC6699"><area style="cursor:pointer" shape="poly" coords="198,135,207,139,207,150,198,154,189,150,189,139" onclick="clickColor('<?php echo $picker_id; ?>',&quot;993366&quot;,-64,189)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;993366&quot;)" alt="#993366"><area style="cursor:pointer" shape="poly" coords="45,150,54,154,54,165,45,169,36,165,36,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;999966&quot;,-49,36)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;999966&quot;)" alt="#999966"><area style="cursor:pointer" shape="poly" coords="63,150,72,154,72,165,63,169,54,165,54,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CCCC00&quot;,-49,54)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CCCC00&quot;)" alt="#CCCC00"><area style="cursor:pointer" shape="poly" coords="81,150,90,154,90,165,81,169,72,165,72,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFFF00&quot;,-49,72)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFFF00&quot;)" alt="#FFFF00"><area style="cursor:pointer" shape="poly" coords="99,150,108,154,108,165,99,169,90,165,90,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FFCC00&quot;,-49,90)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FFCC00&quot;)" alt="#FFCC00"><area style="cursor:pointer" shape="poly" coords="117,150,126,154,126,165,117,169,108,165,108,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF9933&quot;,-49,108)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF9933&quot;)" alt="#FF9933"><area style="cursor:pointer" shape="poly" coords="135,150,144,154,144,165,135,169,126,165,126,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF6600&quot;,-49,126)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF6600&quot;)" alt="#FF6600"><area style="cursor:pointer" shape="poly" coords="153,150,162,154,162,165,153,169,144,165,144,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF5050&quot;,-49,144)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF5050&quot;)" alt="#FF5050"><area style="cursor:pointer" shape="poly" coords="171,150,180,154,180,165,171,169,162,165,162,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC0066&quot;,-49,162)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC0066&quot;)" alt="#CC0066"><area style="cursor:pointer" shape="poly" coords="189,150,198,154,198,165,189,169,180,165,180,154" onclick="clickColor('<?php echo $picker_id; ?>',&quot;660033&quot;,-49,180)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;660033&quot;)" alt="#660033"><area style="cursor:pointer" shape="poly" coords="54,165,63,169,63,180,54,184,45,180,45,169" onclick="clickColor('<?php echo $picker_id; ?>',&quot;996633&quot;,-34,45)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;996633&quot;)" alt="#996633"><area style="cursor:pointer" shape="poly" coords="72,165,81,169,81,180,72,184,63,180,63,169" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC9900&quot;,-34,63)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC9900&quot;)" alt="#CC9900"><area style="cursor:pointer" shape="poly" coords="90,165,99,169,99,180,90,184,81,180,81,169" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF9900&quot;,-34,81)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF9900&quot;)" alt="#FF9900"><area style="cursor:pointer" shape="poly" coords="108,165,117,169,117,180,108,184,99,180,99,169" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC6600&quot;,-34,99)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC6600&quot;)" alt="#CC6600"><area style="cursor:pointer" shape="poly" coords="126,165,135,169,135,180,126,184,117,180,117,169" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF3300&quot;,-34,117)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF3300&quot;)" alt="#FF3300"><area style="cursor:pointer" shape="poly" coords="144,165,153,169,153,180,144,184,135,180,135,169" onclick="clickColor('<?php echo $picker_id; ?>',&quot;FF0000&quot;,-34,135)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;FF0000&quot;)" alt="#FF0000"><area style="cursor:pointer" shape="poly" coords="162,165,171,169,171,180,162,184,153,180,153,169" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC0000&quot;,-34,153)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC0000&quot;)" alt="#CC0000"><area style="cursor:pointer" shape="poly" coords="180,165,189,169,189,180,180,184,171,180,171,169" onclick="clickColor('<?php echo $picker_id; ?>',&quot;990033&quot;,-34,171)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;990033&quot;)" alt="#990033"><area style="cursor:pointer" shape="poly" coords="63,180,72,184,72,195,63,199,54,195,54,184" onclick="clickColor('<?php echo $picker_id; ?>',&quot;663300&quot;,-19,54)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;663300&quot;)" alt="#663300"><area style="cursor:pointer" shape="poly" coords="81,180,90,184,90,195,81,199,72,195,72,184" onclick="clickColor('<?php echo $picker_id; ?>',&quot;996600&quot;,-19,72)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;996600&quot;)" alt="#996600"><area style="cursor:pointer" shape="poly" coords="99,180,108,184,108,195,99,199,90,195,90,184" onclick="clickColor('<?php echo $picker_id; ?>',&quot;CC3300&quot;,-19,90)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;CC3300&quot;)" alt="#CC3300"><area style="cursor:pointer" shape="poly" coords="117,180,126,184,126,195,117,199,108,195,108,184" onclick="clickColor('<?php echo $picker_id; ?>',&quot;993300&quot;,-19,108)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;993300&quot;)" alt="#993300"><area style="cursor:pointer" shape="poly" coords="135,180,144,184,144,195,135,199,126,195,126,184" onclick="clickColor('<?php echo $picker_id; ?>',&quot;990000&quot;,-19,126)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;990000&quot;)" alt="#990000"><area style="cursor:pointer" shape="poly" coords="153,180,162,184,162,195,153,199,144,195,144,184" onclick="clickColor('<?php echo $picker_id; ?>',&quot;800000&quot;,-19,144)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;800000&quot;)" alt="#800000"><area style="cursor:pointer" shape="poly" coords="171,180,180,184,180,195,171,199,162,195,162,184" onclick="clickColor('<?php echo $picker_id; ?>',&quot;993333&quot;,-19,162)" onmouseover="mouseOverColor('<?php echo $picker_id; ?>',&quot;993333&quot;)" alt="#993333"></map>
<div id="selectedhexagon<?php echo $picker_id; ?>" class="color_selector"></div>
<input type="text" class="color_picker_field" id="<?php echo $picker_id;?>" value_name="<?php echo $value_name;?>" 
	d_id="<?php echo $d_id;?>" value="<?php echo $value;?>" 
	onchange="onChangeColorPicker(<?php echo $d_id;?>, <?php echo "'$value_name'";?>, <?php echo "'$picker_id'";?>, this.value)"/>
<script>	
	$( document ).ready(function() {
		onChangeColorPicker(<?php echo "$d_id, '$value_name', '$picker_id', '$value'";?> );
					 $('#<?php echo $picker_id;?>'.replace( /(:|\.|\[|\]|,|\+)/g, "\\$1")).keyup(function() {
						start_timer();		
					 });
	});
</script>	
</span>

<?php
}
?>	
