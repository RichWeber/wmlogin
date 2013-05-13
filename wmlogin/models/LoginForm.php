<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	private $_identity;

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login($answer)
	{
		$WmLogin_WMID = $answer['WmLogin_WMID'];
		$WmLogin_Ticket = $answer['WmLogin_Ticket'];
		$WmLogin_AuthType = $answer['WmLogin_AuthType'];
		$WmLogin_UserAddress = $answer['WmLogin_UserAddress'];
		
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($WmLogin_WMID, $WmLogin_Ticket,
					$WmLogin_AuthType, $WmLogin_UserAddress);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration= 0; // 30 days
			Yii::app()->user->login($this->_identity, $duration);
			
			return true;
		}
		else
			return false;
	}
}
