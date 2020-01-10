<?php

    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    class User_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_user($paging_params = array())
        {
            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);
            $this->db->select('a.*');
            $this->db->from('admin as a');
            $this->db->order_by('a.admin_id', 'ASC');

            if (!empty($paging_params['order_by']))
            {

                if (empty($paging_params['order_direction']))
                {
                    $paging_params['order_direction'] = '';
                }

                switch ($paging_params['order_by'])
                {
                    default:
                        $this->db->order_by($paging_params['order_by'], $paging_params['order_direction']);
                        break;
                }
            }
            $search = $paging_params['search'];
            if (!empty($search))
            {

                $this->db->like('name', $search);
            }


            $return = $this->get_with_count(null, $paging_params['records_per_page'], $paging_params['offset']);
            return $return;
        }

        function save_user($arr_user)
        {
            $return = null;
            if (isset($arr_user['id']))
            {
                $this->db->where('admin_id', $arr_user['id']);
                $this->db->update('admin', $arr_user);
                $return = $arr_user['id'];
            }
            else
            {
                $this->db->insert('admin', $arr_user);
                $return = $this->db->insert_id();
            }

            return $return;
        }

        function get_user_by_id($user_id)
        {
            $return = array();
            if (!empty($user_id))
            {
                $this->db->select('*');
                $this->db->from('admin');
                $this->db->where('admin_id', $user_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        function get_user_by_email($email)
        {
            $return = array();
            if (!empty($email))
            {
                $this->db->select('*');
                $this->db->from('user');
                $this->db->where('email', $email);
                $return = $this->db->get()->row_array();
            }

            return $return;
        }

        function delete_user_by_id($user_id)
        {
            $return = FALSE;
            if (!empty($user_id))
            {
                $this->db->where('admin_id', $user_id);
                $this->db->delete('admin');
                $return = TRUE;
            }
            return $return;
        }

        /*
         * User Login
         */

        public function login($username, $password)
        {

            $this->db->where('name', $username);
            $this->db->where('password', $password);
            $this->db->where('status', 'Active');
            $query = $this->db->get('admin');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array();
                return $row;
            }
            else
            {
                $this->db->where('name', $username);
                $this->db->where('password', $password);
                $query = $this->db->get('admin');
                if ($query->num_rows() > 0)
                {
                    return 'disabled';
                }
                else
                {
                    return FALSE;
                }
                //return FALSE;
            }
        }

        function get_count_co_form($table, $editor_data = array(), $project_data = array(), $project = "")
        {
            //p($project);
            $count = 0;
            $this->db->select('count(id) as count');
            $this->db->from($table);
            $this->db->where('archived', 'no');
            if ($this->session->userdata('user_type') == 'editor')
            {
                $this->db->where('editor_id', $this->session->userdata('user_id'));
            }

            if ($this->session->userdata('user_type') == 'client')
            {
                $this->db->where('client_name', $this->session->userdata('user_id'));
            }

            if (!empty($editor_data))
            {
                foreach ($editor_data as $editor)
                {
                    $editor_id[] = $editor['editor_id'];
                }
                $this->db->where_in('editor_id', $editor_id);
            }

            if (!empty($project_data))
            {
                foreach ($project_data as $projects)
                {
                    $project_id[] = $projects['project_id'];
                }

                $this->db->where_in('project_name', $project_id);
            }
            if (!empty($project))
            {
                $this->db->where('project_name', $project);
            }

            $return = $this->db->get()->row_array();

            return $return['count'];
        }

        function prepareAllUsersCsv()
        {
            $all_users_list = $this->allUsers();
            //p($co_form_list);
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"COFormList.csv\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
//            fputcsv($handle, array(
//                'RC PO Number', 'Change Order No.', 'Date', 'Original PO Value($)', 'Amount Requested($)', 'Revised PO Budget(if approved)($)', 'Brief Description Of Change', 'Approval Status', 'Date', 'Issued to RC', 'Client Approval Status', 'Date'
//            ));
            fputcsv($handle, array(
                'Name', 'Email', 'User Type', 'Status'
            ));

            foreach ($all_users_list as $record)
            {
                $resultarray['name'] = $record->name;
                $resultarray['email'] = $record->email;
                $resultarray['user_type'] = $record->user_type;
                $resultarray['status'] = $record->status;
                //p($resultarray);
                if ($resultarray)
                {
                    fputcsv($handle, $resultarray);
                    //p("hfhh");
                }
            }

            fclose($handle);
        }

        public function allUsers()
        {
            $return = NULL;
            $this->db->select('u.*');
            $this->db->from('user as u');
            $return = $this->db->get()->result();
            //p($this->db->last_query());

            return $return;
        }

        //***************************
        public function get_outlet_by_user_id($admin_id)
        {
            $return = array();
            $this->db->select('outlet_id');
            $this->db->from('user_oulet_trans');
            $this->db->where('user_id', $admin_id);
            $result = $this->db->get()->row_array();
            return $result;
        }

        public function get_admin_by_id($admin_id)
        {
            $return = NULL;
            if (!empty($admin_id))
            {
                $this->db->select('A.*');
                $this->db->from('admin as A');
                $this->db->where('admin_id', $admin_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function save_admin($dataValue, $site_trans= array())
        {
//            p($dataValue);
            $return = null;
            if (!empty($dataValue))
            {
                if (!empty($dataValue['admin_id']))
                {
                    $this->db->where('admin_id', $dataValue['admin_id']);
                    $this->db->update('admin', $dataValue);
                    $return = $dataValue['admin_id'];
                }
                else
                {
                    $this->db->insert('admin', $dataValue);
                    $return = $this->db->insert_id();
                }
                
                if (!empty($return))
                {
                    if (!empty($site_trans))
                    {
                        foreach ($site_trans as $key => $value)
                        {
                            $site_arr[$key]['admin_id'] = $return;
                            $site_arr[$key]['site_id'] = $value;
                        }
                        $this->save_site_trans($return, $site_arr);
                    }
                }
            }
            return $return;
        }

        function save_outlet_trans($admin_id, $outlet_arr)
        {
            $return = null;
            if (!empty($outlet_arr) && !empty($admin_id))
            {
                $this->db->where('user_id', $admin_id);
                $this->db->delete('user_oulet_trans');
                if (!empty($outlet_arr))
                {
                    $this->db->insert("user_oulet_trans", $outlet_arr);
                    $return[] = $this->db->insert_id();
                }
            }
            return $return;
        }

        function save_privilege_trans($admin_id, $privilege_arr)
        {
//            p($privilege_arr);
            $return = null;
            if (!empty($privilege_arr) && !empty($admin_id))
            {
                $this->db->where('user_id', $admin_id);
                $this->db->delete('user_privileges');
                if (!empty($privilege_arr['menu']))
                {
                    if (!empty($privilege_arr['view']))
                    {
                        foreach ($privilege_arr['view'] as $row)
                        {
                            $menu_data = $this->User_model->get_menu_by_name($row);
                            //                        p($menu_data);
                            $data = array(
                                'menu_id' => $menu_data['menu_id'],
                                'user_id' => $admin_id,
                            );
                            if (!empty($privilege_arr['view']) && $db_key = in_array($row, $privilege_arr['view']))
                            {
                                $privilege_data = $this->User_model->get_privilege_by_name('view');
                                $data['privilege_id'] = $privilege_data['privilege_id'];
                                $this->db->insert('user_privileges', $data);
                            }
                            if (!empty($privilege_arr['add']) && $db_key = in_array($row, $privilege_arr['add']))
                            {
                                $privilege_data = $this->User_model->get_privilege_by_name('add');
                                $data['privilege_id'] = $privilege_data['privilege_id'];
                                $this->db->insert('user_privileges', $data);
                            }
                            if (!empty($privilege_arr['edit']) && $db_key = in_array($row, $privilege_arr['edit']))
                            {
                                $privilege_data = $this->User_model->get_privilege_by_name('edit');
                                $data['privilege_id'] = $privilege_data['privilege_id'];
                                $this->db->insert('user_privileges', $data);
                            }
                            if (!empty($privilege_arr['delete']) && $db_key = in_array($row, $privilege_arr['delete']))
                            {
                                $privilege_data = $this->User_model->get_privilege_by_name('delete');
                                $data['privilege_id'] = $privilege_data['privilege_id'];
                                $this->db->insert('user_privileges', $data);
                            }
                            //                        $this->db->insert('user_privileges', $data);
                            $return = $this->db->insert_id();
                        }
                    }
                }
            }
            return $return;
        }

        public function check_email_exist($user)
        {
            $return = null;
            if (!empty($user))
            {
                $this->db->select('*');
                $this->db->from('user');
                $this->db->where('email', $user['email']);
                if (!empty($user['id']))
                {
                    $this->db->where('id !=', $user['id']);
                }
                $return = $this->db->get()->num_rows();
            }
            return $return;
        }

        public function get_user_array()
        {
            $data = array();
            $this->db->select('*');
            $this->db->from('user');
            $this->db->order_by('name');
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['id']] = $row['name'];
            }
            return $data;
        }

        public function get_menu_by_name($menu_name)
        {
            $return = array();
            $this->db->select('menu_id');
            $this->db->from('menu');
            $this->db->where('menu_name', $menu_name);
            $result = $this->db->get()->row_array();
            return $result;
        }

        public function get_privilege_by_name($privilege_name)
        {
            $return = array();
            $this->db->select('privilege_id');
            $this->db->from('privilege');
            $this->db->where('privilege_name', $privilege_name);
            $result = $this->db->get()->row_array();
            return $result;
        }

        public function get_user_privileges_by_id($user_id)
        {
            $return = array();
            $this->db->select('u.*, m.menu_name');
            $this->db->from('user_privileges as u');
            $this->db->join('menu as m', 'm.menu_id = u.menu_id', 'left');
            $this->db->where('u.user_id', $user_id);
            $this->db->group_by('u.menu_id');
            $result = $this->db->get()->result_array();
            return $result;
        }

        function check_validity($siteid)
        {
            if (!empty($siteid))
            {

                $this->db->select('license_validity');
                $this->db->from('site_trans as A');
                $this->db->where('site_id', $siteid);
                $this->db->order_by('id', 'Desc');
                $this->db->limit(1);
                $return = $this->db->get()->row_array();
//                p($this->db->last_query());

                if ($return['license_validity'] >= date("Y-m-d"))
                {
                    return FALSE;
                }
                else
                {
                    return true;
                }
            }
            else
            {
                return false;
            }
        }
        
        function save_site_trans($admin_id, $site_arr)
        {
            $return = null;
            if (!empty($site_arr) && !empty($admin_id))
            {
                $this->db->where('admin_id', $admin_id);
                $this->db->delete('admin_site_trans');
                foreach ($site_arr as $site)
                {
                    if (!empty($site))
                    {
                        $this->db->insert("admin_site_trans", $site);
                        $return[] = $this->db->insert_id();
                    }
                }
            }
            return $return;
        }

    }
    