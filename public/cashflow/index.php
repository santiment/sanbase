<?php
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


//Functions


function getAllByName($name)
{
    $wallets = [];
    global $conn;
    $sql = "SELECT * FROM wallet_data WHERE name='$name'";
    $result = pg_query($conn, $sql);
    $count = 0;
    while ($row = pg_fetch_assoc($result)) {
        $ticker = $row['name'];
        $wallets[$ticker][$count]['address'] = $row['address'];
        $wallets[$ticker][$count]['balance'] = $row['balance'];
        $count++;
    }

    return $wallets;
}

setlocale(LC_MONETARY, 'en_US');

$ethPrice = 0;

$priceResult = json_decode(file_get_contents('https://api.coinbase.com/v2/prices/ETH-USD/sell'),true);

$ethPrice = $priceResult['data']['amount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS, then custom -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
    <script src="https://use.fontawesome.com/6f993f4769.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.bootstrap.min.css"/>
    <link rel="stylesheet" href="css/style_dapp_mvp1.css"/>
    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
            integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
            integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
            crossorigin="anonymous"></script>
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-100571693-1', 'auto');
      ga('send', 'pageview');

    </script>

</head>
<body>

<div class="nav-side-menu">
    <div class="brand"><img src="img/logo_sanbase.png" width="115" height="22" alt="SANbase"/></div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
    <div class="menu-list">
        <ul id="menu-content" class="menu-content collapse out">
            <li>
                <a href="#">
                    <i class="fa fa-home fa-md"></i> Dashboard (tbd)
                </a>
            </li>
            <li data-toggle="collapse" data-target="#products" class="active">
                <a href="#" class="active"><i class="fa fa-list fa-md"></i> Data-feeds <span class="arrow"></span></a>
            </li>
            <ul class="sub-menu" id="products">
                <li><a href="#">Overview (tbd)</a></li>
                <li class="active"><a href="#" class="active">Cash Flow</a></li>
            </ul>
            <li>
                <a href="signals.html"><i class="fa fa-th fa-md"></i> Signals </a>
            </li>
            <!--
            <li data-toggle="collapse" data-target="#service" class="collapsed">
                <a href="signals.html"><i class="fa fa-th fa-md"></i> Signals</a>
            </li> -->
            <li>
              <a href="roadmap.html"><i class="fa fa-comment-o fa-md"></i> Roadmap </a>
            </li>
            <!-- <li data-toggle="collapse" data-target="#new" class="collapsed">
                <a href="roadmap.html"><i class="fa fa-comment-o fa-md"></i> Roadmap</a>
            </li> -->
        </ul>
    </div>
</div>
<div class="container" id="main">
 <!-- <div class="row topbar">
        <div class="col-lg-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="material-icons">search</i></span>
                <input type="text" class="form-control" placeholder="{{ 'SEARCH' | translate }}">
                <span class="input-bar"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <ul class="nav-right pull-right list-unstyled">
                <li>
                    <span style="display: inline-block; padding-top: 22px; font-size: 14px;">12.5 Ξ</span>
                </li>
                <li>
                    <md-select placeholder="brighteye" style="margin: 16px 22px 0 22px; z-index: 1111;">
                        <md-option>brighteye</md-option>
                        <md-option>testacct</md-option>
                        <md-option>stash</md-option>
                </li>
            </ul>
        </div>
    </div> -->
    <div class="row">
        <div class="col-lg-5">
            <h1>Cash Flow</h1>
        </div>
        <div class="col-lg-7 community-actions">
             <span class="legal">brought to you by <a href="https://santiment.net" target="_blank">Santiment</a>
             <br />
             NOTE: This app is a prototype. We give no guarantee data is correct as we are in active development.</span>

            <!-- <a class="btn-secondary" href="#"><i class="fa fa-pencil"></i></a> -->
            <!-- <a class="btn-primary" href="#">Supply ICO Wallet</a> -->
            <!-- <select style="width: 100px; height: 40px;">
                <option>BTC</option>
                <option selected>ETH</option>
                <option>LTC</option>
            </select> -->
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="sortable table-responsive">
                    <table id="projects" class="table table-condensed table-hover" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Project</th>
                            <th>Market Cap</th>
                            <th class="sorttable_numeric">Balance (USD/ETH)</th>
                            <th>Last outgoing TX</th>
                            <th>ETH sent</th>
                        </tr>
                        </thead>
                        <tbody class='whaletable'>
                        <?php
                        $sql = 'SELECT DISTINCT ON(wallet_data.name) * FROM wallet_data, cmm_data  WHERE wallet_data.ticker = cmm_data.ticker AND cmm_data.active =1';
                        $result = pg_query($conn, $sql);
                        while ($row = pg_fetch_assoc($result)) :
                            $market_cap = $row['market_cap'];
                            if($market_cap !== null)
                            {
                                $market_cap = "$".number_format($market_cap,0);
                            }
                            else
                            {
                                $market_cap = "No data";
                            }
                            $ticker = $row['name'];
                            $wallets = getAllByName($ticker);
                            ?>
                            <tr>
                                <td><img src="img/<?php echo strtolower($row['logo_url']); ?>" /><?php echo $row['name'] ?> (<?php echo $row['ticker'] ?>)</td>
                                <td class="marketcap"><?php echo $market_cap; ?></td>
                                <td class="address-link" data-order="<?php echo $row['balance']; ?>">
                                    <?php if (count($wallets[$ticker]) === 1) : ?>
                                    <div class="wallet">
                                        <div class="usd first">$<?php echo number_format(($row['balance'] * $ethPrice), 0);?></div>
                                        <div class="eth">
                                            <a class="address" href="https://etherscan.io/address/<?php echo $row['address']; ?>" target="_blank">Ξ<?php echo $row['balance']; ?>
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                    elseif (count($wallets[$ticker]) > 1) :
                                      for ($i = 0;$i < count($wallets[$ticker]);$i++) :
                                    ?>
                                      <div class="wallet">
                                        <div class="usd first">$<?php echo number_format(($wallets[$ticker][$i]['balance'] * $ethPrice), 0); ?></div>
                                        <div class="eth">
                                            <a class="address" href="https://etherscan.io/address/<?php echo $wallets[$ticker][$i]['address']; ?>" target="_blank">Ξ<?php echo $row['balance']; ?>
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        </div>
                                      </div>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['last_outgoing']; ?></td>
                                <td><?php echo $row['tx_out']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {

        $('.table-hover').DataTable({

            "dom": "<'row'<'col-sm-7'i><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'><'col-sm-7'>>",
            "paging": false,
            fixedHeader: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search"
            },
	        "order": [[ 1, "desc" ]],

            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    type: ''
                }
            }

        });

    });
</script>
</body>
</html>
