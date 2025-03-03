<?php
defined('BASEPATH') or exit('No direct script access allowed');

class blog_posts_model extends MY_Model
{

    protected $tb_users;
    protected $tb_main;
    protected $tb_services;
    protected $filter_accepted;
    protected $field_search_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main = BLOG_POSTS;
        $this->tb_blog_categories = BLOG_CATEGORIES;
        $this->tb_blog_posts_lang = BLOG_POSTS_LANG;
        $this->filter_accepted = array_keys(app_config('config')['status']['default']);
        $this->field_search_accepted = app_config('config')['search']['blog_posts'];
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;

        if ($option['task'] == 'list-items') {
            $this->db->select('bp.id, bp.ids, bp.name, bp.cate_id, , bp.status, bp.created, bp.changed, bp.released');
            $this->db->select('c.name as category_name');
            $this->db->from($this->tb_main . " bp");
            $this->db->join($this->tb_blog_categories . " c", "c.id = bp.cate_id", 'left');

            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        $column = ($column == 'name') ? 'bp.' . $column : 'bp.' . $column;
                        if ($i == 1) {
                            $this->db->like($column, $params['search']['query']);
                        } elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']);
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $column = ($params['search']['field'] == 'name') ? 'bp.' . $params['search']['field'] : 'bp.' . $params['search']['field'];
                $this->db->like($column, $params['search']['query']);
            }

            // Sort By
            if ($params['sort_by']['cate_id'] != "") {
                $this->db->where('bp.cate_id', $params['sort_by']['cate_id']);
            }
            $this->db->order_by("c.sort", 'ASC');
            $query = $this->db->get();
            $result = $query->result_array();
            if ($result) {
                $result = group_by_criteria($result, 'category_name');
            }
        }
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;

        if ($option['task'] == 'get-item') {
            $result = $this->get("id, ids, name, cate_id, name, url_slug, image, sort, status, released", $this->tb_main, ['id' => $params['id']], '', '', true);
        }
        // get-item-post-lang
        if ($option['task'] == 'get-item-post-lang') {
            $result = $this->get("id, ids, post_id, lang_code, name, content, meta_keywords, meta_description", $this->tb_blog_posts_lang, $params, '', '', true);
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
                $this->db->delete($this->tb_blog_posts_lang, ["post_id" => $params['id']]);
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
                // insert to post
                $url_slug = strtolower(url_title(post("name"), 'dash'));
                $exist_item = $this->get('id', $this->tb_main, ['url_slug' => $url_slug]);
                if (!empty($exist_item)) {
                    $url_slug += random(1,10);
                }
                $data_item_post = [
                    "ids"      => ids(),
                    "name"     => post("name"),
                    "cate_id"  => post("category"),
                    "status"   => (int) post("status"),
                    "image"    => post("image"),
                    "url_slug" => $url_slug,
                    "released" => date("Y-m-d H:i:s", strtotime(str_replace('/', '-', post('released')))),
                    "created"  => NOW,
                    "changed"  => NOW,
                ];
                $this->db->insert($this->tb_main, $data_item_post);
                // insert to post lang item_lang_code
                $data_item_post_lang = [
                    "ids"              => ids(),
                    "name"             => post("name"),
                    "post_id"          => $this->db->insert_id(),
                    "lang_code"        => post("item_lang_code"),
                    "content"          => $this->input->post('content', false),
                    "meta_keywords"    => post('meta_keywords'),
                    "meta_description" => post('meta_description'),
                    "created"          => NOW,
                    "changed"          => NOW,
                ];
                $this->db->insert($this->tb_blog_posts_lang, $data_item_post_lang);
                return ["status" => "success", "message" => 'Added a new post successfully'];
                break;

            case 'edit-item':
                // Update to post
                $url_slug = strtolower(url_title(post("url_slug"), 'dash'));
                $exist_item = $this->get('id', $this->tb_main, ['url_slug' => $url_slug, 'id !=' => post('id')]);
                if (!empty($exist_item)) {
                    return ["status" => "error", "message" => 'A url slug with this title does already exist! Please choose another URL slug'];
                }
                $data_item_post = [
                    "ids"      => ids(),
                    "cate_id"  => post("category"),
                    "status"   => (int) post("status"),
                    "image"    => post("image"),
                    "url_slug" => $url_slug,
                    "released" => date("Y-m-d H:i:s", strtotime(str_replace('/', '-', post('released')))),
                    "changed"  => NOW,
                ];
                $exits_item_default_lang = $this->get('id', $this->tb_language_list, ['is_default' => 1 ,'status' => 1, 'code' => post("item_lang_code")]);
                if ($exits_item_default_lang) {
                    $data_item_post['name']     = post("name");
                }
                $this->db->update($this->tb_main, $data_item_post, ['id' => post('id')]);
                // Update post lang or insert new item_lang_code by language
                $data_item_post_lang = [
                    "ids"              => ids(),
                    "name"             => post("name"),
                    "lang_code"        => post("item_lang_code"),
                    "content"          => $this->input->post('content', false),
                    "meta_keywords"    => post('meta_keywords'),
                    "meta_description" => post('meta_description'),
                    "changed"          => NOW,
                ];
                $exist_item_post_lang = $this->get('id', $this->tb_blog_posts_lang, ['lang_code' => post("item_lang_code"), 'post_id' => post('id')]);
                if (empty($exist_item_post_lang)) {
                    $data_item_post_lang['post_id'] = post('id');
                    $data_item_post_lang['ids']     = ids();
                    $data_item_post_lang['created'] = NOW;
                    $this->db->insert($this->tb_blog_posts_lang, $data_item_post_lang);
                } else {
                    $this->db->update($this->tb_blog_posts_lang, $data_item_post_lang, ['id' => post('id_post_lang')]);
                }
                return ["status" => "success", "message" => 'Update successfully'];
                break;

            case 'change-status':
                $this->db->update($this->tb_main, ['status' => $params['status'], 'changed' => NOW], ["id" => $params['id']]);
                // Related Service
                if (!$params['status']) {
                    $this->db->where_in('cate_id', $params['id']);
                    $this->db->update($this->tb_services, ['status' => 0]);
                }
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

                        // Related Service
                        $this->db->where_in('cate_id', $arr_ids);
                        $this->db->delete($this->tb_services);

                        return ["status" => "success", "message" => 'Update successfully'];
                        break;
                    case 'deactive':
                        // Category
                        $this->db->where_in('id', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 0]);

                        // Related Services
                        $this->db->where_in('cate_id', $arr_ids);
                        $this->db->update($this->tb_services, ['status' => 0]);

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
