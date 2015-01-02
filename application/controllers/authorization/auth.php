<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * auth.php
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
class Auth extends CI_Controller{

   
    function index(){
        $request_ur=$this->config->item('base_url');            //Запоминаем, откуда пришел
        $this->load->model('authmodel');                        //подключаем модель
        $this->load->helper('cookie');
        //Проверим куки
        $login=$this->input->cookie('login',TRUE);
        $pass=$this->input->cookie('pass',TRUE);
        if($login&&$pass)
        {                                      //если юзер пытается авторизоваться
            $hash=$this->authmodel->Authorize($login,$pass,FALSE);    //проверим
            if($hash)                                         //если верно, то
            {
                $user_arr=$this->authmodel->Get_user($login);
                $user_id=$user_arr['id'];       //получим данные юзера
                $UID=$user_arr['UID'];
                $sess_data=array('user_id'=>$user_id,        //массив, который пойдет в сессию
                                    'UID'=>$UID,
                                    //'login'=>$login,
                                    //'pass'=>$hash
                                );
                $this->session->set_userdata($sess_data);       //установка данных в сессию
                $data['no_auth']='';
            }
        }
        $user_id=$this->session->userdata('user_id');           //проверяем авторизацию
        if (!$user_id)                                            //Если еще не авторизован
        {
            $login=$this->input->post('login');                     //логин из формы авторизации
            $pass=$this->input->post('pass');                       //пароль оттуда же
            $remember=$this->input->post('remember');               //запомнить навсегда
            $data['no_auth']='';
            if($login&&$pass)
            {
                                                      //если юзер пытается авторизоваться
                $hash=$this->authmodel->Authorize($login,$pass);    //проверим
                if($hash)
                {                                         //если верно, то
                    if($remember)
                    {
                        $cookie = array(
                            'name'   => 'login',
                            'value'  => $login,
                            'expire' => '2592000', // 2592000 - запомним на месяц 
                            //'domain' => '.'.$this->config->item('base_url'),
                        );
                        $this->input->set_cookie($cookie);
                        $cookie = array(
                            'name'   => 'pass',
                            'value'  => $hash,
                            'expire' => '2592000', // 2592000 - запомним на месяц 
                            //'domain' => '.'.$this->config->item('base_url'),
                        );
                        $this->input->set_cookie($cookie);
                    }
                    $user_arr=$this->authmodel->Get_user($login);
                    $user_id=$user_arr['id'];       //получим данные юзера
                    $UID=$user_arr['UID'];
                    $sess_data=array('user_id'=>$user_id,        //массив, который пойдет в сессию
                                    'UID'=>$UID,
                                    //'login'=>$login,
                                    //'pass'=>$hash
                                );
                    $this->session->set_userdata($sess_data);       //установка данных в сессию
                    $data['no_auth']='';
                }
                else
                {
                    $data['no_auth']='<br />Неудачная авторизация. <br /> Попробуете еще раз?';
                }
            }
            if ( ! $user_id)
            {                                         //если нет авторизации,
                $this->load->view('authorization/authview',$data);                      //выводим форму
            }
            else
            {


                $this->output->set_header('Location:'. $request_ur);                         //иначе - пропускаем
            }
        }
        else
        {
            $this->output->set_header('Location:'. $request_ur);                         //иначе - пропускаем (авторизован куками)
        }

        
    }


}


?>
