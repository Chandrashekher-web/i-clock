<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Reader extends MY_Controller
    {

        private $_reader_listing_headers = 'reader_listing_headers';

        public function __construct()
        {
            parent::__construct();
            $this->load->model('admin/Reader_model');
            $this->load->model('admin/Department_model');
            $this->load->model('admin/Command_capacity_profile_model');
            $this->load->model('admin/iclock_model');
        }

        public function add_reader($reader_id = null, $page = null)
        {
            $is_super_admin = is_super_admin();

            $this->form_validation->set_rules('sn', 'Serial No.', 'required|trim|unique[reader.sn.reader_id.' . $this->input->post('reader_id') . ']');
            $this->form_validation->set_rules('name', ' Name', 'required|trim');
//            $this->form_validation->set_rules('transmission_interval', ' Transmission Interval ', 'required|trim');
//            $this->form_validation->set_rules('delay', ' Delay', 'required|trim');

            $dataArray = array();
            $reader_control = array();

            $session_site_id = get_session_site_id();
            $arr_department = $this->Department_model->get_department_array();
            $arr_profile = $this->Command_capacity_profile_model->get_profile_array();
            $arr_fpsource = get_custom_config_item('fpsource');
            $arr_sync_att = get_custom_config_item('sync_att');
            $arr_password_exempted = get_custom_config_item('password_exempted');

            $dataArray['arr_department'] = add_blank_option($arr_department, "-Select Department-");
            $dataArray['arr_profile'] = add_blank_option($arr_profile, "-Select Profile-");
            if ($is_super_admin)
            {
                $dataArray['arr_fpsource'] = $arr_fpsource;
                $dataArray['arr_sync_att'] = $arr_sync_att;
                $dataArray['arr_password_exempted'] = $arr_password_exempted;
            }

            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Add Reader";
                $dataArray['form_action'] = current_url();
                if (!empty($reader_id))
                {
                    $dataArray['form_caption'] = 'Edit Reader';
                    $reader_data = $this->Reader_model->get_reader_by_id($reader_id);


                    $dataArray['name'] = $reader_data['name'];
                    $dataArray['department_id'] = $reader_data['department_id'];
                    $dataArray['sn'] = $reader_data['sn'];
                    if ($is_super_admin)
                    {
                        $dataArray['profile_id'] = $reader_data['profile_id'];
                        $dataArray['stamp'] = $reader_data['stamp'];
                        $dataArray['delay'] = $reader_data['delay'];
                        $dataArray['ttimes'] = $reader_data['ttimes'];
                        $dataArray['transmission_interval'] = $reader_data['transmission_interval'];
                        $dataArray['opstamp'] = $reader_data['opstamp'];
                        $dataArray['seen'] = $reader_data['seen'];
                        $dataArray['fpsource'] = $reader_data['fpsource'];
                        $dataArray['zone'] = $reader_data['zone'];
                        $dataArray['password_exempted'] = $reader_data['password_exempted'];
                        $dataArray['sync_att'] = $reader_data['sync_att'];
                    }

                    $dataArray['reader_id'] = $reader_id;
                }

                $dataArray['local_css'] = array(
                    'jquery.contextMenu',
                );
                $dataArray['local_js'] = array(
                    'ipformat',
                    'jquery.contextMenu',
                    'jquery.ui.position',
                    'jquery.caret',
                );

                $this->load->view('reader-form', $dataArray);
            }
            else
            {
//                p($_POST);
                $reader_id = $this->input->post('reader_id');

                $dataValues = array(
                    'name' => $this->input->post('name'),
                    'department_id' => $this->input->post('department_id'),
//                    'profile_id' => $this->input->post('profile_id'),
                    'site_id' => $session_site_id,
                );

                if ($is_super_admin)
                {
                    $dataValues['profile_id'] = $this->input->post('profile_id');
                    $dataValues['sn'] = $this->input->post('sn');
                    $dataValues['delay'] = $this->input->post('delay');
                    $dataValues['transmission_interval'] = $this->input->post('transmission_interval');
                    $dataValues['fpsource'] = $this->input->post('fpsource');
                    $dataValues['password_exempted'] = $this->input->post('password_exempted');
                    $dataValues['sync_att'] = $this->input->post('sync_att');
                }

                if (!empty($reader_id))
                {
                    $dataValues['reader_id'] = $reader_id;
                }
                else
                {
                    if ($is_super_admin)
                    {
                        $dataValues['stamp'] = strtotime(date("Y-m-d H:i:s"));
                        $dataValues['ttimes'] = "";
                        $dataValues['opstamp'] = 0;
                        $dataValues['uistamp'] = 0;
                        $dataValues['fpstamp'] = 0;
                        $dataValues['facestamp'] = 0;
                        $dataValues['temp'] = 0;
                        $dataValues['lat'] = 0;
                        $dataValues['lon'] = 0;
                        $dataValues['online'] = 0;
                        $dataValues['zone'] = READER_TIMEZONE;
                    }
                }

                $this->Reader_model->save_reader($dataValues);
                $this->session->set_flashdata('reader_operation_message', 'Reader saved successfully.');
                if (empty($page))
                {
                    redirect('admin/reader/list_reader');
                }
                else
                {
                    redirect('admin/' . $page);
                }
            }
        }

        public function list_reader_data()
        {
            $this->load->library('Datatable');
            $arr = $this->config->config[$this->_reader_listing_headers];
            $cols = array_keys($arr);
            $pagingParams = $this->datatable->get_paging_params($cols);
            $resultdata = $this->Reader_model->get_all_reader($pagingParams);
            $json_output = $this->datatable->get_json_output($resultdata, $this->_reader_listing_headers);
            $this->load->setTemplate('json');
            $this->load->view('json', $json_output);
        }

        function list_reader()
        {
            $this->load->library('Datatable');
            $message = $this->session->flashdata('reader_operation_message');
            $table_config = array(
                'source' => site_url('admin/reader/list_reader_data'),
                 'table_id' => "reader_table",
                'datatable_class' => $this->config->config["datatable_class"],
            );
            $dataArray = array(
                'table' => $this->datatable->make_table($this->_reader_listing_headers, $table_config),
                'message' => $message
            );

            $dataArray['local_css'] = array(
                'dataTables.bootstrap',
                'responsive.bootstrap',
                'buttons.bootstrap',
                'select.bootstrap',
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
                'bootbox'
            );

            $dataArray['table_heading'] = 'Reader List';
            $dataArray['form_action'] = current_url();
            $dataArray['new_entry_link'] = base_url() . 'admin/reader/add_reader';
            $dataArray['new_entry_caption'] = "Add Reader";
            $this->load->view('reader-list', $dataArray);
        }

        function delete_reader($reader_id)
        {
            $status = $this->Reader_model->delete_reader_by_id($reader_id);
            if ($status == true)
            {
                $this->session->set_flashdata('reader_operation_message', 'Reader deleted successfully');
                redirect('admin/reader/list_reader');
            }
            else
            {
                show_error('The Reader Details you are trying to delete does not exist.');
            }
        }

        public function reader_control()
        {
            $reader_control = array();
            $reader_id = $this->input->post('reader_id');
            if (!empty($reader_id))
            {
                $reader_control['reader_id'] = $reader_id;
            }
            if (!empty($this->input->post('reader_command')))
            {
                $reader_control['command'] = $this->input->post('reader_command');
                if ($reader_control['command'] == 'reboot')
                {
                    $command = get_reboot_command();
                }
                else if ($reader_control['command'] == 'clear_data')
                {
                    $command = get_clear_data_command();
                }
                else if ($reader_control['command'] == 'change_ip')
                {
                    $reader_control['ipAddress'] = $this->input->post('ipAddress');
                    $command = get_set_ip_address_command($reader_control['ipAddress']);
                }
                else if ($reader_control['command'] == 'change_password')
                {
                    $reader_control['password'] = $this->input->post('password');
                    $command = get_change_push_comm_key_command($reader_control['password']);
                }
                else if ($reader_control['command'] == 'add_work_code')
                {
                    $reader_control['work_code'] = $this->input->post('add_work_code');
                    $reader_control['work_code_name'] = $this->input->post('work_code_name');
                    $command = get_add_workcode_command($reader_control['work_code'], $reader_control['work_code_name']);
                }
                else if ($reader_control['command'] == 'delete_work_code')
                {
                    $reader_control['work_code'] = $this->input->post('delete_work_code');
                    $command = get_delete_workcode_command($reader_control['work_code']);
                }
                else if ($reader_control['command'] == 'send_command')
                {
                    $reader_control['cmd'] = $this->input->post('cmd');
                    $command = $reader_control['cmd'];
                }
            }
//            p($reader_control);

            $data_id = $this->Reader_model->save_reader_command($reader_control, $command);

            $return_data = 'success';
            echo json_encode($return_data);
        }

        public function reader_password()
        {
            $data = array();

//            if(!empty($_POST['password']))
            if (!empty($_POST))
            {
                $new_password = $this->input->post('password');
                $preferences_data = $this->iclock_model->get_preferences();
                $sourceinfo = get_custom_config_item('sourceinfo');
                if ($new_password != $preferences_data['password'])
                {
                    $data_arr = array(
                        'id' => 1,
                        'password' => $new_password,
                        'old_password' => $preferences_data['password']
                    );
                    $this->iclock_model->update_preferences($data_arr);

                    //send command to all readers
                    $reader_data = $this->Reader_model->get_reader_array();
//                    p($reader_data);
                    foreach ($reader_data as $key => $value)
                    {
                        $command = get_change_push_comm_key_command($new_password);
                        $data_arr = array(
                            'reader_id' => $key,
                            'command' => $command,
                            'status' => 'Active',
                            'sourceinfo' => $sourceinfo,
                            'ip_address' => get_ip_address()
                        );
                        $return = save_reader_command($data_arr);
                    }
                }
                $data['message'] = "Password updated successfully!";
            }

            $data['form_caption'] = "Change Reader Password";
            $data['form_action'] = current_url();
            $this->load->view('reader-password', $data);
        }

        public function reader_data_sync()
        {
//            p($_POST);
            $this->load->model('admin/Employee_model');
            $this->load->model('admin/Time_zone_model');
            $this->load->model('Iclock_model');

            $reader_id = $this->input->post('reader_id');
            $sync_user = $this->input->post('sync_user');
            $sync_finger_print = $this->input->post('sync_finger_print');
            $sync_facial = $this->input->post('sync_facial');
            $sync_records = $this->input->post('sync_records');
            $update_user = $this->input->post('update_user');

            $arr_employee_reader_trans_by_reader = $this->Employee_model->get_total_employees_per_reader($reader_id);

//            p(count($arr_employee_reader_trans_by_reader));

            $arr_employee_reader_trans = array();

            if (!empty($arr_employee_reader_trans_by_reader))
            {
                foreach ($arr_employee_reader_trans_by_reader as $key => $employee)
                {
                    $arr_employee_reader_trans[$reader_id][$employee['employee_id']] = "Checked";
                }


                $currentrows = 1;

                if (is_array($arr_employee_reader_trans))
                {
//                    $command_max_config = get_command_max_config();
//                    $command_max_number = $command_max_config['command_max_number'];
//                    $command_max_capacity = $command_max_config['command_max_capacity'];
                    $sourceinfo = get_custom_config_item('sourceinfo');

                    //begin transaction
                    $this->Iclock_model->begin_trans();

                    foreach ($arr_employee_reader_trans as $reader_id => $arr_employee_status)
                    {
                        $command_arr = array();
                        $command_max_config = get_command_max_config($reader_id);
                        $command_max_number = $command_max_config['command_max_number'];
                        $command_max_capacity = $command_max_config['command_max_capacity'];
                        foreach ($arr_employee_status as $employee_id => $employee_status)
                        {
                            if ($employee_status == "Checked")
                            {
                                $arr_employee_data = $this->Employee_model->get_employee_by_id($employee_id);
                                $dataValues = array(
                                    'employee_id' => $employee_id,
                                    'reader_id' => $reader_id
                                );

                                $this->Iclock_model->delete_employee_reader($dataValues);
                                $this->Iclock_model->save_employee_reader($dataValues);

                                if (!empty($sync_user))
                                {
                                    $group_code = $this->Time_zone_model->get_access_group_code_by_access_groups_id($arr_employee_data['access_group']);

                                    $arr_employee_data['grp_data'] = $group_code['code_id'];

                                    $command = get_add_user_command2($arr_employee_data);

                                    //check if command exists
                                    $command_exists = $this->Iclock_model->check_if_command_exists($reader_id, $command);

                                    if ($command_exists == false)
                                    {
                                        $command_in_capacity = check_if_command_in_capacity($command_arr, $command, $command_max_capacity);
                                        if ($command_in_capacity && $currentrows < $command_max_number)
                                        {
                                            // add command to array
                                            $command_arr[] = $command;
                                            $currentrows++;
                                        }
                                        else
                                        {
                                            // get commands from array
                                            $command_str = implode(NEW_LINE, $command_arr);

                                            $data_arr = array(
                                                'reader_id' => $reader_id,
                                                'command' => $command_str,
                                                'status' => 'Active',
                                                'sourceinfo' => $sourceinfo,
                                                'ip_address' => get_ip_address()
                                            );
                                            save_reader_command($data_arr);

                                            //                                        initialize array
                                            $command_arr = array();
                                            $command_arr[] = $command;
                                            $currentrows = 1;
                                        }
                                    }
                                }

                                if (!empty($sync_finger_print))
                                {
                                    // get employee fp count
                                    $employee_fp_data = $this->Iclock_model->get_employee_fp_data($employee_id);
                                    if (!empty($employee_fp_data))
                                    {
                                        foreach ($employee_fp_data as $key => $employee_data)
                                        {
                                            $command = get_add_finger_print_command($employee_data);

                                            //check if command exists
                                            $command_exists = $this->Iclock_model->check_if_command_exists($reader_id, $command);

                                            if ($command_exists == false)
                                            {
                                                $command_in_capacity = check_if_command_in_capacity($command_arr, $command, $command_max_capacity);
                                                if ($command_in_capacity && $currentrows < $command_max_number)
                                                {
                                                    // add command to array
                                                    $command_arr[] = $command;
                                                    $currentrows++;
                                                }
                                                else
                                                {
                                                    // get commands from array
                                                    $command_str = implode(NEW_LINE, $command_arr);

                                                    $data_arr = array(
                                                        'reader_id' => $reader_id,
                                                        'command' => $command_str,
                                                        'status' => 'Active',
                                                        'sourceinfo' => $sourceinfo,
                                                        'ip_address' => get_ip_address()
                                                    );
                                                    save_reader_command($data_arr);

                                                    //                                        initialize array
                                                    $command_arr = array();
                                                    $command_arr[] = $command;
                                                    $currentrows = 1;
                                                }
                                            }
                                        }
                                    }
                                }

                                if (!empty($sync_facial))
                                {
                                    // get employee face count
                                    $employee_face_data = $this->Iclock_model->get_employee_face_data($employee_id);
                                    if (!empty($employee_face_data))
                                    {
                                        foreach ($employee_face_data as $key => $employee_data)
                                        {
                                            $command = get_add_facial_data_command($employee_data);

                                            //check if command exists
                                            $command_exists = $this->Iclock_model->check_if_command_exists($reader_id, $command);

                                            if ($command_exists == false)
                                            {
                                                $command_in_capacity = check_if_command_in_capacity($command_arr, $command, $command_max_capacity);
                                                if ($command_in_capacity && $currentrows < $command_max_number)
                                                {
                                                    // add command to array
                                                    $command_arr[] = $command;
                                                    $currentrows++;
                                                }
                                                else
                                                {
                                                    // get commands from array
                                                    $command_str = implode(NEW_LINE, $command_arr);

                                                    $data_arr = array(
                                                        'reader_id' => $reader_id,
                                                        'command' => $command_str,
                                                        'status' => 'Active',
                                                        'sourceinfo' => $sourceinfo,
                                                        'ip_address' => get_ip_address()
                                                    );
                                                    save_reader_command($data_arr);

                                                    //                                        initialize array
                                                    $command_arr = array();
                                                    $command_arr[] = $command;
                                                    $currentrows = 1;
                                                }
                                            }
                                        }
                                    }
                                }

//                                if (!empty($sync_records))
//                                {
//                                    $starttime = get_custom_config_item('starttime');
//                                    $endtime = date('Y-m-d H:i:s');
//                                    $command = get_sync_ta_records_reader_to_server_command($starttime, $endtime);
//
//                                    //check if command exists
//                                    $command_exists = $this->Iclock_model->check_if_command_exists($reader_id, $command);
//
//                                    if ($command_exists == false)
//                                    {
//                                        $command_in_capacity = check_if_command_in_capacity($command_arr, $command, $command_max_capacity);
//                                        if ($command_in_capacity && $currentrows < $command_max_number)
//                                        {
//                                            // add command to array
//                                            $command_arr[] = $command;
//                                            $currentrows++;
//                                        }
//                                        else
//                                        {
//                                            // get commands from array
//                                            $command_str = implode(NEW_LINE, $command_arr);
//
//                                            $data_arr = array(
//                                                'reader_id' => $reader_id,
//                                                'command' => $command_str,
//                                                'status' => 'Active',
//                                                'sourceinfo' => $sourceinfo,
//                                                'ip_address' => get_ip_address()
//                                            );
//                                            save_reader_command($data_arr);
//
//                                            //                                        initialize array
//                                            $command_arr = array();
//                                            $command_arr[] = $command;
//                                            $currentrows = 1;
//                                        }
//                                    }
//                                }
//
//                                if (!empty($update_user))
//                                {
//                                    $command = get_check_command();
//
//                                    //check if command exists
//                                    $command_exists = $this->Iclock_model->check_if_command_exists($reader_id, $command);
//
//                                    if ($command_exists == false)
//                                    {
//                                        $command_in_capacity = check_if_command_in_capacity($command_arr, $command, $command_max_capacity);
//                                        if ($command_in_capacity && $currentrows < $command_max_number)
//                                        {
//                                            // add command to array
//                                            $command_arr[] = $command;
//                                            $currentrows++;
//                                        }
//                                        else
//                                        {
//                                            // get commands from array
//                                            $command_str = implode(NEW_LINE, $command_arr);
//
//                                            $data_arr = array(
//                                                'reader_id' => $reader_id,
//                                                'command' => $command_str,
//                                                'status' => 'Active',
//                                                'sourceinfo' => $sourceinfo,
//                                                'ip_address' => get_ip_address()
//                                            );
//                                            save_reader_command($data_arr);
//
//                                            //                                        initialize array
//                                            $command_arr = array();
//                                            $command_arr[] = $command;
//                                            $currentrows = 1;
//                                        }
//                                    }
//                                }
                            }
                        }

                        // get commands from array
                        if (!empty($command_arr))
                        {
                            $command_str = implode(NEW_LINE, $command_arr);
                            $data_arr = array(
                                'reader_id' => $reader_id,
                                'command' => $command_str,
                                'status' => 'Active',
                                'sourceinfo' => $sourceinfo,
                                'ip_address' => get_ip_address()
                            );
                            save_reader_command($data_arr);
                        }

                        if (!empty($sync_records))
                        {
                            $starttime = get_custom_config_item('starttime');
                            $endtime = date('Y-m-d H:i:s');
                            $command = get_sync_ta_records_reader_to_server_command($starttime, $endtime);

                            $data_arr = array(
                                'reader_id' => $reader_id,
                                'command' => $command,
                                'status' => 'Active',
                                'sourceinfo' => $sourceinfo,
                                'ip_address' => get_ip_address()
                            );
                            save_reader_command($data_arr);
                        }

                        if (!empty($update_user))
                        {
                            $command = get_check_command();

                            $data_arr = array(
                                'reader_id' => $reader_id,
                                'command' => $command,
                                'status' => 'Active',
                                'sourceinfo' => $sourceinfo,
                                'ip_address' => get_ip_address()
                            );
                            save_reader_command($data_arr);
                        }
                    }

                    //complete transaction
                    $this->Iclock_model->complete_trans();
                }
            }

            if (!empty($sync_records))
            {
                $starttime = get_custom_config_item('starttime');
                $endtime = date('Y-m-d H:i:s');
                $command = get_sync_ta_records_reader_to_server_command($starttime, $endtime);

                //check if command exists
                $command_exists = $this->Iclock_model->check_if_command_exists($reader_id, $command);

                if ($command_exists == false)
                {
                    $command_in_capacity = check_if_command_in_capacity($command_arr, $command, $command_max_capacity);
                    if ($command_in_capacity && $currentrows < $command_max_number)
                    {
                        // add command to array
                        $command_arr[] = $command;
                        $currentrows++;
                    }
                    else
                    {
                        // get commands from array
                        $command_str = implode(NEW_LINE, $command_arr);

                        $data_arr = array(
                            'reader_id' => $reader_id,
                            'command' => $command_str,
                            'status' => 'Active',
                            'sourceinfo' => $sourceinfo,
                            'ip_address' => get_ip_address()
                        );
                        save_reader_command($data_arr);

                        //                                        initialize array
                        $command_arr = array();
                        $command_arr[] = $command;
                        $currentrows = 1;
                    }
                }
            }


            $return_data = 'success';
            echo json_encode($return_data);
        }

        public function setorder()
        {
            $reader_data = $this->Reader_model->get_reader_array_by_order_id();
//            p($reader_data);
            $dataArray = array(
                "results" => $reader_data
            );
            $dataArray['local_js'] = array(
                'jquery-ui'
            );

            $dataArray['local_css'] = array(
                'jquery-ui',
            );

            $this->load->view('reader-set-order', $dataArray);
        }

        public function setordersave()
        {
            $item = $this->input->post('item');
            if (!empty($item))
            {
                foreach ($item as $order => $value)
                {
                    $tmp_orderid = $order + 1;
                    $id = str_replace("orderid_", "", $value);
                    $this->Reader_model->setorder($id, $tmp_orderid);
                }
            }
            $this->load->setTemplate('json');
            $this->load->view('json', array("status" => "success"));
        }

        public function get_ping_info($sn)
        {
            $return_data = get_ping_info($sn);
//            p($return_data);
            echo json_encode($return_data);
        }

        function list_online_offline_readers()
        {
            $this->load->model('admin/Reader_model');
            $dataArray = array();
            $site_date = $this->Reader_model->online_offline_reader();
            $dataArray['site_date'] = $site_date;
            $this->load->view('reader-online-offline-report', $dataArray);
        }

        function get_reader_data_ajax()
        {

            $this->load->model('admin/Reader_model');
            $dataArray = array();
            $site_id = $this->input->post('site_id');
            $reader_data = $this->Reader_model->get_all_reader_data($site_id);
            $output = '';
            if (!empty($reader_data))
            {
                foreach ($reader_data as $key => $reader)
                {
                    $output .= '<tr><td width="25px;" class="text-right">';
                    $output .= '<input type="checkbox" name="reader[]" id="reader_' . $reader["reader_id"] . '" value="' . $reader["reader_id"] . '"  /></td>';
                    $output .= '<td width="300px;"><label for="reader_' . $reader["reader_id"] . '">' . $reader["sn"] . '</label></td>';
                    $output .= '<td width="100px;"><label for="reader_' . $reader["reader_id"] . '">' . $reader["name"] . '</label></td></tr>';
                }
            }


            echo $output;
        }

        public function reader_lookup()
        {
//            p($_GET);
            $dataArray = array();
            if ($this->form_validation->run() == false)
            {
                $dataArray['form_caption'] = "Reader Lookup";
                $dataArray['form_action'] = current_url();
                if (isset($_GET['linked']))
                {
                    $dataArray['linked'] = $_GET['linked'];
                    $dataArray['product_id'] = $_GET['product_id'];
                    $dataArray['outlet_id'] = $_GET['outlet_id'];
                }
                $this->load->view('reader-lookup-form', $dataArray);
            }
        }

        public function get_reader_search_details()
        {
//            p($_POST);
            $output = '';
            $readerdata = array();
            $name = trim($this->input->post('name'));
            $sn = trim($this->input->post('sn'));

            $readerdata = $this->Reader_model->get_reader_search_details($name, $sn);

            if (!empty($readerdata))
            {
                $output .= "<table id='myTable' class='table table-striped table-bordered'><tr><th>Site Name</th><th>SN</th><th>Name</th><th style='width:20%'>Seen</th><th>Stamp</th><th>Opstamp</th></tr>";
                foreach ($readerdata as $key => $reader)
                {
                    $output .= "<tr><td>" . $reader['site_name'] . "</td>";
                    $output .= "<td>" . $reader['sn'] . "</td>";
                    $output .= "<td>" . $reader['name'] . "</td>";
                    $output .= "<td>" . $reader['seen'] . "</td>";
                    $output .= "<td>" . $reader['stamp'] . "</td>";
                    $output .= "<td>" . $reader['opstamp'] . "</td></tr>";
                }
            }
            else
            {
                $output = "<p>No such reader found!</p>";
            }
            echo $output;
        }

        public function connected_reader()
        {
            $dataArray = array();

            $dataArray['form_caption'] = "Connected Reader";
            $dataArray['form_action'] = current_url();

            $this->load->view('connected-reader-list', $dataArray);
        }
	
	 function get_manual_reader_data_ajax()
        {

            $this->load->model('admin/Reader_model');
            $dataArray = array();
            $site_id = $this->input->post('site_id');
            $reader_data = $this->Reader_model->get_all_reader_data($site_id);
            $output = '';
            $form_action =  base_url()."admin/send_command/manual_access_control";
            if (!empty($reader_data))
            {
                foreach ($reader_data as $key => $reader)
                {   
                    $output .= '<tr>';
                    $attributes = array('id' => 'send-command-form', 'class' => 'form-horizontal');
                    $output .= '<td width="300px;"><label for="reader_'.$reader["reader_id"].'">'.$reader["sn"].'</label></td>';
                    $output .= '<td width="100px;"><label for="reader_'.$reader["reader_id"].'">'.$reader["name"].'</label></td>';
                    $output .= '<td width="100px;">';
                    $output .= form_open_multipart($form_action, $attributes);
                    $output .= '<input type="hidden" name="reader[]" id="reader_'.$reader["reader_id"].'"  value="'.$reader["reader_id"].'" />';
                    $output .= '<center><button type="submit" class="btn btn-success">Open</button><center>';
                    $output .= '</form>';
                    $output .= '</td>';
                    $output .= '</tr>';
                    
                }
            }
            echo $output;
        }

    }
    