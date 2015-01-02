<?php
/*
 * authmod.php
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
class Authmodel extends CI_Model {

    /*protected $authoriz=False;
    protected $login='';
    protected $pass='';
    protected $UID='';*/

    function __construct() {
        
        // Call the Model constructor
        parent::__construct();
            
        // Проверяем наличие таблиц в базе
        //auth
        $sql='SHOW TABLES LIKE \''.$this->db->dbprefix('auth').'\'';
        $result = $this->db->query($sql);
        if ($result->num_rows == '0') {
            $this->Create_Table('auth');
        }
        //user
        $sql='SHOW TABLES LIKE \''.$this->db->dbprefix('user').'\'';
        $result = $this->db->query($sql);
        if ($result->num_rows == '0') {
            $this->Create_Table('user');
        }
        //ci_sessions
        $sql='SHOW TABLES LIKE \''.$this->db->dbprefix('ci_sessions').'\'';
        $result = $this->db->query($sql);
        if ($result->num_rows == '0') {
            $this->Create_Table('ci_sessions');
        }
        //recall
        $sql='SHOW TABLES LIKE \''.$this->db->dbprefix('recall').'\'';
        $result = $this->db->query($sql);
        if ($result->num_rows == '0') {
            $this->Create_Table('recall');
        }


         
    }

/*
 * Создаем таблицы в БД (если ее еще нет - проверяет это конструктор модели)
 * 
 */
    private function Create_Table($name_table) {
        
        $this->load->dbforge();
        
        switch ($name_table) {
            case 'auth':
                
                // Добавляем поля
                $this->dbforge->add_field(' `id` int(11) NOT NULL AUTO_INCREMENT COMMENT \'id user\'');
                $this->dbforge->add_field('`login` varchar(64) NOT NULL COMMENT \'Логин\'');
                $this->dbforge->add_field('`password` varchar(64) NOT NULL COMMENT \'Пароль\'');
                $this->dbforge->add_field('`sol` varchar(4) NOT NULL COMMENT \'Соль кодировки\'');
                $this->dbforge->add_field('`UID` int(6) NOT NULL COMMENT \'UID доступ\'');
                $this->dbforge->add_field('`email` varchar(64) NOT NULL COMMENT \'Электропочта\'');
                // Добавим первичный ключ на id
                $this->dbforge->add_key('id', TRUE);
                
                $this->dbforge->create_table('auth', TRUE);  // создали таблицу
                
                // Добавляем первую запись - администратор admin с паролем admin
                $sql='INSERT INTO `'.$this->db->dbprefix('auth').'` (`id`, `login`, `password`, `sol`, `UID`, `email`) VALUES
                        (1, \'admin\', \'18ba4e005809f08f1d5c3bbee97aa98c\', \'mxj\', 10, \'admin@admin.mail\')';
                $this->db->query($sql);
                
                log_message('debug', "Created Table 'auth'");
                break;
            case 'user':
                
                // Добавляем поля
                $this->dbforge->add_field(' `id` int(11) NOT NULL AUTO_INCREMENT COMMENT \'id user\'');
                $this->dbforge->add_field('`id_auth` int(11) NOT NULL COMMENT \'ID авторизации\'');
                $this->dbforge->add_field('`family` varchar(64) NOT NULL COMMENT \'Фамилия\'');
                $this->dbforge->add_field('`name` varchar(64) NOT NULL COMMENT \'Имя\'');
                // Добавим первичный ключ на id
                $this->dbforge->add_key('id', TRUE);
                
                $this->dbforge->create_table('user', TRUE);  // создали таблицу
                
                // Добавляем первую запись - админ же есть
                $sql='INSERT INTO `'.$this->db->dbprefix('user').'` (`id`, `id_auth`, `family`, `name`) VALUES (1, 1, \'\', \'\')';
                $this->db->query($sql);
                
                log_message('debug', "Created Table 'user'");
                break;
            case 'recall':
                
                // Добавляем поля
                $this->dbforge->add_field("
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `email` varchar(64) NOT NULL COMMENT 'Электропочта',
                        `hash` varchar(64) NOT NULL COMMENT 'Хэш ссылки',
                        `date_del` varchar(64) NOT NULL COMMENT 'Дата удаления',
                        PRIMARY KEY (`id`)"
                        );
                // Добавим первичный ключ на id
                //$this->dbforge->add_key('id', TRUE);
                
                $this->dbforge->create_table('recall', TRUE);  // создали таблицу
                
                log_message('debug', "Created Table 'recall'");
                break;
            case 'ci_sessions':
                
                // Добавляем поля
                $this->dbforge->add_field('`session_id` varchar(40) NOT NULL DEFAULT \'0\'');
                $this->dbforge->add_field('`ip_address` varchar(16) NOT NULL DEFAULT \'0\'');
                $this->dbforge->add_field('`user_agent` varchar(50) NOT NULL');
                $this->dbforge->add_field('`last_activity` int(10) unsigned NOT NULL DEFAULT \'0\'');
                $this->dbforge->add_field('`user_data` text NOT NULL');

                // Добавим первичный ключ на id
                $this->dbforge->add_key('session_id', TRUE);
                
                $this->dbforge->create_table('ci_sessions', TRUE);  // создали таблицу
                
                log_message('debug', "Created Table 'ci_sessions'");
                break;
        }

    }




/*
 *
 * Проверка авторизации
 * Вернет FALSE или хеш пароля
 * $pass - слово(по умолчанию) или хэш. В этом случае третий параметр выставить false
 */

    function Authorize($login,$pass,$do_hash=TRUE){
        $pass_hash=FALSE;
        $data=$this->Get_user($login);
        if ($data){
            if($do_hash){
                $pass_hash=$this->GenerateHash($pass,$data['sol']);
            }else{
                $pass_hash=$pass;
            }
            if ($pass_hash != $data['password']) $pass_hash=FALSE;
        }

        return $pass_hash;
    }
/*
 *
 * Создание нового пользователя. Проверка переменных здесь НЕ ПРОИЗВОДИТСЯ!!
 *
 */
    function Create_user($login,$pass,$email,$UID){
        $out['error']=FALSE;
        $out['errort']='';
        if ($this->_Check_unique_email($email)){
            $out['error']=TRUE;
            $out['errort']='Пользователь с таким email уже существует в базе.';
            return $out;
        }
        if (!$this->Get_user($login)){
            $salt=$this->GenerateSalt();
            $pass_md5=$this->GenerateHash($pass,$salt);
            $data = array(
                    'login' => $login ,
                    'password' => $pass_md5 ,
                    'sol' => $salt,
                    'UID'=>$UID,
                    'email'=>$email
                );

            $this->db->insert('auth', $data);
            $user_arr=$this->Get_user($login);
            $user_id=$user_arr['id'];
            $data = array(
                    'id_auth' => $user_id 
                );

            $this->db->insert('user', $data);
            return $out;
        }else{
            $out['error']=TRUE;
            $out['errort']='Пользователь с таким логином уже существует в базе.';
            return $out;
        }
        
        
    }
/*
 *
 * Удаление пользователя по логину
 *
 */
    function Erase_user($login){
        $query=$this->db->get_where('auth',array('login'=>$login),1);
        if ($query->num_rows() > 0){
            $id_1=$query->result_array();
            $id=$id_1[0]['id'];
            $this->db->where('id', $id);
            $this->db->delete('auth');
            $res=TRUE;
        }else{
            $res=FALSE;
        }
        return $res;
    }
/*
 *
 * Получение данных о пользователе по логину
 *
 */
    function Get_user($login){
        $query=$this->db->get_where('auth',array('login'=>$login),1);
        if ($query->num_rows() > 0){
            $row_1=$query->result_array();
            $row=$row_1[0];
            foreach ($row as $key=>$val){
                $res[$key]=$val;
            }
        }else{
            $res=FALSE;
        }
        return $res;
    }

/*
 *
 * Получение данных о пользователе по id
 *
 */
    function Get_user_by_id($user_id){
        $query=$this->db->get_where('auth',array('id'=>$user_id),1);
        if ($query->num_rows() > 0){
            $row_1=$query->result_array();
            $row=$row_1[0];
            foreach ($row as $key=>$val){
                $res[$key]=$val;
            }
        }else{
            $res=FALSE;
        }
        return $res;
    }
/*
 *
 * Редактирование пользователя по логину
 *
 */
    function Edit_user($login,$data){
       $query=$this->db->get_where('auth',array('login'=>$login),1);
        if ($query->num_rows() > 0){
            $id_1=$query->result_array();
            $id=$id_1[0]['id'];
            $this->db->where('id', $id);
            $this->db->update('auth', $data);//$this->db->delete('auth');
            $res=TRUE;
        }else{
            $res=FALSE;
        }
        return $res;
    }
/*
 *
 *Проверка уникальности email
 *
 */
    private function _Check_unique_email($email){
        $this->db->where('email', $email);
        $query = $this->db->get_where('auth', array('email' => $email));
        if ($query->num_rows() > 0){
            return TRUE;  //Найдена запись
        }else{
            return FALSE;   //email уникален
        }
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
