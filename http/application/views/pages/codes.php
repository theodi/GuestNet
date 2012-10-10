     <div align="center" style="border: 1px solid red; border-radius: 10px; width: 100%;">
	<h2>Section Under Development</h2>
     </div>
     <br/><br/>
  
<?php
	if (isset($post['eventName'])) $eventName = $post['eventName'];
	if (isset($post['validFrom'])) $validFrom = $post['validFrom'];
	if (isset($post['validTo'])) $validTo = $post['validTo'];
	if (isset($post["multiUser"])) $multiUser = $post["multiUser"];
	if (isset($post["emailAuth"])) $emailAuth = $post["emailAuth"];
	
	if (isset($event)) {
		$eventName = $event->eventName;
		$validFrom = $event->validFrom->format("Y-m-d H:i:s");
		$validTo = $event->validTo->format("Y-m-d H:i:s");
		if ($event->multiUser) $multiUser = 1;
		if ($event->emailAuth) $emailAuth = 1;
	}
?>

     <div style="text-align: center; margin-left: auto; margin-right: auto; border: 0;">

        <form name="new_code" method="POST" action="">
          <div class="field"><label>Event Name:</label>&nbsp;<input type="text" name="eventName" value="<?php if (isset($eventName)) echo $eventName; ?>"/></div>
	  <div class="field"><label>Accounts Active From:</label>&nbsp;<input type="text" name="validFrom" value="<?php if (isset($validFrom)) echo $validFrom; ?>"/></div>
	  <div class="field"><label>Accounts Active To:</label>&nbsp;<input type="text" name="validTo" value="<?php if (isset($validTo)) echo $validTo; ?>"/></div>	
          <div class="field"><label>Multiple User Code:</label>&nbsp;<input type="checkbox" name="multiUser" <?php if (isset($multiUser)) echo 'checked="checked"'; ?> /></div>
          <div class="field"><label>Requires Email Authentication:</label>&nbsp;<input type="checkbox" name="emailAuth" <?php if (isset($emailAuth)) echo 'checked="checked"'; ?> /></div>
          <br/>
<?php if (!empty($regcode)) { ?>
          <input type="submit" name='update' value="Update Event" />&nbsp;
          <input type="hidden" name="regcode" value='<?= $regcode ?>' />
<?php } else { ?>
          <input type="submit" name="create" value="Create Event" />
<?php } ?>
        </form>
      </div>
<?php if (sizeof($events) > 0) { ?>
      <table width="100%" style="font-size: 0.9em">
        <tr><th>Event Name</th><th>Registration Code</th><th>Active From</th><th>Active To</th><th align="center">Multiuser</th><th align="center">Email Auth</th><th align="center">Options</th></tr>
<?php
	foreach ($events as $e => $event) {
                if (!is_object($event->validFrom) || $event->validFrom->getTimestamp() == 0)
                 	$validFromString = "-";
		else
			$validFromString = $event->validFrom->format("Y-m-d H:i:s");
		if (!is_object($event->validTo) || $event->validTo->getTimestamp() == 0)
			$validToString = "-";
		else
			$validToString = $event->validTo->format("Y-m-d H:i:s");

		$emailAuth = "&#10008;";
		$multiUser = "&#10008;";
		if ($event->emailAuth) 
			$emailAuth = "&#10004;";
		if ($event->multiUser) 
			$multiUser = "&#10004;";
		
?>
        <tr>
          <td><?= $event->eventName ?></td>
          <td><?= $event->regcode ?></td>
          <td><?= $validFromString ?></td>
          <td><?= $validToString ?></td>
          <td align="center"><?= $emailAuth ?></td>
          <td align="center"><?= $multiUser ?></td>
          <td>
            <form name="<?= $event->regcode ?>_manage" method="GET" action="?" style="margin: 0px; padding: 0px; display: inline;">
              <input type='submit' name='edit' value='Edit' style='font-size: 0.8em;'></input>
	      <input type='hidden' name='regcode' value='<?= $event->regcode ?>'/>
            </form>
          </td>
        </tr>
	<?php } ?>
      </table>
<?php } ?>
