<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Configuration extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Configuration_model');
        }

        public function index()
        {

            $this->form_validation->set_rules('auto_refresh_time', 'Auto Refresh Time', 'required|trim');
            $this->form_validation->set_rules('offline_timeout', 'Reader Offline Timeout', 'required|trim');
            $this->form_validation->set_rules('command_max_capacity', 'Max Capacity of Commands in KB', 'required|trim');
            $this->form_validation->set_rules('command_max_number', 'Max Number of Commands', 'required|trim');
            $this->form_validation->set_rules('sms_provider', 'Select SMS Provider', 'required|trim');


            $dataArray = array();
            $sms_provider = get_custom_config_item('sms_provider');
            $dataArray['sms_provider'] = add_blank_option($sms_provider, "-Select SMS Provider-");
            
            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Configuration Settings";
                $dataArray['form_action'] = current_url();
                $message = $this->session->flashdata('configuration_operation_message');
                $dataArray['message'] = $message;
                $config_data = $this->Configuration_model->get_web_config();

                $dataArray['auto_refresh_time'] = $config_data['auto_refresh_time'];
                $dataArray['offline_timeout'] = $config_data['offline_timeout'];
                $dataArray['command_max_capacity'] = $config_data['command_max_capacity'];
                $dataArray['command_max_number'] = $config_data['command_max_number'];
                $dataArray['sms_provider_id'] = $config_data['sms_provider'];
                $dataArray['config_id'] = $config_data['id'];

                $this->load->view('configuration-form', $dataArray);
            }
            else
            {
                $config_id = $this->input->post('config_id');
                $dataValues = array(
                    'auto_refresh_time' => $this->input->post('auto_refresh_time'),
                    'offline_timeout' => $this->input->post('offline_timeout'),
                    'command_max_capacity' => $this->input->post('command_max_capacity'),
                    'command_max_number' => $this->input->post('command_max_number'),
                    'sms_provider' => $this->input->post('sms_provider')
                );

                if ($config_id != "")
                {
                    $dataValues['id'] = $config_id;
                }


                $this->Configuration_model->save_web_config($dataValues);
                $this->session->set_flashdata('configuration_operation_message', 'Configuration Settings Updated successfully.');
                redirect('admin/configuration/index');
            }
        }

        public function time_duration()
        {
            $this->form_validation->set_rules('timed_access_duration_time', 'Max Capacity of Commands in KB', 'required|trim');
            $this->form_validation->set_rules('one_time_access_duration_time', 'Max Number of Commands', 'required|trim');

            $dataArray = array();
            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Time Duration Settings";
                $dataArray['form_action'] = current_url();
                $message = $this->session->flashdata('configuration_operation_message');
                $dataArray['message'] = $message;
                $config_data = $this->Configuration_model->get_web_config();


                $dataArray['timed_access_duration_time'] = $config_data['timed_access_duration'];
                $dataArray['one_time_access_duration_time'] = $config_data['one_time_access_duration'];
                $dataArray['config_id'] = $config_data['id'];
                $time_array = get_custom_config_item('timed_access_duration_time_array');
                $dataArray['timed_access_duration_time_array'] = add_blank_option($time_array, "-Timed Access Duration (in Days.)-");
                $this->load->view('time-duration-form', $dataArray);
            }
            else
            {
                $config_id = $this->input->post('config_id');
                $dataValues = array(
                    'timed_access_duration' => $this->input->post('timed_access_duration_time'),
                    'one_time_access_duration' => $this->input->post('one_time_access_duration_time')
                );

                if ($config_id != "")
                {
                    $dataValues['id'] = $config_id;
                }


                $this->Configuration_model->save_web_config($dataValues);
                $this->session->set_flashdata('configuration_operation_message', 'Time Duration Settings Updated successfully.');
                redirect('admin/configuration/time_duration');
            }
        }

    }
    
