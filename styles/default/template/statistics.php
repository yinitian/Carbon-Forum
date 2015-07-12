<?php
if (!defined('InternalAccess')) exit('error: 403 Access Denied');
?>
<!-- main-content start -->
<!-- ECharts单文件引入 -->
<script src="<?php echo $Config['WebsitePath']; ?>/static/echarts/echarts.js"></script>
<script type="text/javascript">
// 路径配置
require.config({
	paths: {
		echarts: '<?php echo $Config['WebsitePath']; ?>/static/echarts'
	}
});

$(document).ready(function(){
	$("#statistics").easyResponsiveTabs({
		type: 'default', //Types: default, vertical, accordion           
		width: 'auto', //auto or any custom width
		fit: true,   // 100% fits in a container
		closed: false, // Close the panels on start, the options 'accordion' and 'tabs' keep them closed in there respective view types
		activate: function() {}  // Callback function, gets called if tab is switched
	});
});
</script>
<div id="statistics" class="tab-container">
	<ul class='resp-tabs-list'>
		<li><?php echo $Lang['Topics_Statistics']; ?></li>
		<li><?php echo $Lang['Posts_Statistics']; ?></li>
		<li><?php echo $Lang['Users_Statistics']; ?></li>
	</ul>
	<div class="resp-tabs-container">

		<div>
			<div id="TopicsStatistics" style="width:960px;height:560px"></div>
		</div>
		<div>
			<div id="PostsStatistics" style="width:960px;height:560px"></div>
		</div>
		<div>
			<div id="UsersStatistics" style="width:960px;height:560px"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
//预置数据
var StatisticsData = <?php echo $DataJsonString; ?>;

// 使用
require(
	[
		'echarts',
		'echarts/chart/line' // 使用折线图就加载line模块，按需加载
	],
	function (ec) {
		//准备好数据
		var TotalTopicsStatisticsData = [];
		var DaysTopicsStatisticsData = [];

		var TotalPostsStatisticsData = [];
		var DaysPostsStatisticsData = [];

		var TotalUsersStatisticsData = [];
		var DaysUsersStatisticsData = [];

		for(var i in StatisticsData){
		//for (var i = TotalTopicsStatisticsData.length - 1; i >= 0; i--) {
			var CurStatisticsDate = StatisticsData[i][0].split('-');
			var CurStatisticsDateObject=new Date();
			CurStatisticsDateObject.setFullYear(
				CurStatisticsDate[0], 
				CurStatisticsDate[1]-1, 
				CurStatisticsDate[2]
			);
			TotalTopicsStatisticsData.push(
				[
					CurStatisticsDateObject, 
					StatisticsData[i][1],
					1
				]
			);
			DaysTopicsStatisticsData.push(
				[
					CurStatisticsDateObject, 
					StatisticsData[i][4],
					1
				]
			);

			TotalPostsStatisticsData.push(
				[
					CurStatisticsDateObject, 
					StatisticsData[i][2],
					1
				]
			);
			DaysPostsStatisticsData.push(
				[
					CurStatisticsDateObject, 
					StatisticsData[i][5],
					1
				]
			);

			TotalUsersStatisticsData.push(
				[
					CurStatisticsDateObject, 
					StatisticsData[i][3],
					1
				]
			);
			DaysUsersStatisticsData.push(
				[
					CurStatisticsDateObject, 
					StatisticsData[i][6],
					1
				]
			);
		};
		// 基于准备好的dom，初始化echarts图表
		option = {
			title : {
				text : '<?php echo $Lang['Topics_Statistics']; ?>',
				subtext : 'Carbon Forum'
			},
			tooltip : {
				trigger: 'item',
				formatter : function (params) {
					var date = new Date(params.value[0]);
					data = date.getFullYear() + '-'
						   + (date.getMonth() + 1) + '-'
						   + date.getDate() + ' '
						   + date.getHours() + ':'
						   + date.getMinutes();
					return data + '<br/>'
						   + params.value[1]
				}
			},
			toolbox: {
				show : true,
				feature : {
					mark : {show: true},
					dataView : {show: true, readOnly: false},
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			dataZoom: {
				show: true,
				start : 70
			},
			legend : {
				data : [
						'<?php echo $Lang['TotalTopics_Statistics']; ?>',
						'<?php echo $Lang['DaysTopics_Statistics']; ?>'
					]
			},
			grid: {
				y2: 80
			},
			xAxis : [
				{
					type : 'time',
					splitNumber:7
				}
			],
			yAxis : [
				{
					type : 'value'
				}
			],
			series : [
				{
					name: '<?php echo $Lang['TotalTopics_Statistics']; ?>',
					type: 'line',
					showAllSymbol: true,
					data: TotalTopicsStatisticsData
				},
				{
					name: '<?php echo $Lang['DaysTopics_Statistics']; ?>',
					type: 'line',
					showAllSymbol: true,
					data: DaysTopicsStatisticsData
				}
			]
		};

		// 为echarts对象加载数据 
		var TopicsStatistics = ec.init(document.getElementById('TopicsStatistics'));
		TopicsStatistics.setOption(option);

		option.title.text = '<?php echo $Lang['Posts_Statistics']; ?>';
		option.legend.data = [
			'<?php echo $Lang['TotalPosts_Statistics']; ?>',
			'<?php echo $Lang['DaysPosts_Statistics']; ?>'
		];
		option.series[0].name = '<?php echo $Lang['TotalPosts_Statistics']; ?>';
		option.series[0].data = TotalPostsStatisticsData;
		option.series[1].name = '<?php echo $Lang['DaysPosts_Statistics']; ?>';
		option.series[1].data = DaysUsersStatisticsData;
		var PostsStatistics = ec.init(document.getElementById('PostsStatistics'));
		PostsStatistics.setOption(option);

		option.title.text = '<?php echo $Lang['Users_Statistics']; ?>';
		option.legend.data = [
			'<?php echo $Lang['TotalUsers_Statistics']; ?>',
			'<?php echo $Lang['DaysUsers_Statistics']; ?>'
		];
		option.series[0].name = '<?php echo $Lang['TotalUsers_Statistics']; ?>';
		option.series[0].data = TotalUsersStatisticsData;
		option.series[1].name = '<?php echo $Lang['DaysUsers_Statistics']; ?>';
		option.series[1].data = DaysUsersStatisticsData;
		var UsersStatistics = ec.init(document.getElementById('UsersStatistics'));
		UsersStatistics.setOption(option);
	}
);
</script>
<!-- main-content end -->