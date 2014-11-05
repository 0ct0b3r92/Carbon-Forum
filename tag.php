<?php
require(dirname(__FILE__)."/common.php");

$TagName = Request('Get', 'name');
$Page = Request('Get', 'page');
$Page = intval(empty($Page)?1:$Page);

$TagInfo = array();
if($TagName)
{
	$TagInfo = $DB->row('SELECT * FROM '.$Prefix.'tags force index(TagName) Where Name=:Name',array('Name'=>$TagName));
}
if(!$TagInfo)
{
	AlertMsg('标签不存在','标签不存在',404);
}

// 处理正确的页数
$TotalPage = ceil($TagInfo['TotalPosts']/$Config['TopicsPerPage']);
if($Page<0){
	header('location: '.$Config['WebsitePath'].'/tag/'.$TagInfo['Name']);
	exit;
}else if($Page>$TotalPage){
	header('location: '.$Config['WebsitePath'].'/tag/'.$TagInfo['Name'].'/page/'.$TotalPage);
	exit;
}

if($Page == 0)
{
	$Page = 1;
}
$TagIDArray = $DB->column('SELECT TopicID FROM '.$Prefix.'posttags force index(TagsIndex) Where TagID=:TagID ORDER BY TopicID DESC LIMIT '.($Page-1)*$Config['TopicsPerPage'].','.$Config['TopicsPerPage'],array('TagID'=>$TagInfo['ID']));

$TopicsArray = $DB->query('SELECT `ID`, `Topic`, `Tags`, `UserID`, `UserName`, `LastName`, `LastTime`, `Replies` FROM '.$Prefix.'topics force index(PRI) Where ID in (?) and IsDel=0 ORDER BY LastTime DESC',$TagIDArray);
//var_dump($TopicsArray);
$DB->CloseConnection();
// 页面变量
$PageTitle = $TagInfo['Name'];
$PageMetaDesc = $TagInfo['Name'].' - '.htmlspecialchars(strip_tags(mb_substr($TagInfo['Description'], 0, 150, 'utf-8')));
$ContentFile = $TemplatePath.'tag.php';
include($TemplatePath.'layout.php');
?>