<?php
/*
 * dosuccess.php
 * 
 * Представление завершения задачи. Ожидает двух переменных - $error=TRUE при любой ошибке.
 * Текстовое описание удачного исхода или ошибки в переменной  $errort
 * 
 * 
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
	<title>Todo</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.22" />
    <link rel='stylesheet' href="<?php  echo $this->config->item('base_url'); ?>/css/style.css" type='text/css' />

</head>

<body>
    <div id='wrapper'>
    <?php if(!$error): ?>
    	<p id=mess><?php echo $errort; ?></p>
        <form method='post' action="<?=$this->config->item('base_url');?>">
        <input type=submit id="login" name="exit" value='Ok' >
    </form>
    <?php else: ?>
    	<p id=mess><?php echo $errort; ?></p>
        <form method=post action="<?=$this->config->item('base_url');?>">
        <input type=submit id='button' name="exit" value='Попробовать еще' >
    </form>
    <?php endif; ?>
    </div>
</body>

</html>
