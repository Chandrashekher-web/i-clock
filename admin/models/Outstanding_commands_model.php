<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Outstanding_commands_model extends My_Model
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_all_outstanding_commands($pagingParams = array(), $conditions_arr = array())
        {

            $is_super_admin = is_super_admin();
            $session_site_id = get_session_site_id();
            $where_condition = '';
            $this->db->select('SQL_CALC_FOUND_ROWS 1', false);
            $this->db->select('rc.*,r.sn,r.name as reader_name, s.name as site_name');
            $this->db->from('reader_command as rc');
            $this->db->join('reader as r', 'rc.reader_id = r.reader_id', 'left');
            $this->db->join('site as s', 's.site_id = r.site_id', 'left');
            if ($is_super_admin)
            {
                if (!empty($conditions_arr['site_id']))
                {
                    $this->db->where('r.site_id', $conditions_arr['site_id']);
                }
            }
            else
            {
                $this->db->where('r.site_id', $session_site_id);
            }
            if (!empty($conditions_arr['reader_id']))
            {
                $this->db->where('rc.reader_id', $conditions_arr['reader_id']);
            }

            if (!empty($conditions_arr))
            {
                if (!empty($conditions_arr['commands_filter']))
                {
                    foreach ($conditions_arr['commands_filter'] as $key => $filter_field)
                    {
                        $condition = (!empty($conditions_arr['filter_condition'][($key - 1)])) ? $conditions_arr['filter_condition'][($key - 1)] : "";
                        $field_name = !empty($conditions_arr['commands_filter'][($key)]) ? $conditions_arr['commands_filter'][($key)] : "";
                        $date_from = !empty($conditions_arr['date_from']) ? $conditions_arr['date_from'] : "";
                        $date_to = !empty($conditions_arr['date_to']) ? $conditions_arr['date_to'] : "";
                        $date = !empty($conditions_arr['date'][($key)]) ? $conditions_arr['date'][($key)] : "";
                        $operator = !empty($conditions_arr['filter_operator'][($key)]) ? $conditions_arr['filter_operator'][($key)] : "";
                        $date_operator = !empty($conditions_arr['date_filter_operator'][($key)]) ? $conditions_arr['date_filter_operator'][($key)] : "";
                        $status = !empty($conditions_arr['status'][($key)]) ? $conditions_arr['status'][($key)] : "";
                        $value = !empty($conditions_arr['filter_value'][($key)]) ? $conditions_arr['filter_value'][($key)] : "";

                        if ($operator == "contains")
                        {
                            $string = " LIKE '%" . addslashes($value) . "%'";
                        }
                        elseif ($operator == "does_not_contains")
                        {
                            $string = " NOT LIKE '%" . addslashes($value) . "%' OR " . $field_name . " IS NULL ";
                        }
                        elseif ($operator == "starts_with")
                        {
                            $string = " LIKE '" . addslashes($value) . "%'";
                        }
                        elseif ($operator == "does_not_start_with")
                        {
                            $string = " NOT LIKE '" . addslashes($value) . "%' OR " . $field_name . " IS NULL ";
                        }
                        elseif ($operator == "ends_with")
                        {
                            $string = " LIKE '%" . addslashes($value) . "'";
                        }
                        elseif ($operator == "does_not_end_with")
                        {
                            $string = " NOT LIKE '%" . addslashes($value) . "' OR " . $field_name . " IS NULL ";
                        }
                        elseif ($operator == "equals_to")
                        {
                            $string = " = '" . addslashes($value) . "'";
                        }
                        elseif ($operator == "not_equal_to")
                        {
                            $string = " <>  '" . addslashes($value) . "'";
                        }
                        else if ($field_name == 'date')
                        {
                            $field_name = "DATE_FORMAT(rc.created_at,'%Y-%m-%d')";
                            $new_date = date("Y-m-d", strtotime(str_replace('/', '-', $date)));
                            if ($date_operator == "equals_to")
                            {
                                $string = " = '" . $new_date . "'";
                            }
                            else if ($date_operator == "not_equal_to")
                            {
                                $string = " != '" . $new_date . "'";
                            }
                            else if ($date_operator == "less_than")
                            {
                                $string = " < '" . $new_date . "'";
                            }
                            else if ($date_operator == "less_than_or_equal_to")
                            {
                                $string = " <= '" . $new_date . "'";
                            }
                            else if ($date_operator == "greater_than")
                            {
                                $string = " > '" . $new_date . "'";
                            }
                            else if ($date_operator == "greater_than_or_equal_to")
                            {
                                $string = " >= '" . $new_date . "'";
                            }
                        }
                        else if ($field_name == 'status')
                        {
                            $field_name = "status";
                            if ($status == "Active")
                            {
                                $string = " = '" . $status . "'";
                            }
                            else if ($status == "Inactive")
                            {
                                $string = " = '" . $status . "'";
                            }
                        }
                        else
                        {
                            $string = "";
                        }

                        if (!empty($field_name) && !empty($string))
                        {
                            $where_condition .= $condition . " (" . $field_name . " " . $string . " ) ";
                        }
                    }
                }
            }

            if (!empty($where_condition))
            {
                $where = "( " . $where_condition . " )";

                $this->db->where($where, NULL, FALSE);
            }

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
                $this->db->like('rc.command', $search);
            }
            if (!empty($pagingParams['records_per_page']) || !empty($pagingParams['offset']))
            {
                $return = $this->get_with_count(NULL, $pagingParams['records_per_page'], $pagingParams['offset']);
            }
            else
            {
                $return = $this->get_with_count();
            }
//            p($this->db->last_query());
            return $return;
        }

        public function delete_outstanding_commands_by_ids($commands_ids)
        {
            $this->db->where_in('command_id', $commands_ids);
            $res = $this->db->delete('reader_command');

            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

        public function update_outstanding_commands_by_ids($commands_ids, $status)
        {
            $this->db->set('status', $status);
            $this->db->where_in('command_id', $commands_ids);
            $res = $this->db->update('reader_command');

            if ($res)
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }

    }
    