<?php /*a:2:{s:85:"/Users/hushiyu-macbookpro/data/junlai/emu/niucloud/app/install/view/index/step-5.html";i:1730172711;s:77:"/Users/hushiyu-macbookpro/data/junlai/emu/niucloud/app/install/view/base.html";i:1730172711;}*/ ?>
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
    .install-content-procedure .content-procedure-item:first-of-type{
        background: url("/install/img/complete_two.png") no-repeat center / contain;
        color: #fff;
    }
    .install-content-procedure .content-procedure-item:nth-child(2), .install-content-procedure .content-procedure-item:nth-child(3){
        background: url("/install/img/complete_four.png") no-repeat center / contain;
        color: #fff;
    }
    .install-content-procedure .content-procedure-item:nth-child(4){
        background: url("/install/img/complete_three.png") no-repeat center / contain;
        color: #fff;
    }
    .install-content-procedure{border: none;}
    .pop-link-wrap{
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .pop-link-wrap .link-text{
        font-size: 14px;
    }
    .pop-link-wrap .link-text:nth-child(2){
        margin-top: 10px;
        
    }
    .pop-link-wrap .link-text:nth-child(2) a{
        color: var(--base-color);
    }
    .pop-link-wrap .link-btn{
        margin-top: 40px;
    }

    .pop-link-wrap button{
        width: 160px;
        height: 40px;
        background: var(--base-color);
        color: #fff !important;
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
	
<div class="install-success">
    <div class="install-success-box">
        <div class="success-img">
            <img class="install-success-pic" src="/install/img/success_img.jpg" alt="">
        </div>
        <div class="install-success-text">
            <?php if($is_build == 1): ?>
            <p class="install-success-title">恭喜您！已成功安装<?php echo htmlentities($install_config['website_name']); ?>。</p>
            <?php endif; if($is_build == 0): ?>
            <p class="install-success-title">安装过程已结束，您还需要手动编译，才能正常使用! </p>
            <p class="install-success-desc">如何手动编译请参考如下链接 <a href="<?php echo htmlentities($install_config['build_manual']); ?>" target="_blank"><?php echo htmlentities($install_config['build_manual']); ?></a></p>
            <?php endif; ?>
            <p class="install-success-desc">建议删除安装目录install后使用</p>
        </div>
    </div>
</div>
<div class="other-links">
    <div class="back">
        <p class="other-links-title">您现在可以访问：</p>
        <ul class="other-links-list">
            <li class="other-links-item" onclick="visitFn('admin')">
                <div  class="other-links-pic">
                    <img src="/install/img/web.jpg" alt="">
                </div>
                <a href="javascript:void(0)" class="other-links-text" >网站后台</a>
            </li>
            <li class="other-links-item" onclick="visitFn('web')">
                <div  class="other-links-pic">
                    <img src="/install/img/site_web.png" alt="">
                </div>
                <a href="javascript:void(0)" class="other-links-text" >网站电脑端</a>
            </li>
            <li class="other-links-item" onclick="visitFn('wap')">
                <div  class="other-links-pic">
                    <img src="/install/img/site_h5.png" alt="">
                </div>
                <a href="javascript:void(0)" class="other-links-text" >网站手机端</a>
            </li>
            <?php if(!(empty($install_config['website_url']) || (($install_config['website_url'] instanceof \think\Collection || $install_config['website_url'] instanceof \think\Paginator ) && $install_config['website_url']->isEmpty()))): ?>
            <li class="other-links-item" onclick="window.open('<?php echo htmlentities($install_config['website_url']); ?>')">
                <div  class="other-links-pic">
                    <img src="/install/img/admin.jpg" alt="">
                </div>
                <a href="javascript:void(0)" class="other-links-text" ><?php echo htmlentities($install_config['website_name']); ?>官方网站</a>
            </li>
            <?php endif; ?>
        </ul>
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
    ControlContent(4);
    const visitFn = (type)=>{
        let tips = window.localStorage.getItem('pageToLinkTips')
        if(tips){
            pageToLink(type)
            return false
        }
        layer.open({
            type: 1, // page 层类型
            area: ['400px', '230px'],
            title: '提示',
            shade: 0.6, // 遮罩透明度
            shadeClose: true, // 点击遮罩区域，关闭弹层
            anim: 0, // 0-6 的动画形式，-1 不开启
            content: `
                <div class="pop-link-wrap">
                    <p class="link-text">首次运行，请配置伪静态，才能正常使用。</p>
                    <p class="link-text">伪静态如何配置请<a href="https://www.kancloud.cn/niushop/niucloud-admin-app/3199831" class="other-links-text" target="_blank">参考手册</a></p>
                    <p class="link-btn">
                        <button onclick="pageToLink('${type}')" class="layui-btn visit-btn layui-border">确定</button>
                        <button onclick="pageToLink('${type}', ${true})" class="layui-btn visit-btn layui-border">已配置,不需要在提示</button>
                    </p>
                </div>
            `
        });
    }

    // 页面跳转
    function pageToLink(type,flag = false){
        if(flag){
           window.localStorage.setItem('pageToLinkTips', true) 
        }
        if(type == 'admin'){
            layer.closeAll()
            window.open('<?php echo htmlentities($root_url); ?>/admin/login')
        }else if(type == 'web'){
            layer.closeAll()
            window.open('<?php echo htmlentities($root_url); ?>/web/')
        }else if(type == 'wap'){
            layer.closeAll()
            window.open('<?php echo htmlentities($root_url); ?>/wap/')
        }
    }
</script>

</body>
</html>
