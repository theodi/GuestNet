const express = require('express');
const bodyParser = require('body-parser')
const google = require('googleapis').google;
const jwt = require('jsonwebtoken');
const nthash = require('smbhash').nthash;
const mysql = require('mysql');

// Google's OAuth2 client
const OAuth2 = google.auth.OAuth2;

// Including our config file
const CONFIG = require('./config');

// Creating our express application
const app = express();
app.use( express.static( "views" ) );

// Set up body parser
app.use(bodyParser.urlencoded({ extended: false }));

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
        //console.log(err);
        res.status(500).send('Internal server error!');
      } else {
        userData = response.data;
        if (response.data.hd != CONFIG.permittedDomain) {
          res.status(403).send('Access Forbidden! An @'+CONFIG.permittedDomain+' account is required to access this service.');
        } else {
          userData.emailSuffix = emailUsername(userData.email);
          userData.eduroamUsername = userData.emailSuffix + '@eduroam.' + userData.hd;
          //console.log(userData);
          return res.render('pages/main', { data: userData });
        }
      }
    }
  );
});

app.post('/main', function (req, res, next) {
   //console.log(userData.email);
   if (!userData.email) {
    res.redirect('/');
    return;
   }
   
   var ntpassword = nthash(req.body.password);

   var connection = mysql.createConnection({ 
    host: CONFIG.freeRadiusMysql.host, 
    user: CONFIG.freeRadiusMysql.user, 
    password: CONFIG.freeRadiusMysql.password, 
    database: CONFIG.freeRadiusMysql.database 
   });

   var user_query = 'SELECT id from ' + CONFIG.freeRadiusMysql.users_table + ' where username="'+userData.eduroamUsername+'";';
   var update_query = 'UPDATE ' + CONFIG.freeRadiusMysql.users_table + ' set value="'+ntpassword+'" where username="'+userData.eduroamUsername+'" and attribute="NT-Password"';
   var query = 'INSERT INTO ' + CONFIG.freeRadiusMysql.users_table + " (username,attribute,op,value) values('"+userData.eduroamUsername+"','NT-Password',':=','"+ntpassword+"') ON DUPLICATE KEY UPDATE value='"+ntpassword+"'";
   var feedback = 'New record created';

   //console.log(user_query);
   
   connection.connect();

   connection.query(user_query,function(error,results,fields) {
    if (error) {
      //console.log(error);    
      res.status(500).send('Internal server error - Database error (query 1)');
    } else {
      //console.log(results);
      if (results[0]) {
        query = update_query;
        feedback = 'Password updated';
      }
      connection.query(query,function(error,results,fields) {
        if (error) {
          //console.log(error);    
          res.status(500).send('Internal server error - Database error (query 2)');
        } else {
          //console.log(results.insertId);
          userData.feedback = feedback
          return res.render('pages/main', { data: userData });
        }
      });
    }
  });
});

// Listen on the port defined in the config file
app.listen(CONFIG.port, function () {
  console.log(`Listening on port ${CONFIG.port}`);
});

function emailUsername(emailAddress)
{
    return emailAddress.substring(0, emailAddress.indexOf("@"));
}