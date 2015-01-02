<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
        if(!$user_id){
            $this->output->set_header('Location: auth');
        }else{        
           // $this->load->view('profileview');

            $this->load->view('welcome_message');
        }
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
