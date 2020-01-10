<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Time_zone extends MY_Controller
    {

        private $_time_zone_listing_headers = 'time_zone_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Time_zone_model');
        }

        public function add_time_zone($time_zone_id = null)
        {
            $session_site_id = get_session_site_id();
          //  $this->form_validation->set_rules('code_id', 'Code ID', 'required|trim|unique[time_zone.time_zone_id.time_zone_code.' . $this->input->post('code_id') . ']');
            $this->form_validation->set_rules('code_id', 'Code ID', 'required|trim');
            $this->form_validation->set_rules('name', 'name', 'required|trim');
            $dataArray = array();
            $week_day = get_custom_config_item('week_day');
            $dataArray['arr_week_day'] = $week_day;
            $dataArray['site_id'] = $session_site_id;
            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Access Time Zone";
                $dataArray['form_action'] = current_url();


                if (!empty($time_zone_id))
                {
                    $dataArray['form_caption'] = "Edit Access Time Zone";
                    $reader_access_data = $this->Time_zone_model->get_time_zone_by_id($time_zone_id);


                    $dataArray['time_zone_id'] = $time_zone_id;
                    $dataArray['code_id'] = $reader_access_data['time_zone_code'];
                    $dataArray['name'] = $reader_access_data['time_zone_name'];


                    foreach ($week_day as $week_day)
                    {
                        $dataArray['weeks_arr'][$week_day . '_start_time'] = $reader_access_data[$week_day . '_start_time'];
                        $dataArray['weeks_arr'][$week_day . '_end_time'] = $reader_access_data[$week_day . '_end_time'];
                    }
                }


//                $dataArray['local_js'] = array(
//                    'datetimepicker'
//                );
                $this->load->view('time-zone-form', $dataArray);
            }
            else
            {

                $time_zone_id = $this->input->post('time_zone_id');
 
                $dataValues = array(
                    'time_zone_code' => $this->input->post('code_id'),
                    'time_zone_name' => $this->input->post('name'),
                    'site_id' => $this->input->post('site_id'),
                );

                if (!empty($time_zone_id))
                {
                    $dataValues['time_zone_id'] = $time_zone_id;
                }
                else
                {
                    $dataValues['created_at'] = date('Y-m-d H:i:s');
                }

                foreach ($week_day as $week_day)
                {

                    $dataValues[$week_day . '_start_time'] = validateTime($this->input->post($week_day . '_start_time'));
                    $dataValues[$week_day . '_end_time'] = validateTime($this->input->post($week_day . '_end_time'));
                }

                $this->Time_zone_model->save_time_zone($dataValues);
                $this->session->set_flashdata('time_zone_operation_message', 'Reader Time Zone saved successfully.');
                redirect('admin/time_zone');
            }
        }

        public function list_time_zone_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_time_zone_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Time_zone_model->get_all_time_zone($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_time_zone_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function index()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('time_zone_operation_message');
            $table_config = array(
                'source' => site_url('admin/time_zone/list_time_zone_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_time_zone_listing_headers, $table_config),
                'message' => $message
            );

            $dataArray['local_css'] = array(
                'dataTables.bootstrap',
                'responsive.bootstrap',
                'buttons.bootstrap',
                'select.bootstrap'
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
                'buttons.print'
            );

            $dataArray['table_heading'] = 'Access Time Zone List';
            $dataArray['form_action'] = current_url();

            $this->load->view('time-zone-list', $dataArray);
        }

        function delete_time_zone($time_zone_id)
        {

            $status = $this->Time_zone_model->delete_time_zone_by_id($time_zone_id);
            if ($status == true)
            {
                $this->session->set_flashdata('time_zone_operation_message', 'Time Zone deleted successfully');
                redirect('admin/time_zone');
            }
            else
            {
                show_error('The Time Zone Details you are trying to delete does not exist.');
            }
        }

    }
    