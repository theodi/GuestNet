const express = require('express');
const bodyParser = require('body-parser')
const google = require('googleapis').google;
const jwt = require('jsonwebtoken');
const md5 = require('md5');

// Google's OAuth2 client
const OAuth2 = google.auth.OAuth2;

// Including our config file
const CONFIG = require('./config');

// Creating our express application
const app = express();
app.use( express.static( "views" ) );

// Set up body parser
app.use(bodyParser.urlencoded({ extended: false }))

//Create out data object 
var userData = {};

// Allowing ourselves to use cookies
const cookieParser = require('cookie-parser');
app.use(cookieParser());

// Setting up Views
app.set('view engine', 'ejs');
//app.set('views', __dirname);

app.get('/', function (req, res) {
  // Create an OAuth2 client object from the credentials in our config file
  const oauth2Client = new OAuth2(CONFIG.oauth2Credentials.client_id, CONFIG.oauth2Credentials.client_secret, CONFIG.oauth2Credentials.redirect_uris[0]);

  // Obtain the google login link to which we'll send our users to give us access
  const loginLink = oauth2Client.generateAuthUrl({
    access_type: 'offline', // Indicates that we need to be able to access data continously without the user constantly giving us consent
    scope: CONFIG.oauth2Credentials.scopes // Using the access scopes from our config file
  });
  return res.render("pages/index", { loginLink: loginLink });
});

app.get('/auth_callback', function (req, res) {
  // Create an OAuth2 client object from the credentials in our config file
  const oauth2Client = new OAuth2(CONFIG.oauth2Credentials.client_id, CONFIG.oauth2Credentials.client_secret, CONFIG.oauth2Credentials.redirect_uris[0]);

  if (req.query.error) {
    // The user did not give us permission.
    return res.redirect('/');
  } else {
    oauth2Client.getToken(req.query.code, function(err, token) {
      if (err)
        return res.redirect('/');
      
      // Store the credentials given by google into a jsonwebtoken in a cookie called 'jwt'
      res.cookie('jwt', jwt.sign(token, CONFIG.JWTsecret));
      return res.redirect('/main');
    });
  }
});

app.get('/main', function (req, res) {
  if (!req.cookies.jwt) {
    // We haven't logged in
    return res.redirect('/');
  }

  // Create an OAuth2 client object from the credentials in our config file
  const oauth2Client = new OAuth2(CONFIG.oauth2Credentials.client_id, CONFIG.oauth2Credentials.client_secret, CONFIG.oauth2Credentials.redirect_uris[0]);

  // Add this specific user's credentials to our OAuth2 client
  oauth2Client.credentials = jwt.verify(req.cookies.jwt, CONFIG.JWTsecret);

  // Get the auth service
  const service = google.oauth2({auth: oauth2Client, version: 'v2'});
  service.userinfo.v2.me.get(
    function(err,response) {
      if (err) {
        console.log(err);
        res.status(500).send('Internal server error!');
      } else {
        userData = response.data;
        if (response.data.hd != "theodi.org") {
          res.status(403).send('Access Forbidden! An @theodi.org account is required to access this service.');
        } else {
          userData.emailSuffix = emailUsername(userData.email);
          console.log(userData);
          return res.render('pages/main', { data: userData });
        }
      }
    }
  );
});

app.post('/set-password', function (req, res, next) {
   console.log(userData.email);
   console.log(req.body.password);
   console.log(md5(req.body.password));
   res.status(200).send('Got here');
});

// Listen on the port defined in the config file
app.listen(CONFIG.port, function () {
  console.log(`Listening on port ${CONFIG.port}`);
});

function emailUsername(emailAddress)
{
    return emailAddress.substring(0, emailAddress.indexOf("@"));
}
