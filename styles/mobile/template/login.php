<?php
if (!defined('InternalAccess')) exit('error: 403 Access Denied');
if(!$IsAjax){
?>
<div id="header">

</div>
<div id="content">
<?php } ?>
<!-- main-content start -->
	<div data-title="<?php echo $PageTitle; ?>" id="Login" class="panel" selected="true">
<?php
if($error){
?>
		<script type="text/javascript">CarbonAlert("<?php echo $error; ?>");</script>>
<?php
}
?>
		<br />
		<form action="?" method="post" onsubmit="JavaScript:this.Password.value=md5(this.Password.value);">
			<div class="input-group">
				<input type="hidden" value="<?php echo $ReturnUrl; ?>" name="ReturnUrl" />
				<input type="hidden" name="FormHash" value="<?php echo $FormHash; ?>" />

				<label for="UserName"><?php echo $Lang['UserName']; ?></label>
				<input type="text" name="UserName" id="UserName" value="<?php echo htmlspecialchars($UserName); ?>" />
				<br style="clear:both">
				<label for="Password"><?php echo $Lang['Password']; ?></label>
				<input type="password" name="Password" id="Password" value="" />
				<br style="clear:both">
				<label for="Expires"><?php echo $Lang['Login_Expiration_Time']; ?></label>
				<select name="Expires" id="Expires" style="display:inline;">
					<option value="30">30<?php echo $Lang['Days']; ?></option>
					<option value="14">14<?php echo $Lang['Days']; ?></option>
					<option value="7">7<?php echo $Lang['Days']; ?></option>
					<option value="1">1<?php echo $Lang['Days']; ?></option>
					<option value="0">0<?php echo $Lang['Days']; ?></option>
				</select>
				<br style="clear:both">
				<label for="VerifyCode"><?php echo $Lang['Verification_Code']; ?></label>
				<input type="text" name="VerifyCode" id="VerifyCode" onclick="document.getElementById('Verification_Code_Img').src='<?php echo $Config['WebsitePath']; ?>/seccode.php';" value="" placeholder="<?php echo $Lang['Verification_Code']; ?>"  style="width:33%;"/>
				<img src="" id="Verification_Code_Img" style="cursor: pointer;" onclick="this.src+=''" align="absmiddle" />
				<br style="clear:both">
				<a href="<?php echo $Config['WebsitePath']; ?>/register" class="button"><?php echo $Lang['Sign_Up']; ?></a>
				<input type="submit" class="button" value="<?php echo $Lang['Log_In']; ?>" name="submit" style="float:right;" />
				
			</div>
		</form>
	<!-- main-content end -->
<?php
if(!$IsAjax){
?>
	</div>
<!-- this is the default left side nav menu.  If you do not want any, do not include these -->
<nav>
	<ul class="list">
		<?php include($TemplatePath.'sider.php'); ?>
	</ul>
</nav>
<?php } ?>