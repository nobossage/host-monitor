<?php
/*
 * monmodel.php
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


class Monmodel extends CI_Model {
    function __construct() {
        
        // Call the Model constructor
        parent::__construct();
            
        // Проверяем наличие таблицы в базе
        $sql='SHOW TABLES LIKE \''.$this->db->dbprefix('hosts').'\'';
        $result = $this->db->query($sql);
        if ($result->num_rows == '0') {
            $this->Create_Table();
        }
         
        // Проверяем наличие таблицы log в базе
        $sql='SHOW TABLES LIKE \''.$this->db->dbprefix('log').'\'';
        $result = $this->db->query($sql);
        if ($result->num_rows == '0') {
            $this->Create_Table_log();
        }
         
    }

/*
 * Создаем таблицу в БД (если ее еще нет - проверяет это конструктор модели)
 * 
 */
    private function Create_Table() {
        
        $this->load->dbforge();
        
        // Добавляем поля
        $this->dbforge->add_field(' `id` int(11) NOT NULL AUTO_INCREMENT COMMENT \'id Хоста\'');
        $this->dbforge->add_field('`host` varchar(254) NOT NULL COMMENT \'хост\'');
        $this->dbforge->add_field('`host_name` varchar(254) DEFAULT NULL COMMENT \'Имя для вывода\'');
        $this->dbforge->add_field('`status_host` set(\'up\',\'down\') NOT NULL DEFAULT \'up\' COMMENT \'Состояние хоста\'');
        $this->dbforge->add_field('`period` int(8) NOT NULL COMMENT \'Период проверки\'');
        $this->dbforge->add_field('`type` set(\'http\',\'ping\') NOT NULL DEFAULT \'http\' COMMENT \'Тип проверки\'');
        $this->dbforge->add_field('`id_auth` int(11) NOT NULL COMMENT \'Владелец хоста\'');
        $this->dbforge->add_field('`create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'Время создания хоста\'');
        $this->dbforge->add_field('`next` timestamp NULL DEFAULT NULL COMMENT \'Следующая проверка\'');
        $this->dbforge->add_field('`share` set(\'1\',\'0\') NOT NULL DEFAULT \'0\' COMMENT \'Общедоступный хост\'');
        // Добавим первичный ключ на id
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('hosts');  // создали таблицу
        
        // Добавляем уникальность пары полей 'hosts'&'id_auth'
        $sql='ALTER TABLE `'.$this->db->dbprefix('hosts').'` ADD UNIQUE `uniq` ( `host` , `id_auth` )';
        $this->db->query($sql);
        
        log_message('debug', "Created Table 'hosts'");

    }


/*
 * Создаем таблицу в БД (если ее еще нет - проверяет это конструктор модели)
 * 
 */
    private function Create_Table_log() {
        
        $this->load->dbforge();
        
        // Добавляем поля
        $this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT COMMENT \'id\'');
        $this->dbforge->add_field('`stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'Дата записи\'');
        $this->dbforge->add_field('`id_host` int(11) NOT NULL COMMENT \'ID Host\'');
        $this->dbforge->add_field('`change_to` set(\'up\',\'down\') COLLATE utf8_unicode_ci NOT NULL COMMENT \'Новое состояние\'');
        $this->dbforge->add_field('`response` varchar(256) NOT NULL COMMENT \'Результат проверки\'');
        // Добавим первичный ключ на id
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('log');  // создали таблицу
        
        log_message('debug', "Created Table 'log'");

    }



/*
 * редактирование записи в таблице hosts
 * 
 * @param array $new_entry
 * @param int $id
 * 
 * @return int/FALSE
 */
    public function Edit_Entry($id, $new_entry) {
        
        $this->db->where('id', $id);
        $this->db->update('hosts', $new_entry);
       
    }

/*
 * Добавить запись в таблицу hosts
 * 
 * @param array $entry
 * 
 * @return int/FALSE
 */
    public function Add_Entry($new) {
        
        if (! $this->_Check_Entry_Host($new['host'], $new['id_auth'])) {
            $this->db->insert('hosts', $new);
            $id_host = $this->db->insert_id();
            $log_arr=array('id_host'=>$id_host,
                            'change_to'=>'up');
            $this->db->insert('log', $log_arr);
            return $id_host;            
        } else {
            return False;
        }
        
        
    }

/*
 * Проверка существования хоста в таблице hosts
 * 
 * @param string $host
 * @param int $id_auth
 * 
 * @return bool
 */
    private function _Check_Entry_Host($host,$id_auth) {
        
        $where=array('host'=>$host, 'id_auth'=>$id_auth);
        $this->db->where($where);
        $this->db->from('hosts');
        $check = $this->db->count_all_results();
        if ($check > 0) return TRUE; //Уже есть такой хост для этого юзера
        else return FALSE;
    }

/*
 * Получить список хостов пользователя
 * 
 * @param int $id_auth
 * @return array
 */
    public function Get_Entrys($id_auth) {
        
        $this->load->model('authmodel');
        $user=$this->authmodel->Get_user_by_id($id_auth);
        if ($user)
            $UID=$user['UID'];
        else
            $UID=10000;
        
        if ($UID > 10) {   //  Если не админ, то все не выводим - фильтруем вывод
            $this->db->where(array('id_auth'=>$id_auth));
            $query = $this->db->get('hosts');
            $user_hosts=$query->result_array();
            
            $this->db->where(array('share'=>'1'));
            $this->db->where('id_auth !=', $id_auth);
        }
        $query = $this->db->get('hosts');
        $share_hosts=$query->result_array();
        foreach ($share_hosts as $row) {
            $user_hosts[]=$row;
        }
        
        return $user_hosts;
    }

/*
 * Получить запись хоста пользователя
 * 
 * @param int $id
 * @return array
 */
    public function Get_Entry($id) {
        
        $query = $this->db->get_where('hosts',array('id'=>$id));
        $out = $query->result_array();
        return $out;
    }

    
/*
 * Удалить host из таблицы
 * 
 * @param int $id_host
 */
    public function Del_Entry($id_host) {
        
        $this->db->delete('hosts', array('id' => $id_host));
        $this->db->delete('log', array('id_host' => $id_host));
    }


//*******************************************************************************



/*
 * Добавить запись в таблицу log
 * 
 * @param array $entry
 * 
 * @return int/FALSE
 */
    public function Add_Entry_log($new) {
        
        $new['stamp']=gmdate('Y-m-d H:i:s', time());
        $this->db->insert('log', $new);
    }


/*
 * Получить записи хостов
 * 
 * @return array
 */
    public function Get_hosts() {
        
        
        $this->db->where('next <',gmdate('Y-m-d H:i:s',gmmktime()));
        $this->db->or_where('next IS NULL');
        $query = $this->db->get('hosts');
        return $query->result_array();
    }

/*
 * Установим время следущей проверки хоста
 * @param int $id_host
 * @param int $period
 * 
 */
    public function Set_Next($id_host, $period) {
        
        $next_time=gmdate('Y-m-d H:i:s',gmmktime()+$period*60-30);
        $this->db->where('id',$id_host);
        $this->db->update('hosts',array('next'=>$next_time));
    }

/*
 * Выбрать лог хоста
 * 
 * @param int $id_host
 * @param int $start_time
 * 
 * @return $out
 */
    public function Get_Log($id_host, $start_time='0000-00-00 00:00:00') {
        
        $create_time=$this->Get_create_time($id_host);
        if ($start_time < $create_time)  $start_time=$create_time;
        
        $this->db->where('id_host',$id_host);
        $this->db->where('stamp >',$start_time);
        $this->db->from('log');
        $count=$this->db->count_all_results();
        
        $this->db->where('id_host',$id_host);
        $this->db->order_by('id', 'desc');
        $this->db->limit($count+1);
        $query = $this->db->get('log');
        $out = $query->result_array();
        $out=array_reverse($out);
        $out[0]['stamp']=$start_time;
        return $out;
        
    }


/*
 * Выбрать лог хоста
 * 
 * @param int $id_host
 * @param int $start_time
 * 
 * @return $out
 */
    public function Get_Log_host($id_host) {
        
      
        $this->db->where('id_host',$id_host);
        $this->db->order_by('id', 'desc');
        //$this->db->limit($count+1);
        $query = $this->db->get('log');
        $out = $query->result_array();
        //$out=array_reverse($out);
        return $out;
        
    }
/*
 * Выбрать последние события
 * 
 * @param int $col
 * 
 * @return array 
 */
    public function get_last($col=5, $id_auth=0) {
      
        $this->db->select('*');
        $this->db->from('log');
        $this->db->join('hosts', 'log.id_host = hosts.id');
        if ($id_auth!=1) {          //Если админ, то выводим все записи 
            if ($id_auth != 0)
                $this->db->where('id_auth',$id_auth);
            $this->db->or_where('share',1);
        }
        $this->db->order_by('stamp', 'desc');
        $this->db->limit($col);

        $query = $this->db->get();
        $out = $query->result_array();

        return $out;
        
    }

/*
 * Получить дату-время создания задания мониторинга хоста
 * 
 * @param int $id_host
 * 
 * @return string
 */
    public function Get_create_time($id_host) {
        
        $query = $this->db->get_where('hosts',array('id'=>$id_host));
        $out = $query->result_array();
        return $out[0]['create_date'];

    }


}

/* End of file profilemodel.php */
/* Location: ./application/model/profilemodel.php */
