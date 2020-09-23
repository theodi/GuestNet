You might need to create a mysql user with a native password for this library to use:

CREATE user 'nodeadmin'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';

GRANT ALL PRIVILEGES ON radius.* TO 'nodeadmin'@'localhost';

Edit the GuestNet.service file and update the ExecStart line to point at the correct location of node and the Working Directory to point at the path where you downloaded the application to

Copy GuestNet.service to /etc/systemd/system

Run systemctl enable GuestNet.service

Run service GuestNet start
