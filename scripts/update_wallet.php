<?php

//List of wallets, address + description
// $whaleWallets = [

//     ['0xa72dc46ce562f20940267f8deb02746e242540ed', 'EOS Wallet Address'],
//     ['0xfba4ee9f16566d048c56893e993188d7b67ac5a9', 'EOS Transfer Address'],
//     ['0x9a642d6b3368ddc662CA244bAdf32cDA716005BC', 'qtum', 'QTUM'],
//     ['0xd0a6e6c54dbc68db5db3a091b171a77407ff7ccf', 'EOS', 'EOS'],
//     ['0x8f3470A7388c05eE4e7AF3d01D8C722b0FF52374', 'Veritaseum', 'VERI'],
//     ['0xd26114cd6EE289AccF82350c8d8487fedB8A0C07', 'OmiseGo', 'OMG'],
//     ['0x7da82c7ab4771ff031b66538d2fb9b0b047f6cf9', 'Golem', 'GNT'],
//     ['0x154Af3E01eC56Bc55fD585622E33E3dfb8a248d8', 'Iconomi', 'ICN'],
//     ['0x851b7F3Ab81bd8dF354F0D7640EFcD7288553419', 'Gnosis', 'GNO'],
//     ['0xA646E29877d52B9e2De457ECa09C724fF16D0a2B', 'Status', 'SNT'],
//     ['0x185f19B43d818E10a31BE68f445ef8EDCB8AFB83', 'TenX', 'PAY'],
//     ['0xE28e72FCf78647ADCe1F1252F240bbfaebD63BcC', 'Augur', 'REP'],
//     ['0x7c31560552170ce96c4a7b018e93cddc19dc61b6', 'Basic Attention Token', 'BAT'],
//     ['0x1a8CC57F1155D7dF4626978401e203E00DA097f7', 'Populous', 'PPT'],
//     ['0xf0160428a8552ac9bb7e050d90eeade4ddd52843', 'DigixDAO', 'DGD'],
//     ['0x93E682107d1E9defB0b5ee701C71707a4B2E46Bc', 'MCAP', 'MCAP'],
//     ['0x5894110995B8c8401Bd38262bA0c8EE41d4E4658', 'Bancor', 'BNT'],
//     ['0x40395044Ac3c0C57051906dA938B54BD6557F212', 'MobileGo', 'MGO'],
//     ['0x6cc2d616e56e155d8a06e65542fdb9bd2d7f3c2e', 'Aeternity Stage 1', 'AE'],
//     ['0x93B7e9364C4DF6De55eD0D10c80E7aFC0612e93A', 'Metal', 'MTL'],
//     ['0x5901Deb2C898D5dBE5923E05e510E95968a35067', 'SingularDTV', 'SNGLS'],
//     ['0x2323763D78bF7104b54A462A79C2Ce858d118F2F', 'Civic', 'CVC'],
//     ['0xcafE1A77e84698c83CA8931F54A755176eF75f2C', 'Aragon', 'ANT'],
//     ['0xa5384627F6DcD3440298E2D8b0Da9d5F0FCBCeF7', 'FirstBlood', '1ST'],
//     ['0x419D0d8BdD9aF5e606Ae2232ed285Aff190E711b', 'FunFair', 'FUN'],
//     ['0x667088b212ce3d06a1b553a7221E1fD19000d9aF', 'Wings', 'WINGS'],
//     ['0x24C3235558572cff8054b5a419251D3B0D43E91b', 'Etheroll', 'DICE'],
//     ['0x08711D3B02C8758F2FB3ab4e80228418a7F8e39c', 'Edgeless', 'EDG'],
//     ['0x4993CB95c7443bdC06155c5f5688Be9D8f6999a5', 'Round', 'ROUND'],
//     ['0x1e549606B695423368e851fF13Edef7eA4790Fe9', 'Melon', 'MLN'],
// ];

//For the future, add more exchange wallets
$exchangeWallets = [
    ['0x7727E5113D1d161373623e5f49FD568B4F543a9E', 'Bitfinex_Wallet2']
];

$servername = getenv("DB_SERVER");
$database = getenv("DB_DATABASE");
$username = getenv("DB_USER");
$password = getenv("DB_PASSWORD");

// Create connection
$conn_string = "host=".$servername." dbname=". $database ." user=".$username." password=".$password;
$conn = pg_connect($conn_string);
if (!$conn) {
    $error = error_get_last();
    die("Connection failed: " . $error["message"]);
}

//Set timezone
date_default_timezone_set("UTC");

/** 
 * @return array
 *
 */
function getWallets($conn)
{
    $wallets = [];
    $sql = "SELECT address, name, ticker FROM wallet_data";
    $result = pg_query($conn, $sql);

    while ($row = pg_fetch_array($result, NULL, PGSQL_NUM))
    {
        $wallets[] = $row;
    }

    return $wallets;
}

/**
 * @param  $wallet array
 * @return array
 */
function getInternalTx($wallet)
{
    $txData = [];
    $txData['date'] = "No transfers yet";
    $txData['value'] = 0;

    $txRoute = 'http://api.etherscan.io/api?module=account&action=txlistinternal&address=' . $wallet . '&startblock=0&endblock=99999999&sort=asc&apikey=apitoken';
    $res = json_decode(file_get_contents($txRoute), $assoc = true);
    if (!empty($res)) {
        $lastIndex = array_slice($res['result'], -1);
        if (!empty($lastIndex[0]['timeStamp'])) {
            $txData['date'] = date("Y-m-d", $lastIndex[0]['timeStamp']);
        }
        if (!empty($lastIndex[0]['value'])) {

            $txData['value'] = round(($lastIndex[0]['value'] / 1000000000000000000), 2);
        }

        return $txData;
    } else {
        echo "API is down";
        throw new Exception("API is down");
    }

}

/**
 * @param  $wallet array
 * @return array
 */
function getOutgoingTx($wallet)
{
    $txData = [];
    $txData['date'] = "No recent transfers";
    $txData['value'] = 0;

    $txRoute = 'http://api.etherscan.io/api?module=account&action=txlist&address=' . $wallet . '&startblock=0&endblock=99999999&sort=asc&apikey=apitoken';
    $res = json_decode(file_get_contents($txRoute), $assoc = true);
    if (!empty($res)) {
        $lastIndex = array_slice($res['result'], -1);
        $txDate = null;
        $inValue = null;
        // echo $wallet;
        // echo "\n";
        // echo $lastIndex[0]['from'];
        // echo "\n";
        //Outgoing if same address as from as contract/wallet
        if ($lastIndex[0]['from'] === strtolower($wallet)) {
            echo 'found outgoing';
            if (!empty($lastIndex[0]['timeStamp'])) {
                $txData['date'] = date("Y-m-d", $lastIndex[0]['timeStamp']);
            }
            if (!empty($lastIndex[0]['value'])) {

                $txData['value'] = round(($lastIndex[0]['value'] / 1000000000000000000), 2);
            }
        }
        return $txData;
    } else {
        echo "API is down";
        throw new Exception("API is down");
    }

}

/**
 * @param  $address string
 * @return string
 */
function getBalance($address)
{
    $balanceRoute = 'https://api.etherscan.io/api?module=account&action=balance&address=' . $address . '&tag=latest&apikey=apitoken';
    $res = json_decode(file_get_contents($balanceRoute), $assoc = true);
    if (!empty($res)) {
        $balance = round($res['result'] / 1000000000000000000, 2);
        return $balance;
    } else {
        echo "API is down";
        throw new Exception("API is down");
    }
}

?>

<?php

$whaleWallets = getWallets($conn);

foreach ($whaleWallets as $k => $wa) :
    $internalTx = getInternalTx($wa[0]);
    $outgoingTx = getOutgoingTx($wa[0]);
    $balance = getBalance($wa[0]);
    $walletAddress = $wa[0];
    $walletName = $wa[1];
    $ticker = $wa[2];
    $lastInBalance = $internalTx['value'];
    $lastInDate = $internalTx['date'];
    $lastOutBalance = $outgoingTx['value'];
    $lastOutDate = $outgoingTx['date'];

  //  $sql = 'INSERT INTO wallet_data (name, address,	balance, last_incoming,last_outgoing, tx_out, tx_in,ticker,update_date) VALUES
 //   ("'.$walletName.'",
//    "'.$walletAddress.'",
//    '. $balance . ',
//  "'.$lastInDate.'",
//  "'.$lastOutDate.'",
//  '.$lastOutBalance.',
//  '. $lastInBalance.',
//  "'.$ticker.'",
//  NOW()
//   )';

    $sql = "UPDATE  wallet_data  SET balance = '".$balance."'  , last_incoming = '".$lastInDate."' , last_outgoing = '".$lastOutDate."',  tx_out =  '".$lastOutBalance."' , tx_in =  ".$lastInBalance.", update_date=NOW() WHERE address = '".$walletAddress."'";
    echo $sql;
    pg_query($conn, $sql);
    ?>

<?php endforeach; ?>


