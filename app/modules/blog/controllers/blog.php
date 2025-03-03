<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class blog extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "";
        $this->params = [];
        $this->limit_per_page = 10;
    }

    public function index()
    {
        $page        = (int)get("p");
        $page        = ($page > 0) ? ($page - 1) : 0;
        $current_item_lang = get_lang_code_defaut();
        $this->params = [
            'pagination' => [
                'limit'  => $this->limit_per_page,
                'start'  => $page * $this->limit_per_page,
            ],
            'lang_code' => $current_item_lang->code,
        ];
        $items = $this->main_model->list_items( $this->params, ['task' => 'list-items']);
        $data = [
            'items' => $items,
            "from"  => $page * $this->limit_per_page,
            'lang_code' => $current_item_lang->code,
            "pagination"          => create_pagination([
                'base_url'         => cn($this->controller_name),
                'per_page'         => $this->limit_per_page,
                'query_string'     => $_GET, //$_GET 
                'total_rows'       => $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']),
            ])
        ];
        $this->template->set_layout('blog');
        $this->template->build("index", $data);
    }

    public function detail($url_slug = ""){
		// $url_slug = addslashes(trim($url_slug));
        $current_item_lang = get_lang_code_defaut();
        $params_item = [
            'status' => 1,
            'url_slug' => $url_slug,
            'lang_code' => $current_item_lang->code
        ];
        $item = $this->main_model->get_item($params_item, ['task' => 'item-detail']);
        if(empty($item)){
			redirect(cn('blog'));
		}
        // last posts
        $params_last_posts = [
            'status'           => 1,
            'limit'            => 2,
            'current_id_post'  => $item['id'],
            'lang_code'        => $current_item_lang->code
        ];
        $items_last_posts = $this->main_model->list_items($params_last_posts, ['task' => 'list-items-last-post']);
       
        // Related Posts
        $params_related_posts = [
            'status'           => 1,
            'limit'            => 2,
            'current_id_post'  => $item['id'],
            'cate_id'          => $item['cate_id'],
            'lang_code'        => $current_item_lang->code
        ];
        $items_related_posts = $this->main_model->list_items($params_related_posts, ['task' => 'list-items-related-post']);
        // count-items-by-category
        $params_count_posts_by_category = [
            'status'           => 1,
            'lang_code'        => $current_item_lang->code
        ];
        $count_items_by_category = $this->main_model->count_items( $params_count_posts_by_category, ['task' => 'count-items-by-category']);
		$data = array(
			"module"                   => get_class($this),
			"item"                     => $item,
			"lang_code"                => $current_item_lang->code,
			"items_last_posts"         => $items_last_posts,
			"items_related_posts"      => $items_related_posts,
			"count_items_by_category"  => $count_items_by_category,
			"page_title"               => $item['name'],
			"page_meta_keywords"       => $item['meta_keywords'],
			"page_meta_description"    => $item['meta_description'],
		);
		$this->template->set_layout('blog');
        $this->template->build("single_post", $data);
	}

    public function category($url_slug = "")
    {

        $page        = (int)get("p");
        $page        = ($page > 0) ? ($page - 1) : 0;
        $current_item_lang = get_lang_code_defaut();
        $this->params = [
            'pagination' => [
                'limit'  => $this->limit_per_page,
                'start'  => $page * $this->limit_per_page,
            ],
            'lang_code' => $current_item_lang->code,
            'category_url_slug' => strip_tags($url_slug),
        ];
        $items = $this->main_model->list_items( $this->params, ['task' => 'list-items']);
        if (!$items) {
            cn('blog');
        }
        $data = [
            'items' => $items,
            "from"  => $page * $this->limit_per_page,
            'lang_code' => $current_item_lang->code,
            "pagination"          => create_pagination([
                'base_url'         => cn($this->controller_name),
                'per_page'         => $this->limit_per_page,
                'query_string'     => $_GET, //$_GET 
                'total_rows'       => $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']),
            ])
        ];
        $this->template->set_layout('blog');
        $this->template->build("index", $data);
    }
}
