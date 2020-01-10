<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Send_command extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Site_model');
            $this->load->model('admin/Command_library_model');
            $this->load->model('admin/Reader_model');
        }

        public function index()
        {
            $dataArray = array();
            $arr_command = $this->Command_library_model->get_reader_all_command();
            $this->form_validation->set_rules('command', 'Command', 'required|trim');
            $command_arr = '';
            if (!empty($arr_command))
            {
                foreach ($arr_command as $value)
                {
                    $command_arr[$value['command']] = array("name" => $value['command_description']);
                }
            }
            $session_site_id = get_session_site_id();
            $arr_sites = $this->Site_model->get_site_array('name');
            $arr_reader = $this->Reader_model->get_all_reader_data($session_site_id = null);
            $massage = $this->session->flashdata('command_operation_message');
            $dataArray['message'] = $massage;
            $dataArray['reader_command'] = json_encode($command_arr);
            $dataArray['reader_list'] = $arr_reader;
            $dataArray['arr_sites'] = add_blank_option($arr_sites, '-Select all -');
            $dataArray['form_caption'] = "Send Command to Reader";

            $dataArray['local_css'] = array(
                'jquery.contextMenu',
            );
            $dataArray['local_js'] = array(
                'ipformat',
                'jquery.contextMenu',
                'jquery.ui.position',
                'jquery.caret',
            );

            if ($this->form_validation->run() == true)
            {

                $dataValues = array(
                    'command' => $this->input->post('command'),
                    'reader' => $this->input->post('reader'),
                );
                $this->Command_library_model->save_command_for_execute($dataValues);
                $this->session->set_flashdata('command_operation_message', 'Reader Command Save Successfully!');
                redirect('admin/send_command/index');
            }

            $dataArray['form_action'] = current_url();
            $this->load->view('send-command-form', $dataArray);
        }
	
	public function manual_access_control() {
      
        $dataArray = array();
        $arr_command = $this->Command_library_model->get_reader_all_command();
        $this->form_validation->set_rules('reader', 'reader', 'required|trim');
        $command_arr = '';
        if (!empty($arr_command)) {
            foreach ($arr_command as $value) {
                $command_arr[$value['command']] = array("name" => $value['command_description']);
            }
        }
        $session_site_id = get_session_site_id();
        $arr_sites = $this->Site_model->get_site_array('name');
      
        if($this->session->userdata("user_type") == "Site Admin")
        { 
          $site_id = $this->session->userdata('site_id');
          $arr_reader = $this->Reader_model->get_all_reader_data($session_site_id = $site_id);    
        }
        else
        {
            $arr_reader = $this->Reader_model->get_all_reader_data($session_site_id = null);
        }
        $massage = $this->session->flashdata('command_operation_message');
        $dataArray['message'] = $massage;
        $dataArray['reader_command'] = json_encode($command_arr);
        $dataArray['reader_list'] = $arr_reader;
        $dataArray['arr_sites'] = add_blank_option($arr_sites, '-Select all -');
        $dataArray['form_caption'] = "Manual access control";

        $dataArray['local_css'] = array(
            'jquery.contextMenu',
        );
        $dataArray['local_js'] = array(
            'ipformat',
            'jquery.contextMenu',
            'jquery.ui.position',
            'jquery.caret',
        );
        $post_data = $this->input->post();
        if (!empty($post_data)) {
            $dataValues = array(
                'command' => manual_access_control($f = null),
                'reader' => $this->input->post('reader'),
            );
            $this->Command_library_model->manual_access_control_command_for_execute($dataValues);
            $this->session->set_flashdata('command_operation_message', 'Reader Manual Access Control Command Save Successfully!');
            $this->session->set_flashdata('command_sent_reader', $this->input->post('reader'));
            redirect('admin/send_command/manual_access_control');
        }
        $dataArray['form_action'] = current_url();
        $this->load->view('manual-access-control-form', $dataArray);
    }

    }
    