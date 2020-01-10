<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Reader_access_groups extends MY_Controller
    {

        private $_reader_access_groups_listing_headers = 'reader_access_groups_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Reader_access_groups_model');
            $this->load->model('admin/Time_zone_model');
        }

        public function add_reader_access_groups($reader_access_groups_id = null)
        {
            $session_site_id = get_session_site_id();
            $this->form_validation->set_rules('code_id', 'Code ID', 'required|trim|unique[reader_access_groups.code_id.site_id.' . $session_site_id . '.reader_access_groups_id]');
            $this->form_validation->set_rules('description', 'Description', 'required|trim');
            $this->form_validation->set_rules('time_zone[]', 'Time Zone', 'required|trim');
            $this->form_validation->set_rules('group_verify_type', 'Visitors Verify Type', 'required|trim');
            $this->form_validation->set_rules('in_reader[]', 'In Reader', 'required|trim');
            $this->form_validation->set_rules('out_reader[]', 'Out Reader', 'required|trim');
            $this->form_validation->set_rules('exit_reader[]', 'Exit Reader', 'required|trim');
            $dataArray = array();
            $access_record = check_value_access_user();

            $reader_data = $this->Reader_access_groups_model->get_reader_by_site_id($access_record['site_id']);

            $time_zone_data = $this->Time_zone_model->get_time_zone_array($access_record['site_id']);
            $dataArray['time_zone_data'] = $time_zone_data;

            $dataArray['reader_data'] = $reader_data;
            $week_day = get_custom_config_item('week_day');
            $dataArray['arr_week_day'] = $week_day;
            $group_verify_type = get_custom_config_item('group_verify_type');
            $dataArray['arr_group_verify_type'] = add_blank_option($group_verify_type, "-Select Group Verify Type-");


            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Reader Access Groups";
                $dataArray['form_action'] = current_url();
                $dataArray['site_id'] = $access_record['site_id'];

                if (!empty($reader_access_groups_id))
                {
                    $dataArray['form_caption'] = "Edit Reader Access Groups";
                    $reader_access_data = $this->Reader_access_groups_model->get_reader_access_groups_by_id($reader_access_groups_id);

                    $reader_trans_data = $this->Reader_access_groups_model->get_reader_access_reader_trans_by_id($reader_access_groups_id);

                    $dataArray['reader_access_groups_id'] = $reader_access_groups_id;
                    $dataArray['code_id'] = $reader_access_data['code_id'];
                    $dataArray['description'] = $reader_access_data['description'];
                    $dataArray['group_verify_type'] = $reader_access_data['group_verify_type'];
                    $dataArray['in_reader'] = explode(',', $reader_trans_data['in_reader_trans']['in_reader']);
                    $dataArray['out_reader'] = explode(',', $reader_trans_data['out_reader_trans']['out_reader']);
                    $dataArray['exit_reader'] = explode(',', $reader_trans_data['exit_reader_trans']['exit_reader']);
                    $dataArray['time_zone'] = explode(',', $reader_trans_data['time_zone_trans']['time_zone_id']);
                    $dataArray['is_antipass'] = $reader_access_data['is_antipass'];
                }

                $dataArray['local_css'] = array(
                    'select2',
                );
                $dataArray['local_js'] = array(
                    'select2',
                    'datetimepicker'
                );
                $this->load->view('reader-access-groups-form', $dataArray);
            }
            else
            {

                $reader_access_groups_id = $this->input->post('reader_access_groups_id');
                $in_reader_trans_data = $this->input->post('in_reader');
                $out_reader_trans_data = $this->input->post('out_reader');
                $exit_reader_trans_data = $this->input->post('exit_reader');
                $time_zone_trans_data = $this->input->post('time_zone');

                $dataValues = array(
                    'code_id' => $this->input->post('code_id'),
                    'description' => $this->input->post('description'),
                    'site_id' => $this->input->post('site_id'),
                    'group_verify_type' => $this->input->post('group_verify_type'),
                    'is_antipass' => empty($this->input->post('is_antipass')) ? "No" : "Yes",
                );

                if (!empty($reader_access_groups_id))
                {
                    $dataValues['reader_access_groups_id'] = $reader_access_groups_id;
                }
                else
                {
                    $dataValues['created_at'] = date('Y-m-d H:i:s');
                }

                $reader_access_id = $this->Reader_access_groups_model->save_reader_access_groups($dataValues, $in_reader_trans_data, $out_reader_trans_data, $exit_reader_trans_data, $time_zone_trans_data);
                send_timezone_command_to_reader($this->input->post('code_id'), 'ALL', $this->input->post('group_verify_type'));
                $this->session->set_flashdata('reader_access_groups_operation_message', 'Reader Access Groups saved successfully.');
                redirect('admin/reader_access_groups/list_reader_access_groups');
            }
        }

        public function list_reader_access_groups_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_reader_access_groups_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Reader_access_groups_model->get_all_reader_access_groups($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_reader_access_groups_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_reader_access_groups()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('reader_access_groups_operation_message');
            $table_config = array(
                'source' => site_url('admin/reader_access_groups/list_reader_access_groups_data'),
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_reader_access_groups_listing_headers, $table_config),
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
                'buttons.print'
            );

            $dataArray['table_heading'] = 'Reader Access Groups List';
            $dataArray['form_caption'] = 'Reader Access Groups List';

            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_link'] = base_url() . 'admin/reader_access_groups/add_reader_access_groups';
            $dataArray['new_entry_caption'] = "Add Reader Access Groups";
            $this->load->view('reader-access-groups-list', $dataArray);
        }

        function delete_reader_access_groups($reader_access_groups_id)
        {

            $status = $this->Reader_access_groups_model->delete_reader_access_groups_by_id($reader_access_groups_id);
            if ($status == true)
            {
                $this->session->set_flashdata('reader_access_groups_operation_message', 'Reader Access Groups deleted successfully');
                redirect('admin/reader_access_groups/list_reader_access_groups');
            }
            else
            {
                show_error('The Reader Access Groups Details you are trying to delete does not exist.');
            }
        }

        function resend_group_time_zone()
        {
            $this->form_validation->set_rules('reader[]', 'reader', 'required|trim');

            $dataArray = array();
            $access_record = check_value_access_user();

            $reader_data = $this->Reader_access_groups_model->get_reader_by_site_id($access_record['site_id']);
            $dataArray['reader'] = $reader_data;


            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Resend Access Groups & Time Zones To Reader";
                $dataArray['form_action'] = current_url();
                $dataArray['site_id'] = $access_record['site_id'];


                $dataArray['local_css'] = array(
                    'select2',
                );
                $dataArray['local_js'] = array(
                    'select2',
                    'datetimepicker'
                );
                $this->load->view('resend-group-time-zone', $dataArray);
            }
            else
            {


                $reader = $this->input->post('reader');

                $get_access_group = $this->Reader_access_groups_model->get_reader_access_groups($access_record['site_id']);
                if (!empty($get_access_group))
                {
                    foreach ($get_access_group as $access_group)
                    {
                        send_timezone_command_to_reader($access_group['code_id'], 'ALL', $access_group['group_verify_type'], $reader);
                    }
                }



                $this->session->set_flashdata('reader_access_groups_operation_message', 'Reader Access Groups saved successfully.');
                redirect('admin/reader_access_groups/resend_group_time_zone');
            }
        }

        public function update_antipass_reader_order($reader_access_groups_id)
        {

            $reader_data = $this->Reader_access_groups_model->get_reader_for_antipass_by_id($reader_access_groups_id);

            $dataArray = array(
                "results" => $reader_data,
                "reader_access_groups_id" => $reader_access_groups_id
            );
            $dataArray['local_js'] = array(
                'jquery-ui'
            );

            $dataArray['local_css'] = array(
                'jquery-ui',
            );

            $this->load->view('antipass-reader-set-order', $dataArray);
        }

        public function setordersave()
        {
            $item = $this->input->post('item');
            $reader_type = $this->input->post('reader_type');
            $reader_access_groups_id = $this->input->post('reader_access_groups_id');
            if (!empty($item))
            {
                foreach ($item as $order => $value)
                {
                    $tmp_orderid = $order + 1;
                    $id = str_replace("orderid_", "", $value);
                    $this->Reader_access_groups_model->setorder($id, $tmp_orderid, $reader_type, $reader_access_groups_id);
                }
            }
            $this->load->setTemplate('json');
            $this->load->view('json', array("status" => "success"));
        }

        function change_status($reader_access_groups_id)
        {
            $status = $this->Reader_access_groups_model->get_antipass_status($reader_access_groups_id);
            $updatestatus = $this->Reader_access_groups_model->update_status($reader_access_groups_id, $status['is_antipass']);
            echo $updatestatus;
            return;
        }

    }
    