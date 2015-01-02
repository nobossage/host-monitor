<?php
/*
 * blog.php
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


class Recall extends CI_Controller {

	function index()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<p class="error">', '</p>');
        $this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');


		if ($this->form_validation->run() == FALSE){
			$this->load->view('authorization/recallview');
		}else{ 
            $this->load->view('authorization/recallview', $data);
        }
	}
/*
 *
 * Восстановление пароля
 *
 */
    function Sendpass(){
        $this->load->model('recallmodel');
        $this->load->library('email');
        $email=$this->input->post('email');
        $link = $this->recallmodel->Add_recall($email);
        if ($link){
            $this->email->from($this->recallmodel->Get_admin_email(),'Recovery Pass');
            $this->email->to($email); 
            $this->email->subject('Восстановление пароля');
            $this->email->message("
            Это письмо сгенерировано автоматически.
            Кто-то, возможно Вы, запросил восстановление пароля.
            Ссылка для восстановления пароля:
            $link

            Нет нужды отвечать на это письмо, спасибо."); 

            if (!$this->email->send()){
                    $out['error']=True;
                    $out['errort']='Не удалось отправить письмо.';
            }else{
                    $out['error']=False;
                    $out['errort']='Ссылка для смены пароля отправлена.';
            }

        }else{
            //echo 'e-mail в базе не найден';
            $out['error']=True;
            $out['errort']='Указанный e-mail в базе не найден.';
        }
        $this->load->view('authorization/dosuccess',$out);
        
    }
/*
 *
 *
 *Проверка email
 *
 */
    function Confirmemail($hash_user){
        $this->load->model('recallmodel');
        $email=$this->recallmodel->Get_hash($hash_user);
        if($email){
            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<p class="error">', '</p>');
            $this->form_validation->set_rules('password', 'Пароль', 'trim|required|min_length[6]|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                $data = array('email' => $email);
                $this->load->view('authorization/changepassview',$data);
            }else{
                $new_pass=$this->input->post('password');
                $email=$this->input->post('email');
                if ($this->Change_pass($email,$new_pass)){
                    //echo 'Успешно';
                    $out['error']=False;
                    $out['errort']='Пароль изменен успешно.';
                    
                }else{
                    $out['error']=True;
                    $out['errort']='Не удалось сменить пароль.';
                    //echo 'плохо';
                }
                $this->load->view('authorization/dosuccess',$out);
            }
        }else{
            //show_404('page');
            echo "УПСССССССССэж  $hash_user";
        }

    }
/*
 * 
 * Смнеа парлоя
 * 
 */
    function Change_pass($email,$pass){
        $out=false;
        //$salt=$this->db->where('email', $email);
        $query=$this->db->get_where('auth',array('email'=>$email),1);
        if ($query->num_rows() > 0){
            $user_arr=$query->result_array();
            $salt=$user_arr[0]['sol'];
            $hash=md5(md5($pass) . $salt);
        

            $data = array('password' => $hash);
            $this->db->where('email', $email);
            if($this->db->update('auth', $data)){
                $this->db->delete('recall', array('email' => $email));
                $out=true;
            }
        }
        return $out;
    }
/*
 *
 *Удаляем старые записи
 *
 */
 
function clean(){
     $this->load->model('recallmodel');
     //$this->recallmodel->Create_recall_table();
     $this->recallmodel->Clean_old();
}
}

