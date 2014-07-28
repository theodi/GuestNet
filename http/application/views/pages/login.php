<?php if (empty($user)) { ?>
	<div style="height: 50px">&nbsp;</div>
	<p>In order to use eduroam you need to login with your @theodi.org google account. Click the link below to do this.</p>
	<div style="height: 50px">&nbsp;</div>
	<a style="padding: 20px; text-align: center; font-size: 2em; border: 1px solid black; background: lightblue;" href="/glogin?signin">ODI Google Login</a>
	<div style="height: 100px">&nbsp;</div>
<?php } else {
      if (!empty($user->picPath)) { ?>
        <img src="<?= $user->picPath ?>" style="float: right; height: 40px;" border="0"/>
<?php }
      if (!empty($user)) { ?>
        <span class="userheader"><?= $user->username ?>&nbsp;(<a href="glogin?logout">Logout</a>)&nbsp;&nbsp;</span>
<?php 
	}
} ?>
