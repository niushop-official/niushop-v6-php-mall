<?php /*a:2:{s:85:"/Users/hushiyu-macbookpro/data/junlai/emu/niucloud/app/install/view/index/step-4.html";i:1776404891;s:77:"/Users/hushiyu-macbookpro/data/junlai/emu/niucloud/app/install/view/base.html";i:1776404891;}*/ ?>
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
	
<style>
	.layui-layer-title {
		border-bottom: 0!important;
	}
	.auth-layer {
		padding: 20px;
	}
	.auth-layer .layui-form-item {
		margin-bottom: 18px;
		position: relative;
	}
	.auth-layer .layui-form-item .error {
		color: red;
		font-size: 12px;
		position: absolute;
		line-height: 1;
		top: 42px;
		display: none;
	}
	.auth-layer .layui-form-item button,.auth-layer .layui-form-item .layui-input {
		height: 40px;
		width: 100%;
	}
	.auth-layer .layui-form-item .layui-word-aux {
		line-height: 1;
	}
	.auth-layer .to-buy {
		text-align: right;
	}
	.auth-layer .to-buy a {
		height: 40px;
		line-height: 40px;
		color: #409eff;
		border: 1px solid #409eff;
		display:block;
		text-align: center;
		border-radius: 2px;
	}
	.auth-layer .link {
		text-align: right;
		margin-bottom: 0;
	}
	.auth-layer .link a {
		color: #409eff;
	}
	.build-warp {
		margin-left: 50px;
		padding: 20px;
		margin-right: 50px;
		background: #F7F9FA;
		border-radius: 5px;
		height: 60vh;
		overflow-y: scroll;
	}
	.build-warp::-webkit-scrollbar {
		width: 6px;
		border-radius: 4px;
	}
	.build-warp::-webkit-scrollbar-track {
		background-color: rgba(0, 0, 0, 0.1);
	}
	.build-warp::-webkit-scrollbar-thumb {
		background-color: rgba(0, 0, 0, 0.3);
		border-radius: 4px;
	}
	.build-warp .msg-item {
		margin: 20px 0;
	}
	.build-warp .msg-item p {
		white-space: pre-wrap;
		font-size: 13px;
		font-family: Monaco,Menlo,Consolas,monospace;
	}
	.build-log .msg-item:first-child {
		margin-top: 0;
	}
	.build-warp .msg-item .symbol {
		margin-right: 10px;
	}
	.build-warp .msg-item .error {
		background-color: #c0392b;
		margin-right: 10px;
		padding: 2px 3px;
		color: #fff;
		line-height: 1;
		font-size: 12px;
	}
	.build-warp .build-progress {
		display: none;
	}
</style>

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
	
<div class="build-warp">
	<div class="build-log"></div>
	<div class="build-progress">
		<div class="msg-item">
			<span class="symbol"> > </span>
			<span class="content"></span>
		</div>
	</div>
</div>
<div class="other-links"></div>

<template id="authLayer">
	<div class="layui-form auth-layer">
		<div class="bind-auth layui-hide">
			<div class="layui-form-item">
				<input type="text" name="auth_code" placeholder="请输入授权码" autocomplete="off" class="layui-input">
				<div class="error">请输入授权码</div>
			</div>
			<div class="layui-form-item">
				<input type="text" name="auth_secret" placeholder="请输入授权秘钥" autocomplete="off" class="layui-input">
				<div class="error">请输入授权秘钥</div>
			</div>
			<div class="layui-form-item">
				<button class="layui-btn" lay-submit lay-filter="authInfoForm">立即绑定</button>
			</div>
			<div class="layui-form-item to-buy">
				<a href="javascript:;" onclick="prev()">返回上一步</a>
			</div>
		</div>
		<div class="options">
			<div class="layui-form-item">
				<button class="layui-btn" onclick="toBindAuthCode()">已授权，立即绑定授权</button>
			</div>
			<div class="layui-form-item to-buy">
				<a href="javascript:;" onclick="installSuccess(0)" target="_blank">我是开发者，自行编译</a>
			</div>
			<div class="layui-form-item to-buy">
				<a href="javascript:;" onclick="applyExperience()" style="color:#21ba45;border-color:#21ba45;">立即安装使用免费版</a>
			</div>
			<?php if(!(empty($install_config['build_manual']) || (($install_config['build_manual'] instanceof \think\Collection || $install_config['build_manual'] instanceof \think\Paginator ) && $install_config['build_manual']->isEmpty()))): ?>
			<div class="layui-form-item link">
				<a href="<?php echo htmlentities($install_config['build_manual']); ?>" target="_blank">安装帮助指引</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</template>

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
	ControlContent(3);

	const REQUEST_HEADER = { token: '<?php echo htmlentities($token); ?>' }
	var authinfo = null
	var form = null
	var submitTag = false

	layui.use('form', function(){
		form = layui.form;

		form.on('submit(authInfoForm)', function(data){
			var isVerify = true
			if ($('[name="auth_code"]').val() == '') {
				$('[name="auth_code"]').next().show()
				isVerify = false
			} else {
				$('[name="auth_code"]').next().hide()
			}
			if ($('[name="auth_secret"]').val() == '') {
				$('[name="auth_secret"]').next().show()
				isVerify = false
			} else {
				$('[name="auth_secret"]').next().hide()
			}
			isVerify && bindAuthCode()
		});
	});

	function toBindAuthCode() {
		$('.bind-auth').removeClass('layui-hide')
		$('.options').addClass('layui-hide')
	}

	function prev() {
		$('.bind-auth').addClass('layui-hide')
		$('.options').removeClass('layui-hide')
	}

	function bindAuthCode() {
		if (submitTag) return
		submitTag = true

		$.ajax({
			url: '<?php echo htmlentities($root_url); ?>/index.php/adminapi/niucloud/authinfo',
			headers: REQUEST_HEADER,
			data: {
				auth_code: $('[name="auth_code"]').val(),
				auth_secret: $('[name="auth_secret"]').val()
			},
			type: "post",
			dataType: 'json',
			success: function(res){
				if (res.code) {
					layer.closeAll()
					$('.build-progress').show()
					getCloudBuildTask()
				} else {
					layer.msg(res.msg)
				}
				submitTag = false
			}
		});
	}

	function applyExperience() {
		if (submitTag) return
		submitTag = true

		$.ajax({
			url: '<?php echo htmlentities($root_url); ?>/index.php/adminapi/niucloud/apply/experience',
			headers: REQUEST_HEADER,
			type: "get",
			dataType: 'json',
			success: function(res){
				if (res.code) {
					layer.closeAll()
					$('.build-progress').show()
					getCloudBuildTask()
				} else {
					layer.msg(res.msg)
				}
				submitTag = false
			}
		});
	}

	// 获取授权信息
	function getAuthinfo() {
		$.ajax({
			url: '<?php echo htmlentities($root_url); ?>/index.php/adminapi/niucloud/authinfo',
			headers: REQUEST_HEADER,
			type: "get",
			async: false,
			dataType: 'json',
			success: function(res){
				if (res.code && res.data) {
					getCloudBuildTask()
				} else {
					showAuthLayer()
				}
			}
		});
	}
	getAuthinfo()

	function showAuthLayer() {
		layer.open({
			type: 1,
			title: '绑定授权码云编译',
			area: ['400px'],
			content: $('#authLayer').html(),
			btn: [],
			closeBtn: false
		});
	}

	function installSuccess(isBuild = 1) {
		window.location.href = '<?php echo htmlentities($root_url); ?>/install.php/index/installSuccess' + (isBuild ? '' : '?is_build=0');
	}

	function buildProgress() {
		const frames = makeIterator(['/', '—', '\\', '|'])
		setInterval(() => {
			$('.build-progress .content').text(frames.next().value)
		}, 150)
	}
	buildProgress()

	function makeIterator(array) {
		var nextIndex = 0
		return {
			next() {
				if ((nextIndex + 1) == array.length) {
					nextIndex = 0
				}
				return { value: array[nextIndex++] }
			}
		}
	}

	function getCloudBuildTask() {
		$.ajax({
			url: '<?php echo htmlentities($root_url); ?>/index.php/adminapi/niucloud/build',
			headers: REQUEST_HEADER,
			type: "get",
			dataType: 'json',
			success: function(res){
				if (res.data) {
					getCloudBuildLog()
				} else {
					cloudBuild()
				}
			}
		});
	}

	var cloudBuildLog = []
	function getCloudBuildLog() {
		$.ajax({
			url: '<?php echo htmlentities($root_url); ?>/index.php/adminapi/niucloud/build/log',
			headers: REQUEST_HEADER,
			type: "get",
			dataType: 'json',
			success: function(res){
				if (!res.data) {
					if (cloudBuildLog.length) {
						installSuccess()
					}
					return
				}
				const data = res.data.data ?? []
				let error = ''

				if (data[0] && data[0].length) {
					if (cloudBuildLog.length == 0) {
						$('.build-progress').show()
						$('.build-log').html('')
					}

					data[0].forEach(item => {
						if (!cloudBuildLog.includes(item.action)) {
							$('.build-log').append(`<div class="msg-item"><span class="symbol"> > </span><span class="content">正在执行：${item.action}</span></div>`)
							cloudBuildLog.push(item.action)

							if (item.code == 0) {
								error = item.msg
								$('.build-log').append(`<div class="msg-item"><span class="symbol"> > </span><span class="error">error</span><span class="content">${item.msg}</span></div>`)
								$('.build-log').append(`<div class="msg-item"><span class="symbol"> > </span><span class="content">编译失败请不要关闭当前页面，请访问官方网站联系客服解决处理问题</span></div>`)
							}
						}
					})
				}

				if (error) {
					$('.build-progress').hide()
					return
				}

				setTimeout(() => {
					getCloudBuildLog()
				}, 2000)
			}
		});
	}

	function cloudBuild() {
		$.ajax({
			url: '<?php echo htmlentities($root_url); ?>/index.php/adminapi/niucloud/build/install',
			headers: REQUEST_HEADER,
			type: "post",
			dataType: 'json',
			success: function(res){
				if (res.code) {
					getCloudBuildLog()
				} else {
					if (res.msg.indexOf('队列') != -1) {
						$('.build-log').append(`<div class="msg-item"><span class="symbol"> > </span><span class="content">一键云安装小程序队列任务过多，请耐心等待</span></div>`)
						setTimeout(function (){
							cloudBuild()
						}, 10000)
					} else {
						$('.build-log').append(`<div class="msg-item"><span class="symbol"> > </span><span class="error">error</span><span class="content">${res.msg}</span></div>`)
						$('.build-log').append(`<div class="msg-item"><span class="symbol"> > </span><span class="content">编译失败请不要关闭当前页面，请访问官方网站联系客服解决处理问题</span></div>`)
					}
				}
			}
		})
	}
</script>

</body>
</html>
