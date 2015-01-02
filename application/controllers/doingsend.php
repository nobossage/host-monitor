<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Doingsend extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index(){
         $id=$this->input->post('id');
        $operation=$this->input->post('operation');
        $str=$this->input->post('str');
                //echo 'Good!  '.$id;

        switch($operation)
        {
            case 'get_log_host':
                $this->get_log_host($id, $str);
            break;
            case 'get_last_stat':
                $this->_get_last_log($id,$str);
            break;
            default:
                echo '<h2>'.$operation.'</h2>';
            break;
        }
    }
    
/*
 *Получить лог мониторинга хоста
 * 
 * @param int $id_host
 *
 * @return string
 */
    function get_log_host($id_host, $str = 1) {

        $cur_page = $str;     // Текущая страница
        $per_page = 10;     // Записей на страницу
        
        $this->load->model('Monmodel');
        $host = $this->Monmodel->Get_Entry($id_host);
        $array=$this->Monmodel->Get_Log_host($id_host);  //Получим логи хоста

        $total_rows = count($array);    // Всего записей
        $col_pages = ceil($total_rows/$per_page); // Количество страниц
        $all_log=array_chunk($array, $per_page);
        $arr = $all_log[($cur_page - 1)];
        
        for ($i=count($arr)-1; $i > 0; $i--) {
            $arr[$i]['duration'] = $this -> _get_duration(strtotime($arr[$i-1]['stamp']) - strtotime($arr[$i]['stamp']));
        }
        $arr[0]['duration'] = $this -> _get_duration(gmmktime()-strtotime($arr[0]['stamp'])-date('Z'));
        $table='<p>Лог наблюдения за хостом <b>'.$host[0]['host_name'].'</b></p>
        <table>
            <tr>
                <th>Состояние</th>
                <th style="width:30ex;">Ответ сервера</th>
                <th>Время</th>
                <th>Длит.</th>
            </tr>';
        foreach ($arr as $row) {
            ($row['change_to']=='up')?$color_text='green':$color_text='red';
            if (preg_match('/[0-9]{3}/',$row['response']) == 1)  
                $row['response'] = $this->_get_description_err($row['response']); 
            $table .= '
            <tr style="color:'.$color_text.'">
                <td><b>'.$row['change_to'].'</b></td>
                <td>'.$row['response'].'</td>
                <td>'.$row['stamp'].'</td>
                <td>'.$row['duration'].'</td>
            </tr>';
        }
        $table .='</table> <br /> '.$this->_paginat($cur_page, $col_pages, $id_host).'
        <p><input type="button" value="Убрать статистику" onclick="close_stat();" /></p>';
        echo $table;
        
        /*echo '<pre>';
        print_r($all_log);
        echo '</pre>';//*/
        
    }

/*
 * 
 * Получаем последние записи логов
 * 
 * @param int $user_id, $col
 * 
 */
    private function _get_last_log($user_id,$col) {
        
        $this->load->model('Monmodel');
        $stat = $this->Monmodel->get_last($col, $user_id);
        
        $out = '<h4>Последние события</h4>';

                foreach ($stat as $last) {
                    ($last['change_to'] == 'up')? $color='green':$color='red';
                    $out.= '<p style="color:'.$color.'">'.date('Y-m-d H:i:s',strtotime($last['stamp'])+date('Z')). ' узел '.$last['host_name']. ' перешел в состояние <b >'. $last['change_to'].'</b></p>';
                }
                    echo $out;
                  /*  echo '<pre>';
                    print_r($stat);
                    echo '</pre>';//*/
                    
                    
    }
/*
 * считаем количество часов-минут-секунд
 * 
 * @param int $col_sec
 * @return string
 */
    private function _get_duration($col_sec) {
        
        $s= sprintf('%02d',$col_sec%60); // Количество секунд
        $i= sprintf('%02d',round($col_sec/60)%60,-2); //Количество минут
        $H= round ($col_sec/3600); // Кол-во часов
        return $H.':'.$i.':'.$s;
    }
/*
 * Функция пагинации.
 * 
 * @param int $cur_page
 * @param int $col_pages
 * @return string
 */
    private function _paginat($cur_page,$col_pages,$id_host) {

        if ($col_pages == 1) return '';
        
        $link = '';
        $StartStr=' <<....';
        $EndStr='....>> ';
        // Начальный и конечный номер кнопок
        $Start=$cur_page-3;
        if ($Start < 1)  {
            $StartStr='';
            $Start=1;
        }
        $End=$cur_page+3;
        if ($End > $col_pages){
            $EndStr='';
            $End=$col_pages;
        }
      //  $ajax_do = 'onclick="send_ajax('.$row['id'].', \'get_log_host\','..')"';
        //
        if ($cur_page==1)// Первая страница
                $link = "<b>&nbsp; 1 &nbsp; </b>"; //Если активна
         else 
                $link =  '<input type="submit" name="CurPage" value="1" onclick="send_ajax('.$id_host.', \'get_log_host\',\'1\')">'.$StartStr.'&nbsp;'; //Если показываем не первую страницу

        for ($x=1; $x++<($col_pages-1);) {
        //Выводим кнопки
        
            if (($x>=$Start)AND($x<=$End)) {
                if ($x==$cur_page) 
                    $link .=  "<b>&nbsp;$x &nbsp;</b>"; 
                else 
                    $link .=  '<input type="submit" name="CurPage" value="'.$x.'" onclick="send_ajax('.$id_host.', \'get_log_host\',\''.$x.'\')">&nbsp;';
            }
        }

        if ($Start<>$End)  //Будем выводить, если страница не одна.
        {
            if ($cur_page==$col_pages) 
                $link .=  "<b>&nbsp;$col_pages &nbsp;</b>"; 
            else 
                $link .=  $EndStr.'&nbsp;<input type="submit" name="CurPage" value="'.$col_pages.'" onclick="send_ajax('.$id_host.', \'get_log_host\',\''.$col_pages.'\')">';// Последняя страница
        }
        return $link;
    }
    


/*
 * Получить описание ошибки
 * 
 * @param string $err_serv
 * 
 * @return string
 */
 
    private function _get_description_err ($err_srv) {
        switch ($err_srv) {
            case '100':
                $ret='Continue';
                break;
            case '101':
                $ret='Switching Protocols';
                break;
            case '200':
                $ret='OK';
                break;
            case '201':
                $ret='Created';
                break;
            case '202':
                $ret='Accepted';
                break;
            case '203':
                $ret='Non-Authoritative Information';
                break;
            case '204':
                $ret='No content';
                break;
            case '300':
                $ret='Multiple Choices';
                break;
            case '301':
                $ret='Moved Permanently';
                break;
            case '302':
                $ret='Moved Temporarily';
                break;
            case '304':
                $ret='Not Modified';
                break;
            case '400':
                $ret='Bad Request';
                break;
            case '401':
                $ret='Unauthorized';
                break;
            case '403':
                $ret='Forbidden';
                break;
            case '404':
                $ret='Not Found';
                break;
            case '500':
                $ret='Internal Server Error';
                break;
            case '502':
                $ret=' Bad Gateway';
                break;
            case '503':
                $ret='Service Unavailable';
                break;
            default:
                $ret = $err_srv;
                break;
        }
        return $ret;
    }

}

/* End of file doingsend.php */
/* Location: ./application/controllers/doingsend.php */
