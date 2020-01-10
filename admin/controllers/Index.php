<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Index extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function change_session_site_id()
        {
            $session_user_data = $this->session->userdata();
            $session_user_data['site_id'] = $this->input->post('site_id');
            $session_user_data['department_id'] = '';
            $this->session->set_userdata($session_user_data);

            $session_site_id = get_session_site_id();
            $arr = array(
                "session_site_id" => $session_site_id
            );

            echo json_encode($arr);
        }

        public function dashboard()
        {
            $this->load->model('admin/Reader_model');
            $this->load->model('admin/Employee_model');
            $this->load->model('admin/Department_model');

            $dataArray = array();

            $arr_department = $this->Department_model->get_department_array();
            $dataArray['arr_department'] = add_blank_option($arr_department, "-Select Department-");

            $arr_employee_show = get_custom_config_item('employee_show_dashboard');
            $dataArray['employee_show'] = $arr_employee_show;

            $session_user_data = $this->session->userdata();
            if (!empty($session_user_data['auto_refresh']))
            {
                $dataArray["auto_refresh"] = $session_user_data['auto_refresh'];
            }
            else
            {
                $dataArray["auto_refresh"] = 'no';
            }

            if (!empty($session_user_data['department_id']))
            {
                $department_id = $session_user_data['department_id'];
            }
            else
            {
                $department_id = '';
            }
            if (!empty($session_user_data['show_emp_type']))
            {
                $show_emp = $session_user_data['show_emp_type'];
            }
            else
            {
                $show_emp = 'All';
            }

            $preferences_data = $this->iclock_model->get_preferences();
            if (!empty($preferences_data) && !empty($preferences_data['auto_refresh_time']))
            {
                $dataArray["auto_refresh_time"] = $preferences_data['auto_refresh_time'] * 1000; // converted in milliseconds
            }
            else
            {
                $dataArray["auto_refresh_time"] = AUTO_REFRESH_TIME * 1000; // converted in milliseconds
            }
            $pagingParams = array('order_by' => 'order_id , sn', 'department_id' => $department_id);
            $arr_result = (array) $this->Reader_model->get_all_reader_by_department($pagingParams);

            $emp_reader_Params = array('show_emp' => $show_emp);
            $arr_employee_reader_trans = $this->Employee_model->get_employee_reader_trans($emp_reader_Params);
            $dataArray["arr_readers"] = $arr_result["resultSet"];
            $dataArray["arr_employee_reader_trans"] = $arr_employee_reader_trans;


            $dataArray['form_action'] = base_url() . "admin/index/update_employee_reader";

            $dataArray['local_js'] = array(
                'bootbox'
            );

            $this->load->view('dashboard', $dataArray);
        }

        public function update_employee_reader_old()
        {
            $save2 = $this->input->post("save2");
            $employee_reader_arr = array();
            $is_delete = false;
            if (isset($save2))
            {
                $send_commands = false;
            }
            else
            {
                $send_commands = true;
            }

            $this->load->model('admin/Employee_model');
            $this->load->model('Iclock_model');

            //filter by department
            $session_user_data = $this->session->userdata();
            if (!empty($session_user_data['department_id']))
            {
                $department_id = $session_user_data['department_id'];
            }
            else
            {
                $department_id = '';
            }

            $arr_employee_id = $this->input->post("chk_employee_id_arr");

            $arr_employee_reader_trans_by_reader = $this->Employee_model->get_employee_reader_trans_by_reader($department_id);

            $arr_chk = $this->input->post("chk");

            $arr_employee_reader_trans = array();

            if (is_array($arr_employee_reader_trans_by_reader))
            {
                foreach ($arr_employee_reader_trans_by_reader as $reader_id => $reader_trans)
                {
                    $arr_reader_trans = $reader_trans["reader_trans"];
                    $reader_checkbox = (is_array($arr_chk) && array_key_exists($reader_id, $arr_chk)) ? $arr_chk[$reader_id] : array();

                    $diff1 = array_diff($arr_reader_trans, $reader_checkbox);
                    $diff2 = array_diff($reader_checkbox, $arr_reader_trans);

                    if (is_array($diff1))
                    {
                        foreach ($diff1 as $key => $employee_id)
                        {
                            $arr_employee_reader_trans[$reader_id][$employee_id] = "Unchecked";
                        }
                    }

                    if (is_array($diff2))
                    {
                        foreach ($diff2 as $key => $employee_id)
                        {
                            $arr_employee_reader_trans[$reader_id][$employee_id] = "Checked";
                        }
                    }
                }



                if (!empty($arr_employee_id))
                {
                    foreach ($arr_employee_reader_trans as $key => $value)
                    {
                        foreach ($value as $index => $row)
                        {
                            if (!in_array($index, $arr_employee_id))
                            {
                                unset($arr_employee_reader_trans[$key][$index]);
                            }
                        }
                    }
                }



                $currentrows = 1;

                if (is_array($arr_employee_reader_trans))
                {
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

                                if ($send_commands == true)
                                {
                                    //  $arr_employee_data['grp_data'] = $arr_employee_data['access_group'];
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
                            }
                            else if ($employee_status == "Unchecked")
                            {
                                $is_delete = true;
                                $dataValues = array(
                                    'employee_id' => $employee_id,
                                    'reader_id' => $reader_id
                                );

                                $employee_reader_arr[$reader_id][] = $employee_id;
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
                            log_message("MY_INFO", $command_str);
                            save_reader_command($data_arr);
                        }
                    }

                    //complete transaction
                    $this->Iclock_model->complete_trans();
                    if ($is_delete == true)
                    {
                        $this->delete_employee_from_reader($employee_reader_arr, $send_commands);
                    }
                }
            }
            if ($is_delete == false)
            {
                redirect('/admin/dashboard');
            }
        }

        public function update_employee_reader()
        {   
            $save2 = $this->input->post("save2");
            $employee_reader_arr = array();
            $is_delete = false;
            if (isset($save2))
            {
                $send_commands = false;
            }
            else
            {
                $send_commands = true;
            }

            $this->load->model('admin/Employee_model');
            $this->load->model('Iclock_model');

            //filter by department
            $session_user_data = $this->session->userdata();
            if (!empty($session_user_data['department_id']))
            {
                $department_id = $session_user_data['department_id'];
            }
            else
            {
                $department_id = '';
            }

            $arr_employee_id = $this->input->post("chk_employee_id_arr");

            $arr_employee_reader_trans_by_reader = $this->Employee_model->get_employee_reader_trans_by_reader($department_id);
            $add_arr = $this->input->post("add_chk");
          
            $remove_arr = $this->input->post("remove_chk");

            $arr_employee_reader_trans = array();
            if (!empty($add_arr))
            {
                foreach ($add_arr as $reader_id => $employee_id)
                {
                    foreach ($employee_id as $employee_id)
                    {
                        $arr_employee_reader_trans[$reader_id][$employee_id] = "Checked";
                    }
                }
            }
         
            
            if (!empty($remove_arr))
            {
                foreach ($remove_arr as $reader_id => $employee_id)
                {
                    foreach ($employee_id as $employee_id)
                    {
                        $arr_employee_reader_trans[$reader_id][$employee_id] = "Unchecked";
                    }
                }
            }

            if (!empty($arr_employee_id))
            {
                foreach ($arr_employee_reader_trans as $key => $value)
                {
                    foreach ($value as $index => $row)
                    {
                        if (!in_array($index, $arr_employee_id))
                        {
                            unset($arr_employee_reader_trans[$key][$index]);
                        }
                    }
                }
            }

            $currentrows = 1;

            if (is_array($arr_employee_reader_trans))
            {
                $sourceinfo = get_custom_config_item('sourceinfo');
                //begin transaction
                $this->Iclock_model->begin_trans();
                
                $flag=false;
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

                            if ($send_commands == true)
                            {
                                $arr_employee_data['grp_data'] = $arr_employee_data['access_group'];
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
                        }
                        else if ($employee_status == "Unchecked")
                        {
                            $is_delete = true;
                            $dataValues = array(
                                'employee_id' => $employee_id,
                                'reader_id' => $reader_id
                            );

                            $employee_reader_arr[$reader_id][] = $employee_id;
                        }
                    }
                   
                    
                    $employe_detail = $this->Employee_model->get_employee_by_id($employee_id);
                     
                    if($employe_detail['permanent_user'] == 'Yes')
                    {
                        $status = 'Active';
                    }
                    else if($employe_detail['permanent_user'] != 'Yes')
                    {   
                        if(($employe_detail['start_date'] <= date('Y-m-d')) && ($employe_detail['end_date'] >= date('Y-m-d')))
                        {
                           $status = 'Active';    
                        }
                        else
                        {
                            $status = "Inactive";
                        }
                        
                    }
//                    if($status=="Inactive"||$status==""){
//                       
//                       $flag =true;
//                        
//                    }
                    
                   
                    // get commands from array
                    if (!empty($command_arr))
                    {
                       
                         
                        $command_str = implode(NEW_LINE,$command_arr);
                     
                        $data_arr = array(
                            'reader_id' => $reader_id,
                            'command' => $command_str,
                            'status' =>  $status,
                            'sourceinfo' => $sourceinfo,
                            'ip_address' => get_ip_address()
                        );
                          
                        log_message("MY_INFO", $command_str);
                       // p($data_arr);
                        save_reader_command($data_arr);
                      
                    }
                    
                    //continue;  
                }

                //complete transaction
                $this->Iclock_model->complete_trans();
                if ($is_delete == true)
                {

                    $this->delete_employee_from_reader($employee_reader_arr, $send_commands);
                }
            }

            if ($is_delete == false)
            {
                redirect('/admin/dashboard');
            }
        }

        public function set_auto_refresh_variable()
        {
            $session_user_data = $this->session->userdata();
            $session_user_data['auto_refresh'] = $this->input->post('auto_refresh');
            $session_user_data['department_id'] = $this->input->post('department_id');
            $session_user_data['show_emp_type'] = $this->input->post('show_emp');
            $this->session->set_userdata($session_user_data);

            $arr = array(
                "msg" => 'success'
            );
//            p($_SESSION);

            echo json_encode($arr);
        }

        public function get_employee_info()
        {
            $this->load->model('admin/Employee_model');
            $output = '';
            $employee_id = $this->input->post('employee_id');
            $emp_data = $this->Employee_model->get_employee_by_id($employee_id);
            if (!empty($emp_data))
            {
                $output .= "Employee Name : " . $emp_data['name'];
                $output .= "Employee Pin : " . $emp_data['pin'];
            }
            else
            {
                $output = "No such employee";
            }
            echo $output;
        }

        public function delete_employee_from_reader($employee_reader_arr, $send_commands)
        {
            $dataArray = array();
            $this->load->model('admin/Reader_model');
            $this->load->model('admin/Employee_model');
            $dataArray['table_heading'] = 'Reader List';
            $dataArray['form_action'] = base_url() . 'admin/index/del_employee_from_reader';
            
            
            foreach ($employee_reader_arr as $reader => $employee)
            {
                $reader_data = $this->Reader_model->get_reader_by_id($reader);
                foreach ($employee as $employee_id)
                {
                    $employee_data = $this->Employee_model->get_employee_by_id($employee_id);
                    $dataArray['employee_reader'][] = array(
                        'emp_pin' => $employee_data['pin'],
                        'emp_name' => $employee_data['name'],
                        'reader_name' => $reader_data['name'],
                        'employee_id' => $employee_id,
                        'reader_id' => $reader);
                }
            }
            $this->load->view('delete_employee_from_reader', $dataArray);
        }

        public function del_employee_from_reader()
        {

            $this->load->model('Iclock_model');
            $this->load->model('admin/Employee_model');
            $currentrows = 1;
            $sourceinfo = get_custom_config_item('sourceinfo');
            foreach ($this->input->post('reader_emp_arr') as $reader_id => $emparr)
            {

                foreach ($emparr as $employee)
                {
                    $employee_id = $employee;
                    $reader_id = $reader_id;
                    $dataValues = array(
                        'employee_id' => $employee_id,
                        'reader_id' => $reader_id
                    );

                    $this->Iclock_model->delete_employee_reader($dataValues);

                    $command_arr = array();
                    $command_max_config = get_command_max_config($reader_id);
                    $command_max_number = $command_max_config['command_max_number'];
                    $command_max_capacity = $command_max_config['command_max_capacity'];

                    $arr_employee_data = $this->Employee_model->get_employee_by_id($employee_id);
                    $command = get_delete_user_command($arr_employee_data['pin']);

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

                            $command_arr = array();
                            $command_arr[] = $command;
                            $currentrows = 1;
                        }
                    }

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
                }
            }
            redirect('/admin/dashboard/');
        }

    }
    
