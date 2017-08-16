<?php

//List of wallets, address + description
$whaleWallets = [
    ['0xA72Dc46CE562f20940267f8deb02746e242540ed','EOS','EOS'],
['0x7da82c7ab4771ff031b66538d2fb9b0b047f6cf9','Golem','GNT'],
['0x154Af3E01eC56Bc55fD585622E33E3dfb8a248d8','Iconomi','ICN'],
['0x851b7F3Ab81bd8dF354F0D7640EFcD7288553419','Gnosis','GNO'],
['0xA646E29877d52B9e2De457ECa09C724fF16D0a2B','Status','SNT'],
['0x185f19B43d818E10a31BE68f445ef8EDCB8AFB83','TenX','PAY'],
['0x7c31560552170ce96c4a7b018e93cddc19dc61b6','Basic Attention Token','BAT'],
['0x1a8CC57F1155D7dF4626978401e203E00DA097f7','Populous','PPT'],
['0xf0160428a8552ac9bb7e050d90eeade4ddd52843','DigixDAO','DGD'],
['0x5894110995B8c8401Bd38262bA0c8EE41d4E4658','Bancor','BNT'],
['0x6d7ea347ef837462a55337C4f772868F2A80B863','MobileGo','MGO'],
['0x15c19E6c203E2c34D1EDFb26626bfc4F65eF96F0','Aeternity','AE'],
['0x93B7e9364C4DF6De55eD0D10c80E7aFC0612e93A','Metal','MTL'],
['0x5901Deb2C898D5dBE5923E05e510E95968a35067','SingularDTV','SNGLS'],
['0x2323763D78bF7104b54A462A79C2Ce858d118F2F','Civic','CVC'],
['0xcafE1A77e84698c83CA8931F54A755176eF75f2C','Aragon','ANT'],
['0xa5384627F6DcD3440298E2D8b0Da9d5F0FCBCeF7','FirstBlood','1ST'],
['0x24C3235558572cff8054b5a419251D3B0D43E91b','Etheroll','DICE'],
['0x1e549606B695423368e851fF13Edef7eA4790Fe9','Melon','MLN'],
['0x21346283a31A5AD10Fa64377E77A8900Ac12d469','iExec RLC','RLC'],
['0x3dD88B391fe62a91436181eD2D43E20B86CDE60c','Stox','STX'],
['0xa2c9a7578e2172f32a36c5c0e49d64776f9e7883','Humaniq','HMQ'],
['0xe9Eca8bB5e61e8e32f26B5E8c117561F68084a9C','Polybius','PLBT'],
['0x6dD5A9F47cfbC44C04a0a4452F0bA792ebfBcC9a','Santiment','SAN'],
['0xd20E4d854C71dE2428E1268167753e4C7070aE68','district0x','DNT'],
['0x8D9d0BD75319A3780d3CAb012759EFBAe334291B','arcade.city','ARC'],
['0x9A60Ad6de185C4ea95058601bEaf16f63742782a','Bitquence','BQX'],
['0x1446bf7AF9dF857b23a725646D94f9Ec49802227','DAO.Casino','BET'],
['0x96A65609a7B84E8842732DEB08f56C3E21aC6f8a','Centra','CTR'],
['0x0C4b367e876d18d5c102023D9240DD7e9C11b380','Tierion','TNT'],
];

//For the future, add more exchange wallets
$exchangeWallets = [
    ['0x7727E5113D1d161373623e5f49FD568B4F543a9E', 'Bitfinex_Wallet2']
];
$servername = "localhost";
$username = "cashflow";
$password = "cash2000";

// Create connection
$conn = new mysqli($servername, $username, $password, 'cashflow');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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

        return "API is down";
    }

}

/**
 * @param  $wallet array
 * @return array
 */
function getOutgoingTx($wallet)
{
    $txData = [];
    $txData['date'] = "No transfers yet";
    $txData['value'] = 0;

    $txRoute = 'http://api.etherscan.io/api?module=account&action=txlist&address=' . $wallet . '&startblock=0&endblock=99999999&sort=asc&apikey=apitoken';
    $res = json_decode(file_get_contents($txRoute), $assoc = true);
    if (!empty($res)) {
        $lastIndex = array_slice($res['result'], -1);
        $txDate = null;
        $inValue = null;
        //Outgoing if same address as from as contract/wallet
        if (strtolower($lastIndex[0]['from']) === strtolower($wallet)) {
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
        return "API is down";
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
        return "API is down";
    }
}

?>

<?php
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

    $sql = 'INSERT INTO wallet_data (name, address,	balance, last_incoming,last_outgoing, tx_out, tx_in,ticker,update_date,logo_url) VALUES
    ("'.$walletName.'",
    "'.$walletAddress.'",
    '. $balance . ',
  "'.$lastInDate.'",
  "'.$lastOutDate.'",
  '.$lastOutBalance.',
  '. $lastInBalance.',
  "'.$ticker.'",
  NOW(),
  "'.strtolower($walletName).'.png"
   )';



  //  $sql = 'UPDATE  wallet_data  SET balance = "'.$balance.'"  , last_incoming = "'.$lastInDate.'" , last_outgoing = "'.$lastOutDate.'",  tx_out =  "'.$lastOutBalance.'" , tx_in =  "'.$lastInBalance.'", update_date=NOW() WHERE address = "'.$walletAddress.'"';
    echo $sql;
    mysqli_query($conn, $sql);
    ?>

<?php endforeach; ?>

	
