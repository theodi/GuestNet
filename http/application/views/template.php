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
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8" />
  <link rel="shortcut icon" href="http://www.theodi.org/favicon.ico" type="image/vnd.microsoft.icon" />
  <title><?= $title ?> | <?= $company['name'] ?></title>
  <meta name="MobileOptimized" content="width">
  <meta name="HandheldFriendly" content="true">
  <meta name="viewport" content="width=device-width">
  <style>
    @import url("http://www.theodi.org/modules/system/system.base.css?majo3e");
    @import url("http://www.theodi.org/modules/system/system.messages.css?majo3e");
    @import url("http://www.theodi.org/modules/system/system.theme.css?majo3e");
  </style>
  <style>
    @import url("http://www.theodi.org/modules/aggregator/aggregator.css?majo3e");
    @import url("http://www.theodi.org/sites/all/modules/date/date_api/date.css?majo3e");
    @import url("http://www.theodi.org/modules/field/theme/field.css?majo3e");
    @import url("http://www.theodi.org/modules/node/node.css?majo3e");
    @import url("http://www.theodi.org/modules/user/user.css?majo3e");
    @import url("http://www.theodi.org/sites/all/modules/views/css/views.css?majo3e");
  </style>
  <style>
    @import url("http://www.theodi.org/sites/all/themes/odi/css/print.css?majo3e");
    @import url("http://www.theodi.org/sites/all/themes/odi/css/odi.css?majo3e");
  </style>
  <link rel="stylesheet" href="/media/css/style.css" type="text/css">
  <!--[if IE 6]>
    <style>
      @import url("http://www.theodi.org/sites/all/themes/odi/css/bootstrap.ie6.min.css?majo3e");
    @import url("http://www.theodi.org/sites/all/themes/odi/css/ie6.css?majo3e");
   </style>
  <![endif]-->
  <script src="http://www.theodi.org/misc/jquery.js?v=1.4.4"></script>
  <script src="http://www.theodi.org/misc/jquery.once.js?v=1.2"></script>
  <script src="http://www.theodi.org/misc/drupal.js?majo3e"></script>
  <script src="https://www.google.com/jsapi?majo3e"></script>
  <script src="http://www.theodi.org/sites/all/themes/odi/js/script.js?majo3e"></script>
  <script src="http://www.theodi.org/sites/all/themes/odi/js/jquery.nicescroll.min.js?majo3e"></script>
  <script src="http://www.theodi.org/sites/all/themes/odi/js/browser.js?majo3e"></script>
  <!--[if lt IE 9]>
    <script src="/sites/all/themes/zen/js/html5-respond.js"></script>
  <![endif]-->
</head>
<body class="html front <?= $loggedin ?> one-sidebar sidebar-second page-node" >
  <p id="skip-link">
    <a href="#main-menu" class="element-invisible element-focusable">Jump to navigation</a>
  </p>
  <script type="template/html" id="cookie-notification-template">
    <div id="cookie-notification">
      <div class="container">
        <div class="row-fluid">
          <div class="span3 title">Cookies on <?= $company['name'] ?> Website</div>
          <div class="span6 description">This website uses cookies to provide you with the best experience.</div>
          <div class="span3 links">
            <a class="btn btn-info continue" href="javascript:;">Continue</a><br>
            <a class="more" href="<?= $company['cookie_policy_url'] ?>">Find out more</a>
          </div>
        </div>
      </div>
    </div>
  </script>

  <div class="header-bg"></div>

  <div id="page" class="container">
    <header id="header" role="banner">
      <div class="branding">
        <a href="/" title="Home" rel="home" id="logo">
          <img src="/media/images/logo.svg" alt="Home" class="b_svg" width="131" height ="54">
          <img src="/media/images/logo.png" alt="Home" class="b_png" width="131" height ="54">        
        </a>
        <hgroup id="name-and-slogan">
          <h1 id="site-name">
            <a href="/" title="<?= $company['name'] ?>" rel="home">
              <img src="/media/images/logo_a.svg" alt="<?= $company['name'] ?>" class="b_svg" width="326" height ="30">
              <img src="/media/images/logo_a.png" alt="<?= $company['name'] ?>" class="b_png" width="326" height ="30">                                 </a>
          </h1>
          <h2 id="site-slogan">
            <img src="/media/images/logo_b.svg" alt="<?= $company['subname'] ?>" class="b_svg" width="246" height ="14">
            <img src="/media/images/logo_b.png" alt="<?= $company['subname'] ?>" class="b_png" width="246" height ="14">
          </h2>
        </hgroup>
      </div>
<?= View::factory('partial/loggedin') ?>
      <div style="clear: both;"></div>
<?= View::factory("partial/$menu"); ?>
       <h2 class="main_title"><?= $heading ?></h2>
    </header>
<?php if (!Empty($message)) { ?>
      <div class="message">
        <p style="text-align: center; font-size: 1.5em; font-weight: bold;"><?= $message ?></p>
      </div>
<?php  } ?>
<?= $content ?>
    <nav class="bottomnav"></nav>
    <footer>
      <div style="float: right; margin-right: 3em; clear: both;">
        <br/>
        &copy; David Tarrant &amp; David Newman, University of Southampton / The Open Data Institute<br/>
      </div>
    </footer>
  </div>
</body>
</html>

