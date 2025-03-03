<?php
defined('BASEPATH') or exit('No direct script access allowed');

class tickets_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->tb_main = TICKETS;
        $this->tb_staff = STAFFS;
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'list-items') {
            $this->db->select('id, ids, uid, subject, user_read, created, changed, status');
            $this->db->from($this->tb_main);
            $this->db->where('uid', session('uid'));

            //Search
            if ($params['search']['query'] != '') {
                $field_value = $this->db->escape_like_str($params['search']['query']);
                $where_like = "(`id` LIKE '%" . $field_value . "%' ESCAPE '!' OR `description` LIKE '%" . $field_value . "%' ESCAPE '!' OR `subject` LIKE '%" . $field_value . "%' ESCAPE '!')";
                $this->db->where($where_like);
            }

            $this->db->order_by("FIELD ( status, 'answered', 'pending', 'closed')");
            $this->db->order_by('changed', 'DESC');
            if ($params['pagination']['limit'] != "" && $params['pagination']['start'] >= 0) {
                $this->db->limit($params['pagination']['limit'], $params['pagination']['start']);
            }
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-ticket-message') {
            $this->db->select('tm.id, tm.ids, tm.uid, tm.author, tm.message, tm.support, tm.created');
            $this->db->select('u.first_name, u.last_name');
            $this->db->from($this->tb_ticket_message . ' tm');
            $this->db->join($this->tb_users . " u", "tm.uid = u.id", 'left');
            $this->db->where('tm.ticket_id', $params['ticket_id']);
            $this->db->order_by('tm.id', 'DESC');
            $query = $this->db->get();
            $result = $query->result_array();
        }

        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;
        // Count items for pagination
        if ($option['task'] == 'count-items-for-pagination') {
            $this->db->select('id');
            $this->db->from($this->tb_main);
            $this->db->where('uid', session('uid'));
            //Search
            if ($params['search']['query'] != '') {
                $field_value = $this->db->escape_like_str($params['search']['query']);
                $where_like = "(`id` LIKE '%" . $field_value . "%' ESCAPE '!' OR `description` LIKE '%" . $field_value . "%' ESCAPE '!' OR `subject` LIKE '%" . $field_value . "%' ESCAPE '!')";
                $this->db->where($where_like);
            }
            $query = $this->db->get();
            $result = $query->num_rows();
        }
        // Count items: pending
        if ($option['task'] == 'count-items-pending') {
            $this->db->select('id');
            $this->db->from($this->tb_main);
            $this->db->where('uid', session('uid'));
            $this->db->where_in('status', ['pending']);
            $query = $this->db->get();
            $result = $query->num_rows();
        }
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item') {
            $result = $this->get("*", $this->tb_main, ['id' => $params['id']], '', '', true);
        }

        if ($option['task'] == 'view-get-item') {
            $this->db->select('tk.id, tk.ids, tk.uid, tk.subject, tk.description, tk.status, tk.created, tk.created_by');
            $this->db->select('u.email, u.first_name, u.last_name');
            $this->db->from($this->tb_main . ' tk');
            $this->db->join($this->tb_users . " u", "tk.uid = u.id", 'left');
            $this->db->where('tk.id', $params['id']);
            $this->db->where('tk.uid', session('uid'));
            $query = $this->db->get();
            $result = $query->row_array();
            $data_item = [
                'user_read' => 0,
                'changed' => NOW,
            ];
            $this->db->update($this->tb_main, $data_item, ['id' => $params['id']]);
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'add-item') {
            $data = array(
                "ids" => ids(),
                "uid" => session('uid'),
                "subject" => $params['subject'],
                "description" => $params['description'],
                'user_read' => 0,
                'admin_read' => 1,
                "changed" => NOW,
                "created" => NOW,
            );
            $this->db->insert($this->tb_main, $data);
            if ($this->db->affected_rows() > 0) {
                // Send notice to admin with new Ticket
                if (get_option('is_ticket_notice_email_admin', 0)) {
                    $ticket_id = $this->db->insert_id();
                    $author = $_SESSION['user_current_info']['first_name'] . ' ' . $_SESSION['user_current_info']['last_name'];
                    $mail_params = [
                        'template' => [
                            'subject' => "{{website_name}}" . " - New Ticket #" . $ticket_id . " - [" . $params['subject'] . "]",
                            'message' => $params['description'],
                            'type' => 'default',
                        ],
                        'from_email_data' => [
                            'from_email' => $_SESSION['user_current_info']['email'],
                            'from_email_name' => $author,
                        ],
                    ];
                    $this->send_notice_mail($mail_params);
                }
                return ["status" => "success", "message" => lang("ticket_created_successfully")];
            } else {
                return ["error" => "success", "message" => lang("There_was_an_error_processing_your_request_Please_try_again_later")];
            }
        }

        if ($option = 'add-item-ticket-massage') {
            $item = $this->get('id, ids, uid, subject', $this->tb_main, ['ids' => post('ids')], '', '', true);
            if (!$item) {
                return ["status" => "success", "message" => 'There was some wrong with your request'];
            }

            $data_item = [
                'status' => 'pending',
                'user_read' => 0,
                'admin_read' => 1,
                'changed' => NOW,
            ];
            $author = $_SESSION['user_current_info']['first_name'] . ' ' . $_SESSION['user_current_info']['last_name'];
            $data_item_ticket_message = [
                'ids' => ids(),
                'message' => $this->input->post('message', true),
                'uid' => session('uid'),
                "author" => $author,
                "support" => 0,
                'ticket_id' => $item['id'],
                'created' => NOW,
                'changed' => NOW,
            ];
            $this->db->update($this->tb_main, $data_item, ['id' => $item['id']]);
            $this->db->insert($this->tb_ticket_message, $data_item_ticket_message);
            // Send notice to admin when client reply
            if (get_option('is_ticket_notice_email_admin', 0)) {
                $mail_params = [
                    'template' => [
                        'subject' => "{{website_name}}" . " - Relied Ticket #" . $item['id'] . " - [" . $item['subject'] . "]",
                        'message' => $data_item_ticket_message['message'],
                        'type' => 'default',
                    ],
                    'from_email_data' => [
                        'from_email' => $_SESSION['user_current_info']['email'],
                        'from_email_name' => $author,
                    ],
                ];
                $this->send_notice_mail($mail_params);
            }
            return ["status" => "success", "message" => lang("Update_successfully")];
        }
        return $result;
    }

    private function send_notice_mail($params = [], $option = [])
    {
        $staff_mail = $this->get("id, email", $this->tb_staff, [], "id", "ASC")->email;
        if ($staff_mail == "") {
            return ["status" => "error", "message" => lang("There_was_an_error_processing_your_request_Please_try_again_later")];
        }
        $send_message = $this->send_mail_template($params['template'], $staff_mail, $params['from_email_data']);
        if ($send_message) {
            send_mail_error_log(["status" => "error", "message" => $send_message]);
        }
    }
}
