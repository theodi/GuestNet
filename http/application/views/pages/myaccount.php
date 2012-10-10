      <div style="text-align: center; margin-left: auto; margin-right: auto; border: 0;">
        <form name="change_password" method="POST" action="" style="border: none;">
          <table width="80%" style="font-size: 1.6em; border: none;">
            <tr><td width="40%">Username</td><td width="60%"><?= $user->username ?>@<?=  Kohana::$config->load('system.default.company.wifi_net_domain') ?></td></tr>
            <tr><td width="40%">&nbsp;</td><td width="60%">&nbsp;</td></tr>
<?php if (!$user->isAdminUser()) {  ?>
            <tr><td width="40%">Current Password</td><td width="60%"><input type="password" name="current"/></td></tr>
<?php } ?>
            <tr><td width="40%">&nbsp;</td><td width="60%">&nbsp;</td></tr>
            <tr><td width="40%">New Password</td><td width="60%"><input type="password" name="new1"/></td></tr>
            <tr><td width="40%">Confirm Password</td><td width="60%"><input type="password" name="new2"/></td></tr>
            <tr><td width="40%">&nbsp;</td><td width="60%">&nbsp;</td></tr>
          </table>

          <input type="submit" value="Change Password" name="changepass"/>
        </form>
      </div>

