<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Outstanding_commands extends MY_Controller
    {

        private $_outstanding_command_listing_headers = 'outstanding_command_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Site_model');
            $this->load->model('admin/Outstanding_commands_model');
            $this->load->model('admin/Reader_model');
        }

        public function list_outstanding_command_data()
        {

            $conditions_arr = array();
//            p($_GET);
            if (!empty($_GET))
            {
                $conditions_arr = array(
                    'site_id' => empty($_GET['site_id']) ? '' : $_GET['site_id'],
                    'reader_id' => empty($_GET['reader_id']) ? '' : $_GET['reader_id'],
                    'date' => empty($_GET['date']) ? '' : $_GET['date'],
                    'commands_filter' => empty($_GET['commands_filter']) ? '' : $_GET['commands_filter'],
                    'filter_condition' => empty($_GET['filter_condition']) ? '' : $_GET['filter_condition'],
                    'filter_operator' => empty($_GET['filter_operator']) ? '' : $_GET['filter_operator'],
                    'date_filter_operator' => empty($_GET['date_filter_operator']) ? '' : $_GET['date_filter_operator'],
                    'status' => empty($_GET['status']) ? '' : $_GET['status'],
                    'filter_value' => empty($_GET['filter_value']) ? '' : $_GET['filter_value'],
                );
            }
		 
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_outstanding_command_listing_headers];
            $cols = array_keys($arr);

            $pagingParams = $this->datatable->get_paging_params($cols);
//            p($pagingParams);
            $resultdata = $this->Outstanding_commands_model->get_all_outstanding_commands($pagingParams, $conditions_arr);

            $json_output = $this->datatable->get_json_output($resultdata, $this->_outstanding_command_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_outstanding_command()
        {
//            p($_POST);
            $this->load->model('admin/Site_model');
            $this->load->model('admin/Outstanding_commands_model');
            $this->load->library('Datatable');
            $message = $this->session->flashdata('outstanding_command_operation_message');
            $table_config = array(
                'source' => site_url('admin/outstanding_commands/list_outstanding_command_data/'),
                'table_id' => "outstanding_command_table",
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_outstanding_command_listing_headers, $table_config),
                'message' => $message,
            );

            $dataArray['local_css'] = array(
                'dataTables.bootstrap',
                'responsive.bootstrap',
                'buttons.bootstrap',
                'select.bootstrap',
                'datepicker',
                'bootbox'
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
                'blockUI',
                'datepicker',
                'bootbox'
            );

            $arr_sites = $this->Site_model->get_site_array('name');

            $arr_reader = array();
            $massage = $this->session->flashdata('command_operation_message');
            $dataArray['message'] = $massage;
            $dataArray['arr_sites'] = add_blank_option($arr_sites, '-Select all -');
            $dataArray['arr_reader'] = add_blank_option($arr_reader, "-- Select Reader --");
            $dataArray['form_caption'] = "Reader Outstanding Commands";
            $session_site_id = get_session_site_id();
            $dataArray['session_site_id'] = $session_site_id;

            if (!empty($this->input->post()))
            {
//                p($_POST);
                if (!empty($this->input->post('commands_id')))
                {
                    $dataValues = array(
                        'commands_id' => $this->input->post('commands_id'),
                    );
                    $button_type = $this->input->post('button_type');
                    if($button_type == 'delete_selected')
                    {
                        $this->Outstanding_commands_model->delete_outstanding_commands_by_ids($dataValues['commands_id']);
                        $this->session->set_flashdata('command_operation_message', 'Reader Outstanding Commands Deleted Successfully!');
                    }
                    else if($button_type == 'Active' || $button_type == 'Inactive')
                    {
                        $this->Outstanding_commands_model->update_outstanding_commands_by_ids($dataValues['commands_id'], $button_type);
                        $this->session->set_flashdata('command_operation_message', 'Status Updated Successfully!');
                    }
               
                    redirect('admin/outstanding_commands/list_outstanding_command');
                }
                else if (!empty($this->input->post('delete_mode')))
                {
//                    p($_POST);
                    $conditions_arr = array();
                    if (!empty($_POST))
                    {
                        $conditions_arr = array(
                            'site_id' => empty($_POST['site_id']) ? '' : $_POST['site_id'],
                            'reader_id' => empty($_POST['reader_id']) ? '' : $_POST['reader_id'],
                            'date' => empty($_POST['date']) ? '' : $_POST['date'],
                            'commands_filter' => empty($_POST['commands_filter']) ? '' : $_POST['commands_filter'],
                            'filter_condition' => empty($_POST['filter_condition']) ? '' : $_POST['filter_condition'],
                            'filter_operator' => empty($_POST['filter_operator']) ? '' : $_POST['filter_operator'],
                            'date_filter_operator' => empty($_POST['date_filter_operator']) ? '' : $_POST['date_filter_operator'],
                            'filter_value' => empty($_POST['filter_value']) ? '' : $_POST['filter_value'],
                        );
                    }
                    
                    $pagingParams = array();
                    $resultdata = $this->Outstanding_commands_model->get_all_outstanding_commands($pagingParams, $conditions_arr);
//                    p($resultdata);
                    
                    $commands_arr = array();
                    foreach ($resultdata['resultSet'] as $value)
                    {
                        $commands_arr[] = $value->command_id;
                    }
//                    p($commands_arr);
                    $this->Outstanding_commands_model->delete_outstanding_commands_by_ids($commands_arr);
                    $this->session->set_flashdata('command_operation_message', 'Reader Outstanding Commands Deleted Successfully!');
                    redirect('admin/outstanding_commands/list_outstanding_command');
                }
            }

            $dataArray['form_action'] = current_url();
            $dataArray['commands_filter_arr'] = add_blank_option(get_custom_config_item('commands_filter'), 'Select One');
            $dataArray['condition_arr'] = add_blank_option(get_custom_config_item('condition_arr'), 'Select One');
            $dataArray['date_condition_arr'] = add_blank_option(get_custom_config_item('date_condition_arr'), 'Select One');
            $dataArray['status_arr'] = add_blank_option(get_custom_config_item('status_arr'), 'Select One');
            $this->load->view('outstanding-commands-form', $dataArray);
        }

        public function dynamic_filter_view()
        {
            $this->load->helper('form');
            $id = $_GET['id'];
            $add_string = '';
            if (!empty($id))
            {
                $dataArray['commands_filter_arr'] = add_blank_option(get_custom_config_item('commands_filter'), 'Select One');
                $dataArray['condition_arr'] = add_blank_option(get_custom_config_item('condition_arr'), 'Select One');
                $dataArray['date_condition_arr'] = add_blank_option(get_custom_config_item('date_condition_arr'), 'Select One');
                $dataArray['status_arr'] = add_blank_option(get_custom_config_item('status_arr'), 'Select One');
                $dataArray['id'] = $id;
                $dataArray['remove'] = lang('remove');
                
                $dataArray['local_css'] = array(
                    'datepicker'
                );

                $dataArray['local_js'] = array(
                    'datepicker'
                );
                $add_string = $this->load->viewPartial('commands-advanced-search', $dataArray, TRUE);
            }

            echo $add_string;
        }

    }
    