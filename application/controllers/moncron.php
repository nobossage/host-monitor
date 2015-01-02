<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Moncron extends CI_Controller {

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
        
        $this->load->model('Monmodel');
        $work = $this->Monmodel->Get_hosts();
        foreach ($work as $row) {
            $this->Check($row);
        }
    }
    
    /*
     * Проверка хоста
     * 
     * @param array @row
     */
     private function Check($row) {
         
         $retry='';
         switch ($row['type']) {
             
             case 'http':
                // инициализация сеанса
                $ch = curl_init();
                // установка URL и других необходимых параметров
                curl_setopt($ch, CURLOPT_URL, 'http://'.$row['host']);
                
                //curl_setopt($ch, CURLOPT_HEADER, 0); //Без заголовков
                curl_setopt($ch, CURLOPT_NOBODY, 1);  // Нужны только заголовки
                // загрузка страницы и выдача её браузеру
                curl_exec($ch);
                $retry = curl_getinfo ( $ch,CURLINFO_HTTP_CODE);
                // завершение сеанса и освобождение ресурсов
                curl_close($ch);
               // Если retry=200 то хост онлайн
                ($retry==200)? $online=TRUE:$online=FALSE;
                break;
            case 'ping':
                
                $retry=0;
                system("ping -c 1 ".$row['host'],$retry);
                // Если Rest=0 значит хост онлайн
                ($retry==0)? $online=TRUE:$online=FALSE;
                ($online)? $retry='Ответ на пинг получен':$retry='Нет ответа на пинг';
                
                break;             
         }
         

         if (($online && ($row['status_host']=='down')) OR ($row['next']=='')) {
             //Хост поднялся
            $this->db->where('id', $row['id']);
            $this->db->update('hosts', array('status_host'=>'up'));
         
            //Логгируем
            $new=array('id_host'    => $row['id'],
                        'change_to' => 'up',
                        'response'  => strval($retry)
                        );
                        
            $this->Monmodel->Add_Entry_log($new);
         }
        
         if (! $online && ($row['status_host']=='up')) {
             //Хост в дауне
            $this->db->where('id', $row['id']);
            $this->db->update('hosts', array('status_host'=>'down'));
         
            //Логгируем
            $new=array('id_host'    => $row['id'],
                        'change_to' => 'down',
                        'response'  => strval($retry)
                        );
                        
            $this->Monmodel->Add_Entry_log($new);
         }
            
        $this->Monmodel->Set_Next($row['id'],$row['period']);
     }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
