<?php
    header('Access-Control-Allow-Origin: *');
?>
<!-----------------------------------------------------------
#	SongSelect for Shekinah School of Worship vom Upper Room
#	Used Plug-ins: JQUERY, JQUERYUI
#	Author: Domenik Reitzner
------------------------------------------------------------>

<!DOCTYPE html>
<html style="padding: 0; margin: 0">
	 <HEAD>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="theme-color" content="#03A9F4">
		<meta name="viewport" content="width=device-width, 
                           initial-scale=1.0, 
                           maximum-scale=1.0, 
                           user-scalable=no">
		<link rel="icon" sizes="192x192" href="img/favicon.png" type="image/png">
		<link rel="stylesheet" href="general.css">
		<link rel="stylesheet" href="jquery-ui-snip.css">
		<script src="//code.jquery.com/jquery-2.1.1.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<script>
		////////
		// GLOBAL VARIABLES
		////////
			var rowCount;
			var intro = 1;
			
		////////
		// RESET SELECTION
		////////
			function resetSelection(){
				// restore values
				$( "input[name='search']").val("search title, artist or songtext");
				//$("#utb").prop("checked", false);
				$( "#slider-range" ).slider({
				  values: [1,rowCount]
				});
				$("input.sliderValue[data-index=0]").val(1);
				$("input.sliderValue[data-index=1]").val(rowCount);
				$("input.sliderValue[data-index=1]").promise().done(function(){
					// sort table
					tableSort('num');
					tableFilter();
				});
			}
			
		////////
		// RESET LIST
		////////
			function resetList(){
				$('#printList tr').each(function(index, row){
					if( $(row).children().is('td') ) removeList( $(row).children('td').eq(0).text() );
				});
			}
			
		////////
		// RESET
		////////
			function reset(what){
				
				$('#overlay').fadeIn();
				$('#overlay').promise().done( function() {
					if(what == 'selection' || what == 'all') resetSelection();
					if(what == 'list' || what == 'all') resetList();
					if(window.width < 1024) mobileTab(this, 'reset');
					$('#overlay').fadeOut();
				});
			}
			
		////////
		// MAKE RESET... VISIBILE
		////////
			function resetVisible(typ){
				if($('#reset').is(':hidden') ) switchReset();				
				$('#reset'+typ).fadeIn();
				$('#reset'+typ).promise().done( function() {
					if( $('#resetSelection').is(':visible') && $('#resetList').is(':visible') ) $('#resetAll').fadeIn();
				});
			}
			
		///////
		// HIDE RESET
		///////
			function switchReset(){
				$('#reset').animate({"height": "toggle"}, "slow");
				$('#resetResize').toggleClass("resizeM");
				$('#resetResize').toggleClass("resizeS");
			}
			
		///////
		// CHECK IF ALL OF SELECTION IS STANDARD
		///////
			function checkSelection(){
				var searchTxt = $( "input[name='search']").val().toLowerCase();
				if(searchTxt == "search title, artist or songtext")  searchTxt = "";
				var sliderl=$( "#slider-range" ).slider( "values", 0 );
				var sliderr=$( "#slider-range" ).slider( "values", 1 );
				if(searchTxt == "" && sliderl == 1 && sliderr == rowCount && $('#num').is(':hidden') && $('#utb').not(':checked') ) {
					$('#resetSelection').fadeOut();
					$('#resetAll').fadeOut();
					if( $('#resetList').is(':hidden') && $('#resetResize').hasClass('resizeS') ) {
						switchReset();
					}
				}
			}
		
		///////
		// FILTER TABLE BY SELECTION
		///////
			function tableFilter() {
				var go2song = 0;
				var searchTxt = $( "input[name='search']").val()
				if(searchTxt.length <= 3) {
					var numCheckTxt = searchTxt.split('');
					for(var i = 0; i < searchTxt.length; i++){
						if($.isNumeric(numCheckTxt[i])) go2song++;
						if(go2song<1) break;
					}
				}
				if( go2song ){
					if(searchTxt.length == 3) {
						if(numCheckTxt[0] == 0){
							if(numCheckTxt[1] == 0) searchTxt = numCheckTxt[2];
							else searchTxt = numCheckTxt[1]+numCheckTxt[2];
						}
						displaySong(searchTxt);
						$( "input[name='search']").val("search title, artist or songtext");
					}
				}
				else{
					searchTxt = searchTxt.toLowerCase();
					var rows = $('#songlist tr').get();
					if(searchTxt == "search title, artist or songtext")  searchTxt = "";
					var search = searchTxt.split(' ');
					var wordCount = search.length;	
				//youtube only
					var utb = $('#utb').is(":checked"); // gives back TRUE or FALSE
				// range
					var sliderl=$( "#slider-range" ).slider( "values", 0 );
					var sliderr=$( "#slider-range" ).slider( "values", 1 );
					checkSelection();
					$.each(rows, function(index, row){
						//$('#songlist').append(row);
						//if not in number range
						var tempSongNr = $(row).children('td').eq(2).text();
						if(tempSongNr>sliderr || tempSongNr<sliderl){
							$(row).css('display', 'none');
						}
						
						else{
							//get Title and Artist and Text
							var tempTitle = $(row).children('td').eq(0).text().toLowerCase();
							var tempArtist = $(row).children('td').eq(1).text().toLowerCase();
							var tempText = $(row).children('td').eq(6).text().toLowerCase();
							var tempMatch = 0;
							
							
							//look up title & artist
							for( var i = 0; i < wordCount; i=i+1)
							{
								if(search[i]) {
									if(tempTitle.match(search[i]) ) tempMatch=tempMatch+1;
									else if(tempArtist.match(search[i]) ) tempMatch=tempMatch+1;
									else if(tempText.match(search[i]) ) tempMatch=tempMatch+1;
								}
								else tempMatch=tempMatch+1;
							}
							
							// if title & artist no match
							if(wordCount > tempMatch){
								$(row).css('display', 'none');
							}
							else
							{
								//if youtube is checked
								if(utb && $(row).children('td').eq(3).text() == ' ' ) $(row).css('display', 'none');
								// all conditions met -> display 
								else
								{
									// display
									$(row).css('display', 'table-row');
								}
							}
						}
						
					});
				}
				setArrows();
				resetVisible('Selection');
				// need to wait until fade out is done
				$('#resetSelection').promise().done(function() {
					checkSelection();
				});
			}
			
		///////
		// SONG TEXT
		///////
			function songText(){
				if( $("#songText").is(":checked") ){
					//
				}
				else{
					
				}
			}
		
		///////
		// DISPLAY SONG
		///////			
			function displaySong(songNumber){
				var showHTML;
				var showDocKey;
				var showUtb;
				if($('#'+songNumber).children('td').eq(3).text() != ' ')
				{
					showUtb = $('#'+songNumber).children('td').eq(3).find('a').attr('href');
				}
				else showUtb = 0;
				var showDoc = $('#'+songNumber).children('td').eq(4).find('a').attr('href');	
				//get doc key
				var showDocKeyLenght = showDoc.length-5;
				showDocKey = showDoc.substring(35, showDocKeyLenght );
				showHTML = "<iframe id='doc' seamless src='https://docs.google.com/document/d/";
				showHTML += showDocKey;
				showHTML += "/pub?embedded=true'></iframe>";
				if(showUtb)
				{
					// aspect ratio for mobile
					showHTML += "<div id='utbWrapper'>";
					//get utb key
					var drive = 0;
					if(showUtb.match(/be\//) ) showUtb = showUtb.split(/be\//);
					else if(showUtb.match(/\?v=/) ) showUtb = showUtb.split(/\?v=/);
					else drive = 1;
					if(drive) showHTML += "<div> <a href='"+showUtb+"' title='download MP3'><img src='img/drive_icon.png' id='docIcon' height='100' width='100'></a></div>";
					else showHTML += "<iframe class='utb' frameborder='0' src='//www.youtube.com/embed/"+showUtb[1]+"'  allowfullscreen></iframe>";
					showHTML += "</div>";
				}
				showHTML += "<div> <a href='"+showDoc+"' title='ausdrucken oder speichern' target='_blank'><img src='img/google_docs.png' id='docIcon' style='float:right'></a></div>";
				$('#showSong').html(showHTML);
				switch2("showSong");
				var htotal = window.innerHeight;
				var htop = $('#selection').height();
				$('#doc').height(htotal-htop);
				$("#doc").contents().find('.c4').css('padding', '0');
				$('#mobileDisplaySong').fadeIn();
			}
		///////
		// Set arrows
		///////			
			function setArrows(){
				var twt = $("#songlist tr:visible:first td:eq(0)").width();
				var twa = $("#songlist tr:visible:first td:eq(1)").width();
				$("#title").animate({width: twt-6},"fast");
				$("#artist").animate({width: twa},"fast");
			}
		
		///////
		// SORT TABLE BY SELECTION
		///////			
			function tableSort(how2){
				var rows = $('#songlist tr').get();
				var row2sort;
				var asc;
				if(how2 == 'asc') {
					row2sort = 0;
					asc = 1;
					$( '#asc' ).hide();
					$( '#desc' ).show();
					$( '#numa' ).show();
					$( '#num' ).show();
				}
				
				else if(how2 == 'desc') {
					row2sort = 0;
					asc = 0;
					$( '#asc' ).show();
					$( '#desc' ).hide();
					$( '#numa' ).show();
					$( '#num' ).show();
				}
				
				else if(how2 == 'numa'){
					row2sort = 2;
					asc = 1;
					$( '#asc' ).show();
					$( '#desc' ).show();
					$( '#numa' ).hide();
					$( '#num' ).show();
				}
				
				else  {
					row2sort = 2;
					asc = 0;
					$( '#asc' ).show();
					$( '#desc' ).show();
					$( '#numa' ).show();
					$( '#num' ).hide();
				}
				rows.sort(function(a,b) {
					var A = $(a).children('td').eq(row2sort).text();
					var B = $(b).children('td').eq(row2sort).text();
					
					// for text
					if(row2sort == 0){
						if(asc){
							if(A<B) return -1;
							if(A>B) return 1;
							return 0;
						}
						else{
							if(A>B) return -1;
							if(A<B) return 1;
							return 0;
						}
					}
					
					else{
						if(asc) return A-B;
						else return B-A;
					}
				});
				$.each(rows, function(index, row){
					$('#songlist').append(row);
				});	
				resetVisible('Selection');
				// need to wait until fade out is done
				$('#resetSelection').promise().done(function() {
					checkSelection();
				});
			}
		///////
		// add2List
		///////
			function add2List(songNumber){
				var songTitle = $('#'+songNumber).children('td').eq(0).text();
				var printListLenght = $('#printList tr').length;
				if( printListLenght  <= 1){
					$('#printList').append('<tr id="print'+songNumber+'"><td class="nr">'+songNumber+'</td><td class="title">'+songTitle+'</td></tr>');
					$('#delete').append('<tr><td class="pointer hover" onclick="move(&quot;remove&quot;,'+printListLenght+')">X</td></tr>');
					$('#control').fadeIn();
					resetVisible('List');
				}
				else if( printListLenght == 2){
					$('#printList').append('<tr id="print'+songNumber+'"><td class="nr">'+songNumber+'</td><td class="title">'+songTitle+'</td></tr>');
					$('#updown').append('<tr><td> </td><td class="pointer hover" onclick="move(&quot;down&quot;,2)">&#9660;</td></tr>');
					$('#updown').append('<tr><td class="pointer hover" onclick="move(&quot;up&quot;,0)">&#9650;</td><td></td></tr>');
					$('#delete').append('<tr><td class="pointer hover" onclick="move(&quot;remove&quot;,'+printListLenght+')">X</td></tr>');
				}
				else{
					$('#printList').append('<tr id="print'+songNumber+'"><td class="nr">'+songNumber+'</td><td class="title">'+songTitle+'</td></tr>');
					$('#updown tr:last').before('<tr><td class="pointer hover" onclick="move(&quot;up&quot;,'+printListLenght+')">&#9650;</td><td class="pointer hover" onclick="move(&quot;down&quot;,'+printListLenght+')">&#9660;</td></tr>');
					$('#delete').append('<tr><td class="pointer hover" onclick="move(&quot;remove&quot;,'+printListLenght+')">X</td></tr>');
				}
				// change class
				$('#'+songNumber).children('td').eq(7).children('img').toggleClass('add');
				// change image2wbmp
				$('#'+songNumber).children('td').eq(7).children('img').attr('src','img/removeList.png');
				// change onclick
				$('#'+songNumber).children('td').eq(7).attr('onclick','removeList('+songNumber+')');
			}
		///////
		// removeList
		///////
			function removeList(songNumber){
				if(songNumber == "") return;
				var songTitle = $('#'+songNumber).children('td').eq(0).text();
				var printListLenght = $('#printList tr').length;
				$('#print'+songNumber).remove();
				$('#delete tr:last').remove();
				if(printListLenght == 3){
					$('#updown tr:last').remove();
					$('#updown tr:last').promise().done( function() {
						$('#updown tr:last').remove();
					});
				}
				else if(printListLenght > 3) $('#updown tr').eq(printListLenght-2).remove();
				// change class
				$('#'+songNumber).children('td').eq(7).children('img').toggleClass('add');
				// change image2wbmp
				$('#'+songNumber).children('td').eq(7).children('img').attr('src','img/add2List.png');
				// change onclick
				$('#'+songNumber).children('td').eq(7).attr('onclick','add2List('+songNumber+')');
				if( $('#printList tr').length <= 1) {
					$('#control').fadeOut();
					$('#resetAll').fadeOut();
					$('#resetList').fadeOut();
					$('#resetList').promise().done( function() {
						if( $('#resetSelection').is(':hidden') ) {
							switchReset();
						}
					});
				}
			}
		///////
		// switch2 display something else
		///////
			function switch2(place){
				// hide selection and result
				$('#result').fadeOut();
				$('#hideSelection').fadeOut();
				//hide arrows
				$('.noShowEdit').fadeOut();
				switch(place){
					case "printList": 	$('.yesPrint').fadeIn();
										break;
					case "showSong": 	$('#showSong').fadeIn();
										$('#print').fadeOut();
										$('#print').promise().done(function() {
											$('#control').fadeIn();
										});		
										break;
					case "settings": 	$('#settingsTab').fadeIn();
										$('#print').fadeOut();
										$('#print').promise().done(function() {
											$('#control').fadeIn();
										});	
										break;
					default: alert("There seems to be an error");
				}				
				// no overlap in buttons
				$('#edit').fadeOut();
				$('#edit').promise().done(function() {
					$('#finished').fadeIn();
					hChange();		
				});		
			}
		
		///////
		// back
		///////
			function back(){
				// check if we have songlist for control
				if( $('#printList tr').length <= 1) {
					$('#control').fadeOut();
				}
				// print back in the game in case of showSong
				$('#print').fadeIn();
				// fade back in normal stuff
				$('#hideSelection').fadeIn();
				$('#result').fadeIn();
				$('.yesPrint').fadeOut();
				$('#settingsTab').fadeOut();
				$('#showSong').fadeOut();
				$('#mobileDisplaySong').fadeOut();
				$('#finished').fadeOut();
				$('#finished').promise().done(function() {
					$('#edit').fadeIn();
				});
				//restore arrows
				$('.noShowEdit').fadeIn();
				hChange();
			}
		
		///////
		// edit in list
		///////
			function move(way, nr){
				if(way == 'remove'){
					var songNumber = $('#printList tr:eq('+nr+') td:eq(0)').html();
					removeList(songNumber);
				}
				else {
					var row = nr-1;
					if(nr == 0) {
						row = $('#printList tr').length -1;
					}
					var row = $('#printList tr').eq(row);
					if (way == 'up') {
						row.insertBefore(row.prev());
					} else {
						row.insertAfter(row.next());
					}
				}				
			}
		///////	
		// on changed height
		///////
			function hChange(){
				var selectionH = $('#selection').height();            
				$('#content').css('padding-top', selectionH+15);
			}
			
		///////
		// TOGGLE THE SELECTIONS
		///////
			function resize(id,elem){
				$(elem).toggleClass("resizeM");
				$(elem).toggleClass("resizeS");
				$('#'+id).animate({"height": "toggle"}, "slow");
				$('#'+id).promise().done(function() {
					hChange();
				});
			}	
			
		///////
		// TOUR
		///////
			function tour(){
				alert('will be available shortly\ncome back soon');
			}	
			
			
		///////
		// mobileNav
		///////
			function mobileTab(obj,id){
				if( $(obj).attr("src") == "img/finished.png") {
					$(obj).attr("src", "img/"+id+".png");
					$("#"+id+"Tab").fadeOut();
					$("#result").fadeIn();
				}
				
				else{
					// look up all the others
					var lookUp = ["search","numberRange","sort","settings"];
					for ( var i = 0; i <= 3; i++){
						if( lookUp[i] != id) {
							if( $("#"+lookUp[i]+"Tab").is(":visible") ) {
								$("img[src$='finished.png'").attr("src", "img/"+lookUp[i]+".png");
								$("#"+lookUp[i]+"Tab").fadeOut();
								$("#result").fadeIn();
							}
						}
					}
					$(obj).attr("src", "img/finished.png");
					$("#"+id+"Tab").fadeIn();
				}
			}
			
		///////
		// mobileSort
		///////
			function mobileSort(how2){
				tableSort(how2);
				$("img[src$='finished.png'").attr("src", "img/sort.png");
				$("#sortTab").fadeOut();
				$("#result").fadeIn();
			}

		///////
		// LOAD SPREADSHEET INTO LOCAL STORAGE
		///////
			function loadData(){
				$.get( "http://reitzner.at/extern/songselect/getCsv.php?key=11WVFq4HIngN1MWWyGMZsxIDtuvbGbQ_fp6uHaODEZVY", function( data ) {
					data = data.replace(/"/g,"");
					var import_row = data.split(/\r\n|\n/);
					var tempCells ;
					var totalHTML="<table id='songlist'>";
					for( var i = 0; i < import_row.length; i++)
					{
						// check if min of 5 characters 
						if(import_row[i].length >= 5){
							// split row into cells
							tempCells = import_row[i].split(",");
							totalHTML += "<tr id='"+tempCells[2]+"'>";
								// Title and Artist and Number as link to song
								totalHTML += "<td class='pointer' onclick='displaySong("+tempCells[2]+")'>"+tempCells[0]+"</td>";
								totalHTML += "<td class='pointer noMobile' onclick='displaySong("+tempCells[2]+")'>"+tempCells[1]+"</td>";
								totalHTML += "<td class='pointer number' onclick='displaySong("+tempCells[2]+")'>"+tempCells[2]+"</td>";
								//youtube or google drive if not empty
								if(tempCells[3])
								{
									totalHTML += "<td class='noMobile'><a href='"+tempCells[3]+"' target='_blank'><img src='img/";
									if(tempCells[3].match(/google/) ) totalHTML+="drive_icon";
									else totalHTML += "YouTube-icon-full_color";
									totalHTML += ".png' height='24px'></a></td>";
								}
								else  totalHTML += "<td class='noMobile'> </td>";
								// docs1-2
								if(tempCells[4] ) totalHTML += "<td class='noMobile'><a href='"+tempCells[4]+"' target='_blank'><img src='img/google_docs.png' height='27px'></a></td>";
								else  totalHTML += "<td class='noMobile'></td>";
								if(tempCells[5] ) totalHTML += "<td class='noMobile'><a href='"+tempCells[5]+"' target='_blank'><img src='img/google_docs.png' height='27px'></a></td>";
								else  totalHTML += "<td class='noMobile'></td>";
								// text hidden
								totalHTML += "<td class='hidden noMobile'>"+tempCells[6]+"</td>";
								totalHTML += "<td class='noMobile' onclick='add2List("+tempCells[2]+")'><img src='img/add2List.png' class='add pointer'  title='Add number "+tempCells[2]+" to Songlist' height='27px'></td>";
							totalHTML += "</tr>";
						}
					}
					totalHTML += "</table>";
					$('#result').html(totalHTML);
					if(typeof(Storage) !== "undefined") localStorage.data = totalHTML;
					setUp("afterLoad");
				});			
			}	

		///////
		// LOADBAR
		///////
			function loadbar(nr){
				//loadfunction progressbar
				$( "#progressBar" ).progressbar({
				  value: false
				});
				//progressBar set to songs + 4*10 defined steps in load
					$( "#progressBar" ).progressbar({ max: nr });
					var pGress = setInterval(function() {
						var pVal = $('#progressBar').progressbar('option', 'value');
						var pCnt = !isNaN(pVal) ? (pVal + 1) : 1;
						if (pCnt >= nr) {
							clearInterval(pGress);
							$('#load').fadeOut();
							$('#load').promise().done( function(){
								$('#overlay').fadeOut();
							});
						} 
						else {
							$('#progressBar').progressbar({value: pCnt});
							if(pCnt <= nr-40) $( "#loadTxt").text("LOADING...song number "+pCnt);
							else if(pCnt <= nr-30) $( "#loadTxt").text("LOADING...slider");
							else if(pCnt <= nr-20) $( "#loadTxt").text("LOADING...height");
							else if(pCnt <= nr-10) $( "#loadTxt").text("LOADING...set arrows");
							else $( "#loadTxt").text("DONE");
						}
						
					},0);
			}
		
		
		///////
		// setUp
		///////
			function setUp(how){
				if( how != "afterLoad") {
					// GET RESULT FROM LOCAL STORAGE
					$('#result').html(localStorage.data);
				}
				window.rowCount = $('#songlist tr').length;
				if(intro == 1) loadbar(rowCount+40);
				//insert slider 10 values for progressbar
				$( "#slider-range" ).slider({
				  range: true,
				  min: 1,
				  max: rowCount,
				  values: [1,rowCount],
				  stop: function( event, ui ) {
					for (var i = 0; i < ui.values.length; ++i) {
						$("input.sliderValue[data-index=" + i + "]").val(ui.values[i]);
					}
					tableFilter();
				  }
				});
				
				$("input.sliderValue[data-index=1]").val( $( "#slider-range" ).slider( "values", 1 )); 
				
				// insert change in text field to slider 10 values for progressbar
				$("input.sliderValue").change(function() {
					var $this = $(this);
					$("#slider-range").slider("values", $this.data("index"), $this.val());
					tableFilter();
				});
				//set height of #result  10 values for progressbar
				hChange();
				// place arrows for up and down  10 values for progressbar
				setArrows();
				// fadeS
				$('#resetAll').hide();
				$('#resetSelection').hide();
				$('#resetList').hide();
				$('#reset').animate({"height": "toggle"}, "slow");
			}
		
		///////
		// SETTINGS
		///////
			function settings(object){
				var objId = $(object).attr('id');
				// ARRAY PLACES
				// 0: intro
				// 1:
				// 2: 
				// 3: 
				// 4: 
				// 5: 
				// 6: 
				// 7: 
				// 8: 
				// 9: 
				var set = localStorage.settings.split("");
				var i;
				switch(objId){
					case 'intro':	i = 0;
									if ( set[i] == 1 ) set[i] = 0;
									else set[i] = 1;
									break;
					default: alert(objId);
				}
				localStorage.settings = set.join("");
			}
			
		///////
		// LOAD SETTINGS TO VARIABLES
		///////
			function loadSet(){
				var set = localStorage.settings.split("");
				$.each(set, function(index, value){
					switch(index){
						case 0: window.intro = value;
								if( value == 1) $('#intro').prop('checked', true);
								else $('#intro').prop('checked', false);
								break;
						default: break;
					}
				});
				
			}
		///////
		// AFTER LOAD
		///////
			$(function() {	
				
				//don't display selected
				$( '#num' ).hide();
				$('#finished').hide();
				$('#control').hide();
				$('.yesPrint').hide();
				$('#settingsTab').hide();
				// CHECK IF LOCAL STORAGE IS AVAILABLE
				if(typeof(Storage) !== "undefined") {
					// set settings to standard
					if (!localStorage.settings) {
						localStorage.settings = "1111111111";
					}
					// call function to call settings into variable
					loadSet();
					// get the last date of change
					$.get( "http://reitzner.at/extern/songselect/getCsv.php?key=1cfMEo7_qnJfUtf-IRP_0SWat-0K1_9foie30z3fXC5Q", function( data ) {
						var currentSet = localStorage.lastModified;
						// IF THERE WHERE NO CHANGES MADE SINCE LAST TIME
						if( currentSet == data) {
							setUp("localStorage");
						}
						else {
							localStorage.lastModified = data;
							loadData();
						}
					});
				}
				else {
					// Sorry! No Web Storage support..
					alert("We are sorry this is not as fast, but your browser does not support localStorage\nDownload Google Chrome");
					loadData();
				}
				// show intro if settings are set that way
				if(intro == 1){
					$('#overlay').show();
					$('#load').show();
				}
			});
		</script>
	</HEAD>
	<body style="padding: 0; margin: 0">
		<div class='noPrint noList'>
			<div id="overlay"></div>
			<div id="load">
				<img src="img/SongSelect.png">
				<div id="loadTxt">LOADING...</div>
				<div id="progressBar"></div>
				<div id="copyright">&copy; Domenik Reitzner</div>
			</div>
		
<!-- INFO AND SETTINGS FOR FULL SCREEN VERSION -->
			<div id="info" class='noMobile'>
				<div class="tab">
					<div class="center">
						<div id="version" class="hover">
						Version:
						<?php
							$file = fopen("log.txt","r");
							$version = fgets($file);
							fclose($file);
							$version = explode(":", $version);
							echo $version[1];
						?>
						</div>
						<div class="pointer circle iIcons" title="Settings" onclick="switch2('settings')"><div class="hover arrows outline">&lowast;</div></div>
						<a href="log.txt" target="blank"><div class="circle iIcons" title="Version Information"><div class="hover arrows outline">i</div></div></a>
						<div class="pointer circle iIcons" title="How to use SongSelect" onclick="tour()"><div class="hover arrows outline">?</div></div>
					</div>
				</div>
			</div>

<!-- SELECTION FOR FULL SCREEN VERSION (TABS FOR MOBILE) -->
			<div id='selection'>
				<div id="selectionBackground">
					<div id="hideSelection">
						<div id="searchTab" class="tab">
							<div class="resizeS pointer noMobile" onclick="resize('search',this)"></div>
							<div class="header"><h1>Search</h1></div>
							<div class="selectionContent" id="search">
								<input name='search' value="search title, artist or songtext" id='search' class='hover' type='text' size='35' onkeyup='tableFilter()' onfocus="if(this.value == 'search title, artist or songtext') {this.value=''}" onblur="if(this.value == ''){this.value ='search title, artist or songtext'}"></br>
								<div class="hover"><input type="checkbox" id="songText" onclick="songText()"> search in songtext</div>
							</div>
						</div>
						<div id="numberRangeTab" class="tab">
							<div class="resizeS pointer noMobile" onclick="resize('number',this)"></div>
							<div class="header"><h1>Number Range</h1></div>
							<div id="number" class="selectionContent">
								<div class="center" width="120px">
									<input type="text" class="sliderValue" style="border: 0;" data-index="0" size="1" value="1"/>-
									<input type="text" class="sliderValue" style="border: 0;" data-index="1" size="1" />
								</div>
								<div id='slider-range' class='hover'></div>
							</div>
						</div>		
						<div class="tab">
							<div id="resetResize" class="resizeM pointer" onclick="resize('reset',this)"></div>
							<div class="header">Reset</div>
							<div id="reset" class="selectionContent">
								<div class="center">
									<input type='button' value='all' id="resetAll" onclick='reset("all")' class='hover'>
									<input type='button' value='list' id="resetList" onclick='reset("list")' class='hover'>
								</div>
								<div class="center">
									<input type='button' value='selection' id="resetSelection" onclick='reset("selection")' class='hover'>
								</div>
							</div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				
<!-- SORT FOR FULL SCREEN VERSION -->
				<div class="sort noMobile">
					<table>
						<tr>
							<td id="title">
								<div class="center noShowEdit">
									<div id="asc" class="pointer circle" onclick="tableSort('asc')"><div class="hover arrows">&#9650;</div></div>
									<div id="desc" class="pointer circle" onclick="tableSort('desc')"><div class="hover arrows">&#9660;</div></div>
								</div>
							</td>
							<td id="artist"></td>
							<td id="number"  width="68px">
								<div class="center noShowEdit">
									<div id="numa" class="pointer circle" onclick="tableSort('numa')"><div class="hover arrows">&#9650;</div></div>
									<div id="num" class="pointer circle" onclick="tableSort('num')"><div class="hover arrows">&#9660;</div></div>
								</div>
							</td>
							<td width="34px">
								<div class="noShowEdit">
									<div id="youtube" class="pointer circle" ><div class="hover arrows"><input type="checkbox" id="utb" onclick="tableFilter()"></div></div>
								</div>
							</td>
							<td id="control">
								<div class="center">
									<div id="print" class="pointer circle pIcons" onclick="window.print()"><div class="hover arrows"><img src="img/print.png" height="20px"></div></div>
									<div id="finished" class="pointer circle pIcons" onclick="back()"><div class="hover arrows"><img src="img/finished.png" height="20px"></div></div>
									<div id="edit" class="pointer circle pIcons" onclick="switch2('printList')"><div class="hover arrows"><img id="editImg" src="img/edit.png" height="20px"></div></div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div id="line"></div>
				
<!-- MOBILE NAVIGATION -->
				<div id="mobileNav">
					<div class="navPoints">
						<div class="navCircle">
							<img src="img/search.png" onclick="mobileTab(this,'search')">
						</div>
					</div>		
					<div class="navPoints">
						<div class="navCircle">
							<img src="img/numberRange.png" onclick="mobileTab(this,'numberRange')">
						</div>
					</div>		
					<div class="navPoints">
						<div class="navCircle">
							<img src="img/sort.png" onclick="mobileTab(this,'sort')">
						</div>
					</div>		
					<div class="navPoints">
						<div class="navCircle">
							<img src="img/reset.png" onclick="reset('all')">
						</div>
					</div>		
					<div class="navPoints">
						<div class="navCircle">
							<img src="img/settings.png" onclick="mobileTab(this,'settings')">
						</div>
					</div>	
					<div id="mobileDisplaySong">
						<div class="navPoints">
							<div class="navCircle">
								<img src="img/finished.png" onclick="back()">
							</div>
						</div>
					</div>
				</div>

<!-- Sort for Mobile -->
				<div id="sortTab" class="tab">
					<h1>Sort</h1>
					<table width="100%">
						<tr>
							<td>Alphabetic</td>
							<td>Numeric</td>
						</tr>
						<tr class="arrows">
							<td onclick="mobileSort('asc')">&#9650;</td>
							<td onclick="mobileSort('numa')">&#9650;</td>
						</tr>
						<tr class="arrows">
							<td onclick="mobileSort('desc')">&#9660;</td>
							<td onclick="mobileSort('num')">&#9660;</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="content">
				<!-- SongList -->
				<div id="result"></div>
				<!-- showSong -->
				<div id="showSong"></div>
			</div>
			<!-- preload the images -->
			<div style='display:none'>
				
			</div>
			<div id="settingsTab">
				<h1>Settings</h1>
				<input type="checkbox" id="intro" onclick="settings(this)" checked> Intro
			</div>
		</div>
		<div id="showList">
			<div class='yesPrint'>
				<table id='printList' width="565px">
					<tr>
						<th>Number</th>
						<th>Song Title</th>
					</tr>
				</table>
				<table id='updown' width="30px">
					<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</table>
				<table id='delete' width="15px">
					<tr>
						<th>&nbsp;</th>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>