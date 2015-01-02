<?php
/*
 * profileview.php
 * 
 * Copyright 2013 Noble Ossage <nobossage@gmail.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Управление профилем</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.22" />
    <link rel='stylesheet' href="<?php echo $this->config->item('base_url'); ?>/css/style.css" type='text/css' />
    <link href="<?php echo $this->config->item('base_url'); ?>css/jquery.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $this->config->item('base_url'); ?>js/jquery.js"></script>
<script src="<?php echo $this->config->item('base_url'); ?>js/jquery-ui.min.js"></script>
<script src="<?php echo $this->config->item('base_url'); ?>js/jquery.cookie.js"></script>

<script type="text/javascript">
$(document).ready(function() {

    $("#tabs").tabs({
        active: $.cookie("startTab_profile")
    });

    
    $("#tabs").on("tabsactivate", function( event, ui ) {
        
        var selected = $("#tabs").tabs('option', 'active');
        $.cookie("startTab_profile",selected,7);
        
    });



});
        </script>
</head>

<body>
    <div id='container'>
<div id=head>
                    <h1>Управление профилем</h1>

</div>
         <div id='left'>
<?php include('sidebar.php'); ?>
        </div> <!-- left-->
        <div id='right'>
            
            	<div id="tabs">
<!-- <pre>
<?php //print_r($todolist); ?>
</pre> -->
<ul>
<!-- Заголовки -->
<li><a href="#tabs-0" title="">Личные данные</a></li>
<li><a href="#tabs-1" title="">Доступ</a></li>
</ul>
        <div id="tabs-0"  >
        
            
            
            
                <div class="content_tab">
                    <form method=post action="<?php echo $this->config->item('base_url') ?>profile/change_profile" >
                    <table border=0 id=table_profile>
                        <tr>
                            <td>
                                <p>Фамилия</p>
                            </td>
                            <td>
                                <input type=text id='input_text' name='family' value="<?php echo $family; ?>">
                            </td>
                                <td rowspan=2>
                                    <p>Здесь можно поменять имя и фамилию. Предъявлять паспорт не требуется.</p>
                                </td>
                        </tr>
                        <tr>
                            <td>
                                <p>Имя </p>
                            </td>
                            <td>
                                <input type=text id='input_text'  name='name' value="<?php echo $name; ?>">
                            </td>
                        </tr>
                    
                    </table>
        
                    <p><input type=submit id='login' name='edit_profile' value='Сохранить'> </p>
                    </form>
                </div>
        </div> <!-- tab-0 -->
        <div id="tabs-1"  >
                <div class="content_tab">
                    <form method=post action="<?php echo $this->config->item('base_url') ?>profile/change_pass"> 
                        <table border=0>
                            <tr>
                                <td>
                                    <p>e-mail </p>
                                </td>
                                <td>
                                    
                                    <input type=text id='input_text' name='email' value="<?php echo $email; ?>">
                                </td>
                                <td rowspan=3>
                                    Для смены данных на этой странице необходимо ввести текущий пароль.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p>Текущий пароль </p>
                                </td>
                                <td>
                                    <input type=text id='input_text'  name='cur_pass' value="">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p>Новый пароль </p>
                                </td>
                                <td>
                                    
                                    <input type=text id='input_text' name='new_pass' value="">
                                </td>
                            </tr>
                        </table>
                    
                    
        
                    <p><input type=submit id='login' name='edit_pass' value='Сохранить'> </p>
                    </form>     
                </div>
       <!-- <p><input type="checkbox"  name='remember' value='1'> Запомнить меня</p> -->
        
                    <!-- <p><input type=submit id='login' name='edit_profile' value='OK'> </p>        -->

                    
        </div> <!-- tab-1 -->
        
    </div><!-- tabs -->

        </div> <!--id = right -->
        <div id=footer>
        <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
        <p class="footer">Memory usage <strong>{memory_usage}</strong></p>
                </div>
        
</div>
</body>

</html>
