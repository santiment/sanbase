<?php
/**
 * Created by PhpStorm.
 * User: jhildings
 * Date: 2017-08-05
 * Time: 15:46
 */

$servername = "localhost";
$username = "sanbase";
$password = "sanbase";
$database = "postgres";

// Create connection
$conn_string = "host=".$servername." dbname=". $database ." user=".$username." password=".$password;
$conn = pg_connect($conn_string);
if (!$conn) {
    $error = error_get_last();
    die("Connection failed: " . $error["message"]);
}


$result = json_decode(file_get_contents('https://api.coinmarketcap.com/v1/ticker/'),true);

// Process coins in order reverse to the market valuation. In this way
// we can deal with the fake "ICN" coin which otherwise overwrites the
// real market cap with a very small number

$result = array_reverse($result);

foreach($result as $r){
    //check if ticker is in table
    $sql = "SELECT * from cmm_data where ticker='" . $r['symbol'] . "'";
    $result = pg_query($conn, $sql);
    if(pg_num_rows($result) === 0){
        //echo "Inserting ticker ".$r['symbol']."\n";
        $sql = "INSERT INTO cmm_data (ticker,market_cap,price_usd,active) VALUES ('".$r['symbol']."', '".$r['market_cap_usd']."','".$r['price_usd']."', 1)";
    }
    else
    {
        //echo "Updating ticker ".$r['symbol']."\n";
        $sql = "UPDATE  cmm_data SET market_cap = " . $r['market_cap_usd'] . " , price_usd= ".$r['price_usd']."  WHERE ticker = '".$r['symbol']."'";
    }
    echo $sql;
    echo "\n";
    pg_query($conn, $sql);
}



?>
