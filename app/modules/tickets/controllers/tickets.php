<?php
defined('BASEPATH') or exit('No direct script access allowed');

class tickets extends My_UserController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "";
        $this->params = [];
        $this->columns = [];
    }

    public function index()
    {
        $page = (int) get("p");
        $page = ($page > 0) ? ($page - 1) : 0;
        if (in_array($this->controller_name, ['orders', 'dripfeed', 'subscriptions'])) {
            $filter_status = (isset($_GET['status'])) ? get('status') : 'all';
        } else {
            $filter_status = (isset($_GET['status'])) ? (int) get('status') : '3';
        }
        $this->params = [
            'pagination' => [
                'limit' => $this->limit_per_page,
                'start' => $page * $this->limit_per_page,
            ],
            'filter' => ['status' => $filter_status],
            'search' => ['query' => get('query'), 'field' => get('field')],
        ];
        $items = $this->main_model->list_items($this->params, ['task' => 'list-items']);
        $items_status_count = $this->main_model->count_items($this->params, ['task' => 'count-items-group-by-status']);
        $data = array(
            "controller_name" => $this->controller_name,
            "params" => $this->params,
            "columns" => $this->columns,
            "items" => $items,
            "items_status_count" => $items_status_count,
            "from" => $page * $this->limit_per_page,
            "pagination" => create_pagination([
                'base_url' => cn($this->controller_name),
                'per_page' => $this->limit_per_page,
                'query_string' => $_GET, //$_GET
                'total_rows' => $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']),
            ]),
        );
        $this->template->set_layout('user');
        $this->template->build($this->path_views . 'index', $data);
    }

    public function add()
    {
        $data = array(
            "controller_name" => $this->controller_name,
        );
        $this->load->view('add', $data);
    }

    public function view($id = "")
    {
        $item = $this->main_model->get_item(['id' => (int)$id], ['task' => 'view-get-item']);
        if (!$item) redirect(cn($this->controller_name));
        $items_ticket_message = $this->main_model->list_items(['ticket_id' => $id], ['task' => 'list-items-ticket-message']);
        $data = array(
            "controller_name"       => $this->controller_name,
            "item"                  => $item,
            "items_ticket_message"  => $items_ticket_message,
        );
        $this->template->set_layout('user');
        $this->template->build('view', $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(cn($this->controller_name));
        }

        $items_pending_number = $this->main_model->count_items(['status' => 'pending'], ['task' => 'count-items-pending']);
        $default_pending_ticket_per_user = get_option('default_pending_ticket_per_user', 2);
        if ($items_pending_number >= $default_pending_ticket_per_user && $default_pending_ticket_per_user != 0) {
            _validation('error', 'The number of pending tickets has been limited');
        }
        
        $subject     = post("subject");
        $description = $this->input->post('description', true);
        $description = strip_tags($description);

        $this->form_validation->set_rules('subject', 'subject', 'trim|required|xss_clean',[
            'required' => lang("subject_is_required")
        ]);

        switch ($subject) {
            case 'subject_order':
                $subject = lang("Order");
                $request = post("request");
                $orderid = post("orderid");
                $this->form_validation->set_rules('request', 'request', 'trim|required|xss_clean',[
                    'required' => lang("please_choose_a_request")
                ]);
                $this->form_validation->set_rules('orderid', 'orderid', 'trim|required|xss_clean',[
                    'required' => lang("order_id_field_is_required")
                ]);
                switch ($request) {
                    case 'refill':
                        $request = lang("Refill");
                        break;
                    case 'cancellation':
                        $request = lang("Cancellation");
                        break;
                    case 'speed_up':
                        $request = lang("Speed_Up");
                        break;
                    default:
                        $request = lang("Other");
                        break;
                }
                $subject = $subject . " - " . $request . " - " . $orderid;
                break;

            case 'subject_payment':
                $subject = "Payment";
                $payment = post("payment");
                $transaction_id = post("transaction_id");
                $this->form_validation->set_rules('payment', 'payment', 'trim|required|xss_clean',[
                    'required' => lang("please_choose_a_payment_type")
                ]);
                $this->form_validation->set_rules('transaction_id', 'transaction_id', 'trim|required|xss_clean',[
                    'required' => lang("transaction_id_field_is_required")
                ]);

                switch ($payment) {
                    case 'paypal':
                        $payment = lang("Paypal");
                        break;
                    case 'stripe':
                        $payment = lang("Stripe");
                        break;
                    case 'twocheckout':
                        $payment = lang("2Checkout");
                        break;
                    default:
                        $payment = lang("Other");
                        break;
                }
                $subject = $subject . " - " . $payment . " - " . $transaction_id;

                break;

            case 'subject_service':
                $subject = lang("Service");
                break;

            default:
                $subject = lang("Other");
                break;
        }
        $this->form_validation->set_rules('description', 'description', 'trim|required|xss_clean',[
            'required' => lang("description_is_required")
        ]);
        if (!$this->form_validation->run()) _validation('error', validation_errors());
        $this->params = [
            'subject'     => $subject,
            'description' => $description,
        ];
        $response = $this->main_model->save_item($this->params, ['task' => 'add-item']);
        ms($response);
    }
    
    public function store_message()
    {
        if (!$this->input->is_ajax_request()) redirect(cn($this->controller_name));
        $this->form_validation->set_rules('message', 'message', 'trim|required|xss_clean', [
            'required' => lang('message_is_required')
        ]);
        if (!$this->form_validation->run()) _validation('error', validation_errors());
        if(!$this->input->post('ids')) _validation('error', lang("There_was_an_error_processing_your_request_Please_try_again_later"));
        $task   = 'add-item-ticket-massage';
        $response = $this->main_model->save_item( null, ['task' => $task]);
        ms($response);
    }
}
