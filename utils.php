<?php
session_start();

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
		case 'logout':
			logout();
			break;
		case "login":
			login($_POST);
			loglogin($_POST);
			break;
		case "register":
			register($_POST);
			break;
		case "checkuser":
			checkuser($_POST);
			break;
		case "checkcreds":
			checkcreds($_POST);
			break;
		case "checkfavorite":
			checkfavorite($_POST);
			break;
		case "favorite":
			favorite($_POST);
			break;
		case "unfavorite":
			unfavorite($_POST);
			break;
    }
}
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
		case "getlogins":
			getlogins();
			break;
		case "getlogin":
			getlatestlogin();
			break;
		case "getfavorites":
			getfavorites();
			break;
    }
}

function checkuser($post){
	$FILEPATH = 'Data/Users.txt';
	$openedfile = fopen($FILEPATH,"r");
	$userdata = (Array) [
    'username' => $post['username'],
    'password' => $post['password']
	];
	$text = fread($openedfile,filesize($FILEPATH));
	$valid='true';
	$users=explode(',,',$text);
	fclose($openedfile);
	foreach ($users as $user){
		$curuser=json_decode($user, true);
		if(($curuser['username']==$userdata['username'])==1){
			echo "false";
			exit;
		}
		}
	echo "true";
	exit;
}

function register($post) {
	$FILEPATH = 'Data/Users.txt';
	$openedfile = fopen($FILEPATH,"a");
	$userdata = (object) [
    'username' => $post['username'],
    'password' => $post['password']
	];
	$userjson=json_encode($userdata);
	fwrite($openedfile,$userjson.",,");
	fclose($openedfile);
    exit;
}

function checkcreds($post){
	$FILEPATH = 'Data/Users.txt';
	$openedfile = fopen($FILEPATH,"r");
	$userdata = (Array) [
    'username' => $post['username'],
    'password' => $post['password']
	];
	$text = fread($openedfile,filesize($FILEPATH));
	$auth='true';
	$users=explode(',,',$text);
	fclose($openedfile);
	foreach ($users as $user){
		$curuser=json_decode($user, true);
		//print_r([($curuser['username']==$userdata['username']),($curuser['password']==$userdata['password'])]);
		if(($curuser['username']==$userdata['username'])==1){
			if(($curuser['password']==$userdata['password'])==1){
				echo "true";
				exit;
			}
		}
	}
	echo "false";
	exit;
}

function login($post) {
	$userdata = (Array) [
    'username' => $post['username'],
    'password' => $post['password']
	];
	$userjson=json_encode($userdata);
	$_SESSION["username"]=$post['username'];
    return;
}

function loglogin($post){
	$FILEPATH = 'Data/LoginLog.txt';
	$openedfile = fopen($FILEPATH,"a");
	$userdata = (Array) [
    'username' => $post['username'],
    'password' => $post['password'],
	'OSName' => $post['OSName'],
	'browser' => $post['browser'],
	'datetime' => $post['datetime'],
	'geolocation' => $post['geolocation']
	];
	$userjson=json_encode($userdata);
	fwrite($openedfile,$userjson.",,");
	fclose($openedfile);
	return;
}

function getlatestlogin(){
	$FILEPATH = 'Data/LoginLog.txt';
	$openedfile = fopen($FILEPATH,"r");
	$text = fread($openedfile,filesize($FILEPATH));
	$alllogins=explode(',,',$text);
	$username = $_SESSION["username"];
	$mylogins='';
	fclose($openedfile);
	foreach ($alllogins as $login){
		$curlogin=json_decode($login, true);
		if(($curlogin['username']==$username)==1){
			$curloginstring=json_encode($curlogin);
			$mylogins = $curloginstring;
		}
	}
	echo json_encode($mylogins);
	return;
}

function getlogins(){
	$FILEPATH = 'Data/LoginLog.txt';
	$openedfile = fopen($FILEPATH,"r");
	$text = fread($openedfile,filesize($FILEPATH));
	$alllogins=explode(',,',$text);
	$username = $_SESSION["username"];
	$mylogins='';
	fclose($openedfile);
	foreach ($alllogins as $login){
		$curlogin=json_decode($login, true);
		if(($curlogin['username']==$username)==1){
			$curloginstring=json_encode($curlogin);
			$mylogins .= $curloginstring;
		}
	}
	echo json_encode($mylogins);
	return;
}

function checkfavorite($post){
	$FILEPATH = 'Data/Favorites.txt';
	$openedfile = file_get_contents($FILEPATH);
	$username = $_SESSION["username"];
	$article = $post["article"];
	$favorite = (Array) [
		'username' => $username,
		'article' => $article
	];
	$favoritejson=json_encode($favorite);
	$ishere=strpos($openedfile,$favoritejson);
	if (is_numeric($ishere)){
		echo "true";
	} else {
		echo "false";
	}
	return;
}

function favorite($post){
	$FILEPATH = 'Data/Favorites.txt';
	$openedfile = fopen($FILEPATH,"a");
	$username = $_SESSION["username"];
	$article = $post["article"];
	$favorite = (Array) [
		'username' => $username,
		'article' => $article
	];
	$favoritejson=json_encode($favorite);
	fwrite($openedfile,$favoritejson.",,");
	fclose($openedfile);
	return;
}

function unfavorite($post){
	$FILEPATH = 'Data/Favorites.txt';
	$openedfile = file_get_contents($FILEPATH);
	$username = $_SESSION["username"];
	$article = $post["article"];
	$favorite = (Array) [
		'username' => $username,
		'article' => $article
	];
	$favoritejson=json_encode($favorite);
	$openedfile = str_replace($favoritejson.",,","",$openedfile);
	file_put_contents($FILEPATH, $openedfile);
	return;
}

function getfavorites(){
	$FILEPATH = 'Data/Favorites.txt';
	$openedfile = fopen($FILEPATH,"r");
	if (filesize($FILEPATH)!=0){
		$text = fread($openedfile,filesize($FILEPATH));
	} else {
		return;
	}
	$allfaves=explode(',,',$text);
	$username = $_SESSION["username"];
	$myfaves='[';
	fclose($openedfile);
	foreach ($allfaves as $fave){
		$curfave=json_decode($fave, true);
		if(($curfave['username']==$username)==1){
			$curfavestring=json_encode($curfave);
			$myfaves .= $curfavestring.",";
		}
	}
	$myfaves=substr($myfaves,0,-1);
	echo json_encode($myfaves.']');
	return;
}

function logout() {
	$_SESSION["username"] = "";
    exit;
}


?>