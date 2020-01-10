<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Site_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_site($pagingParams = array())
        {
            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);
//            $this->db->select('s.*');
//            $this->db->from('site as s');

            $this->db->select('s.*, st.*, qry.*, s.site_id ');
            $this->db->from('site as s');
            $this->db->join('(SELECT MAX(id) AS max_id, site_id '
                . 'FROM site_trans GROUP BY site_id) as qry', 's.site_id = qry.site_id', 'left');
            $this->db->join('site_trans as st', 'qry.max_id = st.id', 'left');

            if (!empty($pagingParams['order_by']))
            {
                if (empty($pagingParams['order_direction']))
                {
                    $pagingParams['order_direction'] = '';
                }
                switch ($pagingParams['order_by'])
                {
                    default:
                        $this->db->order_by($pagingParams['order_by'], $pagingParams['order_direction']);
                        break;
                }
            }
            $search = empty($pagingParams['search']) ? array() : $pagingParams['search'];
            if (!empty($search))
            {
                $this->db->like('site_code', $search);
                $this->db->or_like('s.name', $search);
                $this->db->or_like('contact', $search);
                $this->db->or_like('notes', $search);
            }
            $return = $this->get_with_count(NULL, $pagingParams['records_per_page'], $pagingParams['offset']);
            return $return;
        }

        public function get_site_by_id($site_id)
        {
            $return = NULL;
            if (!empty($site_id))
            {
                $this->db->select('s.*');
                $this->db->from('site as s');
                $this->db->where('site_id', $site_id);
                $arr_site = $this->db->get()->row_array();

                $this->db->select('st.*');
                $this->db->from('site_trans as st');
                $this->db->where('site_id', $site_id);
                $this->db->order_by('id desc');
                $this->db->limit(1);
                $arr_site_trans = $this->db->get()->row_array();
                unset($arr_site_trans["id"]);
                unset($arr_site_trans["site_id"]);
            }
            $return = array_merge($arr_site, $arr_site_trans);

            return $return;
        }

        public function save_site($dataValue, $dataValue_site_trans)
        {
            $this->db->trans_start();
            $site_id = null;
            if (!empty($dataValue))
            {
                if (!empty($dataValue['site_id']))
                {
                    $this->db->where('site_id', $dataValue['site_id']);
                    $this->db->update('site', $dataValue);
                    $site_id = $dataValue['site_id'];
                }
                else
                {
                    $this->db->insert('site', $dataValue);
                    $site_id = $this->db->insert_id();
                }

                $dataValue_site_trans["site_id"] = $site_id;
                $this->db->insert('site_trans', $dataValue_site_trans);
            }
            $this->db->trans_complete();
            return $site_id;
        }

        public function delete_site_by_id($site_id, $emp_arr, $reader_arr)
        {
            $this->db->trans_start();

            //delete from employee
            $this->db->where('site_id', $site_id);
            $res = $this->db->delete('employee');

            if (!empty($emp_arr))
            {
                //delete from employee_face
                $this->db->where_in('employee_id', $emp_arr);
                $res = $this->db->delete('employee_face');

                //delete from employee_fp
                $this->db->where_in('employee_id', $emp_arr);
                $res = $this->db->delete('employee_fp');

                //delete from employee_pic
                $this->db->where_in('employee_id', $emp_arr);
                $res = $this->db->delete('employee_pic');

                //delete from attendance
                $this->db->where_in('employee_id', $emp_arr);
                $res = $this->db->delete('attendance');
            }

            //delete from reader
            $this->db->where('site_id', $site_id);
            $res = $this->db->delete('reader');

            if (!empty($emp_arr))
            {
                //delete from reader_commands
                $this->db->where_in('reader_id', $reader_arr);
                $res = $this->db->delete('reader_command');

                //delete from reader_commands_history
                $this->db->where_in('reader_id', $reader_arr);
                $res = $this->db->delete('reader_command_history');

                //delete from reader_commands_history_unsuccessful
                $this->db->where_in('reader_id', $reader_arr);
                $res = $this->db->delete('reader_command_history_unsuccessful');

                //delete from employee_reader_trans
                $this->db->where_in('reader_id', $reader_arr);
                $res = $this->db->delete('employee_reader_trans');
            }

            //delete from department
            $this->db->where('site_id', $site_id);
            $res = $this->db->delete('department');

            //delete from site_trans
            $this->db->where('site_id', $site_id);
            $res = $this->db->delete('site_trans');

            //delete from site
            $this->db->where('site_id', $site_id);
            $res = $this->db->delete('site');
            $return = $this->db->trans_complete();
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_site_array($fieldname = 'site_code')
        {
            $data = array();
            $this->db->select('*');
            $this->db->from('site');
            $this->db->order_by($fieldname);
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['site_id']] = $row[$fieldname];
            }
            return $data;
        }

        public function get_sites_from_trans($user_id)
        {
            $data = array();
            $this->db->select('site_id');
            $this->db->from('admin_site_trans');
            $this->db->where('admin_id', $user_id);
            $data = $this->db->get()->result_array();
            return $data;
        }

        public function check_value_access_user($site_id)
        {
            $data = array();
            $this->db->select('access_user,site_id');
            $this->db->from('site_trans');
            $this->db->where('site_id', $site_id);
            $this->db->order_by('id', 'Desc');
            $this->db->limit(1);
            $data = $this->db->get()->row_array();
            return $data;
        }

        public function get_site_array_by_site_id($site_ids, $fieldname = 'site_code')
        {
            $data = array();
            $this->db->select('*');
            $this->db->from('site');
            $this->db->where_in('site_id', $site_ids);
            $this->db->order_by($fieldname);
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['site_id']] = $row[$fieldname];
            }
            return $data;
        }

        public function check_user_access_by_site_id($site_id)
        {
            $this->db->select('site_trans.access_user');
            $this->db->from('site');
            $this->db->join('site_trans', 'site.site_id=site_trans.site_id', 'left');
            $this->db->where('site.site_id', $site_id);
            $this->db->order_by('id', 'Desc');
            $this->db->limit(1);
            return $this->db->get()->row_array();
        }

    }
    