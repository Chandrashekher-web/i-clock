<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Command_library extends MY_Controller
    {

        private $_command_library_listing_headers = 'command_library_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Command_library_model');
        }

        public function add_command($command_id = null)
        {
            $this->form_validation->set_rules('command', 'Command', 'required|trim|unique[reader_command_library.command.command_id.' . $this->input->post('command_id') . ']');
//            $this->form_validation->set_rules('command', 'Command', 'required|trim');
            $this->form_validation->set_rules('command_description', 'Command Description', 'required|trim');
            $dataArray = array();

            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Command";
                $dataArray['form_action'] = current_url();
                if (!empty($command_id))
                {
                    $dataArray['form_caption'] = 'Edit Command';
                    $reader_data = $this->Command_library_model->get_command_by_id($command_id);

                    $dataArray['command'] = $reader_data['command'];
                    $dataArray['command_description'] = $reader_data['command_description'];
                    $dataArray['command_id'] = $command_id;
                }
                $this->load->view('reader-command-form', $dataArray);
            }
            else
            {
                $command_id = $this->input->post('command_id');

                $dataValues = array(
                    'command' => $this->input->post('command'),
                    'command_description' => $this->input->post('command_description'),
                );

                if (!empty($command_id))
                {
                    $dataValues['command_id'] = $command_id;
                }

                $this->Command_library_model->save_command_library($dataValues);
                $this->session->set_flashdata('reader_operation_message', 'Command Saved Successfully.');
                redirect('admin/command_library/list_command_library');
            }
        }

        public function list_command_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_command_library_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Command_library_model->get_all_command($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_command_library_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_command_library()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('reader_operation_message');
            $table_config = array(
                'source' => site_url('admin/command_library/list_command_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_command_library_listing_headers, $table_config),
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

            $dataArray['table_heading'] = 'Command List';
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_caption'] = "Add Command";
            $this->load->view('reader-command-list', $dataArray);
        }

        function delete_command($command_id)
        {
            $status = $this->Command_library_model->delete_command_by_id($command_id);
            if ($status == true)
            {
                $this->session->set_flashdata('reader_operation_message', 'Command deleted successfully');
                redirect('admin/command_library/list_command_library');
            }
            else
            {
                show_error('The Reader Details you are trying to delete does not exist.');
            }
        }

    }
    