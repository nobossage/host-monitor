<?php
/*
 * profilemodel.php
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


class Profilemodel extends CI_Model
{
    function __construct()
     {
         // Call the Model constructor
         parent::__construct();
     }

/*
 *
 *Добавление пользователя. На выходе - FALSE|user_id
 * 
 *$profile - array('field'=>'var')
 *
 */
    function Add_profile($profile)
    {
        $out=$this->db->insert('user', $profile);
        if($out)
        {
            $out=$profile['user_id'];
        }
        return $out;
    }
    
        
/*
 *
 *удаление пользователя. На выходе - FALSE|TRUE
 * 
 *$user_id - int
 *
 */
    function Delete_profile($user_id)
    {
        $out=$this->db->delete('user', array('user_id' => $user_id));
         return $out;
    }
    


/*
 *
 *Изменение пользователя. На выходе - FALSE|TRUE
 * 
 *$data - array ('name_col'=>'value') 
 * $Where - array('name_col'=>'value') 
 *
 */
    function Edit_profile($data,$where)
    {
        $this->db->where($where[0], $where[1]);
        $out=$this->db->update('user', $data);
        return $out;
    }

/*
 *
 *получить данные пользователя. На выходе - FALSE|array
 * 
 *
 * $Where - array('name_col'=>'value') 
 *
 */
    function Get_profile($where)
    {
        $out=FALSE;
        $query=$this->db->get_where('user',$where);
        if ($query->num_rows() > 0){
            $row_1=$query->result_array();
            $row=$row_1[0];
            foreach ($row as $key=>$val)
            {
                $out[$key]=$val;
            }
        }
        return $out;
    }
 
}
/* End of file profilemodel.php */
/* Location: ./application/model/profilemodel.php */
