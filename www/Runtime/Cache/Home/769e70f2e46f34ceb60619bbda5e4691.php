<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
<link href="/Public/static/bootstrap/css/docs.css" rel="stylesheet">
<link href="/Public/static/bootstrap/css/onethink.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
<link rel="stylesheet" href="/Public/Home/css/main.css">
<title>物流查询系统</title>
<![endif]-->

<script src="/Public/Home/js/libs/jquery.min.js"></script>
<script src="/Public/Home/js/libs/bootstrap.min.js"></script>
<script src="/Public/Home/js/main.js"></script>

<?php echo hook('pageHeader');?>

</head>
<body>
	<!-- 头部 -->
	
	<!-- /头部 -->
	
	<!-- 主体 -->
	
<div id="main-container" class="container">
    <div class="row">
        
        <!-- 左侧 nav
        ================================================== -->
            <div class="span3 bs-docs-sidebar">
                
                <ul class="nav nav-list bs-docs-sidenav">
                    <?php echo W('Category/lists', array($category['id'], ACTION_NAME == 'index'));?>
                </ul>
            </div>
        
        
    <!-- 页头 -->
    <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
        <div class="container">
            <a href="javascript:;" class="navbar-brand">物流查询系统</a>
        </div>
    </div>
    <!-- 查询 -->
    <div class="input-group container" id="thisForm">
        <form id="form" action="<?php echo U('index');?>" method="post" class="form-horizontal doc-modal-form">
            <input type="text" class="form-control" placeholder="请输入订单编号" id="searchArea" name = 'logistics_id' value="<?php echo ($logistics_id); ?>" required>
            <div class="invalid-feedback">
                订单编号不能为空~
            </div>
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit" id="searchBtn">查 询</button>
            </div>
        </form>
    </div>
    <!-- 查询结果 -->
    <div class="container">
        <?php if($_list != '' ): ?><table class="table"  id="textStatus">
        <?php else: ?>
            <table class="table" style="display: none" id="textStatus"><?php endif; ?>
            <thead>
            <tr>
                <th class="w-35p">时间</th>
                <th>运单状态</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($_list)): $i = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['status'] == 1 ): ?><tr class="last">
                        <td class="time"><?php echo ($vo["time"]); ?></td>
                        <td class="status"><?php echo ($vo["logistics_msg"]); ?></td>
                    </tr>
                   <?php else: ?>
                    <tr class="past">
                        <td class="time"><?php echo ($vo["time"]); ?></td>
                        <td class="status"><?php echo ($vo["logistics_msg"]); ?></td>
                    </tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            <!--<tr class="last">-->
                <!--<td class="time">2018-07-01 00:55:46</td>-->
                <!--<td class="status">[北京市]已签收,感谢使用顺丰,期待再次为您服务</td>-->
            <!--</tr>-->
            <!--<tr class="past">-->
                <!--<td class="time">2018-07-01 00:47:26</td>-->
                <!--<td class="status">[北京市]快件交给杨荣斌，正在派送途中（联系电话：17611772077）</td>-->
            <!--</tr>-->
            <!--<tr class="past">-->
                <!--<td class="time">2018-07-01 00:40:26</td>-->
                <!--<td class="status">[北京市]快件到达 【北京东城聚宝营业点】</td>-->
            <!--</tr>-->
            <!--<tr class="past">-->
                <!--<td class="time">2018-06-30 23:40:03</td>-->
                <!--<td class="status">[北京市]快件到达 【北京顺义集散中心】</td>-->
            <!--</tr>-->
            <!--<tr class="past">-->
                <!--<td class="time">2018-06-30 22:33:55</td>-->
                <!--<td class="status">[北京市]快件到达 【北京国航集散中心】</td>-->
            <!--</tr>-->
            <!--<tr class="past">-->
                <!--<td class="time">2018-06-30 20:08:46</td>-->
                <!--<td class="status">[北京市]顺丰速运 已收取快件</td>-->
            <!--</tr>-->
            </tbody>
        </table>
    </div>


    </div>
</div>

<script type="text/javascript">
    $(function(){
        $(window).resize(function(){
            $("#main-container").css("min-height", $(window).height() - 343);
        }).resize();
    })
</script>
	<!-- /主体 -->

	<!-- 底部 -->
	
    <!-- 底部
    ================================================== -->
    <footer class="footer">
      <div class="container">
          <p> 本站由 <strong><a href="http://www.onethink.cn" target="_blank">#xxxx公司</a></strong> xxxx公司支持</p>
      </div>
    </footer>

<script type="text/javascript">
(function(){
	var ThinkPHP = window.Think = {
		"ROOT"   : "", //当前网站地址
		"APP"    : "/index.php?s=", //当前项目地址
		"PUBLIC" : "/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>


 <!-- 用于加载js代码 -->
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<?php echo hook('pageFooter', 'widget');?>
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
	
</div>

	<!-- /底部 -->
</body>
</html>