<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Command_capacity_profile_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_profiles($paging_params = array())
        {

            $this->db->select('rcl.*');
            $this->db->from('reader_command_capacity_profile as rcl');
            $this->db->order_by('rcl.profile_id', 'ASC');

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

                $this->db->like('profile_name', $search);
            }


            $return = $this->get_with_count(null, $paging_params['records_per_page'], $paging_params['offset']);
            return $return;
        }

        public function get_profile_by_id($profile_id)
        {

            $return = NULL;
            if (!empty($profile_id))
            {
                $this->db->select('r.*');
                $this->db->from('reader_command_capacity_profile as r');
                $this->db->where('profile_id', $profile_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function save_command_capacity_profile($dataValue)
        {
            $return = null;
            if (!empty($dataValue))
            {

                if (!empty($dataValue['profile_id']))
                {
                    $this->db->where('profile_id', $dataValue['profile_id']);
                    $this->db->update('reader_command_capacity_profile', $dataValue);
                    $return = $dataValue['profile_id'];
                }
                else
                {
                    $this->db->insert('reader_command_capacity_profile', $dataValue);
                    $return = $this->db->insert_id();
                }
            }
            return $return;
        }

        public function delete_profile_by_id($profile_id)
        {
            $this->db->where('profile_id', $profile_id);
            $res = $this->db->delete('reader_command_capacity_profile');
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }
        
        public function get_profile_array()
        {
            $data = array();
            $this->db->select('*');
            $this->db->from('reader_command_capacity_profile');
            $this->db->order_by('profile_name');
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['profile_id']] = $row['profile_name'];
            }
            return $data;
        }
        
        public function get_profile_by_reader_id($reader_id)
        {

            $return = NULL;
            if (!empty($reader_id))
            {
                $this->db->select('r.*');
                $this->db->from('reader_command_capacity_profile as r');
                $this->db->join('reader as re', 're.profile_id = r.profile_id', 'left');
                $this->db->where('re.reader_id', $reader_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

    }
    