<?php
if (!defined('InternalAccess')) exit('error: 403 Access Denied');
?>
<!-- main-content start -->
<div class="main-content">
<?php
if($Page==1)
{
?>
<!-- post main content start -->
<div class="main-box without-title">
<div class="topic-title">
	<div class="topic-title-main float-left">
		<h1><?php  echo $topic['Topic']; ?></h1>
		<div class="topic-title-date">
		By <a href="<?php echo $Config['WebsitePath'].'/u/'.$topic['UserName']; ?>"><?php echo $topic['UserName']; ?></a>
 at <?php echo FormatTime($topic['PostTime']); ?> • <?php echo ($topic['Views']+1); ?>点击
 • 收藏 • 编辑
		</div>
	</div>
	<div class="detail-avatar"><a href="<?php echo $Config['WebsitePath'].'/u/'.$topic['UserName']; ?>">
<?php echo GetAvatar($topic['UserID'], $topic['UserName'], 'large'); ?>

	</a></div>
	<div class="c"></div>
</div>
<div class="topic-content">
<p><?php echo $PostsArray[0]['Content']; ?></p>
</div>
<div class="topic-tags btn">
<?php
if($topic['Tags']){
	foreach (explode("|", $topic['Tags']) as $Tag) {
?><a href="<?php echo $Config['WebsitePath']; ?>/tag/<?php echo urlencode($Tag); ?>" target="_blank"><?php echo $Tag; ?></a>
<?php
	}
}

if($CurUserRole>=4){
	if($topic['IsDel']==0){
?>
<a href="###" onclick="javascript:Manage(<?php echo $id; ?>, 1, 'Delete', true, this);" style="float:right;">删除</a>
<?php
}else{
?>
<a href="###" onclick="javascript:Manage(<?php echo $id; ?>, 1, 'Recover', false, this);" style="float:right;">恢复</a>
<a href="###" onclick="javascript:Manage(<?php echo $id; ?>, 1, 'PermanentlyDelete', true, this);" style="float:right;">永久删除</a>
<?php
	}
}
?>
<a href="###" onclick="javascript:Manage(<?php echo $id; ?>, 4, 1, false, this);" style="float:right;"><?php echo $IsFavorite?'取消收藏':'收藏'; ?></a>
<div class="c"></div>
</div>
</div>
<!-- post main content end -->
<?php
}
unset($PostsArray[0]);
if($topic['Replies']!=0)
{
?>
<!-- comment list start -->
<div class="title">
	<?php echo $topic['Replies']; ?> 回复  |  直到 <?php echo FormatTime($topic['LastTime']); ?>
</div>
<div class="main-box home-box-list">
<?php
foreach($PostsArray as $key => $post)
{
	if($Page!=1)
		$PostFloor=($Page-1)*$Config['PostsPerPage']+$key-1;
	else
		$PostFloor=($Page-1)*$Config['PostsPerPage']+$key;
?>
	<div class="commont-item">
		<a name="Post<?php echo $post['ID'];?>"></a>
		<div class="commont-avatar">
			<a href="<?php echo $Config['WebsitePath'].'/u/'.$post['UserName']; ?>">
			<?php echo GetAvatar($post['UserID'], $post['UserName'], 'middle'); ?>

	</a>
		</div>
		<div class="commont-data">
			<div class="commont-content">
			<p><?php echo $post['Content']; ?></p>
			</div>
			
			<div class="commont-data-date">
				<div class="float-left"><a href="<?php echo $Config['WebsitePath'].'/u/'.$post['UserName']; ?>"><?php echo $post['UserName'];?></a>
			 &nbsp;&nbsp;&nbsp;<?php if($CurUserRole>=4){ ?> • &nbsp;&nbsp;&nbsp;<a href="###" onclick="javascript:Manage(<?php echo $post['ID']; ?>, 2, 'Delete', true, this);" style="float:right;">删除</a><?php } ?>

				</div>
				<div class="float-right">
	&laquo; <a href="#reply" onclick="JavaScript:Reply('<?php echo $post['UserName'];?>', <?php echo $PostFloor; ?>, <?php echo $post['ID'];?>);">回复</a>
	<span class="commonet-count">#<?php echo $PostFloor; ?></span></div>
				<div class="c"></div>
			</div>
			<div class="c"></div>
		</div>
		<div class="c"></div>
	</div>
<?php
}
if($TotalPage>1){
?>
<div class="pagination">
	<?php Pagination("/t/".$id."-",$Page,$TotalPage); ?>
<div class="c"></div>
</div>
<?php
}
?>
</div>
<!-- comment list end -->
<?php
}
?>
<!-- editor start -->
<?php
if(!$topic['IsLocked'] && !$CurUserInfo){
?>
<div class="ad"><p>登陆后方可回帖</p></div>
<?php
}else if($topic['IsLocked']){
?>
<div class="ad"><p>此帖已被锁定，禁止回复</p></div>
<?php
}else{
?>

<div class="title">
	<div class="float-left">添加一条新回复<a name="reply"></a></div>
	<div class="float-right"><a href="#">↑ 回到顶部</a></div>
	<div class="c"></div>    
</div>
<div class="main-box">
	<script>
	var MaxPostChars = <?php echo $Config['MaxPostChars']; ?>;//主题内容最多字节数
	</script>
	<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/editor/ueditor.config.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/editor/ueditor.all.min.js"> </script>
	<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
	<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
	<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/editor/lang/zh-cn/zh-cn.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/js/reply.function.js"></script>
	<form action="<?php echo $Config['WebsitePath']; ?>/reply" method="post" name="reply" onsubmit="JavaScript:return SubmitCheck();">
	<input type="hidden" name="FormHash" value="<?php echo $FormHash; ?>">
	<input type="hidden" name="TopicID" value="<?php echo $id; ?>">
	<p>
		<script id="editor" type="text/plain" style="width:648px;height:160px;"></script>
		<script type="text/javascript">
			//实例化编辑器
			window.UEDITOR_CONFIG['textarea'] = 'Content';
			//window.UEDITOR_CONFIG['initialFrameHeight'] = 160;
			window.UEDITOR_CONFIG['elementPathEnabled'] = false;
			window.UEDITOR_CONFIG['toolbars'] = [['fullscreen', 'source', '|', 'bold', 'italic', 'underline', '|' , 'blockquote', 'insertcode', 'insertorderedlist', 'insertunorderedlist', '|', 'emotion', 'simpleupload', 'insertimage', 'scrawl', 'insertvideo', 'music', 'attachment', '|', 'removeformat', 'autotypeset']];
			UE.getEditor('editor',{onready:function(){
				if(window.localStorage){
					//从草稿中恢复
					RecoverContents();
				}
			}});
		</script>
	</p>
	<div class="float-left"><input type="submit" value=" 提 交 " name="submit" class="textbtn"></div>
	<div class="c"></div> 
	<p></p>
	</form>
</div>
<?php
}
?>
<!-- editor end -->
</div>
<!-- main-content end -->
<!-- main-sider start -->
<div class="main-sider">
	<?php include($TemplatePath.'sider.php'); ?>
</div>
<!-- main-sider end -->
<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/editor/ueditor.parse.min.js"> </script>
<script type="text/javascript">
uParse('.main-content',{
	'rootPath': '<?php echo $Config['WebsitePath']; ?>/static/editor/',
	'liiconpath':'<?php echo $Config['WebsitePath']; ?>/static/editor/themes/ueditor-list/'//使用 '/' 开头的绝对路径
});
//强制所有链接在新窗口中打开
var AllPosts = document.getElementsByClassName("commont-content");
AllPosts[AllPosts.length]=document.getElementsByClassName("topic-content")[0];
for (var j=0; j<=AllPosts.length; j++) {
	var AllLinks = AllPosts[j].getElementsByTagName("a");
	for(var i=0; i<AllLinks.length; i++)
	{
		var a = AllLinks[i];
		console.log(a);
		a.target="_blank";
	};
};

</script>