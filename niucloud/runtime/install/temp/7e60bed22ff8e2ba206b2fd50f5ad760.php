<?php /*a:2:{s:85:"/Users/hushiyu-macbookpro/data/junlai/emu/niucloud/app/install/view/index/step-1.html";i:1730172711;s:77:"/Users/hushiyu-macbookpro/data/junlai/emu/niucloud/app/install/view/base.html";i:1730172711;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title>安装程序 - <?php echo htmlentities($install_config['website_name']); ?></title>
	<link rel="icon" type="image/x-icon" href="/install/img/shop_bitbug_favicon.ico" />
	<link rel="stylesheet" type="text/css" href="/install/css/style.css" />
	<link rel="stylesheet" type="text/css" href="/install/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="/install/css/common.css" />
	<script src="/install/js/jquery-3.1.1.js"></script>
	<script src="/install/js/layui.js"></script>

	<script>
		layui.use(['layer', 'upload', 'element'], function() {});
	</script>
	
</head>

<body>
<div class="head-block">
	<div class="top">
		<div class="top-logo">
			<div class="top-logo-img">
				<?php if(is_url($install_config['logo'])): ?>
				<img src="<?php echo htmlentities($install_config['logo']); ?>">
				<?php else: ?>
				<img src="/install/img/<?php echo htmlentities($install_config['logo']); ?>">
				<?php endif; ?>
			</div>
		</div>
		<div class="top-sub" style="flex:1;"></div>
		<ul class="top-link">
			<?php if(!(empty($install_config['website_url']) || (($install_config['website_url'] instanceof \think\Collection || $install_config['website_url'] instanceof \think\Paginator ) && $install_config['website_url']->isEmpty()))): ?><li><a href="<?php echo htmlentities($install_config['website_url']); ?>" target="_blank">官方网站</a></li><?php endif; if(!(empty($install_config['bbs_url']) || (($install_config['bbs_url'] instanceof \think\Collection || $install_config['bbs_url'] instanceof \think\Paginator ) && $install_config['bbs_url']->isEmpty()))): ?><li><a href="<?php echo htmlentities($install_config['bbs_url']); ?>" target="_blank">技术论坛</a></li><?php endif; ?>
		</ul>
	</div>
</div>
<div class="step-content">
	<div style="width:1000px;margin:0 auto;">

		<!--  标题进度条 start-->
		<div class="content" style="margin:30px 0 0 0;width: 100%;">
			<div class="processBar">
				<div class="text" style="margin: 10px -53px;"><span class='poetry'>1.许可协议</span></div>
				<div id="line0" class="bar">
					<div id="point0" class="c-step c-select"></div>
				</div>
			</div>
			<div class="processBar">
				<div class="text" style="margin: 10px -58px;"><span class='poetry'>2.环境检测</span></div>
				<div id="line1" class="bar">
					<div id="point1" class="c-step"></div>
				</div>
			</div>
			<div class="processBar">
				<div class="text" style="margin: 10px -58px;"><span class='poetry'>3.参数配置</span></div>
				<div id="line2" class="bar">
					<div id="point2" class="c-step"></div>
				</div>
			</div>
			<div class="processBar">
				<div class="text" style="margin: 10px -50px;"><span class='poetry'>4.代码编译</span></div>
				<div id="line3" class="bar">
					<div id="point3" class="c-step"></div>
				</div>
			</div>
			<div class="processBar" style="width:10px;">
				<div class="text" style="margin: 10px -39px;"><span class='poetry'>5.安装完成</span></div>
				<div id="line4" class="bar" style="width: 0;">
					<div id="point4" class="c-step"></div>
				</div>
			</div>
		</div>
		<!--  标题进度条 end-->
		<div style="clear: both;"></div>
	</div>
</div>
<div class="install-content">
	

<div class="pright layui-form">
	<h3 class="pr-title">阅读许可协议</h3>
	<div class="pr-agreement">
		<?php echo html_entity_decode($install_config['agreement']); ?>
	</div>
	<div class="btn-box">
		<div class="btn-box-text">
			<div class="layui-form-item">
				<input type="checkbox" name="readpact" id="readpact" title="我已经阅读并同意此协议" value="1" lay-skin="primary">
			</div>
		</div>
		<button class="layui-btn" lay-submit lay-filter="go">继续</button>
	</div>
</div>


</div>

<script>
	var index=0;
	$(document).ready(function(){
		$("#education").addClass('main-hide');
		$("#work").addClass('main-hide');
		$("#social").addClass('main-hide');
		$('#previous_step').hide();

		/*上一步*/
		$('#previous_step').bind('click', function () {
			index--;
			ControlContent(index);
		});
		/*下一步*/
		$('#next_step').bind('click', function () {
			index++;
			ControlContent(index);
		});

	});

	function ControlContent(index) {
		var stepContents = ["basicInfo","education","work","build","social"];
		var key;//数组中元素的索引值
		for (key in stepContents) {
			var stepContent = stepContents[key];//获得元素的值
			if (key == index) {
				if(stepContent=='basicInfo'){
					$('#previous_step').hide();
				}else{
					$('#previous_step').show();
				}
				if(stepContent=='social'){
					$('#next_step').hide();
				}else{
					$('#next_step').show();
				}
				$('#'+stepContent).removeClass('main-hide');
				$('#point'+key).addClass('c-select');
				$('#line'+key).removeClass('b-select');
			}else {
				$('#'+stepContent).addClass('main-hide');
				if(key>index){
					$('#point'+key).removeClass('c-select');
					$('#line'+key).removeClass('b-select');
				}else if(key<index){
					$('#point'+key).addClass('c-select');
					$('#line'+key).addClass('b-select');
				}
			}
		}

	}

	function success(message){
		layer.alert(message, {
			icon: 1,
			skin: 'layer-ext-moon',
			title:'提示'
		})
	}
	function error(message){
		layer.alert(message, {
			icon: 2,
			skin: 'layer-ext-moon',
			title:'提示'
		})
	}
</script>

<script>
	layui.use('form', function () {
		var form = layui.form;

		form.on('submit(go)', function (data) {
			var readpact = data.field.readpact;
			if (readpact == 1) {
				window.location.href = '<?php echo htmlentities($root_url); ?>/install.php?step=2';
			} else {
				error('您必须同意软件许可协议才能安装！');
			}

		});
	});
</script>

</body>
</html>
