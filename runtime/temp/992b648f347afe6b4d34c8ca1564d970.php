<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:69:"D:\phpStudy\WWW\lbcc\public/../application/admin\view\admin\rule.html";i:1525334488;s:59:"D:\phpStudy\WWW\lbcc\application\admin\view\common\top.html";i:1522230592;s:62:"D:\phpStudy\WWW\lbcc\application\admin\view\common\header.html";i:1522231280;s:63:"D:\phpStudy\WWW\lbcc\application\admin\view\common\sidebar.html";i:1522231178;}*/ ?>
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
</head>
<body class="no-skin">
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
      <div class="breadcrumbs" id="breadcrumbs">
        <ul class="breadcrumb">
          <li> <i class="ace-icon fa fa-home home-icon"></i> <a href="<?php echo url('Index/index'); ?>"><?php echo config('WEB_SITE_NAME'); ?></a> </li>
          <li> <a href="<?php echo url('index'); ?>">权限管理</a> </li>
          <li class="active"><?php echo $pagename; ?></li>
        </ul>
        <!-- /.breadcrumb --> 
      </div>
      <div class="page-content">
        <div class="page-header">
          <h1> <?php echo $pagename; ?> <small> <i class="ace-icon fa fa-angle-double-right"></i> 后台权限列表，同时可作为右侧导航使用 </small> <a class="btn btn-sm btn-success" style="float:right; margin-right:10px;" href="<?php echo url('add_rule'); ?>" >添加权限</a></h1>
        </div>
        <!-- /.page-header -->
        
        <div class="row">
          <div class="col-xs-12"> 
            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
              <div class="col-xs-12">
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th class="center">ID</th>
                      <th>权限名称</th>
                      <th>对应URL</th>
                      <th class="col-xs-1">排序</th>
                      <th class="col-xs-1">图标</th>
                      <th class="col-xs-1">显示状态</th>
                      <th class="col-xs-1">启用状态</th>
                      <th>操作</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                      <tr>
                        <td class="center"><?php echo $vo['id']; ?></td>
                        <td><i class="menu-icon fa fa-<?php echo $vo['icon']; ?>"> <?php echo $vo['title']; ?></td>
                        <td><?php echo $vo['name']; ?></td>
                        <td><input class="col-xs-10 sort" value="<?php echo $vo['sort']; ?>" data-id="<?php echo $vo['id']; ?>"></td>
                        <td><input class="col-xs-10 icon" value="<?php echo $vo['icon']; ?>" data-id="<?php echo $vo['id']; ?>"></td>
                        <td>
                          <select class="is_show form-control" data-id="<?php echo $vo['id']; ?>" >
                            <?php if(is_array($is_show) || $is_show instanceof \think\Collection || $is_show instanceof \think\Paginator): $i = 0; $__LIST__ = $is_show;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $so['value']; ?>" <?php if($vo['is_show'] == $so['value']): ?>selected<?php endif; ?> ><?php echo $so['key']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                          </select>
                        </td>
                        <td>
                          <select class="status form-control" data-id="<?php echo $vo['id']; ?>" >
                            <?php if(is_array($status) || $status instanceof \think\Collection || $status instanceof \think\Paginator): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $so['value']; ?>" <?php if($vo['status'] == $so['value']): ?>selected<?php endif; ?> ><?php echo $so['key']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                          </select>
                        </td>
                        <td><a class="btn btn-sm btn-success" href="<?php echo url('edit_rule?id='.$vo['id']); ?>">修改</a></td>
                      </tr>
                      <?php if(is_array($vo['child']) || $vo['child'] instanceof \think\Collection || $vo['child'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?>
                      <tr>
                        <td class="center"><?php echo $sub['id']; ?></td>
                        <td> ∟ <?php echo $sub['title']; ?></td>
                        <td><?php echo $sub['name']; ?></td>
                        <td><input class="col-xs-10 sort" value="<?php echo $sub['sort']; ?>" data-id="<?php echo $sub['id']; ?>"></td>
                        <td> -- </td>
                        <td>
                          <select class="is_show form-control" data-id="<?php echo $sub['id']; ?>" >
                            <?php if(is_array($is_show) || $is_show instanceof \think\Collection || $is_show instanceof \think\Paginator): $i = 0; $__LIST__ = $is_show;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $so['value']; ?>" <?php if($sub['is_show'] == $so['value']): ?>selected<?php endif; ?> ><?php echo $so['key']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                          </select>
                        </td>
                        <td>
                          <select class="status form-control" data-id="<?php echo $sub['id']; ?>" >
                            <?php if(is_array($status) || $status instanceof \think\Collection || $status instanceof \think\Paginator): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $so['value']; ?>" <?php if($sub['status'] == $so['value']): ?>selected<?php endif; ?> ><?php echo $so['key']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                          </select>
                        </td>
                        <td><a class="btn btn-sm btn-success" href="<?php echo url('edit_rule?id='.$sub['id']); ?>">修改</a></td>
                      </tr>
                      <?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                  </tbody>
                </table>
              </div>
              <!-- /.span --> 
            </div>
            <!-- /.row --> 
            <!-- PAGE CONTENT ENDS --> 
          </div>
          <!-- /.col --> 
        </div>
        <!-- /.row --> 
      </div>
      <!-- /.page-content --> 
    </div>
  </div>
  <!-- /.main-content -->
  
  <div class="footer">
    <div class="footer-inner"> 
      <!-- #section:basics/footer -->
      <div class="footer-content"> <span class="bigger-120"> <span class="blue bolder"><?php echo config('WEB_SITE_NAME'); ?> </span><?php echo WEB_VERSION; ?>版 </span></div>
      <!-- /section:basics/footer --> 
    </div>
  </div>
  <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse"><i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i></a> </div>
<!-- /.main-container --> 
<!-- basic scripts --> 
<include file="common/bottom" />
<script src="/static/ace/js/layer/layer.js"></script>
<script type="text/javascript">
  jQuery(function($) { 
    //更改显示状态
    $('.is_show').change(function() {
      $.post("<?php echo url('edit_rule'); ?>", {id: $(this).attr('data-id'),is_show: $(this).val()}).success(function(data) {
        layer.msg(data.info, {icon: data.status,time: 1500},function(){
          location.href=self.location.href;
        });
      })
    });
    //更改启用状态
    $('.status').change(function() {
      $.post("<?php echo url('edit_rule'); ?>", {id: $(this).attr('data-id'),status: $(this).val()}).success(function(data) {
        layer.msg(data.info, {icon: data.status,time: 1500},function(){
          location.href=self.location.href;
        });
      })
    });
    //排序
    $('.table input.sort').change(function() {
      $.post("<?php echo url('edit_rule'); ?>", {id: $(this).attr('data-id'),sort: $(this).val()}).success(function(data) {
        layer.msg(data.info, {icon: data.status,time: 1500},function(){
          location.href=self.location.href;
        });
      })
    });
    //排序
    $('.table input.icon').change(function() {
      $.post("<?php echo url('edit_rule'); ?>", {id: $(this).attr('data-id'),icon: $(this).val()}).success(function(data) {
        layer.msg(data.info, {icon: data.status,time: 1500},function(){
          location.href=self.location.href;
        });
      })
    });
  });
</script>
</body>
</html>