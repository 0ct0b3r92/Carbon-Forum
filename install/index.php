<?php
set_time_limit(0);
$Message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$fp = @fopen(dirname(__FILE__).'/database.sql', "r") or die("不能打开SQL文件");
	$DBHost = $_POST['DBHost'];
	$DBName = $_POST['DBName'];
	$DBUser = $_POST['DBUser'];
	$DBPassword = $_POST['DBPassword'];
	$WebsitePath = $_POST['WebsitePath'];
	//初始化数据库操作类
	require('../includes/PDO.class.php');
	$DB = new Db($DBHost, $DBName, $DBUser, $DBPassword);
	//数据库安装
	while($SQL=GetNextSQL()){
		$DB->query($SQL);
	}
	$DB->query("INSERT INTO `carbon_config` VALUES ('WebsitePath', '".$WebsitePath."')");
	$DB->query("INSERT INTO `carbon_config` VALUES ('LoadJqueryUrl', '".$WebsitePath."/static/js/jquery.js')");
	$DB->CloseConnection();
	fclose($fp) or die("Can’t close file");

	//写入config文件
	$ConfigPointer=fopen(dirname(__FILE__).'/config.tpl','r');
	$ConfigBuffer=fread($ConfigPointer, filesize(dirname(__FILE__).'/config.tpl'));
	$ConfigBuffer = str_replace("{{DBHost}}",$DBHost,$ConfigBuffer);
	$ConfigBuffer = str_replace("{{DBName}}",$DBName,$ConfigBuffer);
	$ConfigBuffer = str_replace("{{DBUser}}",$DBUser,$ConfigBuffer);
	$ConfigBuffer = str_replace("{{DBPassword}}",$DBPassword,$ConfigBuffer);
	fclose($ConfigPointer);
	$ConfigPHP = fopen("../config.php","w+");       
	fwrite($ConfigPHP,$ConfigBuffer);
	fclose($ConfigPHP);

	//写入htaccess文件
	$HtaccessPointer=fopen(dirname(__FILE__).'/htaccess.tpl','r');
	$HtaccessBuffer=fread($HtaccessPointer, filesize(dirname(__FILE__).'/htaccess.tpl'));
	$HtaccessBuffer = str_replace("{{WebSitePath}}",$WebsitePath,$HtaccessBuffer);
	fclose($HtaccessPointer);
	$Htaccess = fopen("../.htaccess","w+");       
	fwrite($Htaccess, $HtaccessBuffer );
	fclose($Htaccess);

	//rewrite文件配置
	$Message = '安装成功，安装完成后请马上删除install文件夹。<br /><a href="../register">点我马上注册管理员账号</a>';
	
}
//从文件中逐条取SQL
function GetNextSQL() {
	global $fp;
	$sql="";
	while ($line = @fgets($fp, 40960)) {
		$line = trim($line);
		//以下三句在高版本php中不需要，在部分低版本中也许需要修改
		/*
		$line = str_replace("////","//",$line);
		$line = str_replace("/’","’",$line);
		$line = str_replace("//r//n",chr(13).chr(10),$line);
		*/
		//$line = stripcslashes($line);
		if (strlen($line)>1) {
			if ($line[0]=="-" && $line[1]=="-") {
				continue;
			}
		}
		$sql.=$line.chr(13).chr(10);
		if (strlen($line)>0){
			if ($line[strlen($line)-1]==";"){
				break;
			}
		}
	}
	return $sql;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta content="True" name="HandheldFriendly" />
<title>Carbon Forum安装程序</title>
<link href="../styles/default/theme/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<!-- content wrapper start -->
	<div class="wrapper">
		<!-- main start -->
		<div class="main">
			<!-- main-content start -->
<div class="main-content">
	<div class="title">
		Carbon Forum &raquo; 安装
	</div>
	<div class="main-box">
			<form action="?" method="post">
			<table cellpadding="5" cellspacing="8" border="0" width="100%" class="fs14">
				<tbody>
					<tr>
						<td width="180" align="right"></td>
						<td width="auto" align="left"><span class="red"><?php echo $Message; ?></span></td>
					</tr>
					<tr>
						<td width="180" align="right">数据库地址</td>
						<td width="auto" align="left"><input type="text" name="DBHost" class="sl w200" value="127.0.0.1" /></td>
					</tr>
					<tr>
						<td width="180" align="right">数据库名</td>
						<td width="auto" align="left"><input type="text" name="DBName" class="sl w200" value="" /></td>
					</tr>
					<tr>
						<td width="180" align="right">数据库登陆账号</td>
						<td width="auto" align="left"><input type="text" name="DBUser" class="sl w200" value="root" /></td>
					</tr>
					<tr>
						<td width="180" align="right">数据库密码</td>
						<td width="auto" align="left"><input type="password" name="DBPassword" class="sl w200" value="" /></td>
					</tr>
					<tr>
						<?php
							$WebsitePath = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
							if(preg_match('/(.*)\/install/i', $WebsitePath , $WebsitePathMatch))
							{
								$WebsitePath = $WebsitePathMatch[1];
							}else{
								$WebsitePath = '';
							}
						?>
						<td width="180" align="right">安装路径<br />(自动检测，无需修改)</td>
						<td width="auto" align="left"><input type="text" name="WebsitePath" class="sl w200" value="<?php echo $WebsitePath; ?>" /></td>
					</tr>
					<tr>
						<td width="180" align="right"></td>
						<td width="auto" align="left"><input type="submit" value="安 装" name="submit" class="textbtn" /></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
	<!-- main-content end -->
	<div class="main-sider">
		<div class="sider-box">
			<div class="sider-box-title">安装说明</div>
			<div class="sider-box-content">
				<p>
				如果出现“Access denied”错误说明填写不整齐，请返回重新填写。
				</p>
				<p>
				安装完毕后，第一个注册的用户将会自动成为管理员。
				</p>
			</div>
		</div>
	</div>
		<div class="c"></div>
		</div>
		<!-- main end -->
		<div class="c"></div>

		<!-- footer start -->
		<div class="Copyright">
			<p>
			Power By <a href="http://www.94cb.com" target="_blank">Carbon Forum V3.2.0</a> © 2006-2014
			</p>
		</div>
		<!-- footer end -->

	</div>
	<!-- content wrapper end -->
</body>
</html>