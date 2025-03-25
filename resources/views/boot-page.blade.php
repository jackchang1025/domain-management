<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, viewport-fit=cover"/>
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>请在浏览器中打开</title>
	
	<script language=JavaScript>
	document.oncontextmenu=new Function("event.returnValue=false;");
	document.onselectstart=new Function("event.returnValue=false;");
	</script>
	
	<style type="text/css">
	*{margin:0; padding:0; box-sizing: border-box; -webkit-tap-highlight-color: transparent;}
	
	body {
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", "Helvetica Neue", Helvetica, Arial, sans-serif;
		line-height: 1.5;
		color: #333;
		background-color: #f5f5f5;
		min-height: 100vh;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 20px;
	}
	
	.container {
		max-width: 500px;
		width: 100%;
		margin: 0 auto;
		background-color: #fff;
		border-radius: 12px;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
		overflow: hidden;
		padding-bottom: 15px;
	}
	
	.header {
		background-color: #07C160;
		color: #fff;
		padding: 15px 20px;
		text-align: center;
		font-size: 18px;
		font-weight: 600;
		position: relative;
	}
	
	.header .icon {
		display: inline-block;
		vertical-align: middle;
		margin-right: 5px;
	}
	
	.content {
		padding: 20px;
	}
	
	.img-container {
		margin: 0 auto;
		max-width: 100%;
		max-height: 100%;
		padding: 10px;
		overflow: hidden;
	}
	
	.img-container img {
		width: 100%;
		display: block;
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	}
	
	.tips {
		margin-top: 20px;
		padding: 0 15px;
	}
	
	.tips-title {
		font-size: 16px;
		font-weight: 600;
		color: #333;
		margin-bottom: 10px;
		text-align: center;
	}
	
	.tips-item {
		position: relative;
		padding-left: 20px;
		margin-bottom: 10px;
		color: #666;
		font-size: 14px;
	}
	
	.tips-item:before {
		content: '';
		position: absolute;
		left: 0;
		top: 8px;
		width: 6px;
		height: 6px;
		background-color: #07C160;
		border-radius: 50%;
	}
	
	.arrow {
		margin: 10px auto;
		width: 40px;
		height: 40px;
		background-color: #f5f5f5;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		color: #07C160;
		font-size: 20px;
	}
	
	.footer {
		text-align: center;
		margin-top: 20px;
		color: #999;
		font-size: 12px;
	}
	</style>
</head>
<body>
	<div class="container">
		<div class="content">
			
			
			<div class="img-container">
				<img src="/images/live_weixin.png" alt="微信打开"/>
			</div>
			
			<!-- <div class="tips">
				<div class="tips-item">点击微信右上角<strong>「...」</strong>按钮</div>
				<div class="tips-item">选择<strong>「在浏览器打开」</strong>选项</div>
				<div class="tips-item">即可安全访问本页面内容</div>
			</div> -->
		</div>
		
		
	</div>
</body>
</html>