<?php
  session_start();
  require 'config.php';

  if(!isset($_SESSION['loggedIn'])) {
    header("location: index.php");
    exit;
  } else if((isset($_GET['action']) && $_GET['action'] === 'logout')) {
    session_destroy();
    header('location: index.php');
    exit;
  }
?>
<?php
  // Query activity information
  $query = 'SELECT * FROM sportplannerActivities ORDER BY date ASC';
  $result = mysql_query($query, $conn);
  $array = array();
  $arrayComing = array();
  $arrayPast = array();

  // Query players information
  $queryP = 'SELECT activityId, playerId, present FROM sportplanner GROUP BY id';
  $resultP = mysql_query($queryP, $conn);
  $arrayPlayers = array();

  while($item = mysql_fetch_assoc($resultP)) {
    $arrayPlayers[] = $item;
  }
  while($item = mysql_fetch_assoc($result)) {
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
  $array = array('activities' => $arrayComing, 'players' => $arrayPlayers);

  // Present/absent activity
  if((isset($_GET['action']) && $_GET['action'] === 'present')) {
    $queryPr = mysql_query('INSERT INTO sportplanner (id, activityId, playerId, present) VALUES (null, '. $_GET['activity'] .', '. $_SESSION['user']['id'] .', 1)');
    header('location: overview.php');
  }
  if((isset($_GET['action']) && $_GET['action'] === 'absent')) {
    $queryAb = mysql_query('INSERT INTO sportplanner (id, activityId, playerId, present) VALUES (null, '. $_GET['activity'] .', '. $_SESSION['user']['id'] .', 0)');
    header('location: overview.php');
  }

  // New activity
  if(isset($_POST['submitActivity'])) {
    $name = mysql_real_escape_string($_POST['name']);
    $weather = mysql_real_escape_string($_POST['weather']);
    $date = mysql_real_escape_string($_POST['date']);
    $time = mysql_real_escape_string($_POST['time']);
    $club = mysql_real_escape_string($_POST['club']);
    $location = mysql_real_escape_string($_POST['location']);

    $queryAc = mysql_query("SELECT * FROM sportplannerActivities WHERE name = '$name' OR date = '$date' AND time = '$time'");
    if(mysql_num_rows($queryAc) == 0) {
      $queryPostAc = mysql_query("INSERT INTO sportplannerActivities (id, name, weather, date, time, club, location) VALUES (null, '$name', '$weather', '$date', '$time', '$club', '$location')");
      header('location: overview.php');
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

  <div id="form-popup" class="white-popup mfp-hide">
    <h3>Nieuwe activiteit</h3>
      <form action="overview.php" method="post">
        <input type="text" name="name" placeholder="Naam activiteit" required>
        <input type="number" name="weather" placeholder="Weersverwachting" required>
        <input type="date" name="date" required>
        <input type="time" name="time" required>
        <input type="text" name="club" placeholder="Club" required>
        <input type="text" name="location" placeholder="Locatie" required>
        <input type="submit" name="submitActivity" value="Maak nieuwe activiteit aan">
      </form>
    <div class="clear"></div>
  </div>

  <div class="page">
    <header>
      <div class="row">
        <div class="columns logo large-10">
          <a href="overview.php">
            <img src="img/logo.png" alt="Sportplanner">
            <h1>Sportplanner</h1>
          </a>
        </div>
        <div class="columns menu large-14">
          <ul>
            <li>Hallo, <?php echo $_SESSION['user']['first_name']; ?></li>
            <?php if($_SESSION['user']['admin'] == 1) { echo '<li><a href="#form-popup" class="open-popup-link">Nieuwe activiteit</a></li>'; } ?>
            <li><a href="?action=logout">Uitloggen</a></li>
          </ul>
          <div class="current-date"></div>
        </div>
      </div>
    </header>

    <section class="content">
      <div class="row">
        <div class="columns box upcoming-events medium-12 large-14">
          <div class="box-inside">
            <h3>Komende activiteiten</h3>
            <ul>
              <?php
                foreach($array['activities'] as $activity) {
                  $showOptions = true;
                  $count = 0;
                  $countPresent = 0;

                  foreach($array['players'] as $player) {
                    if($activity{'id'} == $player['activityId']) {
                      if($player['playerId'] == $_SESSION['user']['id']) {
                        $showOptions = false;
                      }

                      $count += count($player['present']);
                      $countPresent += $player['present'];
                    }
                  }

                  echo '
                    <li class="event">
                      <div class="event-inside">
                        <div class="title">
                          <h4>'. $activity['name'] .'</h4>
                          <div class="weather"><span>'. $activity['weather'] .'</span>º</div>
                        </div>
                        <div class="info">
                          <p>'. $activity['date'] .' - '. $activity['time'] .' <br> '. $activity['club'] .', '. $activity['location'] .'</p>
                          <div class="details">';
                          if($showOptions) {
                            echo '<a class="button present" href="?action=present&activity='. $activity['id'] .'">+ <span>('. $countPresent .')</span></a>
                              <a class="button absent" href="?action=absent&activity='. $activity['id'] .'">- <span>('. ($count - $countPresent) .')</span></a>';
                          }
                          echo '
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

        <div class="columns box end past-events medium-12 large-10">
          <div class="box-inside">
            <h3>Afgelopen activiteiten</h3>
            <ul>
              <?php
                foreach($arrayPast as $activity) {
                  echo '
                    <li class="event">
                      <div class="title">
                        <h4>'. $activity['name'] .'</h4>
                        <div class="weather"><span>'. $activity['weather'] .'</span>º</div>
                      </div>
                      <div class="info">
                        <p>'. $activity['date'] .' - '. $activity['time'] .' <br> '. $activity['club'] .', '. $activity['location'] .'</p>
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
