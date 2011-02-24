<?php

class user {

public $username;
public $password;
public $email;

private $activated;
private $emailCount;
private $session_id;
private $ip;

private $minRegDelay; //delay

private $CookieLifeTime;

private $SALT_LENGTH;

	//runs every time a new reference is created
	public function __construct($newUsername, $newPassword, $newEmail) {
	
		//create useless session
		$this->createSession();
		
		$this->CookieLifeTime = 60*60*24*50; // 50 days
		$this->minRegDelay = 20; // one day 60*60*24
		$this->SALT_LENGTH = 32;

		$this->ip = $_SERVER['REMOTE_ADDR'];
		
		$this->username = $newUsername;
		$this->password['plaintext'] = $newPassword;
		$this->email = $newEmail;
		echo "class constructed: " . $this->username . "</br>";
	}

	//check registration possible
   public function registrationPossible() {
		//check time between last registration from IP (too many sign ups from your ip)
		//username allredy used
		//email allredy used
		$errorCode = 0;
		

		$result = mysql_query("
			SELECT UNIX_TIMESTAMP(MAX(usrCreated)) FROM tUser WHERE tUser.usrIP = '" . $this->ip . "'
		");
		
		$usrCreated = mysql_result($result, 0);
		
		if ( time() - $usrCreated < $this->minRegDelay ) {
			$errorCode = 10; // to much registrations
		}
		
		
		$result = mysql_query("
			SELECT COUNT(*) FROM tUser WHERE tUser.usrName = '" . $this->username . "'
		");
		if (mysql_result($result, 0) > 0) {
			$errorCode = 11; //username allready used
		}

		$result = mysql_query("
			SELECT COUNT(*) FROM tUser WHERE tUser.usrEmail = '" . $this->email . "'
		");
		if (mysql_result($result, 0) > 0) {
			
			if ($errorCode == 11) { $errorCode = 13;} //username and emailaddress are allready used
			else { $errorCode = 12;} // only the email adress is used
		}
		
		return $errorCode;
	  
	  
      //return exact faillure
   
   }
	
	//register user
	public function register() {
	  //if faillure = registrationPossible()
		//write new user in DB
		//sendValidationEmail();
	  //else put out faillure
	 
	  $errorCode = $this->registrationPossible();
	  
	  if ($errorCode == 0) {
       //if (true == true) {
       	  
          $saltAndHash = $this->generateHash($this->password['plaintext']);
          list($this->password['salt'], $this->password['hash']) = explode(";", $saltAndHash, 2);
  
       	  
       	  
		  mysql_query("
			  INSERT INTO tUser (usrName, usrPassword, usrSalt, usrEmail, usrIP)
			  VALUES ('" . $this->username . "', '" . $this->password['hash'] . "', '" . $this->password['salt'] . "', '" . $this->email . "', '" . $this->ip . "')
		   ");
		   
		   
	  
			$this->sendActivationEmail();
      		return true;
	  }
	  
	 switch ($errorCode) {
        case 0:
          echo "registred sucessfully";
          break;
        case 10:
          echo "to much registrattions from your IP";
          break;
        case 11:
          echo "username allready used";
          break;
        case 12:
          echo "email allready used";
          break;
        case 13:
          echo "email and username allready used";
          break;
      }
	  
	}

	//check validation in DB
    public function checkActivationDB() {
      //lookup in DB if valide
      //return true of false
      
      $result = mysql_query("SELECT COUNT(*) FROM tUser WHERE tUser.usrName = '" . $this->username . "' AND tUser.usrActiv = TRUE");
      $sqlResultCount = mysql_result($result, 0);
      
      if ($sqlResultCount == 1)
      {
        return true;
      }
      else {
        return false;
      }

      
      
    }   

	//send validation Email
    public function sendActivationEmail() {
    //if checkUserDB();
      //if not valide jet
        //if not sent to much Emails then
          // increment Email Sent count
          //send Mail with link from DB
          //or send again write in DB
          //return true if sent
      //else return false if not sent
	
	
	
	  $activationkey = md5(uniqid(rand() * rand(), true) . $this->username);
	  
	$bool = mysql_query("
		UPDATE tUser SET tUser.usrActivationtionkey = '$activationkey', usrActivationtionkeysent = usrActivationtionkeysent+1 WHERE tUser.usrName = '$this->username'
	");
      
		$body = <<<EOF
Hello Hello $this->username,

your activation key 
http://kebaparis.ch/login.php?akey=$activationkey
	#dev purposes
	http://127.0.0.1/dev/kebaparis/www/login_form.php?akey=$activationkey


EOF;
	  
		sendEmail($this->username . " <" . $this->email . ">", "kebaparis.ch registration", $body);
      

	}
	
	//generates the password hash and the salt
	public function generateHash($plainText, $salt = null) {

		if ($salt === null)
		{
			$salt = substr(md5(uniqid(rand(), true)), 0, $this->SALT_LENGTH);
		}
		else
		{
			$salt = substr($salt, 0, $this->SALT_LENGTH);
		}

		return $salt . ";" . md5($salt . $plainText);

	}


	//check validation link <> user
    public function checkActivationLink($linkSent) {

		$sqlResult = mysql_query("UPDATE tUser SET tUser.usrActiv = TRUE WHERE tUser.usrActivationtionkey = '$linkSent'");

		if  ($sqlResult) {
			$this->makeSessionUsable(); //login
			return true;
		}
		else {
			return false;
		}

	}

	//check if registred
    public function checkUserDB() {
      //lookup in DB if user exists
	  
	  $sqlResult = mysql_query("SELECT COUNT(*) FROM tUser WHERE tUser = '$this->username'");
	  $count = mysql_result($sqlResult, 0);
	  
	  if ($count == 1) {
		return true;
	  }
	  else {
		return false;
	  }

    }

	//creates a usless standart session
	private function createSession() {
	
		$this->session_id = session_id();
		if(empty($this->session_id)) {
			//create session if no jet existing
			session_name('usr_session');
			
			ini_set('session.use_cookies', true);
			ini_set('session.gc_maxlifetime', time() + $this->CookieLifeTime + 60);
			ini_set('session.cookie_lifetime', time() + $this->CookieLifeTime);
			
			session_set_cookie_params($this->CookieLifeTime);
			session_start();
			//standart values
			$_SESSION['logedin'] = false;
			$_SESSION['activated'] = false;
			$_SESSION['type'] = "user";
		}
	}
	
	//makeSessionUsable > login
    public function makeSessionUsable() {
      //checkLogin() if allready loged in
        // or modify session (rewrite session variable valide checkValidation())
      //not allready loged in
        //check email or username | password
          //write session variables (loggedin, checkValidationSession() username, email, usrType, )
          //write in DB last logged in and IP
          //return error
      //return true false
	

		$_SESSION['username'] = $this->username;
		$_SESSION['email'] = $this->email;
		$_SESSION['logedin'] = true;
		$_SESSION['type'] = "user";
		$_SESSION['activated'] = $this->checkActivationDB();
	  
	  
    }
    
	//check validation in Session
    public function checkValidationSession() {
      //lookup in Session if valide
      //return true of false
      if ($_SESSION['activated'] == true) {
        return true;
      }
      else {
        return false;
      }
      
    } 

	//destroy session > logout
    public function destroySession() {
      //if checkLogin true then
        //destroy session
        //return true
      //else
      //return false
    }


	//check Session > check login
    public function checkLogin() {
        //check session variable loggded in
      if ($_SESSION['logedin'] == true) {
        return true;
      }
      else {
        return false;
      }
		
    }

	//set user inactive
    public function removeUser() {
        //drop user OR set user inactive
        //reutn true if user dropped or not exsisted
        //return false if user not droped
        
        $bool = mysql_query("UPDATE tUser SET tUser.usrActiv = FAlSE WHERE tUser.usrName = '" . $this->username . "' AND tUser.usrActiv = TRUE");
        
		if ($bool) {
			echo "user was active ...";
		
		} else {
			echo "user was not active...";
		
		}
        
    }

	//runs every time the reference is droped
	public function __destruct() {
		//db
	}
   
   


} //end class user

class Database {

	private $db_handler;
	private $db_server;
	private $db_user;
	private $db_password;
	private $db_name;



	public function __construct() {
		include 'db_config.php';
	}
	
	
	public function connect() {

		if (!isset($this->db_handler)) {
			$this->db_handler = mysql_connect($this->db_server, $this->db_user, $this->db_password) or die ("connect to db failed!");
			mysql_select_db($this->db_name, $this->db_handler) or die ("select of db failed!");
			//echo "db conected: " . $this->db_server . "</br>";
		}
	
	}
	
	
	public function quit() {
	
		if (isset($this->db_handler)) {
			mysql_close($this->db_handler);
			$this->db_handler = NULL;
			//echo "db disconnected: " . $this->db_server;
		}
	
	}

	
	public function __destruct() {
	$this->quit();
	
	}




}


function sendEmail($recipient, $subject, $body) {
  
	if (!is_numeric($recipient)) {
		$to = $recipient;
	}
	else {
		//database query for email
	}
	
	require_once "Mail.php";

	include 'mail_config.php';


	$headers = array (
		'From' => $from,
		'To' => $to,
		'Subject' => $subject
	);
 
	$smtp = Mail::factory(
	'smtp',
	array (
		'host' => $host,
		'port' => $port,
		'auth' => TRUE,
		'username' => $username,
		'password' => $password,
		'debug' => false
		)
	);

	$mail = $smtp->send($to, $headers, $body);
	
	/*
	if (PEAR::isError($mail)) {
		echo("<p>" . $mail->getMessage() . "</p>");
	}
	else {
		echo("<p>Message successfully sent!</p>");
	} */
}





?>