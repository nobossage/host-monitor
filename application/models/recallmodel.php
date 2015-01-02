<?php
/*
 * recallmodel.php
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
class Recallmodel extends CI_Model {

    /*protected $authoriz=False;
    protected $login='';
    protected $pass='';
    protected $UID='';*/

	function Recallmodel()
     {
        $this->load->library('email');
        log_message('info', 'Загрузка Recallmodel успешно');

     }
/*
 *
 * Проверка наличия активной процедуры восстановления
 * Вернет FALSE или ссылку восстановления
 * 
 */

    function Check_email_recall($email){
        $link=FALSE;
        $query=$this->db->get_where('recall',array('email'=>$email),1);
        if ($query->num_rows() > 0){
            $user_arr=$query->result_array();
            $hash=$user_arr[0]['hash'];
            $link=$this->config->item('base_url');
            $link.='recall/confirmemail/'.$hash;

        }
        return $link;
    }

/*
 *
 * Удаление записи по email`у
 *
 */
    function Erase_email($email){
        $query=$this->db->get_where('recall',array('email'=>$email),1);
        if ($query->num_rows() > 0){
            $user_arr=$query->result_array();
            $id=$user_arr[0]['id'];
            $this->db->where('id', $id);
            $this->db->delete('recall');
            $res=TRUE;
        }else{
            $res=FALSE;
        }
        return $res;
    }
/*
 *
 * Добавление записи
 * Возвращает ссылку или фальш
 */
    function Add_recall($email){
        if($this->_Check_email($email)){
            $link=$this->Check_email_recall($email);
            if($link) return $link;
            $date_del=date('Y-m-d',strtotime("+3 day"));
            $salt=$this->GenerateSalt(6);
            $hash=$this->GenerateHash($email,$salt);
            $link=$this->GenerateLink($hash);
            $data = array(
                    'date_del' => $date_del ,
                    'hash' => $hash ,
                    'email' => $email
                );
            if ($this->db->insert('recall', $data)){
                return $link;
            }else{
                return False;
            }
        }else{
            return False;
        }
    }

/*
 *
 * Удаление старых записей
 *
 */
    function Clean_old(){
        $this->db->where('date_del <', date('Y-m-d'));
        $this->db->select('email');
        $query=$this->db->get('recall');
        if ($query->num_rows() > 0){
            $user_arr=$query->result_array();
            foreach ($user_arr[0] as $key=>$val){
                $this->Erase_email($val);
            }
        }
    }
/*
 *
 *Получить хеш восстановления пароля
 *
 */
    function Get_hash($hash){
        $out=False;
        $query = $this->db->get_where('recall', array('hash' => $hash));
        if ($query->num_rows() > 0){
            $user_arr=$query->result_array();
            $out=$user_arr[0]['email'];
        }
        return $out;
    }
/*
 *
 *Проверка наличия email
 *
 */
    private function _Check_email($email){
        //$this->db->where('email', $email);
        $query = $this->db->get_where('auth', array('email' => $email));
        if ($query->num_rows() > 0){
            return TRUE;  //Найдена запись
        }else{
            return FALSE;   //email уникален
        }
    }

/*
 *
 *Получить email admin`a
 *
 */
    public function Get_admin_email(){
        $this->db->select('email');
        $query = $this->db->get_where('auth', array('login' => 'admin'));
        $row = $query->row();
        if ($query->num_rows() > 0){
            return $row->email;  //Возвращаем email admin`a
        }else{
            return FALSE;   //Не найден логин admin
        }
    }


/*
 *
 *Генерация ссылки восстановления пароля
 *
 */
    private function GenerateLink($hash){
        $out='';
        //$hash=$this->GenerateHash($email,$salt);
        $out=$this->config->item('base_url');
        $out.='recall/confirmemail/'.$hash;
        
        return $out;
}

    
/*
 *
 *Генерация "соли" для хеша пароля
 *
 */
    private function GenerateSalt($n=3){
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
        $counter = strlen($pattern)-1;
        for($i=0; $i<$n; $i++){
            $key .= $pattern{rand(0,$counter)};
        }
        return $key;
}

/*
 *
 * Генерация хеша пароля
 *
 */

    private function GenerateHash($password,$salt){

        $key = md5(md5($password) . $salt);
        
        return $key;
    }
}
?>
