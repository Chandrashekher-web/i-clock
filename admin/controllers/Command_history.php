<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Command_history extends MY_Controller
    {

        private $_command_history_listing_headers = 'command_history_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Site_model');
            $this->load->model('admin/Commands_history_model');
            $this->load->model('admin/Reader_model');
        }

        public function list_command_history_data($param = null)
        {
            $table_name = '';
            if (!empty($param))
            {
                $table_name = $param;
            }
            $conditions_arr = array();

            if (!empty($_GET))
            {
                $conditions_arr = array(
                    'site_id' => empty($_GET['site_id']) ? '' : $_GET['site_id'],
                    'reader_id' => empty($_GET['reader_id']) ? '' : $_GET['reader_id'],
                    'date_to' => empty($_GET['date_to']) ? '' : $_GET['date_to'],
                    'date_from' => empty($_GET['date_from']) ? '' : $_GET['date_from'],
                    'date' => empty($_GET['date']) ? '' : $_GET['date'],
                    'commands_filter' => empty($_GET['commands_filter']) ? '' : $_GET['commands_filter'],
                    'filter_condition' => empty($_GET['filter_condition']) ? '' : $_GET['filter_condition'],
                    'filter_operator' => empty($_GET['filter_operator']) ? '' : $_GET['filter_operator'],
                    'date_filter_operator' => empty($_GET['date_filter_operator']) ? '' : $_GET['date_filter_operator'],
                    'filter_value' => empty($_GET['filter_value']) ? '' : $_GET['filter_value'],
                );
            }

            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_command_history_listing_headers];
            $cols = array_keys($arr);

            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Commands_history_model->get_all_history_commands($pagingParams, $conditions_arr, $table_name);

            $json_output = $this->datatable->get_json_output($resultdata, $this->_command_history_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_command_history($param = null)
        {
            $this->load->model('admin/Site_model');
            $this->load->model('admin/Commands_history_model');
            $this->load->library('Datatable');
            $message = $this->session->flashdata('outstanding_command_operation_message');
            $table_config = array(
                'source' => site_url('admin/command_history/list_command_history_data/' . $param),
                'table_id' => "outstanding_command_table",
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_command_history_listing_headers, $table_config),
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

            if (!empty($param))
            {
                $form_caption = 'Reader Commands History Unsuccessful';
            }
            else
            {
                $form_caption = 'Reader Commands History';
            }

            $arr_reader = array();
            $massage = $this->session->flashdata('command_operation_message');
            $dataArray['message'] = $massage;
            $dataArray['arr_sites'] = add_blank_option($arr_sites, '-Select all -');
            $dataArray['arr_reader'] = add_blank_option($arr_reader, "-- Select Reader --");
            $dataArray['form_caption'] = $form_caption;
            $dataArray['form_action'] = current_url();
            $dataArray['commands_filter_arr'] = add_blank_option(get_custom_config_item('commands_filter'), 'Select One');
            $dataArray['condition_arr'] = add_blank_option(get_custom_config_item('condition_arr'), 'Select One');
            $dataArray['date_condition_arr'] = add_blank_option(get_custom_config_item('date_condition_arr'), 'Select One');
            $dataArray['param'] = $param;
            $session_site_id = get_session_site_id();
            $dataArray['session_site_id'] = $session_site_id;
            $this->load->view('commands-history-form', $dataArray);
        }

    }
    
   