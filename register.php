<html>
	<head>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<link rel="stylesheet" href="style.css" type="text/css" />
	</head>
	<body>
		<script>
			var ajaxurl="Utils.php";
			function registeruser(){
				var username = $("#username").val();
				var password = $("#password").val();
				var password2 = $("#password2").val();
				console.log(username, password, password2, password===password2);
				if (password && password2 && username){
					if (password===password2){
						var valid='false';
						$.post(ajaxurl, {'action':'checkuser','username':username,'password':password}, function (response) {
							valid=response;
							if (valid=="true"){
								$.post(ajaxurl, {'action':'register','username':username,'password':password}, function (response) {
									window.location.href = "index.php";
									alert(username+ ", you are registered!")
								});
							} else {
								document.getElementById("msg").innerHTML = "Please choose a new username";
							}
						});
					} else {
						document.getElementById("msg").innerHTML = "Passwords do not match";
					}
				} else {
					document.getElementById("msg").innerHTML = "Please fill out all fields";
				}
			}
		</script>
		<div id="headerbanner">
			<div class="page">
				<div id="header">
					<img id="logo" src="Static/logo-espn-82x20.png" /><h1 id="title">Sports News</div>
				</div>
			</div>
		</div>
		<div class="page">
			<div class = "form">
				<h2>Register</h2>
				<h4><p id="msg"></p></h4>
				<div class="formrow"><b class="label">Username:</b> <input type = "text" name = "username" id="username" required autofocus></div>
				<div class="formrow"><b class="label">Password:</b> <input type = "password"  name = "password" id="password" required></div>
				<div class="formrow"><b class="label"> Verify Password:</b> <input type = "password"  name = "password2" id="password2" required></div>
				<div class="formrow">
					<button class = "button" type = "submit" name = "back" value="back" onclick="location.href='index.php';">Back</button>
					<button class = "button" onclick="registeruser()">Register</button>
				</div>
			</div>
		</div> 
   </body>
</html>