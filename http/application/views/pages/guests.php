<?php
if (empty($post['validTo']))
	$validTo = $valid_to_default;
else
	$validTo = $post['validTo'];
?>
     <div style="text-align: center; margin-left: auto; margin-right: auto; border: 0;">
	<form name="add_guest" method="POST" action="">
          <div class="field"><label>Person's Name:</label>&nbsp;<input type="text" name="name" value="<?php if (isset($post['name'])) echo $post['name']; ?>"/></div>
	  <div class="field"><label>Person's Email:</label>&nbsp;<input type="text" name="email" value="<?php if (isset($post['email'])) echo $post['email']; ?>"/></div>
          <div class="field"><label>Valid To:</label>&nbsp;<input type="text" name="validTo" value="<?= $validTo ?>"/></div>
          <input type="submit" value="Add Guest User" name="addguest"/>
      </form>

<?php if (sizeof($guests) > 0) { ?>
      <table width="100%" style="font-size: 0.9em">
        <tr><th>Name</th><th>Email</th><th>Wireless Username</th><th>Valid From</th><th>Valid To</th><th>Options</th></tr>
<?php
	foreach ($guests as $g => $guest) {
                if (!is_object($guest->validFrom) || $guest->validFrom->getTimestamp() == 0)
                 	$validFromString = "-";
		else
			$validFromString = $guest->validFrom->format("Y-m-d H:i:s");
		if (!is_object($guest->validTo) || $guest->validTo->getTimestamp() == 0)
			$validToString = "-";
		else
			$validToString = $guest->validTo->format("Y-m-d H:i:s");
?>
        <tr>
          <td><?= $guest->name ?></td>
          <td><?= $guest->email ?></td>
          <td><?= $guest->username ?>@<?= Kohana::$config->load('system.default.company.wifi_net_domain')  ?></td>
          <td><?= $validFromString ?></td>
          <td><?= $validToString ?></td>
          <td>
            <form name="<?= $guest->username ?>_manage" method="POST" action="" style="margin: 0px; padding: 0px; display: inline;">
		<?php if ($guest->validTo->getTimestamp() < time()) { ?>
              <input type='submit' name='reactivate' value='Reactivate' style='font-size: 0.8em;'></input>
                <?php } else { ?>
              <input type='submit' name='expire' value='Expire' style='font-size: 0.8em;'></input>
		<?php } ?>
              <input type='submit' name='reset_pass' value='Reset Password' style='font-size: 0.8em;'></input><input type='hidden' name='username' value='<?= $guest->username ?>'/>
            </form>
          </td>
        </tr>
	<?php } ?>
      </table>
<?php } ?>
