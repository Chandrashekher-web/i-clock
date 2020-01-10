<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Reader_access_groups_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_reader_access_groups($pagingParams = array())
        {
            $session_site_id = get_session_site_id();
            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);

            $this->db->select('rag.*,group_concat(DISTINCT(IR.name)) as in_reader,group_concat(DISTINCT(OR.name)) as out_reader,group_concat(DISTINCT(ER.name)) as exit_reader');
            $this->db->from('reader_access_groups as rag');
            $this->db->join('in_reader_trans as irt', 'rag.reader_access_groups_id=irt.reader_access_groups_id', 'left');
            $this->db->join('out_reader_trans as ort', 'rag.reader_access_groups_id=ort.reader_access_groups_id', 'left');
            $this->db->join('exit_reader_trans as ert', 'rag.reader_access_groups_id=ert.reader_access_groups_id', 'left');
            $this->db->join('reader as IR', 'irt.in_reader=IR.reader_id', 'left');
            $this->db->join('reader as OR', 'ort.out_reader=OR.reader_id', 'left');
            $this->db->join('reader as ER', 'ert.exit_reader=ER.reader_id', 'left');
            $this->db->where('rag.site_id', $session_site_id);
            $this->db->group_by('rag.reader_access_groups_id');

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
                $this->db->group_start();
                $this->db->like('rag.description', $search);
                $this->db->or_like('irt.in_reader', $search);
                $this->db->or_like('ort.out_reader', $search);
                $this->db->or_like('ert.exit_reader', $search);
                $this->db->group_end();
            }
            $return = $this->get_with_count(NULL, $pagingParams['records_per_page'], $pagingParams['offset']);
            // p($this->db->last_query());
            return $return;
        }

        public function save_reader_access_groups($dataValue, $in_reader_trans_data, $out_reader_trans_data, $exit_reader_trans_data, $time_zone_trans_data)
        {
            $reader_access_groups_id = null;
            if (!empty($dataValue))
            {
                if (!empty($dataValue['reader_access_groups_id']))
                {

                    $this->db->where('reader_access_groups_id', $dataValue['reader_access_groups_id']);
                    $this->db->update('reader_access_groups', $dataValue);
                    $reader_access_groups_id = $dataValue['reader_access_groups_id'];
                    $this->db->trans_start();
                    $this->db->where('reader_access_groups_id', $dataValue['reader_access_groups_id']);
                    $this->db->delete('in_reader_trans');

                    //delete from out_reader_trans
                    $this->db->where('reader_access_groups_id', $dataValue['reader_access_groups_id']);
                    $this->db->delete('out_reader_trans');

                    //delete from exit_reader_trans
                    $this->db->where('reader_access_groups_id', $dataValue['reader_access_groups_id']);
                    $this->db->delete('exit_reader_trans');

                    $this->db->where('reader_access_groups_id', $dataValue['reader_access_groups_id']);
                    $this->db->delete('time_zone_trans');

                    $this->save_reader_access_groups_trans($dataValue['reader_access_groups_id'], $in_reader_trans_data, $out_reader_trans_data, $exit_reader_trans_data, $time_zone_trans_data);
                    $this->db->trans_complete();
                }
                else
                {

                    $this->db->insert('reader_access_groups', $dataValue);
                    $reader_access_groups_id = $this->db->insert_id();
                    $this->save_reader_access_groups_trans($reader_access_groups_id, $in_reader_trans_data, $out_reader_trans_data, $exit_reader_trans_data, $time_zone_trans_data);
                }
//                 
            }

            return $reader_access_groups_id;
        }

        public function save_reader_access_groups_trans($reader_access_groups_id, $in_reader_trans_data, $out_reader_trans_data, $exit_reader_trans_data, $time_zone_trans_data)
        {
            $session_site_id = get_session_site_id();
            if (!empty($reader_access_groups_id))
            {
                $this->db->trans_start();
                foreach ($in_reader_trans_data as $key => $in_reader)
                {
                    $in_reader_data["reader_access_groups_id"] = $reader_access_groups_id;
                    $in_reader_data['in_reader'] = $in_reader;
                    $in_reader_data['site_id'] = $session_site_id;
                    $in_reader_data['order_id'] = $key + 1;
                    $this->db->insert('in_reader_trans', $in_reader_data);
                }
                foreach ($out_reader_trans_data as $key => $out_reader)
                {
                    $out_reader_data["reader_access_groups_id"] = $reader_access_groups_id;
                    $out_reader_data['out_reader'] = $out_reader;
                    $out_reader_data['site_id'] = $session_site_id;
                    $out_reader_data['order_id'] = $key + 1;
                    $this->db->insert('out_reader_trans', $out_reader_data);
                }
                foreach ($exit_reader_trans_data as $key => $exit_reader)
                {
                    $exit_reader_data["reader_access_groups_id"] = $reader_access_groups_id;
                    $exit_reader_data['exit_reader'] = $exit_reader;
                    $exit_reader_data['site_id'] = $session_site_id;
                    $exit_reader_data['order_id'] = $key + 1;
                    $this->db->insert('exit_reader_trans', $exit_reader_data);
                }

                foreach ($time_zone_trans_data as $time_zone)
                {
                    $time_zone_data['reader_access_groups_id'] = $reader_access_groups_id;
                    $time_zone_data['time_zone_id'] = $time_zone;
                    $this->db->insert('time_zone_trans', $time_zone_data);
                }
                $this->db->trans_complete();
            }
//                 
        }

        public function delete_reader_access_groups_by_id($reader_access_groups_id)
        {

            $this->db->trans_start();

            //delete from reader_access_groups
            $this->db->where('reader_access_groups_id', $reader_access_groups_id);
            $res = $this->db->delete('reader_access_groups');

            //delete from in_reader_trans
            $this->db->where('reader_access_groups_id', $reader_access_groups_id);
            $this->db->delete('in_reader_trans');

            //delete from out_reader_trans
            $this->db->where('reader_access_groups_id', $reader_access_groups_id);
            $this->db->delete('out_reader_trans');

            //delete from exit_reader_trans
            $this->db->where('reader_access_groups_id', $reader_access_groups_id);
            $this->db->delete('exit_reader_trans');

            $this->db->trans_complete();
            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function get_reader_by_site_id($site_id)
        {
            $data = null;

            if (!empty($site_id))
            {
                $this->db->select('reader_id,name');
                $this->db->from('reader');
                $this->db->where('site_id', $site_id);
                $query = $this->db->get()->result_array();

                if (!empty($query))
                {
                    foreach ($query as $row)
                    {
                        $data[$row['reader_id']] = $row['name'];
                    }
                }
            }
            return $data;
        }

        public function get_reader_access_groups_by_id($reader_access_groups_id)
        {
            $return = null;
            if (!empty($reader_access_groups_id))
            {
                $this->db->select('*');
                $this->db->from('reader_access_groups');
                $this->db->where('reader_access_groups_id', $reader_access_groups_id);
                $return = $this->db->get()->row_array();
            }
            return $return;
        }

        public function get_reader_access_reader_trans_by_id($reader_access_groups_id)
        {
            $data = null;
            if (!empty($reader_access_groups_id))
            {
                $this->db->select('group_concat(in_reader) as in_reader');
                $this->db->where('reader_access_groups_id', $reader_access_groups_id);
                $data['in_reader_trans'] = $this->db->get('in_reader_trans')->row_array();

                $this->db->select('group_concat(out_reader) as out_reader');
                $this->db->where('reader_access_groups_id', $reader_access_groups_id);
                $data['out_reader_trans'] = $this->db->get('out_reader_trans')->row_array();

                $this->db->select('group_concat(exit_reader) as exit_reader');
                $this->db->where('reader_access_groups_id', $reader_access_groups_id);
                $data['exit_reader_trans'] = $this->db->get('exit_reader_trans')->row_array();

                $this->db->select('group_concat(time_zone_id) as time_zone_id');
                $this->db->where('reader_access_groups_id', $reader_access_groups_id);
                $data['time_zone_trans'] = $this->db->get('time_zone_trans')->row_array();
            }
            return $data;
        }

        public function get_reader_access_groups_array($site_id)
        {
            $data = null;
            if (!empty($site_id))
            {
                $this->db->select('code_id,reader_access_groups_id');
                $this->db->where('site_id', $site_id);
                $query = $this->db->get('reader_access_groups')->result_array();
                if (!empty($query))
                {
                    foreach ($query as $row)
                    {
                        $data[$row['code_id']] = $row['code_id'];
                    }
                }
            }
            return $data;
        }

        public function get_reader_by_access_groups($reader_access_groups_id)
        {
            $data = null;
            $session_site_id = get_session_site_id();
             
            if (!empty($reader_access_groups_id))
            {

                $this->db->select('group_concat(in_reader) as in_reader');
                $this->db->from('in_reader_trans as irt');
                $this->db->join('reader_access_groups as rag', 'irt.reader_access_groups_id = rag.reader_access_groups_id', 'left');
                $this->db->where('rag.code_id', $reader_access_groups_id);
                $this->db->where('irt.site_id', $session_site_id);
                $data['in_reader_trans'] = $this->db->get()->row_array();
                
                

                $this->db->select('group_concat(out_reader) as out_reader');
                $this->db->from('out_reader_trans as ort');
                $this->db->join('reader_access_groups as rag', 'ort.reader_access_groups_id = rag.reader_access_groups_id', 'left');
                $this->db->where('rag.code_id', $reader_access_groups_id);
                $this->db->where('ort.site_id', $session_site_id);
                $data['out_reader_trans'] = $this->db->get()->row_array();

                $this->db->select('group_concat(exit_reader) as exit_reader');
                $this->db->from('exit_reader_trans as ert');
                $this->db->join('reader_access_groups as rag', 'ert.reader_access_groups_id = rag.reader_access_groups_id', 'left');
                $this->db->where('rag.code_id', $reader_access_groups_id);
                $this->db->where('ert.site_id', $session_site_id);
                $data['exit_reader_trans'] = $this->db->get()->row_array();
            }
            
            return $data;
        }

        public function get_reader_access_groups($site_id)
        {
            $data = null;
            if (!empty($site_id))
            {
                $this->db->select('code_id,reader_access_groups_id,group_verify_type');
                $this->db->where('site_id', $site_id);
                $data = $this->db->get('reader_access_groups')->result_array();
                if (!empty($query))
                {
                    return $data;
                }
            }
            return $data;
        }

        public function get_reader_access_code($reader_access_groups_id)
        {
            $data = null;
            $session_site_id = get_session_site_id();
            if (!empty($reader_access_groups_id))
            {
                $this->db->select('code_id');
                $this->db->where('reader_access_groups_id', $reader_access_groups_id);
                $this->db->where('site_id', $site_id);
                $data = $this->db->get('reader_access_groups')->row_array();
                if (!empty($data))
                {
                    return $data['code_id'];
                }
            }
            return $data;
        }

        public function get_reader_for_antipass_by_id($reader_access_groups_id)
        {
            $data = null;
            $session_site_id = get_session_site_id();
            if (!empty($reader_access_groups_id))
            {

                $this->db->select('in_reader as reader_id,name as reader_name');
                $this->db->from('in_reader_trans as irt');
                $this->db->join('reader as r', 'irt.in_reader = r.reader_id', 'left');
                $this->db->where('irt.reader_access_groups_id', $reader_access_groups_id);
                $this->db->where('irt.site_id', $session_site_id);
                $this->db->order_by('irt.order_id');
                $data['in_reader'] = $this->db->get()->result_array();

                $this->db->select('out_reader as reader_id,name as reader_name');
                $this->db->from('out_reader_trans as ort');
                $this->db->join('reader as r', 'ort.out_reader = r.reader_id', 'left');
                $this->db->where('ort.reader_access_groups_id', $reader_access_groups_id);
                $this->db->where('ort.site_id', $session_site_id);
                $this->db->order_by('ort.order_id');
                $data['out_reader'] = $this->db->get()->result_array();

                $this->db->select('exit_reader as reader_id,name as reader_name');
                $this->db->from('exit_reader_trans as ert');
                $this->db->join('reader as r', 'ert.exit_reader = r.reader_id', 'left');
                $this->db->where('ert.reader_access_groups_id', $reader_access_groups_id);
                $this->db->where('ert.site_id', $session_site_id);
                $this->db->order_by('ert.order_id');
                $data['exit_reader'] = $this->db->get()->result_array();
            }

            return $data;
        }

        public function setorder($id, $order, $reader_type, $reader_access_groups_id)
        {
            $arr = array(
                "order_id" => $order,
            );
            $this->db->where($reader_type, $id);
            $this->db->where('reader_access_groups_id', $reader_access_groups_id);
            $this->db->update($reader_type . '_trans', $arr);
        }

        function get_antipass_status($reader_access_groups_id)
        {
            $return = array();
            if (!empty($reader_access_groups_id))
            {
                $this->db->select('is_antipass');
                $this->db->from('reader_access_groups');
                $this->db->where('reader_access_groups_id', $reader_access_groups_id);
                $return = $this->db->get()->row_array();
            }

            return $return;
        }

        function update_status($reader_access_groups_id, $status)
        {

            if ($status == 'No')
            {
                $arr_data = array('is_antipass' => 'Yes');
            }
            else
            {
                $arr_data = array('is_antipass' => 'No');
            }
            if (isset($reader_access_groups_id))
            {
                $this->db->where('reader_access_groups_id', $reader_access_groups_id);
                $this->db->update('reader_access_groups', $arr_data);
            }
        }

    }
    