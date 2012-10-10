<?php 
if ($user->isAdminUser())
	$menu_items = array("main" => "Home", "myaccount" => "Manage Account", "guests" => "ODI Guest Accounts", "codes" => "Generate Registration Codes");
else
	$menu_items = array("main" => "Home", "myaccount" => "Manage Account", "redeem" => "Redeem Registration Code");

?>
      <div id="main-menu-navbar" class="navbar navbar-inverse">
        <div class="navbar-inner">
          <nav id="main-menu" role="navigation" class="container">
            <h2 class="element-invisible">Main menu</h2>
            <ul class="links inline clearfix nav">
<?php
$i=0;
$first=" first";
foreach ($menu_items as $uri => $title)
{
	$i++;
	$active = "";
	if ($uri == $current_uri)
		$active = " active";
	echo '              <li class="menu-00' . $i . $first . $active .'"><a href="'.URL::base(TRUE, TRUE).$uri.'" class="' . $active . '">'.$title.'</a></li>' . "\n";
}
$first="";
?>
            </ul>
          </nav>
          <div id="main-menu-pointer" class=""></div>
        </div>
      </div>
