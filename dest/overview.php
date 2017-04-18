<?php require 'config.php'; ?>
<?php
  // Query activity information
  $query = 'SELECT * FROM sportplannerActivities ORDER BY date ASC';
  $result = mysql_query($query, $conn);
  $arrayComing = array();
  $arrayPast = array();

  // Query players information
  $queryP = 'SELECT activityId, COUNT(*) AS count, SUM(present = 1) AS present FROM sportplanner GROUP BY activityId';
  $resultP = mysql_query($queryP, $conn);
  $arrayPlayers = array();

  while($item = mysql_fetch_assoc($resultP)) {
    $arrayPlayers[] = $item;
  }
  while($item = mysql_fetch_assoc($result)) {
    foreach($arrayPlayers as $player) {
      if($item{'id'} == $player['activityId']) {
        $item += $player;
      }
    }

    // Date format
    setlocale(LC_ALL, 'nld_nld');
    $date = strtotime($item{'date'});

    // Time format
    $item{'time'} = date('H:i', strtotime($item{'time'}));

    if(strtotime(date("Y/m/d")) > strtotime($item{'date'})) {
      $formattedDate = strftime("%d %B %Y", $date);
      $item{'date'} = $formattedDate;

      $arrayPast[] = $item;
    } else {
      $formattedDate = strftime("%d %B %Y", $date);
      $item{'date'} = $formattedDate;

      $arrayComing[] = $item;
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
        <div class="columns box upcoming-events medium-14">
          <div class="box-inside">
            <h3>Komende activiteiten</h3>
            <ul>
              <?php
                foreach($arrayComing as $activity) {
                  if(!isset($activity['activityId'])) {
                    $activity['count'] = 0;
                    $activity['present'] = 0;
                  }

                  echo '
                    <li class="event">
                      <div class="event-inside">
                        <div class="title">
                          <h4>'. $activity['name'] .'</h4>
                          <div class="weather">--<span>º</span></div>
                        </div>
                        <div class="info">
                          <p>'. $activity['date'] .' - '. $activity['time'] .' <br> '. $activity['location'] .'</p>
                          <div class="details">
                            <a class="button present" href="#">+ <span>('. $activity['present'] .')</span></a>
                            <a class="button absent" href="#">- <span>('. ($activity['count'] - $activity['present']) .')</span></a>
                            <a class="button" href="detail.php?id='. $activity['id'] .'">details</a>
                          </div>
                        </div>
                      </div>
                    </li>
                  ';
                }
              ?>
            </ul>
          </div>
        </div>

        <div class="columns box end past-events medium-10">
          <div class="box-inside">
            <h3>Afgelopen activiteiten</h3>
            <ul>
              <?php
                foreach($arrayPast as $activity) {
                  echo '
                    <li class="event">
                      <div class="title">
                        <h4>'. $activity['name'] .'</h4>
                        <div class="weather">--<span>º</span></div>
                      </div>
                      <div class="info">
                        <p>'. $activity['date'] .' - '. $activity['time'] .' <br> '. $activity['location'] .'</p>
                        <div class="details">
                          <a class="button" href="detail.php?id='. $activity['id'] .'">details</a>
                        </div>
                      </div>
                    </li>
                  ';
                }
              ?>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="js/vendor.min.js"></script>
  <script src="js/script.min.js"></script>
</body>
</html>
