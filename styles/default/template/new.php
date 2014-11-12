<?php
if (!defined('InternalAccess')) exit('error: 403 Access Denied');
?>
<!-- main-content start -->
<script>
var MaxTagNum = <?php echo $Config["MaxTagsNum"]; ?>;//最多的话题数量
var MaxTitleChars = <?php echo $Config['MaxTitleChars']; ?>;//主题标题最多字节数
var MaxPostChars = <?php echo $Config['MaxPostChars']; ?>;//主题内容最多字节数
</script>
<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/editor/ueditor.config.js?version=<?php echo $Config['Version']; ?>"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/editor/ueditor.all.min.js?version=<?php echo $Config['Version']; ?>"> </script>
<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/editor/lang/zh-cn/zh-cn.js?version=<?php echo $Config['Version']; ?>"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/js/jquery.autocomplete.min.js?version=<?php echo $Config['Version']; ?>"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $Config['WebsitePath']; ?>/static/js/new.function.js?version=<?php echo $Config['Version']; ?>"></script>
<div class="main-content">
	<div class="title"><a href="<?php echo $Config['WebsitePath']; ?>/"><?php echo $Config['SiteName']; ?></a> &raquo; <?php echo $PageTitle; ?></div>
		<div class="main-box">
		<?php if($Error){ ?>
		<p class="red fs12" style="margin-left:60px;">
		› <?php echo $Error; ?> <br/></p>
		<?php } ?>
			<form action="?" method="post" name="NewForm" onkeydown="if(event.keyCode==13)return false;" onsubmit="JavaScript:return SubmitCheck();">
			<input type="hidden" name="FormHash" value="<?php echo $FormHash; ?>" />
			<input type="hidden" name="ContentHash" value="" />
			<p><input type="text" name="Title" id="Title" value="<?php echo htmlspecialchars($Title); ?>" style="width:624px;" placeholder="标题" /></p>
			<p>
				<script id="editor" type="text/plain" style="width:648px;height:500px;"></script>
				<script type="text/javascript">
					//实例化编辑器
					//建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
					window.UEDITOR_CONFIG['textarea'] = 'Content';
					window.UEDITOR_CONFIG['toolbars'] = [['fullscreen', 'source', '|', 'bold', 'italic', 'underline', 'paragraph', 'fontsize', 'fontfamily', 'forecolor', '|', 'justifyleft','justifycenter', 'justifyright', 'justifyjustify', '|','undo', 'redo'],['insertcode', 'link','inserttable', 'blockquote', 'insertorderedlist', 'insertunorderedlist', '|', 'emotion', 'simpleupload', 'insertimage', 'scrawl', 'insertvideo', 'music', 'attachment', '|', 'removeformat', 'autotypeset']];
					UE.getEditor('editor',{onready:function(){
						if(window.localStorage){
							//从草稿中恢复
							RecoverContents();
						}
						var content='<?php echo $Content; ?>';
						if(content){
							this.setContent(content);
						}
					}});
				</script>
			</p>
			<p>
				<div class="tags-list bth" style="width:624px;height:33px;" onclick="JavaScript:document.NewForm.AlternativeTag.focus();">
					<span id="SelectTags" class="btn"></span>
					<input type="text" name="AlternativeTag" id="AlternativeTag" value="" class="tag-input" onkeydown="JavaScript:TagKeydown(this);" onfocus="JavaScript:GetTags();" placeholder="添加话题(按Enter添加)" />
				</div>
			</p>
			<script TYPE="text/javascript">
			<?php
				if($TagsArray){
					foreach ($TagsArray as $key => $value) {
						echo "AddTag(\"".$value."\", ".$TimeStamp.$key.");\n";
					}
				}
				?>
			</script>
			<p>
				<div id="TagsList" class="btn">
				</div>
			</p>
			<p><div class="text-center"><input type="submit" value=" 发 表 " name="submit" class="textbtn" /></div><div class="c"></div></p>
			</form>
	</div>
</div>
<!-- main-content end -->
<!-- main-sider start -->
<div class="main-sider">
	<?php include($TemplatePath.'sider.php'); ?>
</div>
<!-- main-sider end -->