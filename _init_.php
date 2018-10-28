<?php
class GoogleAuth{
	private $CODE ;
	private $SECRET ;
	private $GA ;
	private $APPNAME ;
	private $USERNAME ;
	private $QRCODE ;
	public $HASLOGIN ;
	public function  __construct($APPNAME="TestApp",$USERNAME="TestName"){
		include_once 'FixedBitNotation.php';
		include_once 'GoogleAuthenticatorInterface.php';
		include_once 'GoogleAuthenticator.php';
		include_once 'GoogleQrUrl.php';
		if(session_status() == PHP_SESSION_NONE)
			session_start();
		$this->APPNAME = defined("APPNAME") ? APPNAME : $APPNAME;
		$this->USERNAME = defined("USERNAME") ? USERNAME : $USERNAME;
		$this->GA = new GoogleAuthenticator\GoogleAuthenticator();
	}
	public function hasLogin($CODE=false,$CODE_KEY="code",$SECRET_KEY="secret"){
		try{
			$this->CODE = isset($_REQUEST[$CODE_KEY]) || ($CODE === false)?$_REQUEST[$CODE_KEY]:$CODE;
			$this->SECRET = isset($_SESSION[$SECRET_KEY])?$_SESSION[$SECRET_KEY]:$SECRET_KEY/*This Could be ""*/;
			echo "Secret is ".$this->SECRET." And Code is ".$this->CODE."<br>";
			$this->HASLOGIN = $this->GA->checkCode(strval($this->SECRET), strval($this->CODE));
			$_SESSION['has_login'] = $this->HASLOGIN;
			$_SESSION[$CODE_KEY] = $this->CODE;
			$_SESSION[$SECRET_KEY] = $this->SECRET;
			return $this->HASLOGIN;
		}finally{
			if($this->HASLOGIN === false){
				$this->SECRET = $this->SECRET == $SECRET_KEY?$this->GA->generateSecret():$this->SECRET;
				$this->QRCODE = base64_encode(file_get_contents(GoogleAuthenticator\GoogleQrUrl::generate($this->USERNAME, $this->SECRET, $this->APPNAME)));
				$_SESSION[$SECRET_KEY] = $this->SECRET;
				$_SESSION['qrcode'] = $this->QRCODE;
			}
		}
	}
	public function showQr($CLASS=""){
		echo "<img class='$CLASS' src='data:image/png;base64,".$this->QRCODE."' width='200px' height='200px' />";
	}
}
/*
*	
*	
*/


?>