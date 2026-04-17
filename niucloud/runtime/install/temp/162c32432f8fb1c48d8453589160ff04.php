<?php /*a:2:{s:85:"/Users/hushiyu-macbookpro/data/junlai/emu/niucloud/app/install/view/index/step-3.html";i:1730172711;s:77:"/Users/hushiyu-macbookpro/data/junlai/emu/niucloud/app/install/view/base.html";i:1730172711;}*/ ?>
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
    .install-content-procedure .content-procedure-item:nth-child(2){
        background: url("/install/img/complete_four.png") no-repeat center / contain;
        color: #fff;
    }
    .install-content-procedure .content-procedure-item:nth-child(3){
        background: url("/install/img/conduct.png") no-repeat center / contain;
        color: #fff;
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
	
<div id='postloader' class='waitpage'></div>
<div class="pright layui-form">
    <div class="pr-title install-title layui-hide"></div>
    <div class="pr-agreement">
        <form  class="layui-form " id="install-form">
            <div class="testing parameter">
                <div class="testing-item">
                    <h3>数据库设置</h3>
                    <table border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
                        <tr>
                            <td class="onetd"><span class="required">*</span>数据库主机：</td>
                            <td>
                                <input name="dbhost" id="dbhost" type="text" lay-verify="empty" placeholder="请输入数据库主机" value="" class="input-txt" onChange="testDb()" />
                                <small>一般为localhost</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="onetd"><span class="required">*</span>Mysql端口：</td>
                            <td>
                                <input name="dbport" id="dbport" type="text" value="3306" class="input-txt" lay-verify="empty" placeholder="请输入Mysql端口"/>
                                <small>一般为3306</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="onetd"><span class="required">*</span>数据库用户：</td>
                            <td>
                                <input name="dbuser" id="dbuser" type="text" value="" class="input-txt" onChange="testDb()" lay-verify="empty" placeholder="请输入数据库用户"/>
                                <small>默认root</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="onetd"><span class="required">*</span>数据库密码：</td>
                            <td>
                                <div style='float:left;margin-right:3px;'>
                                    <input name="dbpwd" id="dbpwd" type="text" class="input-txt" onChange="testDb()" lay-verify="empty" placeholder="请输入数据库密码" />
                                </div>
                                <div style='float:left' class="mysql-message" id='dbpwdsta'></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="onetd"><span class="required">*</span>数据库名称：</td>
                            <td>
                                <div style='float:left;margin-right:3px;'><input name="dbname" id="dbname" type="text" value="" class="input-txt" onChange="haveDB()" lay-verify="empty" placeholder="请输入数据库名称" /></div>
                                <div style='float:left' class="mysql-message" id='havedbsta'></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="onetd">数据表前缀：</td>
                            <td>
                                <div style='float:left;margin-right:3px;'><input name="dbprefix" id="dbprefix" type="text" value="" class="input-txt" placeholder="请输入数据表前缀"/></div>
                            </td>
                        </tr>

                        <tr>
                            <td class="onetd">数据库编码：</td>
                            <td>
                                <label class="install-code">UTF8</label>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="testing-item">
                    <h3>网站设置</h3>
                    <table border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
                        <tr>
                            <td class="onetd"><span class="required">*</span><span>网站标题：</span></td>
                            <td>
                                <input name="admin_name" id="admin_name" type="text" value="" class="input-txt" lay-verify="empty" placeholder="请输入网站标题"/>
                                <small id="mess_admin_name">网站标题 必填</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="onetd"><span class="required">*</span><span>管理员用户名：</span></td>
                            <td>
                                <input name="username" id="username" type="text" value="" class="input-txt" lay-verify="empty" placeholder="请输入平台用户名"/>
                                <small id="mess_username">管理员用户名 必填</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="onetd"><span class="required">*</span><span>管理员密码：</span></td>
                            <td>
                                <input name="password" id="password" type="password" value="" class="input-txt" lay-verify="empty" placeholder="请输入平台密码"/>
                                <small id="mess_password">密码 必填</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="onetd"><span class="required">*</span><span>确认密码：</span></td>
                            <td>
                                <input name="password2" id="password2" type="password" value="" class="input-txt" lay-verify="empty" placeholder="请输入平台确认密码"/>
                                <small id="mess_password2">确认密码 必填</small>
                            </td>
                        </tr>
                    </table>
<!--                    <h3>默认站点</h3>-->
<!--                    <table border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">-->
<!--                        <tr>-->
<!--                            <td class="onetd"><span class="required">*</span><span>站点名称：</span></td>-->
<!--                            <td>-->
<!--                                <input name="site_name" id="site_name" type="text" value="" class="input-txt" lay-verify="empty" placeholder="请输入站点用户名"/>-->
<!--                                <small id="mess_site_name">站点名称 必填</small>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td class="onetd"><span class="required">*</span><span>站点管理员：</span></td>-->
<!--                            <td>-->
<!--                                <input name="site_username" id="site_username" type="text" value="" class="input-txt" lay-verify="empty" placeholder="请输入站点用户名"/>-->
<!--                                <small id="mess_site_username">站点管理员 必填</small>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        <div>-->
<!--                            <td class="td-aux"></td>-->
<!--                            <td class="td-aux"><span id="">请不要和网站管理员相同</span></td>-->
<!--                        </div>-->
<!--                        <tr>-->
<!--                            <td class="onetd"><span class="required">*</span><span>管理员密码：</span></td>-->
<!--                            <td>-->
<!--                                <input name="site_password" id="site_password" type="password" value="" class="input-txt" lay-verify="empty" placeholder="请输入站点密码"/>-->
<!--                                <small id="mess_site_password">管理员密码 必填</small>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td class="onetd"><span class="required">*</span><span>确认密码：</span></td>-->
<!--                            <td>-->
<!--                                <input name="site_password2" id="site_password2" type="password" value="" class="input-txt" lay-verify="empty" placeholder="请输入站点确认密码"/>-->
<!--                                <small id="mess_site_password2">确认密码 必填</small>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                    </table>-->
                </div>
            </div>
        </form>

        <div class="layui-form layui-hide testing" id="install-log">
            <div class="install-log">
            </div>
            <div class="btn-box layui-hide">
                <input type="button" class="btn-back" value="后退" onclick="back()" />
            </div>
        </div>
    </div>
    <div class="btn-box">
        <input type="button" class="btn-back" value="后退" onclick="window.location.href='<?php echo htmlentities($root_url); ?>/install.php/index/index?step=2'" />
        <input type="button" class="btn-next" lay-submit lay-filter="install" value="开始安装" id="form_submit">
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

<script type="text/javascript">
    ControlContent(2);
    var is_existdb = 1;//数据库是否存在
    var message = '数据库账号或密码不能为空';
    var is_install = false;
    function inputBoxPointer(id){
        return document.getElementById(id);
    }
    layui.use('form', function(){
        var form = layui.form;
        form.verify({
            empty: function(value, item){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    var msg = $(item).attr("placeholder");
                    return msg;
                }
            }

        });
        form.on('submit(install)', function(data){
            if(is_existdb == 2){
                layer.confirm('数据库存在，系统将覆盖数据库!', {
                    btn: ['继续','取消'] //按钮
                }, function(){
                    layer.closeAll();
                    install(data.field);

                }, function(){
                    layer.closeAll();
                    return false;
                });
            }else{
                if(is_existdb <= 0){
                    error(message);
                    return false;
                }
                install(data.field);
            }

            return false;
        });

    });

    //数据库连接测试
    function testDb()
    {
        var dbhost = inputBoxPointer('dbhost').value;
        var dbuser = inputBoxPointer('dbuser').value;
        var dbpwd = inputBoxPointer('dbpwd').value;
        var dbport = inputBoxPointer('dbport').value;
        inputBoxPointer('dbpwdsta').innerHTML='<img src="/install/img/ajax-loader.gif">';

        $.ajax({ //post也可
            url: '<?php echo htmlentities($root_url); ?>/install.php/index/testdb',
            data: { dbhost: dbhost, dbport : dbport, dbuser:dbuser, dbpwd:dbpwd},
            type: "post",
            dataType: 'json',
            success: function(data){
                inputBoxPointer('dbpwdsta').innerHTML = data.data.message;
                is_existdb = data.data.status;
                message = data.data.message;
            }
        });
    }

    /**
     *验证数据库是否存在
     */
    function haveDB()
    {
        var dbhost = inputBoxPointer('dbhost').value;
        var dbname = inputBoxPointer('dbname').value;
        var dbuser = inputBoxPointer('dbuser').value;
        var dbpwd = inputBoxPointer('dbpwd').value;
        var dbport = inputBoxPointer('dbport').value;
        inputBoxPointer('havedbsta').innerHTML='<img src="/install/img/ajax-loader.gif">';

        $.ajax({ //post也可
            url: '<?php echo htmlentities($root_url); ?>/install.php/index/testdb',
            data: { dbhost: dbhost, dbport : dbport, dbuser:dbuser, dbpwd:dbpwd,dbname:dbname},
            type: "post",
            dataType: 'json',
            success: function(data){
                inputBoxPointer('havedbsta').innerHTML = data.data.message;
                is_existdb = data.data.status;
                message = data.data.message;
            }
        });

    }

    function getStatus() {
        $.ajax({
            url: "<?php echo htmlentities($root_url); ?>/install.php/index/getInstallInfo",
            dataType: 'json',
            type: 'get',
            success : function(data) {
                if(data.code == 1) {
                    $(data.data.log).each(function (i, e){
                        if($('.log-'+i).length == 0) {
                            let html = `<div class="log-${i}">
                                        <span>${e[0]}${e[1] == 'success' ? "成功" : ""}</span>
                                        <span>${e[2]}</span>
                                    </div>`;
                            $('.install-log').append(html);
                            SetScroll();
                        }
                    })

                    if(data.data.status == 0 || data.data.status == 1) {
                        setTimeout(() => {
                            getStatus();
                        }, 200)
                    }else{
                        $('#install-log .btn-box').removeClass('layui-hide')
                    }
                }
            }
        })
    }

    function install(data){
        if(is_install) return false;

        $('#install-form').addClass('layui-hide')
        $('#install-log').removeClass('layui-hide')
        // $('.install-log').html('<div class="install-head">正在安装...</div>');
        $('.install-title').removeClass('layui-hide')
        $('.install-title').html('<div class="install-head">正在安装...</div>');
        $('.pright>.btn-box').addClass('layui-hide')
        is_install = true;
        $.ajax({
            url: "<?php echo htmlentities($root_url); ?>/install.php/index/install",
            data: data,
            dataType: 'json',
            type: 'post',
            success : function(res) {
                layer.close(index);
                if(res.code == 1){
                    $.ajax({
                        url: "<?php echo htmlentities($root_url); ?>/install.php/index/initData",
                        data: data,
                        dataType: 'json',
                        type: 'post',
                        success : function(data) {
                            if(data.code == 1){
                                window.location.href = '<?php echo htmlentities($root_url); ?>/install.php/index/build';
                            }
                        }
                    })
                }else{
                    error(res.msg);
                    is_install = false;
                }
            },
        })
        setTimeout(() => {
            getStatus();
        }, 500)
    }
    function back(){
        $('#install-form').removeClass('layui-hide')
        $('#install-log').addClass('layui-hide')
        $('.install-title').addClass('layui-hide')
        $('.pright>.btn-box').removeClass('layui-hide')
    }

    window.onload = SetScroll;
    function SetScroll(){
        var height=$(".install-log")[0].scrollHeight;
        $(".install-log").scrollTop(height);
    }
</script>

</body>
</html>
