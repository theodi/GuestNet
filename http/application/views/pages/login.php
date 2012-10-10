      <div style="border: 1px solid black; border-radius: 15px; width: 48%; float: left;">
        <div style="text-align: center; margin-left: auto; margin-right: auto;">
          <form name="login" method="POST">
            <h3>Network Account Login</h3>
<?php if (!empty($_GET["message"])) {
      $message = urldecode($_GET["message"]);
      echo '          <br/><div style="font-size: 1.2em; font-weight: bold;">' . $message . '</div>';
} else {
?>
            <div style="font-size: 1.0em; font-weight: bold;">For ADMIN login please use the link above</div>
<?php } ?>
            <br/><br/>
            <span style="font-size: 1.6em; ">Username:</span>
            <input type="text" name="username"/>
	    <br/>
            <span style="font-size: 1.6em; ">Password:</span>
            <input type="password" name="password"/>
            <br/><br/>
            <input type="submit" value="Login" name="login"/>
          </form>
        </div>
      </div>

      <div style="border: 1px solid black; border-radius: 15px; width: 48%; float: right;">
        <div style="text-align: center; margin-left: auto; margin-right: auto;">
          <form name="redeem" method="POST">
            <h3>Use Registration Code</h3>
            <div style="font-size: 1.0em; font-weight: bold;">Create an account using a registration code.</div>
            <br/><br/>
            <span style="font-size: 1.6em; ">Code:</span>
            <input type="text" name="regcode"/>
            <br/><br/>
            <input type="submit" value="Redeem" name="redeem"/>
          </form>
        </div>
      </div>
