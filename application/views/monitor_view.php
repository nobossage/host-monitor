<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
 
	<title>Мониторинг</title>

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
    <script src="<?php echo $this->config->item('base_url'); ?>js/jquery.js"></script>
<script>
    
var last_stat = '';
 var Timer = setInterval(new_last_stat, 3*60*1000); 
$(document).ready(function() {
    var old_last_stat = document.getElementById("get_log");
    last_stat = old_last_stat.innerHTML;
    
});


/*Новое распределение */
function send_ajax(id, operation, str)
{
    
            var type_req='html';
            if(operation=='get_step_4')
            {
                type_req='json';
            }
                $.ajax(
                {
                    type: "POST",
                    url: "doingsend",
                    data: "id=" + id + "&operation=" + operation + "&str="+ str,
                    dataType:type_req,
                     success: function(html)
                    {
                        
                        switch (operation)
                        {
                            case 'get_last_stat':
                                $("#get_log").html(html);
                                var old_last_stat = document.getElementById("get_log");
                                last_stat = old_last_stat.innerHTML;
                                break;
                            case 'get_log_host':

                                $("#get_log").html(html);
                                break;

                            default:
                                $("#get_ajax").html(html);
                        }
                    }
                });

}

function close_stat() {
    $("#get_log").html(last_stat);
}

function new_last_stat() {
    send_ajax('<?php echo $user_id; ?>','get_last_stat',10);
    
}
</script>
</head>
<body>

<div id="container">
    <div id='head'>
        <h1>Мониторинг</h1>
    </div>
    <div id='left'>
 <?php include('sidebar.php'); ?>
        </div> <!-- left-->


	<div id="right">
        <div id="body">
            <div class="col-wrap1">
                <div class="col-wrap2">
                                
                    <div id="get_log" class="col2" style="float:right;">
<!-- Вывод логов --><h4>Последние события</h4>

<?php
                foreach ($stat as $last) {
                    ($last['change_to'] == 'up')? $color='green':$color='red';
                    $string_last= '<p style="color:'.$color.'">'.date('Y-m-d H:i:s',strtotime($last['stamp'])+date('Z')). ' узел '.$last['host_name']. ' перешел в состояние <b >'. $last['change_to'].'</b></p>';
                    echo $string_last;
                }
                    
                  /*  echo '<pre>';
                    print_r($stat);
                    echo '</pre>';//*/
                    
                    ?>
                
                    </div>
                    <div class="col1">

                        <table border=0 >
                             <tr>
 <?php //if ($user_id > 0) { ?>   <th style="width:50px;"></th>    <?php //} ?>
                                <th>Имя хоста</th>
                                <th>7 дней</th>
                                <th>Последние сутки</th>
                                <th>Хост</th>
                            </tr>
                                        
                        <?php 
                            foreach ($table as $row) {
                                ($row['status_host']=='up')?$color_text='green':$color_text='red';
                                echo ' <tr style="color : '.$color_text.';"><div id="'.$row['id'].'_host">';
                                echo '<td>';
                                if (($user_id == $row['id_auth'])OR($UID < 11)) {
                                    echo '<a href="'.$this->config->item('base_url').'monitor/edit/'.$row['id'].'"><img src="'.$this->config->item('base_url').'images/edit_icon.png" title="Редактировать" /></a>';
                                }
                                echo '</td>';
                                        echo '<td style="cursor:pointer;" onclick="send_ajax('.$row['id'].', \'get_log_host\',1)"><b>'.$row['host_name'].'</b></td>
                                             <td>('.round($row['uptime']*100,2).'%)</td>
                                             <td><img src="'.$this->config->item('base_url').'monitor/image/'.$row['id'].'" /></td>
                                             <td>'.$row['host'].'</td>
                                         
                                    </div></tr> ';
                            }
                        ?>
                        </table>
                    </div>
                    <div class="clear"></div>
                <!-- /WRAP2 -->
                </div>
            <!-- /WRAP1 -->
            </div>
                
            <div>
<?php
    if ($user_id > 0) {
        
?>
            <code id="get_ajax">
                <?php echo validation_errors(); ?>
                <?php echo $error_add;  ?>
                <br />
                <pre><?php  //print_r($table);  ?></pre>
            </code>
            <form method="post" action="<?php echo $this->config->item('base_url'); ?>monitor">
                <p><h4>Добавить узел для слежения:</h4></p>
                <label>
                    Хост <input type="text" size=30 name="host" id="input_text" value="<?php echo set_value('host'); ?>" />
                </label>
                <label>
                    Будет показываться так: <input type="text" size=30 name="host_name" id="input_text" value="<?php echo set_value('host_name'); ?>" />
                </label><br />
                <label>
                    Тип мониторинга 
                    <select name="type">
                        <!-- <option disabled >Выберите</option> -->
                        <option value="http" <?php echo set_select('type', 'http'); ?> >Http</option>
                        <option value="ping" <?php echo set_select('type', 'ping'); ?>>Ping</option>
                    </select>
                </label>
                <label>
                    Периодичность в минутах (макс. 1440) <input type="text" name="period" size=6 id="input_period" value = "<?php echo set_value('period','5'); ?>" />
                </label>
                <label>
                    Логи видны всем<input type="checkbox" name="share" id="input_share" value = "<?php echo set_value('share','1'); ?>" checked="checked" />
                </label>
                <br /><input type="reset" id="button" value="Очистить" /><input type="submit" id="button" name="add_host" value="Добавить" />
            </form>
<?php
    }
?>

</div>
        </div>
    </div>
    
    <div id='footer'>
        <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
        <p class="footer">Memory usage <strong>{memory_usage}</strong></p>
    </div>
</div>

</body>
</html>
