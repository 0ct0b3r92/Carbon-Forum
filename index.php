<?php
require(dirname(__FILE__)."/common.php");

$Page = intval(Request('Get', 'page', 1));
// 处理正确的页数
$TotalPage = ceil($Config['NumTopics']/$Config['TopicsPerPage']);
if($Page<=0){
	header('location: '.$Config['WebsitePath'].'/');
	exit;
}else if($Page>$TotalPage){
	header('location: '.$Config['WebsitePath'].'/page/'.$TotalPage);
	exit;
}

if($Page == 0)
{
	$Page = 1;
}

if($Page<=10)
{
	$TopicsArray = $DB->query('SELECT * FROM '.$Prefix.'topics force index(LastTime) WHERE IsDel=0 ORDER BY LastTime DESC LIMIT '.($Page-1)*$Config['TopicsPerPage'].','.$Config['TopicsPerPage']);
}else{
	$TopicsArray = $DB->query('SELECT * FROM '.$Prefix.'topics force index(LastTime) WHERE LastTime<=(SELECT LastTime FROM '.$Prefix.'topics ORDER BY LastTime DESC LIMIT '.($Page-1)*$Config['TopicsPerPage'].',1) and IsDel=0 ORDER BY LastTime DESC LIMIT '.$Config['TopicsPerPage']);
}



$DB->CloseConnection();
// 页面变量
$PageTitle = $Config['SiteName'];
$PageMetaDesc = htmlspecialchars(mb_substr($Config['SiteDesc'], 0, 150, 'utf-8'));
$ContentFile = $TemplatePath.'home.php';
include($TemplatePath.'layout.php');
?>