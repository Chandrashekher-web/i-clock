<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Category_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_category($paging_params = array())
        {

            $this->db->select('cat.*');
            $this->db->from('category as cat');
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

                $this->db->like('category_name', $search);
            }


            $return = $this->get_with_count(null, $paging_params['records_per_page'], $paging_params['offset']);
            return $return;
        }

        public function get_category_by_id($category_id)
        {

            $return = NULL;
            if (!empty($category_id))
            {
                $this->db->select('cat.*');
                $this->db->from('category as cat');
                $this->db->where('category_id', $category_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function save_category($dataValue)
        {
            $return = null;
            if (!empty($dataValue))
            {

                if (!empty($dataValue['category_id']))
                {
                    $this->db->where('category_id', $dataValue['category_id']);
                    $this->db->update('category', $dataValue);
                    $return = $dataValue['category_id'];
                }
                else
                {
                    $this->db->insert('category', $dataValue);
                    $return = $this->db->insert_id();
                }
            }
            return $return;
        }

        public function delete_category_by_id($category_id)
        {
            $this->db->where('category_id', $category_id);
            $res = $this->db->delete('category');
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_category_array()
        {
            $data = array();
            $this->db->select('*');
            $this->db->from('category');
            $this->db->order_by('category_name');
            $query = $this->db->get();
            foreach ($query->result_array() as $row)
            {
                $data[$row['category_id']] = $row['category_name'];
            }
            return $data;
        }

        public function get_category_by_name($category)
        {

            $return = NULL;
            if (!empty($category))
            {
                $this->db->where('category_name', $category);
                $return = $this->db->get('category')->row_array();
                if (!empty($return))
                {
                    $return = $return['category_id'];
                }
            }
            return $return;
        }

    }
    