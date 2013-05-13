<?php
/*
 * Ответ на страницу: http://org.richweber.net/index.php/wmlogin/default/login
 */

define("WMXI_URL_LOGIN_AUTHORIZE", "https://login.wmtransfer.com/ws/authorize.xiface");

class WmloginModule extends CWebModule
{
	public $siteHolder = '';
	public $urlid = '';
	public $encoding = "UTF-8";
	//var $wm_cert = $_SERVER[DOCUMENT_ROOT] . "/protected/modules/wmlogin/components/WMunited.cer";
	//var $wm_cert = "../components/WMunited.cer";
	var $wm_cert = "";
	
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'wmlogin.models.*',
			'wmlogin.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
	
	/*
	 * change string encoding
	 */
	public function _change_encoding($text, $encoding, $entities = false)
	{
		$text = $entities ? htmlspecialchars($text, ENT_QUOTES) : $text;
		return mb_convert_encoding($text, $encoding, $this->encoding);
	}
	
	/*
	 * internal request structure creator
	 */
	public function xml($data)
	{
		$result = "";
	
		foreach($data as $k => $v) {
			$value = is_array($v) ? "\n".$this->xml($v) : $this->_change_encoding($v, "HTML-ENTITIES", true);
			$result .= "<$k>$value</$k>\n";
		}
	
		return $result;
	}
	
	/*
	 * check the ticket for a regular expression
	 */
	public function checkTicket($ticket)
	{
		//echo 'I am here checkTicket<br />';
		if (preg_match('/^[a-zA-Z0-9\$\!\/]{32,48}$/i', $ticket)) return true;
		else return false;
	}
	
	/*
	 * external request structure creator 
	 */ 
	public function _xml($data, $name = "request") 
	{
		return $this->xml(array($name => $data));
	}
	
	/*
	 * request to server
	 */
	public function _request($url, $xml) 
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		if ($this->wm_cert != "") {
			curl_setopt($ch, CURLOPT_CAINFO, $this->wm_cert);
		} else {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
	
		$result = curl_exec($ch);
		if(curl_errno($ch) != 0) {
			$result = "";
			$result .= "<errno>".curl_errno($ch)."</errno>\n";
			$result .= "<error>".curl_error($ch)."</error>\n";
		};
		curl_close($ch);
	
		return $result;
	}
	
	/*
	 * check wmlogin authorize
	 */
	public function LX1($WmLogin_WMID, $WmLogin_Ticket, $WmLogin_AuthType, 
			$WmLogin_UserAddress) 
	{
		$data=array('siteHolder' => $this->siteHolder,
				'user' => $WmLogin_WMID,
				'ticket' => $WmLogin_Ticket,
				'urlId' => $this->urlid,
				'authType' => $WmLogin_AuthType,
				'userAddress' => $WmLogin_UserAddress);
		
		$xml=$this->_xml($data);
		$result = $this->_request(WMXI_URL_LOGIN_AUTHORIZE, $xml);
		
		$response = simplexml_load_string($result);
		$arr = array();
	
		$arr['retval'] = $response->attributes()->retval;
	
		return $arr['retval'];
	}
}
