<?php
include(dirname(__FILE__) . '/common.php');

Auth(1,0,true);

$Error = '';
$Title = '';
$Content = '';
$TagsArray = array();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(!ReferCheck($_POST['FormHash'])) {
		AlertMsg('来源错误','来源错误(unknown referer)',403);
	}
	$Title = Request('Post','Title');
	$Content = Request('Post','Content');
	$TagsArray = $_POST['Tag'];
	if($Title){
		if(strlen($Title) <= $Config['MaxTitleChars'] || strlen($Content) <= $Config['MaxPostChars']){
			if(!empty($TagsArray) && !in_array('', $TagsArray) && count($TagsArray)<=$Config["MaxTagsNum"])
			{
				//获取已存在的标签
				$TagsExistArray = $DB->query("SELECT ID,Name FROM `".$Prefix."tags` WHERE `Name` in (?)", $TagsArray);
				$TagsExist = ArrayColumn($TagsExistArray,'Name');
				$TagsID = ArrayColumn($TagsExistArray,'ID');
				//var_dump($TagsExist);
				$NewTags = TagsDiff($TagsArray, $TagsExist);
				//新建不存在的标签
				if($NewTags)
				{
					foreach ($NewTags as $Name) {
						$DB->query("INSERT INTO `".$Prefix."tags` (`ID`, `Name`,`Icon`,`Description`, `IsEnabled`, `TotalPosts`, `MostRecentPostTime`, `DateCreated`) VALUES (?,?,?,?,?,?,?,?)",array(null, htmlspecialchars(trim($Name)), 0, null, 1, 1, $TimeStamp, $TimeStamp));
						$TagsID[] = $DB->lastInsertId();
					}
					//更新全站统计数据
					$NewConfig = array(
						"NumTags" => $Config["NumTags"]+count($NewTags)
						);
					//var_dump($NewTags);
				}
				$TagsArray = array_merge($TagsExist, $NewTags);
				//往Topics表插入数据
				$TopicData = array(
					"ID"=> null, 
					"Topic"=> htmlspecialchars($Title), 
					"Tags" => implode("|", $TagsArray), //过滤不合法的标签请求
					"UserID" => $CurUserID, 
					"UserName" => $CurUserName, 
					"LastName" => "", 
					"PostTime" => $TimeStamp, 
					"LastTime" => $TimeStamp, 
					"IsGood" => 0, 
					"IsTop" => 0, 
					"IsLocked" => 0, 
					"IsDel" => 0, 
					"IsVote" => 0, 
					"Views" => 0, 
					"Replies" => 0, 
					"RatingSum" => 0, 
					"TotalRatings" => 0, 
					"LastViewedTime" => 0, 
					"PostsTableName" => null, 
					"ThreadStyle" => "", 
					"Lists" => "", 
					"ListsTime" => $TimeStamp, 
					"Log" => ""
					);
				$NewTopicResult = $DB->query("INSERT INTO `".$Prefix."topics`(`ID`, `Topic`, `Tags`, `UserID`, `UserName`, `LastName`, `PostTime`, `LastTime`, `IsGood`, `IsTop`, `IsLocked`, `IsDel`, `IsVote`, `Views`, `Replies`, `RatingSum`, `TotalRatings`, `LastViewedTime`, `PostsTableName`, `ThreadStyle`, `Lists`, `ListsTime`, `Log`) VALUES (:ID,:Topic,:Tags,:UserID,:UserName,:LastName,:PostTime,:LastTime,:IsGood,:IsTop,:IsLocked,:IsDel,:IsVote,:Views,:Replies,:RatingSum,:TotalRatings,:LastViewedTime,:PostsTableName,:ThreadStyle,:Lists,:ListsTime,:Log)",$TopicData);

				$TopicID = $DB->lastInsertId();
				//往Posts表插入数据
				$PostData = array(
					"ID" => null, 
					"TopicID" => $TopicID, 
					"IsTopic" => 1, 
					"UserID" => $CurUserID, 
					"UserName" => $CurUserName, 
					"Subject" => htmlspecialchars($Title), 
					"Content" => XssEscape($Content),
					"PostIP" => $CurIP, 
					"PostTime" => $TimeStamp
					);
				$NewPostResult = $DB->query("INSERT INTO `".$Prefix."posts`(`ID`, `TopicID`, `IsTopic`, `UserID`, `UserName`, `Subject`, `Content`, `PostIP`, `PostTime`) VALUES (:ID,:TopicID,:IsTopic,:UserID,:UserName,:Subject,:Content,:PostIP,:PostTime)",$PostData);

				$PostID = $DB->lastInsertId();

				if($NewTopicResult && $NewPostResult)
				{
					//更新全站统计数据
					$NewConfig = array(
						"NumTopics" => $Config["NumTopics"]+1
						);
					UpdateConfig($NewConfig);
					//更新用户自身统计数据
					$DB->query("UPDATE `".$Prefix."users` SET Topics=Topics+1, LastPostTime=? WHERE `ID`=?",array($TimeStamp, $CurUserID));
					//标记附件所对应的帖子标签
					$DB->query("UPDATE `".$Prefix."upload` SET PostID=? WHERE `PostID`=0 and `UserName`=?",array($PostID, $CurUserName));
					//记录标签与TopicID的对应关系
					foreach ($TagsID as $TagID) {
						$DB->query("INSERT INTO `".$Prefix."posttags`(`TagID`, `TopicID`, `PostID`) VALUES (?,?,?)",array($TagID, $TopicID, $PostID));
					}
					//更新标签统计数据
					if($TagsExist)
					{
						$DB->query("UPDATE `".$Prefix."tags` SET TotalPosts=TotalPosts+1, MostRecentPostTime=".$TimeStamp." WHERE `Name` in (?)",$TagsExist);
					}
					//添加提醒消息
					AddingNotifications($Content, $TopicID, $PostID);
					//跳转到主题页
					header('location: '.$Config['WebsitePath'].'/t/'.$TopicID);
				}

			}else{
				$Error = '标签不能为空';
			}
		}else{
			$Error = '标题长度不能超过'.$Config['MaxTitleChars'].'个字节，内容长度不能超过'.$Config['MaxPostChars'].'个字节';
		}
	}else{
		$Error = '标题不能为空';
	}
}
$DB->CloseConnection();
// 页面变量
$PageTitle = '发新帖';
$ContentFile = $TemplatePath.'new.php';
include($TemplatePath.'layout.php');
?>