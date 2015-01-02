<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monitor extends CI_Controller {

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
        //$this->output->cache(60);
        if($this->input->post('exit')){
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('UID');
                        $cookie = array(                            //Если вышел - значит забудем логин и пароль, которые
                            'name'   => 'login',                    //навсегда запоминали
                            'value'  => '',
                        );
            $this->input->set_cookie('login','');
                        $cookie = array(
                            'name'   => 'pass',
                            'value'  => '',
                        );
            $this->input->set_cookie('pass','');
        }
        $user_id=$this->session->userdata('user_id');           //проверяем авторизацию
        $UID = $this->session->userdata('UID');
        if(!$user_id){
            $user_id=0;
            $UID=10000;
            //$this->output->set_header('Location: auth');
            $new='';
            $this->load->model('Monmodel');
            $data['new']=$new;
            $data['table'] = $this->Monmodel->Get_Entrys($user_id);
            foreach ($data['table'] as $key=>$row) {
                $uptime=$this->Get_stat($row['id']);
                $data['table'][$key]['uptime']= ($uptime['up'] / ($uptime['up'] + $uptime['down']));
                $data['table'][$key]['line']= $uptime['line'] ;
            }


            
        }else{        
            $data['error_add']='';
            $new='';
            $this->load->model('Monmodel');
            $this->load->helper('form');
            $this->load->library('form_validation');
            //Set-rules
            $this->form_validation->set_rules('period', 'Period', 'trim|required|integer|callback_check_period|xss_clean');
            $this->form_validation->set_rules('host', 'Host', 'trim|required|xss_clean');
            $this->form_validation->set_rules('type', 'Тип', 'required|xss_clean');
                
            if ($this->input->post('add_host')){
                $new['host']=$this->cut_protocol($this->input->post('host'));
                $new['host_name']=$this->cut_protocol($this->input->post('host_name'));
                $new['type']=$this->input->post('type');
                $new['period']=$this->input->post('period');
                $new['create_date']=gmdate('Y-m-d H:i:s',gmmktime());
                $new['share']=$this->input->post('share');
                $new['id_auth']=$user_id;
                
                if ($this->form_validation->run()) {
                   
                    $result = $this->Monmodel->Add_Entry($new);
                    if (! $result) $data['error_add']='<span style="color:red">Данная запись уже есть среди наблюдаемых.</span>';
                    else $data['error_add']='<span style="color:green">Хост '.$new['host'].' успешно поставлен на наблюдение с периодичностью '.$new['period'].' мин.</span>';
                }
                                
            }
            $data['new']=$new;
            /*echo '<pre>';
            print_r($new);
            echo '</pre>';//*/
            
            $data['table'] = $this->Monmodel->Get_Entrys($user_id);
            foreach ($data['table'] as $key=>$row) {
                $uptime=$this->Get_stat($row['id']);
                $data['table'][$key]['uptime']= ($uptime['up'] / ($uptime['up'] + $uptime['down']));
                $data['table'][$key]['line']= $uptime['line'] ;
            }

            //$this->load->view('monitor_view',$data);

        }
        $data['stat']=$this->Monmodel->get_last(10, $user_id);
        
        $data['user_id']=$user_id;
        $data['UID']=$UID;
        $this->load->view('monitor_view',$data);

	}
    
/*
 * Редактирование записи о host
 * 
 *  @param int $id
 */
 	public function edit($id){
        //$this->output->cache(60);
        if($this->input->post('exit')){
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('UID');
                        $cookie = array(                            //Если вышел - значит забудем логин и пароль, которые
                            'name'   => 'login',                    //навсегда запоминали
                            'value'  => '',
                        );
            $this->input->set_cookie('login','');
                        $cookie = array(
                            'name'   => 'pass',
                            'value'  => '',
                        );
            $this->input->set_cookie('pass','');
        }
        $user_id=$this->session->userdata('user_id');           //проверяем авторизацию
        if(!$user_id){
            $this->output->set_header('Location: auth');
        }else{        
            $data['error_add']='';
            $new='';
            $this->load->model('Monmodel');
            $this->load->helper('form');
            $this->load->library('form_validation');
            //Set-rules
            $entry=$this->Monmodel->Get_Entry($id);
            ($entry)? $data['old'] = $entry[0]: $data['old']= False;
            $this->form_validation->set_rules('period', 'Period', 'trim|required|integer|callback_check_period|xss_clean');
            $this->form_validation->set_rules('host', 'Host', 'trim|required|xss_clean');
            $this->form_validation->set_rules('host_name', 'Host_name', 'trim|xss_clean');
            $this->form_validation->set_rules('type', 'Тип', 'required|xss_clean');
                
            if ($this->input->post('delete')){
                $this->Monmodel->Del_Entry($id);
                $this->output->set_header('Location: '.$this->config->item('base_url').'monitor');
                

                
            }
            
            if ($this->input->post('edit_host')){
                $new['host']=$this->cut_protocol($this->input->post('host'));
                $new['host_name']=$this->cut_protocol($this->input->post('host_name'));
                $new['type']=$this->input->post('type');
                $new['period']=$this->input->post('period');
                $new['share']=$this->input->post('share');
                if ($new['share'] != 1) $new['share']='0';
                $new['id_auth']=$user_id;
                
                if ($this->form_validation->run()) {
                   
                    $result = $this->Monmodel->Edit_Entry($id, $new);
                    /*if (! $result) $data['error_add']='<span style="color:red">Данная запись уже есть среди наблюдаемых.</span>';
                    else $data['error_add']='<span style="color:green">Хост '.$new['host'].' успешно поставлен на наблюдение с периодичностью '.$new['period'].' мин.</span>';
                //*/
                    $this->output->set_header('Location: '.$this->config->item('base_url').'monitor');
                }
                                
            }
            $data['new']=$new;
            $data['table'] = $this->Monmodel->Get_Entrys($user_id);
            foreach ($data['table'] as $key=>$row) {
                $uptime=$this->Get_stat($row['id']);
                $data['table'][$key]['uptime']= ($uptime['up'] / ($uptime['up'] + $uptime['down']));
                
            }
            $data['user_id'] = $user_id;
            $this->load->view('monitor_edit_view',$data);

        }
	}
    
/*
 * Проверка данных формы - период мониторинга
 * 
 * @param int $period
 * @return bool
 */
    public function check_period($period) {
        if ((4 < $period) AND ($period < 1441)) return TRUE;
        $this->form_validation->set_message('check_period', 'Период мониторинга должен быть между 5 и 1440 мин.');
        return FALSE;
    }

/*
 * Отрезаем сведения о протоколе. если они есть.
 * 
 * @param string $host
 * @return string
 */
    private function cut_protocol($host){
        
        $arr=explode('://', $host);
        if (count($arr)==1)  $out=$arr[0];
        if (count($arr)==2)  $out=$arr[1];
        if (substr($out,-1) == '/') $out=substr($out, 0, strlen($out)-1);  //Если что, отрежем задний слеш
        return $out;
    }
    
/*
 * Функция Аякс
 * 
 */
/*    public function ajax() {
        
        $this->load->model('Monmodel');
        $id=$this->input->post('id');
        $operation=$this->input->post('operation');
        $str=$this->input->post('str');
                //echo 'Good!  '.$id;

        switch($operation)
        {
            case 'delete_host':
                //$this->Monmodel->Del_Entry($id);
            break;

            default:
                echo '<h2>'.$operation.'</h2>';
            break;
        }
        
    }
    
/*
 * Считаем процент онлайна
 * 
 * @param int $id_host
 * @param int $start_time
 * 
 * @return double
 */
    public function Get_stat($id_host, $str_time='0000-00-00 00:00:00') {
        
        if ($str_time=='0000-00-00 00:00:00') $str_time = gmdate('Y-m-d H:i:s', time()-60*60*24*7); //7 дней

        if ($str_time < $this->Monmodel->Get_create_time($id_host)) $str_time = $this->Monmodel->Get_create_time($id_host);  // Если старт статистики раньше создания хоста
        $up=0;
        $down=0;
        
        $array = $this->Monmodel->Get_log($id_host, $str_time);
        $s_time=strtotime($str_time);
        
        $end_time = mktime(gmdate('H'),gmdate('i'),gmdate('s'),gmdate('m'),gmdate('d'),gmdate('Y'));//mktime(gmdate('H'));
        if ($end_time - $s_time < 100) {
            return array (  'up'=>1,
                            'down'=>0,
                            'line'=>'100u');  // если узел только добавили, то какая статистика?
        }
        $all_time_img = $end_time - $s_time; //Это будет вся длина (100%) для рисунка
        $line='';
        
        $counter = count($array) - 1;
        for ($i = $counter;  $i >= 0; $i--) {
            $row = $array[$i];
            $start_time = strtotime($row['stamp']);
            $cur_time = $end_time - $start_time;
            $end_time = $start_time;
            switch ($row['change_to']) {
                case 'up':
                    $up = $up + $cur_time;
                    $line .= round(100*$cur_time/$all_time_img).'u_';
                    break;
                case 'down':
                    $down = $down + $cur_time;
                    $line .= round(100*$cur_time/$all_time_img).'d_';
                    break;
            }
        }
        //Считаем от 0 записи до стартового периода.
        $cur_time = $end_time - $s_time;
        switch ($row['change_to']) {
            case 'up':
                $up = $up + $cur_time;
                $line .= round(100*$cur_time/$all_time_img).'u_';
                break;
            case 'down':
                $down = $down + $cur_time;
                $line .= round(100*$cur_time/$all_time_img).'d_';
                break;
        }
        $line = substr($line,0,strlen($line)-1);
        $out['up']=$up;
        $out['down']=$down;
        $out['line']=$line;
        return $out;
    }


/*
 * рисуем график
 * 
 * @param string $line
 * 
 * @return рисунок
 */
    public function Image($id_host) {
        
        $this->load->model('Monmodel');
        $row=$this->Get_stat($id_host, gmdate('Y-m-d H:i:s',time()-60*60*24));
        $line=$row['line'];
        $pattern='/(\d?\d)(u|d)/';
        $img_w = 200;  //Ширина рисунка
        $img_h = 10;   //Высота рисунка

        $gr_red = explode('_',$line);
        foreach ($gr_red as $sect) {
            preg_match($pattern, $sect, $test);
            $light[] = $test;
        }
        
        header ("Content-type: image/png");  
        $im = ImageCreate ($img_w, $img_h)  
                or die ("Ошибка при создании изображения");          

        $color_green = ImageColorAllocate ($im, 0, 255, 0);  //Фон - зеленый
        $color_red = ImageColorAllocate ($im, 255, 0, 0); // - это красный
        
        ImageSetThickness($im, round($img_w/100)); //толщина линии
        // рисуем красным - моменты down
        $x=0;
        
        foreach ($light as $pen) {
            //if ($pen[1]==0) continue;
            ($pen[2]=='d')? $color=$color_red: $color=$color_green;
            for ($i = $x; $i < $x + $pen[1]; $i++) {
                ImageLine ($im, $i*$img_w/100, 0, $i*$img_w/100, $img_h, $color);
            }
            $x = $x + $pen[1];
            
        }//*/
        ImagePng ($im);  
    }
/*
 * Получить логи мониторинга
 * 
 * @param int $id_host
 * @return array
 */
    public function get_logs ($id_host) {
        
        $arr = $this->Monmodel->Get_Log($id_host);
        $arr['response']=$this->_get_description_err($arr['response']);
        return $arr;
    }

                
                
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
