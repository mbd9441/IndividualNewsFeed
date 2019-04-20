<?php
	session_start();
	if (empty($_SESSION["username"])){
		echo "<script> location.href='index.php'; </script>";
	} else {
		$username = $_SESSION["username"];
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
 	<title>News</title>
	<!-- Import jQuery -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<link rel="stylesheet" href="style.css" type="text/css" />
	<script>
		//this forces javascript to conform to some rules, like declaring variables with var
		"use strict";
		var ajaxurl = 'Utils.php'
		var newsitems = [];
		var sports = {
			"NFL":"http://www.espn.com/espn/rss/NFL/news",
			"NBA":"http://www.espn.com/espn/rss/NBA/news",
			"MLB":"http://www.espn.com/espn/rss/MLB/news",
			"NHL":"http://www.espn.com/espn/rss/NHL/news"
		}
		var url=sports["NFL"]
		var checked={};

		window.onload = function(){
			init(url);
		}

		function init(url){	
			makeSelectors();
			getlatestlogin();
			initCheckBoxes();
			onChange();
			document.querySelector("#content").innerHTML = "<b>Loading news...</b>";
			$("#content").fadeOut(250);
		}
		
		function initCheckBoxes(){
			var checkboxes = document.querySelectorAll('input[type=checkbox]');
			for(var i = 0; i < checkboxes.length; i++) {
				checkboxes[i].addEventListener('change', function(){
					onChange();
				});
			}
		}
		
		function getlatestlogin(){
			$.get(ajaxurl, {'action':'getlogin'}, function (response) {
				var latestlogin=JSON.parse(JSON.parse(response.valueOf()));
				var datetime=latestlogin.datetime;
				var html="<p class='username'> <?php echo $username; ?> <img class='logout' value='Logout' onclick='logout();' src='/Static/Logout.png'></p><p class='datetime'>Last logged in<br/>" +  datetime + "</p>";
				document.querySelector("#userdisplay").innerHTML = html;
			});
		}

		function getlogins(){
			$.get(ajaxurl, {'action':'getlogins'}, function (response) {
				console.log(response);
			});
		}
		
		function logout(){
			$.post(ajaxurl, {'action':'logout'}, function (response) {
				window.location.href = "index.php";				
			});
		}

		function makeSelectors(){
			var html="<form id='selectionform'>";
			for (var i in sports){
				html +="<input class='selector' type='checkbox' name='sport' value="+i+" checked onClick()>"+i
			}
			html += "<img class='refresh' onclick='onChange();' src='/Static/Refresh.png'> Refresh"
			document.querySelector("#headerselectors").innerHTML = (html+"</form>");
		}	

		function onChange(){
			newsitems=[];
			var checkboxes = document.querySelectorAll('input[type=checkbox]');
			for(var i = 0; i < checkboxes.length; i++) {
				checked[checkboxes[i].value] = checkboxes[i].checked;
			}
			loadLists();
			xmlLoaded();
		}

		async function loadLists(){
			for (var i in checked){
				if (checked[i]){
					$.get(sports[i]).done(function(data){addXMLToList(data);});
				}
			}
		}

		function addXMLToList(data){
			var items = data.querySelectorAll("item");
			for (var i=0;i<items.length;i++){
				var newsItem = items[i];
				newsitems.push(newsItem)
			}
		}

		
		function favorite(number){
			let data = newsitems[number];
			var xmlText = new XMLSerializer().serializeToString(data);
			$.post(ajaxurl, {'action':'checkfavorite','article':xmlText}, function (response) {
				if (response=="false"){
					$.post(ajaxurl, {'action':'favorite','article':xmlText}, function (response) {} );
					alert("You favorited this article! Check the favoritesd tab to view it!")
				}
			} else {
				alert("you already favorited this article!")
			}
			} );
		}

		function unfavorite(number){
			let data = newsitems[number];
			var xmlText = new XMLSerializer().serializeToString(data);
			$.post(ajaxurl, {'action':'unfavorite','article':xmlText}, function (response) 
			{
				loadfavorites();
				alert("You unfavorited the article")
			} );
		}
		
		function loadfavorites(){
			var parser, xmlDoc;
			document.querySelector("#favetoggle").innerHTML="<input type='submit' class='button' name='favorites' value='News' onclick='loadnews();' />";
			document.querySelector("#content").innerHTML = '';
			document.querySelector("#headerselectors").innerHTML = '';
			$.get(ajaxurl, {'action':'getfavorites'}, function (response) {
				newsitems=[];
				if (response.length>0){
					var xml="<root>";
					var faves=JSON.parse(JSON.parse(response.valueOf()));
					for (var fave in faves){
						xml += faves[fave].article;
					}
					xml+="</root>";
					parser = new DOMParser();
					xmlDoc = parser.parseFromString(xml,"text/xml");
					addXMLToList(xmlDoc);
				}
				xmlLoaded(true);
			});
		}
		
		function loadnews(){
			document.querySelector("#favetoggle").innerHTML="<input type='submit' class='button' name='favorites' value='Favorites' onclick='loadfavorites();' />";
			makeSelectors();
			onChange();
			initCheckBoxes();
		}
		
		async function xmlLoaded(isfavorites=false){
			setTimeout(function(){
				var html = "";
				newsitems.sort(function(a,b) {
					if (b.querySelector("pubDate") && a.querySelector("pubDate")){
						return new Date(b.querySelector("pubDate").firstChild.nodeValue)-new Date(a.querySelector("pubDate").firstChild.nodeValue);
					}else{
						return 0;
					}
				});
				if(newsitems.length<1){
					document.querySelector("#content").innerHTML = "<p>No News for You!</p>";
				}else{
					for (var i=0;i<newsitems.length;i++){
						//get the data out of the item
						var newsItem = newsitems[i];
						//console.log(newsitems[i])
						var title = newsItem.querySelector("title").firstChild.nodeValue;
						if(newsItem.querySelector("image")){
							var image = newsItem.querySelector("image").firstChild.nodeValue;
						}
						var description = newsItem.querySelector("description").firstChild.nodeValue;
						var link = newsItem.querySelector("link").firstChild.nodeValue;
						var pubDate = newsItem.querySelector("pubDate").firstChild.nodeValue;
						//present the item as HTML
						var line = '<div class="item">';
						line += "<div class='headerrow'>";
						line += "<h2>"+title+"</h2>"
						if (isfavorites){
							line += "<img class='favorite-me' onclick='unfavorite("+i+");' src='/Static/UnStar.png'>"
						} else {
							line += "<img class='favorite-me' onclick='favorite("+i+");' src='/Static/Star.png'>"
						}
						line += "<a href="+link+" target='_blank'><img class='lilicon'  src='/Static/Open.png'></a>"
						line+="</div>";
						if (image) {
							line += "<img class='newspic' src="+image+">";
						}
						line += "<div class = 'blob'> <p>"+description+"<p></div>"
						line += "<div class='footerrow'><i>"+pubDate+"</i></div>";
						line += "</div>";
						
						html += line;
					}
					document.querySelector("#content").innerHTML = html;
				}
				$("#content").fadeIn(1000);
			}, 500);
		}
	</script>
</head>
<body>
	<div id="headerbanner">
		<div class="page">
			<div id="header">
				<img id="logo" src="Static/logo-espn-82x20.png" /><h1 id="title">Sports News</h1><div id="userdisplay"></div>
			</div>
		</div>
	</div>
	<div class="page">
		<div class="selectorcontainer">
			<div id="headerselectors">
				<p> No selectors </p>
			</div>
			<div id="favetoggle">
				<input type='submit' class='button' name='favorites' value='Favorites' onclick='loadfavorites();' />
			</div>
		</div>
		<div id="content">
			<p>No data has been loaded.</p>
		</div>
	 </div>

</body>
</html>
