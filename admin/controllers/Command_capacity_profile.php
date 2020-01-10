<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Command_capacity_profile extends MY_Controller
    {

        private $_command_capacity_profile_listing_headers = 'command_capacity_profile_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Command_capacity_profile_model');
        }

        public function add_command_capacity_profile($profile_id = null)
        {
            $this->form_validation->set_rules('profile_name', 'Profile Name', 'required|trim|unique[reader_command_capacity_profile.profile_name.profile_id.' . $this->input->post('profile_id') . ']');
            $this->form_validation->set_rules('command_max_capacity', 'Max Capacity of Commands in KB', 'required|trim');
            $this->form_validation->set_rules('command_max_number', 'Max Number of Commands', 'required|trim');
            $dataArray = array();

            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Profile";
                $dataArray['form_action'] = current_url();
                if (!empty($profile_id))
                {
                    $dataArray['form_caption'] = 'Edit Profile';
                    $reader_data = $this->Command_capacity_profile_model->get_profile_by_id($profile_id);

                    $dataArray['profile_name'] = $reader_data['profile_name'];
                    $dataArray['command_max_capacity'] = $reader_data['command_max_capacity'];
                    $dataArray['command_max_number'] = $reader_data['command_max_number'];
                    $dataArray['profile_id'] = $profile_id;
                }
                $this->load->view('command-capacity-profile-form', $dataArray);
            }
            else
            {
                $profile_id = $this->input->post('profile_id');

                $dataValues = array(
                    'profile_name' => $this->input->post('profile_name'),
                    'command_max_capacity' => $this->input->post('command_max_capacity'),
                    'command_max_number' => $this->input->post('command_max_number'),
                );

                if (!empty($profile_id))
                {
                    $dataValues['profile_id'] = $profile_id;
                }

                $this->Command_capacity_profile_model->save_command_capacity_profile($dataValues);
                $this->session->set_flashdata('reader_operation_message', 'Profile Saved Successfully.');
                redirect('admin/command_capacity_profile/list_command_capacity_profile');
            }
        }

        public function list_command_capacity_profile_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_command_capacity_profile_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Command_capacity_profile_model->get_all_profiles($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_command_capacity_profile_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_command_capacity_profile()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('reader_operation_message');
            $table_config = array(
                'source' => site_url('admin/command_capacity_profile/list_command_capacity_profile_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_command_capacity_profile_listing_headers, $table_config),
                'message' => $message
            );

            $dataArray['local_css'] = array(
                'dataTables.bootstrap',
                'responsive.bootstrap',
                'buttons.bootstrap',
                'select.bootstrap',
            );

            $dataArray['local_js'] = array(
                'dataTables',
                'dataTables.FilterOnReturn',
                'dataTables.bootstrap',
                'dataTables.responsive',
                'responsive.bootstrap',
                'dataTables.buttons',
                'buttons.bootstrap',
                'buttons.html5',
                'buttons.flash',
                'buttons.print',
            );

            $dataArray['table_heading'] = 'Command Capacity Profile List';
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_caption'] = "Add Command Capacity Profile";
            $this->load->view('command-capacity-profile-list', $dataArray);
        }

        function delete_command_capacity_profile($profile_id)
        {
            $status = $this->Command_capacity_profile_model->delete_profile_by_id($profile_id);
            if ($status == true)
            {
                $this->session->set_flashdata('reader_operation_message', 'Profile deleted successfully');
                redirect('admin/command_capacity_profile/list_command_capacity_profile');
            }
            else
            {
                show_error('The Profile Details you are trying to delete does not exist.');
            }
        }

    }
    