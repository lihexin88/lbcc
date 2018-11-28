<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:67:"D:\phpStudy\WWW\lbcc\public/../application/admin\view\user\add.html";i:1537958918;s:59:"D:\phpStudy\WWW\lbcc\application\admin\view\common\top.html";i:1522230592;s:62:"D:\phpStudy\WWW\lbcc\application\admin\view\common\header.html";i:1522231280;s:63:"D:\phpStudy\WWW\lbcc\application\admin\view\common\sidebar.html";i:1522231178;s:62:"D:\phpStudy\WWW\lbcc\application\admin\view\common\bottom.html";i:1490663526;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta charset="utf-8" />
<title><?php if(isset($info['name'])): ?><?php echo $info['name']; ?> - <?php endif; ?> <?php echo $pagename; ?> -  <?php echo config('WEB_SITE_NAME'); ?>管理后台</title>
<meta name="description" content=" <?php echo config('WEB_SITE_DESCRIPTION'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<link rel="stylesheet" href="/static/ace/css/bootstrap.css" />
<link rel="stylesheet" href="/static/ace/css/font-awesome.css" />
<link rel="stylesheet" href="/static/ace/css/ace-fonts.css" />
<link rel="stylesheet" href="/static/ace/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />
<script src="/static/ace/js/ace-extra.js"></script>
<script type="text/javascript">window.jQuery || document.write("<script src='/static/ace/js/jquery.js'>"+"<"+"/script>");</script>
<script type="text/javascript">if('ontouchstart' in document.documentElement) document.write("<script src='/static/ace/js/jquery.mobile.custom.js'>"+"<"+"/script>");</script>
<style type="text/css">
input[type="date"], input[type="time"], input[type="datetime-local"], input[type="month"] {
  line-height: inherit;
}
.help-inline{
  line-height: 32px;
}
select{
	height: 34px;
}
.main-container .table tr td {
	vertical-align: middle;
}
.main-container .table tr td a{
	margin-right:10px;
}
#preview{
  height: 120px;
  width: auto;
}
</style>
</head><body class="no-skin">
<div id="navbar" class="navbar navbar-default">
  <div class="navbar-container" id="navbar-container">
  <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar"> <span class="sr-only">Toggle sidebar</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
    <div class="navbar-header pull-left"> <a href="<?php echo url('Index/index'); ?>" class="navbar-brand"> <small><?php echo \think\Config::get('WEB_SITE_NAME'); ?>网站管理后台 </small> </a> </div>
    <div class="navbar-buttons navbar-header pull-right" role="navigation">
      <ul class="nav ace-nav">
        <li class="light-blue"> <a data-toggle="dropdown" href="#" class="dropdown-toggle"> <img class="nav-user-photo" src="/static/ace/avatars/user.png" />
        <span class="user-info"><small>欢迎你</small><?php echo $_SESSION['think']['username']; ?></span> <i class="ace-icon fa fa-caret-down"></i> </a>
          <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
            <li> <a href="<?php echo url('Index/repwd'); ?>"> <i class="ace-icon fa fa-cog"></i> 修改密码 </a></li>
            <li> <a href="<?php echo url('Index/profile'); ?>"> <i class="ace-icon fa fa-user"></i> 详细信息 </a> </li>
            <li class="divider"></li>
            <li> <a href="<?php echo url('Publics/logout'); ?>"> <i class="ace-icon fa fa-power-off"></i> 退出后台 </a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="main-container" id="main-container"> 
  <!-- #section:basics/sidebar --> 
  <div id="sidebar" class="sidebar ">
  <div class="sidebar-shortcuts" id="sidebar-shortcuts">
    <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
      <button class="btn btn-success">
      <i class="ace-icon fa fa-group"></i>
      </button>
      <button class="btn btn-info">
      <i class="ace-icon fa fa-list"></i>
      </button>
      <button class="btn btn-warning">
      <i class="ace-icon fa fa-arrow-circle-up"></i>
      </button>
      <button class="btn btn-danger">
      <i class="ace-icon fa fa-cog"></i>
      </button>
    </div>
    <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
      <span class="btn btn-success"></span>
      <span class="btn btn-info"></span>
      <span class="btn btn-warning"></span>
      <span class="btn btn-danger"></span>
    </div>
  </div>
  <ul class="nav nav-list">
    <?php if(is_array($sidebar) || $sidebar instanceof \think\Collection || $sidebar instanceof \think\Paginator): $i = 0; $__LIST__ = $sidebar;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <li <?php if($vo['name'] == $rule2): ?>class="open active"<?php endif; ?>><a href="<?php echo url('/'.$vo['name']); ?>" <?php if($vo['count'] != 0): ?>class="dropdown-toggle"<?php endif; ?>><i class="menu-icon fa fa-<?php echo $vo['icon']; ?>"></i><span class="menu-text"> <?php echo $vo['title']; ?> </span><b class="arrow <?php if($vo['count'] != 0): ?>fa fa-angle-down<?php endif; ?>"></b></a>
        <b class="arrow"></b>
        <ul class="submenu">
          <?php if(is_array($vo['child']) || $vo['child'] instanceof \think\Collection || $vo['child'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?>
          <li <?php if($sub['name'] == $rule): ?>class="active"<?php endif; ?>><a href="<?php echo url('/'.$sub['name']); ?>"><i class="menu-icon fa fa-caret-right"></i> <?php echo $sub['title']; ?> </a><b class="arrow"></b></li>
          <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
      </li>
    <?php endforeach; endif; else: echo "" ;endif; ?>
  </ul>
  <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
    <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
  </div>
</div> 
  <!-- /section:basics/sidebar -->
  <div class="main-content">
    <div class="main-content-inner"> 
      <!-- #section:basics/content.breadcrumbs -->
      <div class="breadcrumbs" id="breadcrumbs">
        <ul class="breadcrumb">
          <li> <i class="ace-icon fa fa-home home-icon"></i> <a href="<?php echo url('Index/index'); ?>"><?php echo config('WEB_SITE_NAME'); ?></a> </li>
          <li> <a href="<?php echo url('index'); ?>">用户管理</a> </li>
          <li class="active"><?php echo $pagename; ?></li>
        </ul>
        <!-- /.breadcrumb --> 
      </div>
      <!-- /section:basics/content.breadcrumbs -->
      <div class="page-content">
        <div class="page-header">
          <h1> <?php echo $pagename; ?> </h1>
        </div>
          <!-- 添加用户 -->
          <div class="row">
            <div class="col-xs-12">
              <form class="form-horizontal form-post" role="form">
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right"> 头像地址 </label>
                    <div class="col-sm-3 col-lg-3" style="padding-right: 0px;">
                        <input name="url" type="text" class="form-control" readonly placeholder="这里显示头像上传地址">
                    </div>
                    <div class="col-sm-2 col-lg-2" style="padding-left: 0px;">
                        <a href="javascript:void(0);" class="btn btn-sm btn-success" id="test1" data-type="headimg">图片上传</a>
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 用户姓名 </label>
                  <div class="col-sm-9">
                    <input name="username" type="text" class="col-xs-10 col-sm-5" placeholder="请填写用户姓名" value="<?php echo $info['username']; ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 支付宝账号 </label>
                  <div class="col-sm-9">
                    <input name="alipay_accout" type="text" class="col-xs-10 col-sm-5" placeholder="请填写支付宝账号" value="<?php echo $info['alipay_accout']; ?>" />
                  </div>
                </div>
             <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right"> 支付宝二维码 </label>
                    <div class="col-sm-3 col-lg-3" style="padding-right: 0px;">
                        <input name="url1" type="text" class="form-control" readonly placeholder="这里显示支付宝二维码上传地址">
                    </div>
                    <div class="col-sm-2 col-lg-2" style="padding-left: 0px;">
                        <a href="javascript:void(0);" class="btn btn-sm btn-success" id="test2" data-type="headimg">支付宝上传</a>
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 微信账号 </label>
                  <div class="col-sm-9">
                    <input name="wechat" type="text" class="col-xs-10 col-sm-5" placeholder="请填写微信账号" value="<?php echo $info['wechat']; ?>" />
                  </div>
                </div>
               <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right"> 微信二维码 </label>
                    <div class="col-sm-3 col-lg-3" style="padding-right: 0px;">
                        <input name="url2" type="text" class="form-control" readonly placeholder="这里显示支付宝二维码上传地址">
                    </div>
                    <div class="col-sm-2 col-lg-2" style="padding-left: 0px;">
                        <a href="javascript:void(0);" class="btn btn-sm btn-success" id="test3" data-type="headimg">微信二维码上传</a>
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 推荐人ID </label>
                  <div class="col-sm-9">
                    <input name="parent_id" type="text" class="col-xs-10 col-sm-5" placeholder="请填写推荐人" value="<?php echo $info['parent_id']; ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> USDT </label>
                  <div class="col-sm-9">
                    <input name="usdt" type="text" class="col-xs-10 col-sm-5" placeholder="宝石" value="<?php echo $info['usdt']; ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> GCU </label>
                  <div class="col-sm-9">
                    <input name="gcu" type="text" class="col-xs-10 col-sm-5" placeholder="宝石" value="<?php echo $info['gcu']; ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 手机号 </label>
                  <div class="col-sm-9">
                    <input name="tel" type="text" class="col-xs-10 col-sm-5" placeholder="请填写手机号" value="<?php echo $info['tel']; ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 身份证 </label>
                  <div class="col-sm-9">
                    <input name="identity" type="text" class="col-xs-10 col-sm-5" placeholder="请填写身份证" value="<?php echo $info['identity']; ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 真实姓名 </label>
                  <div class="col-sm-9">
                    <input name="real_name" type="text" class="col-xs-10 col-sm-5" placeholder="请填写真实姓名" value="<?php echo $info['real_name']; ?>" />
                  </div>
                </div>
                 <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right"> 开户银行 </label>
                  <div class="col-sm-9">
                    <input name="bank" type="text" class="col-xs-10 col-sm-5" placeholder="请填写开户银行" value="<?php echo $info['bank']; ?>" />
                  </div>
                </div>
                 <div class="form-group">
                  <label class="col-sm-3 control-label no-padding-right">银行卡号 </label>
                  <div class="col-sm-9">
                    <input name="bank_number" type="text" class="col-xs-10 col-sm-5" placeholder="请填写银行卡号" value="<?php echo $info['bank_number']; ?>" />
                  </div>
                </div>
                <div class="space-4"></div>
                <div class="alert alert-danger" style="display:none;"></div>
                <div class="clearfix form-actions">
                  <div class="col-md-offset-3 col-md-9">
                  <input name="id" class="hidden" type="text" value="<?php echo $info['id']; ?>">
                    <button class="btn btn-info" type="submit" id="btn"> <i class="ace-icon fa fa-check bigger-110"></i> 保存 </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
      </div>
    </div>
  </div>
  <!-- /.main-content -->
  <div class="footer">
    <div class="footer-inner">
      <div class="footer-content"> <span class="bigger-120"> <span class="blue bolder"><?php echo config('WEB_SITE_NAME'); ?> </span><?php echo WEB_VERSION; ?>版 </span></div>
    </div>
  </div>
  <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse"> <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i> </a> </div>
<!-- /.main-container --> 
<!-- basic scripts --> 
<script type="text/javascript">if($(window).width()<1024)  $("#sidebar").addClass('menu-min');</script>
<script src="/static/ace/js/bootstrap.js"></script>
<script src="/static/ace/js/ace/ace.js"></script> 
<script src="/static/ace/js/ace/ace.sidebar.js"></script> 
<link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
<script src="/static/layui/layui.js"></script>
<script>
layui.use('upload', function(){
  var upload = layui.upload;
  //执行实例
  var uploadInst = upload.render({
    elem: '#test1' //绑定元素
    ,url: "<?php echo url('Publics/upload'); ?>" //上传接口
    ,data: {type: 'headimg'}
    ,done: function(res){
      //上传完毕回调
      if(res.status == 0){
        layer.msg(res.info, {icon: res.status,time: 1500});
      }else{
        $("input[name=url]").val(res.msg)
      }
      
    }
  });

});
</script>
<script>
layui.use('upload', function(){
  var upload = layui.upload;
  //执行实例
  var uploadInst = upload.render({
    elem: '#test2' //绑定元素
    ,url: "<?php echo url('Publics/upload'); ?>" //上传接口
    ,data: {type: 'aiplay'}
    ,done: function(res){
      //上传完毕回调
      if(res.status == 0){
        layer.msg(res.info, {icon: res.status,time: 1500});
      }else{
        $("input[name=url1]").val(res.msg)
      }
      
    }
  });
});
</script>
<script>
layui.use('upload', function(){
  var upload = layui.upload;
  //执行实例
  var uploadInst = upload.render({
    elem: '#test3' //绑定元素
    ,url: "<?php echo url('Publics/upload'); ?>" //上传接口
    ,data: {type: 'weixinplay'}
    ,done: function(res){
      //上传完毕回调
      if(res.status == 0){
        layer.msg(res.info, {icon: res.status,time: 1500});
      }else{
        $("input[name=url2]").val(res.msg)
      }
      
    }
  });
});
</script>

<script type="text/javascript">
$('a[href="/Admin/User/index.html"]').parents().filter('li').addClass('open active');
//提交表单
$(".form-post").find('button:submit').click(function() {
  $.post("<?php echo url('add'); ?>", $(".form-post").serialize()).success(function(data) {
    $('#btn').text('正在保存').attr('disabled', "true");
    if (data.status === 0) {
      $(".form-post .alert").text(data.info).show();
      setTimeout(function() {
        $('#btn').text('保存').removeAttr('disabled');
        $(".form-post .alert").empty().hide();
      },
      1000);
    }else{
      setTimeout(function() {
        location.href = data.url;
      },
      1000);
    }
  });
  return false;
});
</script> 
</body>
</html>