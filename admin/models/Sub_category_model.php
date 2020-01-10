<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Sub_category_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_sub_category($paging_params = array())
        {

            $this->db->select('cat.*,c.*');
            $this->db->join('category as c', 'cat.category_id=c.category_id', 'left');
            $this->db->from('sub_category as cat');

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

                $this->db->like('cat.sub_category_name', $search);
                $this->db->do_like('c.category_name', $search);
            }


            $return = $this->get_with_count(null, $paging_params['records_per_page'], $paging_params['offset']);
            return $return;
        }

        public function get_sub_category_by_id($sub_category_id)
        {
            $return = NULL;
            if (!empty($sub_category_id))
            {
                $this->db->select('cat.*');
                $this->db->from('sub_category as cat');
                $this->db->where('sub_category_id', $sub_category_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function save_sub_category($dataValue)
        {
            $return = null;
            if (!empty($dataValue))
            {

                if (!empty($dataValue['sub_category_id']))
                {
                    $this->db->where('sub_category_id', $dataValue['sub_category_id']);
                    $this->db->update('sub_category', $dataValue);
                    $return = $dataValue['sub_category_id'];
                }
                else
                {
                    $this->db->insert('sub_category', $dataValue);
                    $return = $this->db->insert_id();
                }
            }
            return $return;
        }

        public function delete_sub_category_by_id($sub_category_id)
        {
            $this->db->where('sub_category_id', $sub_category_id);
            $res = $this->db->delete('sub_category');
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_sub_category_array_by_category_id($category_id)
        {
            $data = NULL;
            if (!empty($category_id))
            {
                $this->db->select('cat.*');
                $this->db->from('sub_category as cat');
                $this->db->where('category_id', $category_id);
                $query = $this->db->get();
                foreach ($query->result_array() as $row)
                {
                    $data[$row['sub_category_id']] = $row['sub_category_name'];
                }
            }
            return $data;
        }

        public function get_sub_category_by_category_id($category_id)
        {
            $return = NULL;
            if (!empty($category_id))
            {
                $this->db->select('cat.*');
                $this->db->from('sub_category as cat');
                $this->db->where('category_id', $category_id);
                $return = $this->db->get()->result_array();
            }
            return $return;
        }

        public function get_sub_category_by_name($sub_category)
        {
            $return = NULL;
            if (!empty($sub_category))
            {
                $this->db->where('sub_category_name', $sub_category);
                $return = $this->db->get('sub_category')->row_array();
                if (!empty($return))
                {
                    $return = $return['sub_category_id'];
                }
            }
            return $return;
        }

    }
    