<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * registration.php
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

class Registration extends CI_Controller {

    var $out=array(
        'error'=>false,
        'errort'=>''
        );
 
	function index(){

		$this->load->helper(array('form', 'url'));
		
		$this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<p class="error">', '</p>');
        $this->form_validation->set_rules('username', 'Логин', 'trim|required|min_length[5]|max_length[18]');
		$this->form_validation->set_rules('password', 'Пароль', 'trim|required|min_length[6]|max_length[18]');
		//$this->form_validation->set_rules('passconf', 'Проверка пароля', 'required');
		$this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');


		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('authorization/regview');
		}
		else
		{
            $this->load->model('authmodel');
            $login=$this->input->post('username');
            $pass=$this->input->post('password');
            $email=$this->input->post('email');
            $this->Add_user($login,$pass,$email);
			$this->load->view('authorization/regsuccess',$this->out);
		}

	}

/*
 * Добавить пользователя
 */
    function Add_user($login,$pass,$email,$UID=1000){

        $login = mysql_real_escape_string($login);
        $password = mysql_real_escape_string($pass);
        $email = mysql_real_escape_string($email);
        $UID = mysql_real_escape_string($UID);
        // проверяем на наличие ошибок (например, длина логина и пароля)
        $this->_Check_login($login);
        $this->_Check_pass($pass);
        $this->_Check_email($email);
        if (!$this->out['error']){
            $add_u = $this->authmodel->Create_user($login,$pass,$email,$UID);
            $this->out['error']=$add_u['error'];
            $this->out['errort']=$add_u['errort'];
            if(!$this->out['error']) {
                $user_arr=$this->authmodel->Get_user($login);
                $user_id=$user_arr['id'];
                $sess_data=array('user_id'=>$user_id,        //массив, который пойдет в куки
                                    'UID'=>$UID,
                                );
                $this->session->set_userdata($sess_data);       //установка данных в куки - считаем его авторизованным
            }
        }
    }

/*
 *
 *Проверка длины логина
 *
 */

    private function _Check_login($login,$len=2){
        if (strlen($login) < $len){
            $this->out['error'] = true;
            $this->out['errort'] .= "Длина логина должна быть не менее $len символов.<br />";
        }
    }

/*
 *
 *Проверка длины пароля
 *
 */

    private function _Check_pass($pass,$len=6){
        if (strlen($pass) < $len){
            $this->out['error'] = true;
            $this->out['errort'] .= "Длина пароля должна быть не менее $len символов.<br />";
        }
    }
/*
 *
 *Проверка e-mail
 *
 */

    private function _Check_email($email){
        if(!(preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $email))) { 
            $this->out['error'] = true;    
            $this->out['errort'] .= 'Некорректный e-mail.<br />';
        }
    }
}
?>
