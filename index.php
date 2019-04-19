<html>
	<head>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script>
			var ajaxurl="Utils.php"
			var browser="Unknown Browser";
			var DateTime="None";
			var OSName="Unknown OS";
			var geolocation="Unknown Location";

			window.onload = function(){
				getBrowser();
				getOS();
				getDateTime();
				getLocation();
			}

			function loginuser(){
				var username = $("#username").val();
				var password = $("#password").val();
				console.log(username, password);
				console.log(browser, OSName, datetime, geolocation);
				if (username && password){
					$.post(ajaxurl, {'action':'checkcreds','username':username,'password':password}, function (response) {
						if(response=="true"){
							$.post(ajaxurl, {'action':'login','username':username,'password':password,'OSName':OSName,'browser':browser,'datetime':datetime,'geolocation':geolocation}, function (response) {
								window.location.href = "newsfeed.php";
							});
						} else {
							document.getElementById("msg").innerHTML = "Incorrect Credentials";
						}
					});
				} else {
					document.getElementById("msg").innerHTML = "Please fill out all fields";
				}
			}
			function getBrowser() {
				if (navigator.userAgent.search("MSIE") !=-1) browser="Internet Explorer";
				else if (navigator.userAgent.search("Chrome") !=-1) browser="Chrome";
				else if (navigator.userAgent.search("Firefox") !=-1) browser="Firefox";
				else if (navigator.userAgent.search("Safari") !=-1) browser="Safari";
				else if (navigator.userAgent.search("Opera") !=-1) browser="Opera";
			}
			
			function getDateTime(){
				const months = {
					1: "January",2: "February",3: "March",4: "April",5: "May",6:"June",
					7:"July",8:"August",9:"September",10:"October",11:"November",12:"December"
				}
				const weekdays = {
					1: "Monday",2: "Tuesday",3: "Wedneday",4: "Thursday",5: "Friday",6: "Saturday",7: "Sunday"
				}
				var currentdate = new Date();
				datetime = (
					weekdays[currentdate.getDay()] + ", "
					+ months[(currentdate.getMonth()+1)]  + " "
					+ currentdate.getDate() + " "
					+ currentdate.getFullYear() + " at "
					+ ("0" + currentdate.getHours()).slice(-2) + ":"  
					+ ("0" + currentdate.getMinutes()).slice(-2)
				);
			}
			
			function getOS(){
				if (navigator.appVersion.indexOf("Win")!=-1) OSName="Windows";
				else if (navigator.appVersion.indexOf("Mac")!=-1) OSName="MacOS";
				else if (navigator.appVersion.indexOf("X11")!=-1) OSName="UNIX";
				else if (navigator.appVersion.indexOf("Linux")!=-1) OSName="Linux";
				else if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					OSName="Mobile";
				}
			}

			function getLocation() {
				navigator.geolocation.getCurrentPosition(function(position) {
					geolocation={'Latitude':position.coords.latitude, 'Longitude':position.coords.longitude};
					console.log(geolocation)
				});
			}
		</script>
	</head>
	<body>
		<div id="headerbanner">
			<div class="page">
				<div id="header">
					<img id="logo" src="Static/logo-espn-82x20.png" /><h1 id="title">Sports News</div>
				</div>
			</div>
		</div>
		<div class="page">
			<div class = "form" >
				<h2>Login</h2>
				<h4 class = "form-signin-heading"><p id="msg"></p></h4>
				<div class="formrow"><b class="label">Username:</b> <input type = "text" name = "username" id="username" required autofocus></div>
				<div class="formrow"><b class="label">Password:</b> <input type = "password"  name = "password" id="password" required></div>
				<button class = "button" onclick="loginuser()">Login</button>
				<p> Need an account? Register <a href="register.php"> here. </p>
			</div>
		</div> 
   </body>
</html>