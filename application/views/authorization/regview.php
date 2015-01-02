<?php
/*
 * regview.php
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
	<title>Регистрация</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.22" />
    <link rel='stylesheet' href="<?php  echo $this->config->item('base_url'); ?>/css/style.css" type='text/css' />

</head>

<body>

<div id='wrapper'>
    <h1>Регистрация</h1>
    <form method='post'>

<h4>Логин</h4>
<?php echo form_error('username'); ?>
<input type="text" id='user_login'  name="username" value="<?php echo set_value('username'); ?>" size="50" />

<h4>Пароль</h4>
<?php echo form_error('password'); ?>
<input type="password" id='user_pass' name="password" value="<?php echo set_value('password'); ?>" size="50" />

<h4>Email</h4>
<?php echo form_error('email'); ?>
<input type="text" id='user_email' name="email" value="<?php echo set_value('email'); ?>" size="50" />

<div align=right><input id='login' type="submit" value="Отправить" /></div>

</div>
</form>
    <!--<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
    <p class="footer">Memory usage <strong>{memory_usage}</strong></p>-->

</body>

</html>
