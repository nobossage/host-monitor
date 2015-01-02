<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
 
	<title>Редактирование записи о хосте.</title>

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
        <h1>Редактирование записи</h1>
    </div>
    <div id='left'>
 <?php include('sidebar.php'); ?>
        </div> <!-- left-->


	<div id="right">
        <div id="body">
           <!--  <p></p> -->
            <?php 

            $html_sel='';
            $ping_sel='';
            ($old['type']=='http')? $html_sel='selected':$ping_sel='selected';
            ?>
            <code>
                <?php echo validation_errors(); ?>
                <?php echo $error_add;  ?>
                <br />
                <!-- <pre>old<?php  //print_r($old);  ?></pre>
                <pre>new<?php  //print_r($new);  ?></pre> -->
            </code>

            <form method="post" action="<?php echo $this->config->item('base_url'); ?>monitor/edit/<?php echo $old['id']; ?>">
                <input type="hidden" name="id" value = "<?php echo $old['id']; ?>" />
                <p><h4>Редактирование:</h4></p>
                <label>
                    Хост <input type="text" size=30 name="host"  id="input_text" value="<?php echo set_value('host',$old['host']); ?>" />
                </label>
                <label>
                    Имя для отображения: <input type="text" size=30 name="host_name"  id="input_text" value="<?php echo set_value('host_name',$old['host_name']); ?>" />
                </label>
                <label>
                    Тип мониторинга 
                    <select name="type">
                        
                        <option <?php echo $html_sel; ?> value="http" <?php echo set_select('type', 'http'); ?>  >Http</option>
                        <option <?php echo $ping_sel; ?> value="ping" <?php echo set_select('type', 'ping'); ?>  >Ping</option>
                    </select>
                </label>
                <label>
                    Периодичность в минутах (макс. 1440) <input type="text" id="input_period" name="period" size=6 value = "<?php echo set_value('period',$old['period']); ?>" />
                </label>
                <label>
                    Логи видны всем<input type="checkbox" name="share" id="input_share" value = "1" <?php echo (($old['share']=='1')? 'checked':''); ?>/>
                </label>
                <br />
                <input type="button" id="button" value="Отмена" onclick="window.location.href='<?php echo $this->config->item('base_url'); ?>monitor'"/><input type="submit" id="button" name="edit_host" value="Готово" />
            </form>
            <form method="post" action="<?php echo $this->config->item('base_url'); ?>monitor/edit/<?php echo $old['id']; ?>">
                <input type="submit" id="button" name="delete" value="Удалить запись" />
            
            </form>

        </div>
    </div>
    
    <div id='footer'>
        <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
        <p class="footer">Memory usage <strong>{memory_usage}</strong></p>
    </div>
</div>

</body>
</html>
