<?php if (!defined('THINK_PATH')) exit();?>
<div class="container-span top-columns cf">
    <dl class="show-num-mod col_01">
        <dt><i class="count-icon user-count-icon"></i></dt>
        <dd>
            <span>用户数</span>
            <strong><?php echo ($info["user"]); ?></strong>
        </dd>
        <ul style="clear:both;">
        <li>昨日注册 <?php echo ($info["yesterday"]); ?></li>
        <li>今日注册 <?php echo ($info["today"]); ?></li>
        <li>今日登陆 <?php echo ($info["login"]); ?></li>
        </ul>
    </dl>
    <dl class="show-num-mod col_05">
        <dt><i class="count-icon category-count-icon"></i></dt>
        <dd>
            <span>游戏数</span>
            <strong><?php echo ($info["game"]); ?></strong>
        </dd>
        <ul style="clear:both;">
        <li>新增游戏 <?php echo ($info["add"]); ?></li>
        <li class="last">本月新增游戏 <?php echo ($info["monthadd"]); ?></li>
        </ul>
    </dl>
    <dl class="show-num-mod col_03">
        <dt><i class="count-icon doc-modal-icon"></i></dt>
        <dd>
            <span>总流水</span>
        <strong><?php if($info["total"] != null): echo ($info["total"]); else: ?>0<?php endif; ?></strong>
        </dd>
        <ul style="clear:both;">
        <li>今日流水 <?php if($info["ttotal"] == ''): ?>0<?php else: echo ($info["ttotal"]); endif; ?></li>
        <li>昨日流水 <?php if($info["ytotal"] == ''): ?>0<?php else: echo ($info["ytotal"]); endif; ?></li>
        <li>推广流水 <?php if($info["ptotal"] == ''): ?>0<?php else: echo ($info["ptotal"]); endif; ?></li>
        </ul>
    </dl>
    <dl class="show-num-mod col_02">
        <dt><i class="count-icon user-action-icon"></i></dt>
        <dd>
            <span>渠道数量</span>
            <strong><?php echo ($info["promote"]); ?></strong>
        </dd>
        <ul style="clear:both;">
        <li>新增渠道 <?php echo ($info["padd"]); ?></li>
        <li class="last">本月新增渠道 <?php echo ($info["monthpadd"]); ?></li>
        </ul>
    </dl>  
    <dl class="show-num-mod col_04">
        <dt><i class="count-icon wz-action-icon"></i></dt>
        <dd>
            <span>文章数</span>
            <strong><?php echo ($info["document"]); ?></strong>
        </dd>
        <ul style="clear:both;">
        <li>媒体站文章 <?php echo ($info["media"]); ?></li>
        <li class="last">渠道站文章 <?php echo ($info["blog"]); ?></li>
        </ul>
        <!-- <dd style="width:auto;font-size: 12px;text-align: left;">
        <p>徐州梦创--版权所有</p>   
        <p>网站：www.vlcms.com</p>
        <p>版本：手游系统商业版v2.0.6.10</p> 
        </dd> -->
    </dl>    
</div>
<div class="span2" >
	<div class="columns-mod">
		<div class="hd cf">
			<h5>最近 7 天流水概况</h5>
			<div class="title-opt">
			</div>
		</div>
		<div class="" style="height:278px;width:100%;">
            <div id="z_chart" style="height:100%;position:relative"><div id="chart" style="height:100%;"></div></div>
        </div>
    </div>
    <script type="text/javascript">
        var min = '<?php echo ($info["pay"]["min"]); ?>';
        var data = [<?php echo ($info["pay"]["data"]); ?>];
        var max = '<?php echo ($info["pay"]["max"]); ?>';
        var cate = [<?php echo ($info["pay"]["cate"]); ?>];
        jQuery(function(){jQuery("#chart").kendoChart({
            legend:{"position":"top","labels":{"font":"12px  DejaVu Sans"}},
            series:[{"type":"line","data":data,"name":"七天流水（单位：元）","color":"#f8a20f","axis":"temp"}],
            valueAxes:[{"name":"temp","min":min,"max":max}],
            categoryAxis:[{"categories":cate,"axisCrossingValue":[0,2],"justified":true,"line":{"visible":false},"majorGridLines":{"visible":false}}],
            tooltip:{"visible":true,"format":"{0}","shared":true}
        });});
    </script>
</div>
<div class="span2" >
	<div class="columns-mod">
		<div class="hd cf">
			<h5>最近 7 天注册概况</h5>
			<div class="title-opt">
			</div>
		</div>
		<div class="" style="height:278px;width:100%;">
            <div id="z_chart1" style="height:100%;position:relative"><div id="chart1" style="height:100%;"></div></div>
        </div>
    </div>
    <script type="text/javascript">
        var min1 = '<?php echo ($info["reg"]["min"]); ?>';
        var data1 = [<?php echo ($info["reg"]["data"]); ?>];
        var max1 = '<?php echo ($info["reg"]["max"]); ?>';
        var cate1 = [<?php echo ($info["reg"]["cate"]); ?>];
        jQuery(function(){jQuery("#chart1").kendoChart({
            legend:{"position":"top","labels":{"font":"12px  DejaVu Sans"}},
            series:[{"type":"line","data":data1,"name":"七天注册（单位：个）","color":"#f8a20f","axis":"temp"}],
            valueAxes:[{"name":"temp","min":min1,"max":max1}],
            categoryAxis:[{"categories":cate1,"axisCrossingValue":[0,2],"justified":true,"line":{"visible":false},"majorGridLines":{"visible":false}}],
            tooltip:{"visible":true,"format":"{0}","shared":true}
        });});
    </script>
</div>