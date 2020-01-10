<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Time_zone_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_time_zone($pagingParams = array())
        {
            $session_site_id = get_session_site_id();
            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);
            $this->db->select('tz.*');
            $this->db->from('time_zone as tz');
            $this->db->where('tz.site_id', $session_site_id);

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
                $this->db->like('tz.time_zone_code', $search);
                $this->db->or_like('tz.time_zone_name', $search);
            }
            $return = $this->get_with_count(NULL, $pagingParams['records_per_page'], $pagingParams['offset']);
            //                 p($this->db->last_query());
            return $return;
        }

        public function save_time_zone($dataValue)
        {


            $reader_access_groups_id = null;
            if (!empty($dataValue))
            {
                if (!empty($dataValue['time_zone_id']))
                {
                    $session_site_id = get_session_site_id();
                    $this->db->where('time_zone_id', $dataValue['time_zone_id']);
                    $this->db->where('site_id', $session_site_id);
                    $this->db->update('time_zone', $dataValue);
                    $time_zone_id = $dataValue['time_zone_id'];
                }
                else
                {

                    $this->db->insert('time_zone', $dataValue);
                    $time_zone_id = $this->db->insert_id();
                }
            }


            return $reader_access_groups_id;
        }

        public function get_time_zone_by_id($time_zone_id)
        {
            $return = null;
            if (!empty($time_zone_id))
            {
                $session_site_id = get_session_site_id();
                $this->db->where('time_zone_id', $time_zone_id);
                $this->db->where('site_id', $session_site_id);

                $return = $this->db->get('time_zone')->row_array();
            }
            return $return;
        }

        public function delete_time_zone_by_id($time_zone_id)
        {
            $session_site_id = get_session_site_id();
            $this->db->where('time_zone_id', $time_zone_id);
            $this->db->where('site_id', $session_site_id);

            $res = $this->db->delete('time_zone');

            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_time_zone_array($site_id)
        {
            $data = array();
            $this->db->select('time_zone_id,time_zone_name');

            $this->db->where('site_id', $site_id);
            $query = $this->db->get('time_zone');
            foreach ($query->result_array() as $row)
            {
                $data[$row['time_zone_id']] = $row['time_zone_name'];
            }
            return $data;
        }

        public function get_time_zone_by_access_groups($reader_access_id)
        {
            $data = array();
            $this->db->select('tz.*,');
            $this->db->from('time_zone_trans as tzt');
            $this->db->join('time_zone as tz', 'tzt.time_zone_id = tz.time_zone_id', 'left');
            $this->db->join('reader_access_groups as rag', 'tzt.reader_access_groups_id = rag.reader_access_groups_id', 'left');

            $this->db->where('rag.code_id', $reader_access_id);
            $data = $this->db->get()->result_array();
            return $data;
        }

        public function get_access_group_code_by_access_groups_id($reader_access_id)
        {
            $data = array();
            //  $this->db->select('code_id');    
            $this->db->select('reader_access_groups_id');

            $this->db->where('reader_access_groups_id', $reader_access_id);
            $data = $this->db->get('reader_access_groups')->row_array();
            return $data['reader_access_groups_id'];
        }

    }
    