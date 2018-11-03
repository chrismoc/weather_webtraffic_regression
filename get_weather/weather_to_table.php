<?php
// ---- WEATHER PREFERENCES

$key = "xyz"; // your secret darksky key
$latitude = "52.436458"; // type in latitude of location
$longitude = "13.313740"; // type in longitde of location
$units = "ca"; // eg. type "ca" for celsius degree and metric system (wind speed in km/h)


// ---- DATABASE (MYSQL) PREFERENCES

$servername = "xyz"; // enter servername
$username = "xyz"; // enter username
$password = "xyz"; // enter password
$dbname = "xyz"; // enter database


// ---- TABLE PREFERENCES (COLUMN NAMES)
$table = "xyz"; // enter tablename data will be written into
$atemp = "xyz"; //enter name for column avg. temp will be written into
$precib = "xyz"; //enter name for column precib will be written into
$precpr = "xyz"; //enter name for column precib probability will be written into
$sum = "xyz"; //enter name for column short summary will be written into
$date = "xyz"; //enter name for column constructed date will be written into  

// ---- GET WEATHER DATA

function getWeatherData($k, $la, $lo, $un)
	{
    
    // construct request (fetch data for today & next 5 days) and get data
    $link = 'https://api.darksky.net/forecast/' . $k . '/' . $la . ',' . $lo . '?units=' . $un . '&exclude=currently,minutely,hourly,alerts';
    $a = file($link);
	$dim = json_decode($a[0], TRUE);
    
    // get values for current day (avg. temp, precib, short summary) and put into array. Do some mini calc to floor values and get avg. temperature
	$avgTemp = floor(($dim['daily']['data']['0']['temperatureLow'] + $dim['daily']['data']['0']['temperatureHigh']) / 2);
	$precty = $dim['daily']['data']['0']['precipType'];
	$precprob = floor($dim['daily']['data']['0']['precipProbability'] * 100);
	$sum = $dim['daily']['data']['0']['icon'];
	$today = getdate();
	$todarr = [$today["year"], $today["mon"], $today["mday"]];
	$rdate = implode("-", $todarr);
    
    //return array
    return [$avgTemp, strval($precty) , $precprob, strval($sum) , strval($rdate) ];
	}
    
    
// ---- OPEN CONNECTION AND APPEND DATA   

function connectDb($dataarr, $sn, $un, $pw, $db, $ta, $te, $pre, $prep, $su, $da)
	{

	// Create connection
    $conn = new mysqli($sn, $un, $pw, $db);

	// Check connection
    if ($conn->connect_error)
		{
		die("Connection failed: " . $conn->connect_error);
		}
    
    // Insert array into table !! watch out for column names and order!!
	$sql = "INSERT INTO $ta ($te, $pre, $prep, $su, $da)
    VALUES ('$dataarr[0]', '$dataarr[1]', '$dataarr[2]', '$dataarr[3]', '$dataarr[4]')";
	if ($conn->query($sql) === TRUE)
		{
		echo "New record created successfully";
		}
	  else
		{
		echo "Error: " . $sql . "<br />" . $conn->error;
		}

	$conn->close();
	}
    
 
// ---- EXECUTE

connectDb(getWeatherData($key, $latitude, $longitude, $units), $servername, $username, $password, $dbname, $table, $atemp, $precib, $precpr, $sum, $date);
?>    