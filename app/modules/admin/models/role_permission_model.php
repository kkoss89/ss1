<?php
defined('BASEPATH') or exit('No direct script access allowed');

class role_permission_model extends MY_Model
{

    protected $tb_users;
    protected $tb_main;
    protected $filter_accepted;
    protected $field_search_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main = ROLE_PERMISSIONS;

        $this->filter_accepted = array_keys(app_config('template')['status']);
        unset($this->filter_accepted['3']);
        $this->field_search_accepted = app_config('config')['search']['role_permission'];
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;

        if ($option['task'] == 'list-items') {
            $this->db->select('id, ids, name, status, permissions, description');
            $this->db->from($this->tb_main);

            // filter
            if ($params['filter']['status'] != 3 && in_array($params['filter']['status'], $this->filter_accepted)) {
                $this->db->where('status', $params['filter']['status']);
            }
            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        if ($i == 1) {
                            $this->db->like($column, $params['search']['query']);
                        } elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']);
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $this->db->like($params['search']['field'], $params['search']['query']);
            }
            $this->db->order_by('id', 'DESC');
            $query = $this->db->get();
            $result = $query->result_array();
        }
        
        // List active in Staffs
        if ($option['task'] == 'staff-active-list-items') {
            $result = $this->fetch("id, ids, permissions, name", $this->tb_main, ['id !=' => 1], '', '', '', '', true);
        }
        
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item') {
            $result = $this->get("id, ids, name, permissions, description, status", $this->tb_main, ['id' => $params['id']], '', '', true);
        }
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'count-items-group-by-status') {
            $this->db->select('count(id) as count, status');
            $this->db->from($this->tb_main);
            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        if ($i == 1) {
                            $this->db->like($column, $params['search']['query']);
                        } elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']);
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $this->db->like($params['search']['field'], $params['search']['query']);
            }

            $this->db->order_by('status', 'DESC');
            $this->db->group_by('status');
            $query = $this->db->get();
            $result = $query->result_array();
        }

        // Count items for pagination
        if ($option['task'] == 'count-items-for-pagination') {
            return null;
        }
        return $result;
    }

    public function delete_item($params = null, $option = null)
    {
        $result = [];
        if ($option['task'] == 'delete-item') {
            $item = $this->get("id, ids", $this->tb_main, ['id' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_main, ["id" => $params['id']]);
                $result = [
                    'status' => 'success',
                    'message' => 'Deleted successfully',
                    "ids" => $item->ids,
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'There was an error processing your request. Please try again later',
                ];
            }
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        switch ($option['task']) {
            case 'add-item':
                $data = [
                    "name" => post("name"),
                    "permissions" => json_encode(post("permissions")),
                    "description" => post("description"),
                    "created" => NOW,
                    "changed" => NOW,
                ];
                $this->db->insert($this->tb_main, $data);
                return ["status" => "success", "message" => 'Update successfully'];
                break;

            case 'edit-item':
                $data = [
                    "name" => post("name"),
                    "permissions" => json_encode(post("permissions")),
                    "description" => post("description"),
                    "changed" => NOW,
                ];
                $this->db->update($this->tb_main, $data, ["id" => post('id')]);
                return ["status" => "success", "message" => 'Update successfully'];
                break;

            case 'change-status':
                $this->db->update($this->tb_main, ['status' => $params['status'], 'changed' => NOW], ["id" => $params['id']]);
                return ["status" => "success", "message" => 'Update successfully'];
                break;

            case 'sort-table':
                $this->db->update_batch($this->tb_main, $params['services'], 'id');
                return ["status" => "success", "message" => 'Update successfully'];
                break;

            case 'change-sort':
                $this->form_validation->set_rules('sort', 'sort', "trim|required|is_natural_no_zero|is_unique[$this->tb_main.sort]");
                if (!$this->form_validation->run()) {
                    _validation('error', strip_tags(validation_errors()));
                }

                $this->db->update($this->tb_main, ['sort' => $params['sort'], 'changed' => NOW], ["id" => $params['id']]);
                return ["status" => "success", "message" => 'Update successfully'];
                break;

            case 'bulk-action':
                if (in_array($params['type'], ['delete', 'deactive', 'active']) && empty($params['ids'])) {
                    return ["status" => "error", "message" => 'Please choose at least one item'];
                }
                $arr_ids = convert_str_number_list_to_array($params['ids']);
                switch ($params['type']) {
                    case 'delete':
                        // Category
                        $this->db->where_in('id', $arr_ids);
                        $this->db->delete($this->tb_main);

                        return ["status" => "success", "message" => 'Update successfully'];
                        break;
                    case 'deactive':
                        // Category
                        $this->db->where_in('id', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 0]);
                        return ["status" => "success", "message" => 'Update successfully'];
                        break;
                    case 'active':
                        $this->db->where_in('id', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 1]);

                        return ["status" => "success", "message" => 'Update successfully'];
                        break;
                }
                break;
        }
    }

    public function save_items($params = null, $option = null)
    {
        if ($option['task'] == 'sort-table') {
            $this->db->update_batch($this->tb_main, $params['items'], 'id');
            return ["status" => "success", "message" => 'Update successfully'];
        }
    }
}
