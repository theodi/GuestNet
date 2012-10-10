      <nav class="topnav">
<?php if (empty($user)) { ?>
        <a href="/glogin?signin">Login</a>
<?php } else {
      if (!empty($user->picPath)) { ?>
        <img src="<?= $user->picPath ?>" style="float: right; height: 40px;" border="0"/>
<?php }
      if (!empty($user)) { ?>
        <span class="userheader"><?= $user->username ?>&nbsp;(<a href="glogin?logout">Logout</a>)&nbsp;&nbsp;</span>
<?php 
	}
} ?>
      </nav>
