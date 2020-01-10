<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Expired_visitor_report extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Expired_visitor_report_model');
            $this->load->model('admin/Employee_model');

            $this->load->model('iclock_model');
        }

        function index()
        {
            $dataArray = array();
            $visitor_data = $this->Expired_visitor_report_model->get_expired_visitor();

            $dataArray['visitor_data'] = $visitor_data;
            $dataArray['form_caption'] = ' Visitor Time Expired List';
            $this->load->view('expired-visitor-report', $dataArray);
        }

        function remove_visitor()
        {
            $created_by = $this->input->post('employee_created');
            $employee_pin = $this->input->post('employee_pin');
            $employee_id = $this->input->post('employee_id');
            $get_reader_arr = $this->iclock_model->get_reader_by_access_groups($created_by, 'ALL');

            foreach ($get_reader_arr as $reader)
            {
                $sourceinfo = get_custom_config_item('sourceinfo');
                $command = "DATA DEL_USER PIN=" . $employee_pin;
                $dataValues = array(
                    'reader_id' => $reader,
                    'command' => $command,
                    'status' => 'Active',
                    'sourceinfo' => $sourceinfo
                );

                $this->iclock_model->save_command($dataValues);

                $dataValues = array(
                    'employee_id' => $employee_id,
                    'reader_id' => $reader
                );

                $this->iclock_model->delete_employee_reader($dataValues);
                $this->Employee_model->save_employee_before_delete($employee_id);
                $this->Employee_model->delete_employee_by_id($employee_id);
            }
        }

    }
    