<?php
require(dirname(__FILE__) . '/common.php');
require(dirname(__FILE__) . '/language/' . ForumLanguage . '/forgot.php');
$Message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$UserName   = strtolower(Request('Post', 'UserName'));
	$Email      = strtolower(Request('Post', 'Email'));
	$VerifyCode = intval(Request('Post', 'VerifyCode'));
	$UserInfo = array();

	if (!ReferCheck($_POST['FormHash'])) {
		AlertMsg($Lang['Error_Unknown_Referer'], $Lang['Error_Unknown_Referer'], 403);
	}
	if($UserName && $Email && $VerifyCode){
		session_start();
		if (isset($_SESSION[$Prefix . 'VerificationCode']) && $VerifyCode === intval($_SESSION[$Prefix . 'VerificationCode'])) {
			$UserInfo = $DB->row('SELECT * FROM ' . $Prefix . 'users 
				Where UserName=:UserName', 
				array(
				'UserName' => $UserName
			));
			if ($UserInfo){
				if($Email === $UserInfo['UserMail']){
					//生成有效期2小时的Access Token
					$TokenExpirationTime = 7200 + $TimeStamp;
					$AccessToken = base64_encode($UserName .'|' . $TokenExpirationTime . '|' . md5($UserInfo['Password'] . $UserInfo['Salt'] . md5($TokenExpirationTime) . md5($SALT)));
					$ResetPasswordURL = 'http://'.$Config['Main_Domainname'].'/reset_password/'.$AccessToken;
					//向数据库里的密保邮箱发送邮件
					require(dirname(__FILE__) . '/includes/PHPMailer.smtp.class.php');
					$MailObject = new PHPMailer;
					//$MailObject->SMTPDebug = 3;// Enable verbose debug output

					$MailObject->isSMTP();// Set mailer to use SMTP
					$MailObject->Host = $Config['SMTPHost'];  // Specify main and backup SMTP servers
					$MailObject->SMTPAuth = true;// $Config['SMTPAuth']           Enable SMTP authentication
					$MailObject->Username = $Config['SMTPUsername'];// SMTP username
					$MailObject->Password = $Config['SMTPPassword'];// SMTP password
					$MailObject->SMTPSecure = 'tls';// Enable TLS encryption, `ssl` also accepted
					$MailObject->Port = intval($Config['SMTPPort']);// TCP port to connect to

					$MailObject->From = $Config['SMTPUsername'];
					$MailObject->FromName = $Config['SiteName'];
					$MailObject->addAddress($UserInfo['UserMail'], $UserName);// Add a recipient
					/*
					$MailObject->addAddress('ellen@example.com');// Name is optional
					$MailObject->addReplyTo('info@example.com', 'Information');
					$MailObject->addCC('cc@example.com');
					$MailObject->addBCC('bcc@example.com');
					
					$MailObject->addAttachment('/var/tmp/file.tar.gz');// Add attachments
					$MailObject->addAttachment('/tmp/image.jpg', 'new.jpg');// Optional name
					*/
					$MailObject->isHTML(true);// Set email format to HTML

					$MailObject->Subject = str_replace('{{UserName}}', $UserName, str_replace('{{SiteName}}', $Config['SiteName'], $Lang['Mail_Template_Subject']));
					$MailObject->Body    = $MailObject->Subject = str_replace('{{UserName}}', $UserName, str_replace('{{ResetPasswordURL}}', $ResetPasswordURL, $Lang['Mail_Template_Body']));
					//$MailObject->AltBody = 'This is the body in plain text for non-HTML mail clients';

					if(!$MailObject->send()) {
						$Message = $Lang['Email_Could_Not_Be_Sent'] . 'Mailer Error: ' . $MailObject->ErrorInfo;
					} else {
						$Message =  $Lang['Email_Has_Been_Sent'];
					}
				}else{
					$Message = $Lang['Email_Error'];
				}
			}else{
				$Message = $Lang['User_Does_Not_Exist'];
			}
		} else {
			$Message = $Lang['Verification_Code_Error'];
		}
		unset($_SESSION[$Prefix . 'VerificationCode']);
	}else{
		$Message = $Lang['Forms_Can_Not_Be_Empty'];
	}
}


$DB->CloseConnection();
$PageTitle    = $Lang['Forgot_Password'];
$ContentFile  = $TemplatePath . 'forgot.php';
include($TemplatePath . 'layout.php');