<?php
  //error_reporting(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title><?php if(isset($title)){echo $title;} ?></title>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta charset="utf-8"/>
<link rel="stylesheet" href="styles/css/bootstrap.css"/>
<link rel="stylesheet" href="styles/css/bootstrap.min.css"/>
<link rel="stylesheet" href="styles/css/font-awesome.min.css"/>
<!--
<link rel="stylesheet" href="styles/css/font-awesome.css"/>
-->
<link rel="stylesheet" href="styles/css/css.css"/>
<link rel="stylesheet" href="styles/css/style.css"/>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="style/js/html5shiv.js"></script>
      <script src="style/js/respond.min.js"></script>
<![endif]-->
<script src="styles/js/jquery-1.10.2.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <form method="post" class="form-horisontal">
                    <div class="col-lg-12"><label>Save HTML CSS JS fails from saits URL</label></div>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" name="URL_SEARCH" placeholder="URL Saits or URL TemplateMonster DEMO Sait" value="<?php if(isset($_POST['URL_SEARCH'])){ echo $_POST['URL_SEARCH']; } ?>"/>
                        <input type="radio" name="template" value="template" checked/> Template Monster Saite Demo
                        <input type="radio" name="template" value="url"/> URL Saits
                    </div>
                    <div class="col-lg-4">
                        <input type="submit" class="btn btn-block btn-success" value="Save Sait"/>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <label>Console Result</label>
                <div class="col-lg-12">
                <?php
                    require_once 'app.php';
                    $app = new APP();
                ?>
                </div>
            </div>
        </div>
    </div>
    <script src="styles/js/bootstrap.min.js"></script>
    <script src="styles/js/script.js"></script>
    <script src="styles/js/jquery.elevatezoom.js"></script>
</body>
</html>
