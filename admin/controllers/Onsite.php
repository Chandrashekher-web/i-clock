<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Onsite extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Onsite_model');
            $this->load->model('admin/Employee_model');
            $this->load->model('admin/Reader_model');
            $this->load->model('admin/Department_model');
        }

        function index()
        {
            $dataArray = array();
            $conditions_arr = array();

            $employee_arr = $this->Employee_model->get_employee_array('name');
            $dataArray['employee_arr'] = add_blank_option($employee_arr, "-Select Employee-");

            $reader_arr = $this->Reader_model->get_reader_array('name');
            $dataArray['reader_arr'] = add_blank_option($reader_arr, "-Select Reader-");

            $department_arr = $this->Department_model->get_department_array();
            $dataArray['department_arr'] = add_blank_option($department_arr, "-Select Department-");

            $filter = get_custom_config_item('onsile_filter');
            $dataArray['site_hours_filter'] = add_blank_option($filter, "-Select Hours-");
            
            if ($this->input->post('mode') && $this->input->post('mode') == 'filter')
            {
                $conditions_arr['hours_filter'] = $this->input->post('hours_filter');
                $conditions_arr['employee_id'] = $this->input->post('employee');
                $conditions_arr['reader_id'] = $this->input->post('reader');
                $conditions_arr['department_id'] = $this->input->post('department');
            }
            $dataArray['conditions_arr'] = $conditions_arr;
            $clocking_data = $this->Onsite_model->get_live_clocking($conditions_arr);
           
            $temp = array_unique(array_column($clocking_data, 'employee_id'));
            $clocking_data = array_intersect_key($clocking_data, $temp);
 

            $in_array = array();
            $clocking_in_arr = array();
            $clocking_out_arr = array();
            if (!empty($clocking_data))
            {
                foreach ($clocking_data as $key => $clocking)
                {
                    if (!empty($clocking['access_group']))
                    {
                        $reader = get_reader_by_access_groups($clocking['access_group'], 'IN');
                        if (in_array($clocking['reader_id'], $reader))
                        {
                            $clocking_in_arr[$key] = $clocking;
                        }
                        else
                        {
                            $clocking_out_arr[$key] = $clocking;
                        }
                    }
                }
            }
            
//            p($clocking_in_arr,0);
//            p('---------------------------',0);
//            p($clocking_out_arr,0);
//            p('---------------------------',0);
            
            foreach ($clocking_in_arr as $key => $in_clocking)
            {
                foreach ($clocking_out_arr as $out_clocking)
                {
                    if ($out_clocking['employee_id'] == $in_clocking['employee_id'])
                    {
                        if($out_clocking['attendance_id']>$in_clocking['attendance_id'])
                        {
                        unset($clocking_in_arr[$key]);
                        break;
                        }
                    }
                }
            }
//            p($clocking_in_arr,0);
            $dataArray['local_css'] = array(
                'datepicker'
            );
            $dataArray['local_js'] = array(
                'datepicker',
            );
            $dataArray['form_caption'] = 'On Site List';
//            rsort($clocking_in_arr);
            $dataArray['clocking_data'] = $clocking_in_arr;
            $this->load->view('onsite/onsite-list', $dataArray);
        }

        function get_latest_clocking()
        {
            $dataArray = array();


            $dataArray['hours_filter'] = $this->input->post('hours_filter');
            $dataArray['employee_id'] = $this->input->post('employee');
            $dataArray['reader_id'] = $this->input->post('reader');
            $dataArray['department_id'] = $this->input->post('department');
            $clocking_data = $this->Onsite_model->get_live_clocking($dataArray);
 $temp = array_unique(array_column($clocking_data, 'employee_id'));
            $clocking_data = array_intersect_key($clocking_data, $temp);
 

            $in_array = array();
            $clocking_in_arr = array();
            $clocking_out_arr = array();
            if (!empty($clocking_data))
            {
                foreach ($clocking_data as $key => $clocking)
                {
                    if (!empty($clocking['access_group']))
                    {
                        $reader = get_reader_by_access_groups($clocking['access_group'], 'IN');
                        if (in_array($clocking['reader_id'], $reader))
                        {
                            $clocking_in_arr[$key] = $clocking;
                        }
                        else
                        {
                            $clocking_out_arr[$key] = $clocking;
                        }
                    }
                }
            }
            
//            p($clocking_in_arr,0);
//            p('---------------------------',0);
//            p($clocking_out_arr,0);
//            p('---------------------------',0);
            
            foreach ($clocking_in_arr as $key => $in_clocking)
            {
                foreach ($clocking_out_arr as $out_clocking)
                {
                    if ($out_clocking['employee_id'] == $in_clocking['employee_id'])
                    {
                        if($out_clocking['attendance_id']>$in_clocking['attendance_id'])
                        {
                        unset($clocking_in_arr[$key]);
                        break;
                        }
                    }
                }
            }
//            p($clocking_in_arr,0);
            $dataArray['clocking_data'] = $clocking_in_arr;
            $return_str = $this->load->viewPartial('onsite/onsite-list-ajex', $dataArray);
            echo $return_str;
        }

        function remove_clocking()
        {
            $clocking_id = $this->input->post('clocking_id');
            $this->Onsite_model->remove_clocking($clocking_id);
        }

    }
    