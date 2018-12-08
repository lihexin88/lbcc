<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:76:"D:\wamp64\www\lbcc\public/../application/admin\view\game\account_recode.html";i:1543974757;s:57:"D:\wamp64\www\lbcc\application\admin\view\common\top.html";i:1522230592;s:60:"D:\wamp64\www\lbcc\application\admin\view\common\header.html";i:1522231280;s:61:"D:\wamp64\www\lbcc\application\admin\view\common\sidebar.html";i:1522231178;s:60:"D:\wamp64\www\lbcc\application\admin\view\common\bottom.html";i:1490663526;}*/ ?>
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
<style type="text/css">
    .main-container .table tr td {
        vertical-align: middle;
    }
    .main-container .table tr td a{
        margin-right:10px;
    }
</style>
<link rel="stylesheet" href = "/static/ace/css/userauth.css"></link>
</head>
<body class="no-skin" style="font-size: 13px;">
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
<div class="main-container" id="main-container"> <div id="sidebar" class="sidebar ">
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
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li> <i class="ace-icon fa fa-home home-icon"></i> <a href="<?php echo url('Index/index'); ?>"><?php echo config('WEB_SITE_NAME'); ?></a> </li>
                    <li> <a href="<?php echo url('/admin/game/index'); ?>">有奖竞猜</a> </li>
                    <li class="active"><?php echo $pagename; ?></li>
                </ul>
            </div>
            <div class="page-content">
                <div class="page-header">
                    <h1 style="text-align: left;"> <?php echo $pagename; ?> <small> <i class="ace-icon fa fa-angle-double-right"></i> 查询出<small style="color: blue"><?php echo isset($count)?$count:0; ?></small>条数据 </small> </h1>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12" style="margin-bottom:10px;">
                                <form action="<?php echo url('account_recode'); ?>" method="get" class="form-inline" role="form">
                                    <div class="form-group">
                                        <label>用户账户</label>
                                        <input name="keywords" type="text" class="form-control" placeholder="请输入账户">
                                    </div>
                                    <div class="form-group"><label>状态</label>
                                        <select name="direction" class="form-control">
                                            <option value="">全部</option>
                                            <option <?php echo $direction==-1?'selected':''; ?> value="-1">提现</option>
                                            <option <?php echo $direction==1?'selected':''; ?> value="1">充值</option>
                                            <option <?php echo $direction==2?'selected':''; ?> value="2">中奖</option>
                                            <option <?php echo $direction==3?'selected':''; ?> value="3">押注</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">查询</button>
                                    <button type="reset" class="btn btn-sm btn-danger hidden-xs" style="float:right;margin-right:10px;">清空查询条件</button>
                                </form>
                            </div>
                            <div class="col-xs-12">
                                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>订单号</th>
                                        <th>用户</th>
                                        <th>交易类型</th>
                                        <th>方向</th>
                                        <th>数额</th>
                                        <th>时间</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(is_array($all_recode) || $all_recode instanceof \think\Collection || $all_recode instanceof \think\Paginator): $k = 0; $__LIST__ = $all_recode;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
                                        <tr>
                                            <td><?php echo $k; ?></td>
                                            <td><?php echo $vo['id']; ?></td>
                                            <td><?php echo $vo['account']; ?></td>
                                            <td class="center">
                                                <?php switch($vo['direction']): case "1": ?>
                                                        <span class=" purple">充值</span>
                                                    <?php break; case "-1": ?>
                                                        <span class="red">提现</span>
                                                    <?php break; case "2": ?>
                                                        <span class="badge-yellow">中奖</span>
                                                    <?php break; case "3": ?>
                                                        <span>押注</span>
                                                    <?php break; endswitch; ?>
                                            </td>
                                            <td>
                                                <?php if((($vo['direction'] == -1) ||($vo['direction'] == 3))): ?>
                                                    取出
                                                <?php else: ?>
                                                    存入
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if((($vo['direction'] == -1) ||($vo['direction'] == 3))): ?>
                                                 <span class="red">-</span>
                                                <?php else: ?>
                                                 <span class="green">+</span>
                                                <?php endif; ?>
                                                <?php echo $vo['number']; ?>
                                            </td>
                                            <td><?php echo $vo['create_time']; ?></td>
                                            <td style="cursor: pointer" onclick="del_this(this,<?php echo $vo['id']; ?>)"><button class="btn-danger">删除</button></td>
                                        </tr>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </tbody>
                                </table>
                                <center><?php echo $page; ?></center>
                                <div style="width:100%;margin: 0 auto; text-align:center;">
                                    <ul class="pagination" >
                                        <?php echo $info['page']; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content"> <span class="bigger-120"> <span class="blue bolder"><?php echo config('WEB_SITE_NAME'); ?> </span><?php echo WEB_VERSION; ?>版 </span></div>
        </div>
    </div>
    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse"><i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i></a> </div>
<script type="text/javascript">if($(window).width()<1024)  $("#sidebar").addClass('menu-min');</script>
<script src="/static/ace/js/bootstrap.js"></script>
<script src="/static/ace/js/ace/ace.js"></script> 
<script src="/static/ace/js/ace/ace.sidebar.js"></script> 
<script src="/static/ace/js/layer/layer.js"></script>
<script type="text/javascript">
    // 定位
    $('a[href="/Admin/game/account_recode.html"]').parents().filter('li').addClass('open active');
</script>
<script type="text/javascript">
    <?php if(input('get.keywords')): ?>
    $('input[name="keywords"]').val('<?php echo $_GET["keywords"]; ?>');
    <?php endif; if(is_numeric(input('get.status'))): ?>
        $('select[name="status"]').val(<?php echo $_GET['status']; ?>);
        <?php endif; ?>
</script>
<script type="text/javascript">
    $('a[href="/Admin/game/account_recode.html"]').parents().filter('li').addClass('open active');
    jQuery(function($) {
        //清除查询条件
        $('#reset').click(function() {
            location.href = '<?php echo url('index'); ?>';
        });
        //更改状态
        $('.state').change(function() {
            var state = $(this).val();
            var id = $(this).attr('data-id');
            $.post("<?php echo url('edit'); ?>", {id: id,status: state}).success(function(data) {
                layer.msg(data.info, {icon: data.status,time: 1500},function(){
                    location.href=self.location.href;
                });
            })
        });
    });
</script>
<script>
    function del_this(obj,id) {
        var data = {
            "id" : id,
        };
        layer.confirm("确定删除吗？",function () {
            $.ajax(
                {
                    type:"post",
                    url:"del_recode",
                    data:data,
                    success:function (r) {
                        r = JSON.parse(r);
                        if(r['code'] == -1){
                            layer.msg("删除出错！");
                        }else{
                            layer.msg("已删除");
                            obj.parentNode.remove();
                        }
                    }
                }
            )
        })
    }
</script>
</body>
</html>