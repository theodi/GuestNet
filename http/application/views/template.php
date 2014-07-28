<?php
if (empty($title))
	throw new HTTP_Exception_403('You do not have permission to access this page.');	
if (empty($heading)) 
	$heading = $title; 
$loggedin = "logged-in";
if (!isset($user))
{
	$menu = "nomenu";
	$user = FALSE;
	$loggedin = "not-logged-in";
}
else
	$menu = "menu";
?>
<!DOCTYPE html>
<html prefix="dct: http://purl.org/dc/terms/
              rdf: http://www.w3.org/1999/02/22-rdf-syntax-ns#
              dcat: http://www.w3.org/ns/dcat#
              odrs: http://schema.theodi.org/odrs#">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Open Data Barometer Data | {{ page.title }}</title>
<link href="http://assets.theodi.org/css/odi-bootstrap-pomegranate.css" rel="stylesheet">
<link href="/css/style.css" rel="stylesheet">
<link rel="shortcut icon" href="{{ site.url_root }}/img/odifavicon32.ico">
<script src="http://code.jquery.com/jquery-1.10.1.min.js"> </script>
<script src="http://assets.theodi.org/js/bootstrap-tab.js"> </script>
</head>
<body>
<nav>
	<div class='navbar navbar-inverse navbar-static-top' id='topbar'>
		<div class='container'>
			<div class='navbar-inner'>
				<h1><a href="/">Eduroam control portal</a></h1>
				<span class="label" style="top: -10px; position: absolute; left: 310px;">
					BETA
				</span>
				<a class='brand' href='/'>
					<img alt="Logo" src="http://assets.theodi.org/img/logo-footer.png" />
				</a>
			</div>
		</div>
	</div>
</nav>
<div align="center">
<h2 class="main_title"><?= $heading ?></h2>
<div class="message">
  <?php if (!Empty($message)) { ?>
        <p style="text-align: center; font-size: 1.5em; font-weight: bold;"><?= $message ?></p>
  <?php  } ?>
</div>
<?= $content ?>
</div>
<footer id='footer' style="bottom: 0; width: 100%; padding-top: 10px; padding-bottom: 10px; min-height: 120px;">
	<div style="margin-left: auto; margin-right: auto; width: 80%;">
		<div class='span6 footer-content'>
			<p> <a href='http://www.openstreetmap.org/?lat=51.522205&amp;lon=-0.08176500000001852&amp;zoom=16&amp;layers=T&amp;mlat=51.52210&amp;mlon=-0.08343'>Open Data Institute</a><span>, 65 Clifton Street, London EC2A 4JE</span></p>
			<p><a href='mailto:info@theodi.org'>info@theodi.org</a> · Company <a href='http://opencorporates.com/companies/gb/08030289'>08030289</a>	· <span>VAT</span> <span>143 7796 80</span></p>
		</div>
		<div style="display: inline-block; float: right;">
			
      			<?php if (!empty($user)) { ?>
      			  <span class="userheader"><?= $user->username ?>&nbsp;(<a href="glogin?logout">Logout</a>)&nbsp;&nbsp;</span>
			<?php } ?>
			<p class='license'><a href='http://creativecommons.org/licenses/by-sa/2.0/uk/deed.en_GB' rel='license'><img alt="Creative Commons Licence" height="15" src="http://static.theodi.org/assets/cc-853c95321fbeb898ecb83c38fb156a71.png" width="80" /></a></p>
		</div>
	</div>
</footer>


</body>
</html>

