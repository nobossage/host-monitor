<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller {

	/**
	 * Редактирование профиля
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
	public function index()
    {
        //$this->output->cache(60);
        $user_id=$this->session->userdata('user_id');           //проверяем авторизацию
        if( ! $user_id)
        {
            $this->output->set_header('Location: auth');
        }
        else
        {
            $this->load->model('profilemodel');
            $this->load->model('authmodel');
            $profile=$this->profilemodel->Get_profile(array('id_auth'=>$user_id));
            $user_arr=$this->authmodel->Get_user_by_id($user_id);
            $profile['email']=$user_arr['email'];
            $profile['user_id'] = $user_id;
            $this->load->view('profileview',$profile);
                //$this->load->model('authmodel');
                //$login=$this->input->post('username');
                //$pass=$this->input->post('password');
                //$email=$this->input->post('email');
//                $this->Change_pass();
                //$this->load->view('authorization/regsuccess',$this->out);
  //          }


            /*echo '<pre> Проф_'.$user_id;
            print_r($profile);
            echo '</pre>';*/


        }
	}
/*
 *
 *
 * Редактирование профиля
 *
 */
    function Change_profile()
    {
        $user_id=$this->session->userdata('user_id');           //проверяем авторизацию
        if( ! $user_id)
        {
            $this->output->set_header('Location:'.$this->config->item('base_url'));
        }
        else
        {
            $this->load->model('profilemodel');
            $where[0]='id_auth';
            $where[1]=$user_id;
            $data['family']=$this->input->post('family');
            $data['name']=$this->input->post('name');
            $edit=$this->profilemodel->Edit_profile($data,$where);
            /*if ($edit) {
                echo '<pre>';
                print_r($where);

                print_r($data);
                echo '</pre>';
            }*/
            $profile=$this->profilemodel->Get_profile(array('id_auth'=>$user_id));
            $this->output->set_header('Location: profile');
        }

    }
 
/*
 *
 *
 * Редактирование профиля
 *
 */
    function Change_pass()
    {
        $user_id=$this->session->userdata('user_id');           //проверяем авторизацию
        if( ! $user_id)
        {
            $this->output->set_header('Location:'.$this->config->item('base_url'));
        }
        else
        {
            $this->load->model('authmodel');
            $where[0]='id';
            $where[1]=$user_id;
            $new_email=$this->input->post('email');
            $cur_pass=$this->input->post('cur_pass');
            $new_pass=$this->input->post('new_pass');
            if($cur_pass)
            {
                $user_arr=$this->authmodel->Get_user_by_id($user_id);
                $hash=$user_arr['password'];
                $salt=$user_arr['sol'];
                $new_hash=md5(md5($cur_pass) . $salt);
                if($new_hash != $hash)
                {
                    $data['errort']='Неверно указан текущий пароль';
                    
                    exit;
                }
                    
                $data['email']=$new_email;
                if($new_pass)
                {
                    $data['password']=md5(md5($new_pass) . $salt);
                }
                $login=$user_arr['login'];
                $edit=$this->authmodel->Edit_user($login,$data);
            
                /*echo '<pre>';
                print_r($login);

                print_r($data);
                echo '</pre>';*/
            
                //$profile=$this->profilemodel->Get_profile(array('id_auth'=>$user_id));
                $this->output->set_header('Location: profile');
               //$this->load->view('blogview');
            }else{
                $out['error']=TRUE;
                $out['errort']='Не введен текущий пароль';
                $this->load->view('authorization/dosuccess',$out);
            }
        }

    }

 
}

/* End of file profile.php */
/* Location: ./application/controllers/profile.php */
