<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"D:\wamp64\www\lbcc\public/../application/admin\view\publics\login.html";i:1522284110;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta charset="utf-8" />
<title>登录 - <?php echo config('WEB_SITE_NAME'); ?></title>
<meta name="description" content="User login page" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<!-- bootstrap & fontawesome -->
<link rel="stylesheet" href="/static/ace/css/bootstrap.css" />
<link rel="stylesheet" href="/static/ace/css/font-awesome.css" />
<!-- text fonts -->
<link rel="stylesheet" href="/static/ace/css/ace-fonts.css" />
<!-- ace styles -->
<link rel="stylesheet" href="/static/ace/css/ace.css" />
<!--[if lte IE 9]><link rel="stylesheet" href="/static/ace/css/ace-part2.css" /><![endif]-->
<link rel="stylesheet" href="/static/ace/css/ace-rtl.css" />
<!--[if lte IE 9]><link rel="stylesheet" href="/static/ace/css/ace-ie.css" /><![endif]-->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]><script src="/static/ace/js/html5shiv.js"></script><script src="/static/ace/js/respond.js"></script><![endif]-->
</head>
<body class="login-layout">
<div class="main-container">
  <div class="main-content">
    <div class="row">
      <div class="col-sm-10 col-sm-offset-1">
        <div class="login-container">
          <div class="center">
            <h1 style="color: #00a0e9 !important;font-weight: bold;font-size: 35px;"><span><?php echo config('WEB_SITE_NAME'); ?></span> <span class="white" id="id-text2">管理后台</span> </h1>
          </div>
          <div class="space-6"></div>
          <div class="position-relative">
            <div id="login-box" class="login-box visible widget-box no-border">
              <div class="widget-body">
                <div class="widget-main">
                  <h4 class="header blue lighter bigger"> <i class="ace-icon fa fa-coffee green"></i> 请输入用户名和密码登录 </h4>
                  <div class="space-6"></div>
                  <form class="form-signin">
                    <fieldset>
                      <label class="block clearfix"> <span class="block input-icon input-icon-right">
                        <input name="username" type="text" class="form-control" placeholder="用户名" />
                        <i class="ace-icon fa fa-user"></i> </span> </label>
                      <label class="block clearfix"> <span class="block input-icon input-icon-right">
                        <input name="password" type="password" class="form-control" placeholder="密码" />
                        <i class="ace-icon fa fa-lock"></i> </span> </label>
                      <label class="block clearfix"> <span class="block input-icon input-icon-right">
                        <input name="verify" type="text" class="form-control" placeholder="验证码" />
                        <i class="ace-icon fa fa-refresh"></i> </span> </label>
                      <div class="space"></div>
                      <div class="alert alert-danger" style="display:none;"></div>
                      <div class="clearfix">
                        <button type="submit" class="width-35 pull-right btn btn-sm btn-primary"> <i class="ace-icon fa fa-key"></i> <span class="bigger-110">登录</span> </button>
                      </div>
                      <div class="space-4"></div>
                    </fieldset>
                  </form>
                  <div class="social-or-login center"> <span class="bigger-110">验证码</span> </div>
                  <div class="space-6"></div>
                  <div class="social-login center"> <img class="verifyimg reloadverify" alt="点击切换" src="<?php echo captcha_src(); ?>" title="点击刷新验证码"> </div>
                </div>
                <!-- /.widget-main --> 
              </div>
              <!-- /.widget-body --> 
            </div>
            <!-- /.login-box -->
          </div>
          <!-- /.position-relative -->
          
          <div class="navbar-fixed-top align-right hide"> <br />
            &nbsp; <a id="btn-login-dark" href="#">Dark</a> &nbsp; <span class="blue">/</span> &nbsp; <a id="btn-login-blur" href="#">Blur</a> &nbsp; <span class="blue">/</span> &nbsp; <a id="btn-login-light" href="#">Light</a> &nbsp; &nbsp; &nbsp; </div>
        </div>
      </div>
      <!-- /.col --> 
    </div>
    <!-- /.row --> 
  </div>
  <!-- /.main-content --> 
</div>
<!-- /.main-container --> 
<!-- basic scripts --> 
<!--[if !IE]> --><script type="text/javascript">window.jQuery || document.write("<script src='/static/ace/js/jquery.js'>"+"<"+"/script>");</script><!-- <![endif]--> 
<!--[if IE]><script type="text/javascript"> window.jQuery || document.write("<script src='/static/ace/js/jquery1x.js'>"+"<"+"/script>");</script>
<![endif]--><script type="text/javascript">if('ontouchstart' in document.documentElement) document.write("<script src='/static/ace/js/jquery.mobile.custom.js'>"+"<"+"/script>");</script> 
<!-- inline scripts related to this page --> 
<script type="text/javascript">
jQuery(function($) {
  //登录
  $(".form-signin").find('button:submit').click(function() {
    var btn = $(this);
    $.post("<?php echo url('login'); ?>", $(".form-signin").serialize()).success(function(data) {
      if (data.status === 0) {
        $(".form-signin .alert").text(data.info).show();
        setTimeout(function() {
          $(".form-signin .alert").empty().hide();
          $(".reloadverify").click();
        },
        500);
      } else {
        setTimeout(function() {
          btn.text(data.info);
          location.href = data.url;
        },
        500);
      }
    });
    return false;
  });

  //刷新验证码
  var verifyimg = $(".verifyimg").attr("src");
  $(".reloadverify").click(function() {
    if (verifyimg.indexOf('?') > 0) {
      $(".verifyimg").attr("src", verifyimg + '&random=' + Math.random());
    } else {
      $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
    }
  });

});

//you don't need this, just used for changing background
jQuery(function($) {
  $('#btn-login-dark').on('click',function(e) {
    $('body').attr('class', 'login-layout');
    $('#id-text2').attr('class', 'white');
    $('#id-company-text').attr('class', 'blue');
    e.preventDefault();
  });
  $('#btn-login-light').on('click',function(e) {
    $('body').attr('class', 'login-layout light-login');
    $('#id-text2').attr('class', 'grey');
    $('#id-company-text').attr('class', 'blue');
    e.preventDefault();
  });
  $('#btn-login-blur').on('click',function(e) {
    $('body').attr('class', 'login-layout blur-login');
    $('#id-text2').attr('class', 'white');
    $('#id-company-text').attr('class', 'light-blue');
    e.preventDefault();
  });

});
</script>
</body>
</html>