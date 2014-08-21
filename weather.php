<?php
$ip = $_SERVER['REMOTE_ADDR']; //put your IP address here
$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));

/*

-----------------These will help you to get the name of the city and additional details for it------------------------------


//print_r($details);
//echo $details->city;
//echo $city;

*/


$placeName =$details->city; // you put your location
         $yql_base_url = "http://query.yahooapis.com/v1/public/yql"; //calling the yahoo api for searching woeid using the name of the city  
         $yql_query    = "select * from geo.places  where text='".strtoupper($placeName)."'";
         $yql_query_url = $yql_base_url . "?q=" . urlencode($yql_query);
         $yql_query_url .= "&format=json";
         $session = curl_init($yql_query_url);
         curl_setopt($session, CURLOPT_RETURNTRANSFER,true);     
         $json   = curl_exec($session);   
         $phpObj =  json_decode($json);
		// print_r($phpObj);
		 $count= $phpObj->query->count;
		 
		 if($count==1)//if the results have only 1 matching cities
		 {
			 $codeid=$phpObj->query->results->place->woeid; //parse the result to get the zip code of the location
		 }
		 else ////if the results have more than one matching cities
		 {
			 $codeid=$phpObj->query->results->place[0]->woeid;  //parse the result to get the zip code of the location
		 }




$_POST['zipcode']=$codeid; //if you already have a zip code you can put it here.

if(isset($_POST['zipcode']) && is_numeric($_POST['zipcode'])){
    $zipcode = $_POST['zipcode'];
}else{
    $zipcode = '50644'; //setting a deafult zip code if the zip code is not available
}

//now pull the weather foorecast using yahoo weather api
$result = file_get_contents('http://weather.yahooapis.com/forecastrss?w=' . $zipcode . '&u=c');
$xml = simplexml_load_string($result);
 

 
$xml->registerXPathNamespace('yweather', 'http://xml.weather.yahoo.com/ns/rss/1.0');
$location = $xml->channel->xpath('yweather:location');
 
if(!empty($location)){
    foreach($xml->channel->item as $item){
        $current = $item->xpath('yweather:condition');
        $forecast = $item->xpath('yweather:forecast');
        $current = $current[0];
        $output = <<<END
END;
    }
}else{
    $output = '<h1>No results found, please try a different zip code.</h1>';
}



$city="{$location[0]['city']}, {$location[0]['region']}"; //get the name of the city
$temprature="{$current['temp']}&deg;C"; //get the temperature of the city
$updatedate="{$current['date']}"; //date and time of last update of the forecast
$img="http://l.yimg.com/a/i/us/we/52/{$current['code']}.gif"; //get a image based on the weather conditions
$wtext="&nbsp;{$current['text']}"; //get the description of the forecast



//you can see a live example on my website www.talkingupdate.com----------------------------------------------------

?>
