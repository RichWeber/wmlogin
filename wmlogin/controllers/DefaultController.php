<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		header("Location: https://login.wmtransfer.com/GateKeeper.aspx?RID="
				. $this->module->urlid . "&lang=ru-RU");
		
		//$this->render('index');
	}
	
	public function actionLogin()
	{
		$model=new LoginForm;
		$answer = $_POST;

		// collect user input data
		if(isset($answer['WmLogin_Ticket']))
		{
			if($model->login($answer))
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		//$this->render('login',array('model'=>$model));
		
		//$this->render('login');
	}
	
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}