<?php
/*
 * Без имени.php
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
	<title>Авторизация</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.22" />
    <link rel='stylesheet' href="<?php  echo $this->config->item('base_url'); ?>/css/style.css" type='text/css' />

</head>

<body>
    <div id='wrapper'>
        <h1>Авторизация</h1>
<?php
    if($no_auth !="")
    {
        echo '<p class="error">'.$no_auth.'</p><br/>';
    }
?>
	<form method=post>
       <h4>Логин</h4> <p><input type=text id='user_login' name=login></p>
        <h4>Пароль</h4><p><input type=password id='user_pass' name=pass></p>
        <p><input type="checkbox"  name='remember' value='1'> Запомнить меня</p>
        <table border=0 width=100%>
            <tr>
                <td>
                    <p><input type=submit id='login' name='login_button' value='OK'>        
                    </form></p>
                </td>
 
 
                <td align='right'>
                    <p><form method=post action='registration'><input type='submit' id='login' value='Регистрация' /></form></p>
                </td>
            </tr>
        </table>
        <div id=last><p >
                <a href="recall">Я забыл свой пароль!</a>
</p></div>
    
        <!--<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
        <p class="footer">Memory usage <strong>{memory_usage}</strong></p>-->
</div>
</body>

</html>
