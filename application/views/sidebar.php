<?php
/*
 * sidebar.php
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


            <!--<p>
                <a target=_blank href="<?php echo $this->config->item('base_url') ?>dbscaf">БД</a>
                <i>Привет!</i>
            </p> -->

          <p><a href="<?php echo $this->config->item('base_url') ?>">На главную</a></p> 
              
              
<?php  if ($user_id > 0) { ?>
            <p><a href="<?php echo $this->config->item('base_url') ?>profile">Профиль</a></p>
<?php } else {  ?>
            <p><a href="<?php echo $this->config->item('base_url') ?>auth">Вход</a></p>
            <p><a href="<?php echo $this->config->item('base_url') ?>registration">Регистрация</a></p>
<?php } ?>

            <!-- <p><a href="<?php echo $this->config->item('base_url') ?>monitor">Монитор</a></p> -->
<?php  if ($user_id > 0) { ?>
    <form method=post action='<?php echo $this->config->item('base_url') ?>'>
        <input type=submit id='button' name="exit" value='Выход' >
    </form>
<?php } ?>


