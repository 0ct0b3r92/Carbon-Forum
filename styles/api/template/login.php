<?php
if (!defined('InternalAccess')) exit('{"Status": 0,"ErrorMessage": "403"}');
if($Error){
?>{
	"Status": 0,
	"ErrorMessage": "<?php echo $Error;?>"
}
<?php
}else{
?>
{
	"Status": 1,
	"UserID": "<?php echo $DBUser['ID']; ?>",
	"UserExpirationTime" : <?php echo $TemporaryUserExpirationTime; ?>,
	"UserCode" : <?php echo md5($DBUser['Password'] . $DBUser['Salt'] . $TemporaryUserExpirationTime . $SALT); ?>,
	"UserInfo" : <?php 
	unset($DBUser['Password']);
	unset($DBUser['Salt']);
	unset($DBUser['PasswordQuestion']);
	unset($DBUser['PasswordAnswer']);
	echo json_encode($DBUser, true);
	?>
}
<?php
}
?>