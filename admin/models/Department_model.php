<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Department_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_department($pagingParams = array())
        {
            $session_site_id = get_session_site_id();

            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);
            $this->db->select('d.*');
            $this->db->from('department as d');
            $this->db->where('d.site_id', $session_site_id);

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
                $this->db->like('name', $search);
            }
            $return = $this->get_with_count(NULL, $pagingParams['records_per_page'], $pagingParams['offset']);
            return $return;
        }

        public function get_department_by_id($department_id)
        {
            $return = NULL;
            if (!empty($department_id))
            {
                $this->db->select('d.*');
                $this->db->from('department as d');
                $this->db->where('department_id', $department_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function save_department($dataValue)
        {
            $return = null;
            if (!empty($dataValue))
            {

                if (!empty($dataValue['department_id']))
                {
                    $this->db->where('department_id', $dataValue['department_id']);
                    $this->db->update('department', $dataValue);
                    $return = $dataValue['department_id'];
                }
                else
                {
                    $this->db->insert('department', $dataValue);
                    $return = $this->db->insert_id();
                }
            }
            return $return;
        }

        public function delete_department_by_id($department_id)
        {
            $this->db->where('department_id', $department_id);
            $res = $this->db->delete('department');
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_department_array()
        {
            $session_site_id = get_session_site_id();
            $data = array();
            $this->db->select('*');
            $this->db->from('department');
            $this->db->where('site_id', $session_site_id);
            $this->db->order_by('name');
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['department_id']] = $row['name'];
            }
            return $data;
        }

        public function get_department_status($deptid)
        {
            $return = NULL;
            if (!empty($deptid))
            {

                $this->db->select('*');
                $this->db->from('reader');
                $this->db->where('department_id', $deptid);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

    }
    