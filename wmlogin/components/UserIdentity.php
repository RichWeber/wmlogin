<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $WmLogin_WMID;
	public $WmLogin_Ticket;
	public $WmLogin_AuthType;
	public $WmLogin_UserAddress;
	
	private $_id;
	
	/**
      * Constructor.
      * @param string $username username
      * @param string $password password
      */
     public function __construct($WmLogin_WMID, $WmLogin_Ticket,
     		$WmLogin_AuthType, $WmLogin_UserAddress)
     {
        $this->WmLogin_WMID=$WmLogin_WMID;
        $this->WmLogin_Ticket=$WmLogin_Ticket;
        $this->WmLogin_AuthType=$WmLogin_AuthType;
        $this->WmLogin_UserAddress=$WmLogin_UserAddress;
         
     }
	
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{		
		if (!Yii::app()->controller->module->checkTicket($this->WmLogin_Ticket))
			$this->errorCode=self::ERROR_WMLOGIN_TICKET_INVALID;
			
		
		$auth = Yii::app()->controller->module->LX1( $this->WmLogin_WMID, $this->WmLogin_Ticket,
				$this->WmLogin_AuthType, $this->WmLogin_UserAddress);
		
		if($auth != 0)
			header("Location: https://login.wmtransfer.com/GateKeeper.aspx?RID="
						. Yii::app()->controller->module->urlid . "&lang=ru-RU");		
		else
			$this->_id=$this->WmLogin_WMID;
			$this->setState('name', $this->WmLogin_WMID);
			$this->errorCode=self::ERROR_NONE;
		
		return !$this->errorCode;
	}
	
	public function getId()
	{
		return $this->_id;
	}
}