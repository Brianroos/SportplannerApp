<?php
  session_start();
  require 'config.php';
?>
<?php
  if(isset($_POST['submit'])) {
    $email = mysql_real_escape_string($_POST['email']);
    $pass = mysql_real_escape_string($_POST['password']);

    $query = 'SELECT * FROM sportplannerPlayers WHERE email = "'. $email .'" AND password = "'. $pass .'"';
    $result = mysql_query($query, $conn);

    if($query) {
      if($row = mysql_fetch_assoc($result)) {
        $_SESSION['loggedIn'] = true;
        $_SESSION['user'] = $row;

        if($_SESSION['loggedIn'] && $_SESSION['loggedIn'] == true) {
          header('location: overview.php');
          exit;
        }
      }
    }
  }
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
        <div class="columns logo center medium-12 medium-offset-6">
          <img src="img/logo.png" alt="Sportplanner">
          <h1>Sportplanner</h1>
        </div>
      </div>
    </header>

    <section class="content">
      <div class="row">
        <div class="columns box form medium-16 medium-offset-4 large-12 large-offset-6">
          <div class="box-inside">
            <h3>Inloggen</h3>
            <form action="index.php" method="post">
							<input type="email" name="email" placeholder="E-mail" required>
							<input type="password" name="password" placeholder="Wachtwoord" required>
							<input type="submit" name="submit" value="Inloggen">
						</form>
            <div class="clear"></div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="js/vendor.min.js"></script>
  <script src="js/script.min.js"></script>
</body>
</html>
