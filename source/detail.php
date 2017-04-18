<?php require 'config.php'; ?>
<?php
  // Query activity information
  $query = 'SELECT * FROM sportplannerActivities WHERE id = "'. $_GET['id'] .'"';
  $result = mysql_query($query, $conn);
  $arrayDetail = array();

  while($item = mysql_fetch_assoc($result)) {
    // Date format
    setlocale(LC_ALL, 'nld_nld');
    $date = strtotime($item{'date'});
    $formattedDate = strftime("%d %B %Y", $date);
    $item{'date'} = $formattedDate;

    // Time format
    $item{'time'} = date('H:i', strtotime($item{'time'}));

    $arrayDetail = $item;
  }

  // Query players information
  $queryP = 'SELECT present, first_name, last_name, heartbeat FROM sportplanner
    INNER JOIN sportplannerActivities ON sportplanner.activityId = sportplannerActivities.id
    INNER JOIN sportplannerPlayers ON sportplanner.playerId = sportplannerPlayers.id
    WHERE sportplannerActivities.id = "'. $_GET['id'] .'"
    ORDER BY last_name ASC';
  $resultP = mysql_query($queryP, $conn);
  $arrayPresentPlayers = array();
  $arrayAbsentPlayers = array();

  while($item = mysql_fetch_assoc($resultP)) {
    if($item{'present'} == 1) {
      $arrayPresentPlayers[] = $item;
    } else {
      $arrayAbsentPlayers[] = $item;
    }
  }

  // Query players football pitch
  $queryF = 'SELECT present, first_name, heartbeat FROM sportplanner
    INNER JOIN sportplannerActivities ON sportplanner.activityId = sportplannerActivities.id
    INNER JOIN sportplannerPlayers ON sportplanner.playerId = sportplannerPlayers.id
    WHERE sportplannerActivities.id = "'. $_GET['id'] .'"
    ORDER BY heartbeat ASC
    LIMIT 11';
  $resultF = mysql_query($queryF, $conn);
  $arrayPitchPlayers = array();

  while($item = mysql_fetch_assoc($resultF)) {
    if($item{'present'} == 1) {
      $arrayPitchPlayers[] = $item;
    }
  }

  mysql_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Sportplanner App</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge; chrome=1">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <link rel="stylesheet" type="text/css" href="css/style.min.css">
</head>
<body>

  <div class="page">
    <header>
      <div class="row">
        <div class="columns logo medium-12">
          <a href="overview.php">
            <img src="img/logo.png" alt="Sportplanner">
            <h1>Sportplanner</h1>
          </a>
        </div>
        <div class="columns menu medium-12">
          <!-- <ul>
            <li>Hallo, Admin</li>
            <li><a href="#">Nieuwe activiteit</a></li>
          </ul> -->
          <div class="current-date"></div>
        </div>
      </div>
    </header>

    <section class="content">
      <div class="row">
        <div class="columns box detail-info medium-7">
          <div class="box-inside">
            <h3>Informatie</h3>
            <div class="description">
              <?php
                echo '
                  <h5>Activiteit</h5>
                  <p>'. $arrayDetail['name'] .'</p>
                  <h5>Weerbericht</h5>
                  <p>17 graden, kans op regenbuien 20%</p>
                  <h5>Datum en tijd</h5>
                  <p>'. $arrayDetail['date'] ." - ". $arrayDetail['time'] .'</p>
                  <h5>Locatie</h5>
                  <p>'. $arrayDetail['location'] .'</p>
                ';
              ?>
            </div>
          </div>
        </div>

        <div class="columns box players-overview medium-7">
          <div class="box-inside">
            <h3>Aanwezigen <span>(<?php echo count($arrayPresentPlayers); ?>)</span></h3>
            <ul>
              <?php
                foreach($arrayPresentPlayers as $player) {
                  echo '
                    <li class="player">
                      <h4>'. $player['first_name'] .' '. $player['last_name'] .'</h4>
                      <div class="heartrate">
                        <img src="img/heartrate.png" alt="">
                        <span>'. $player['heartbeat'] .'</span>
                      </div>
                    </li>
                  ';
                }
              ?>
            </ul>
          </div>
          <div class="box-inside">
            <h3>Afwezigen <span>(<?php echo count($arrayAbsentPlayers); ?>)</span></h3>
            <ul>
              <?php
                foreach($arrayAbsentPlayers as $player) {
                  echo '
                    <li class="player">
                      <h4>'. $player['first_name'] .' '. $player['last_name'] .'</h4>
                      <div class="heartrate">
                        <img src="img/heartrate.png" alt="">
                        <span>'. $player['heartbeat'] .'</span>
                      </div>
                    </li>
                  ';
                }
              ?>
            </ul>
          </div>
        </div>

        <div class="columns box starting-team medium-10">
          <div class="box-inside">
            <h3>Basiselftal <span>(komende wedstrijd)</span></h3>
            <div class="football-pitch">
              <img src="img/football-pitch.jpg" alt="">
              <ul>
                <?php
                  foreach($arrayPitchPlayers as $player) {
                    echo '
                      <li>
                        <img src="img/heartrate.png" alt="">
                        <span>'. $player['heartbeat'] .'</span>
                        '. $player['first_name'] .'
                      </li>
                    ';
                  }
                ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="js/vendor.min.js"></script>
  <script src="js/script.min.js"></script>
</body>
</html>
