<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
 
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
		border: 1px solid #D0D0D0;
        background: #fff;
		padding: 12px 10px 12px 10px;
	}
	
	#container{
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
    <link rel='stylesheet' href="<?php  echo $this->config->item('base_url'); ?>/css/style.css" type='text/css' />

</head>
<body>

<div id="container">
    <div id='head'>
        <h1>Welcome to CodeIgniter!</h1>
    </div>
    <div id='left'>
 <?php include('sidebar.php'); ?>
        </div> <!-- left-->


	<div id="right">
        <div id="body">
            <p>The page you are looking at is being generated dynamically by CodeIgniter.</p>

            <p>If you would like to edit this page you'll find it located at:</p>
            <code>application/views/welcome_message.php</code>

            <p>The corresponding controller for this page is found at:</p>
            <code>application/controllers/welcome.php</code>

            <p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="user_guide/">User Guide</a>.</p>
        </div>
    </div>
    
    <div id='footer'>
        <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
        <p class="footer">Memory usage <strong>{memory_usage}</strong></p>
    </div>
</div>

</body>
</html>
