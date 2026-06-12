<?php
require_once(__DIR__ . '/../config.php');
class Master extends DBConnection {
    private $settings;
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }
    public function __destruct(){
        parent::__destruct();
    }

    private function validate_csrf($expected_suffix) {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        $expected = hash('sha256', session_id() . '::' . $expected_suffix);
        return $token === $expected;
    }

    private function capture_err(){
        if(!$this->conn->error)
            return false;
        else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
            return json_encode($resp);
        }
    }

    function save_company(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id','csrf_token'))){
                if(!empty($data)) $data .=",";
                $v = $this->conn->real_escape_string($v);
                $data .= " `{$k}`='{$v}' ";
            }
        }
        $check = $this->conn->query("SELECT * FROM `company_list` where `name` = '".$this->conn->real_escape_string($name)."' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Company Name already exist.";
            return json_encode($resp);
        }
        if(empty($id)){
            $sql = "INSERT INTO `company_list` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `company_list` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success', isset($id) && !empty($id) ? "Company successfully updated." : "New Company successfully saved.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }

    function delete_company(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `company_list` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Company successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function save_fee(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(!in_array($k,array('id','csrf_token'))){
                $v = $this->conn->real_escape_string($v);
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        $check = $this->conn->query("SELECT * FROM `fee_list` where `amount_from` = '{$this->conn->real_escape_string($amount_from)}' and `amount_to` = '{$this->conn->real_escape_string($amount_to)}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
        if($this->capture_err())
            return $this->capture_err();
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Amount Range already exists.";
            return json_encode($resp);
        }
        if(empty($id)){
            $sql = "INSERT INTO `fee_list` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `fee_list` set {$data} where id = '{$id}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            if(empty($id))
                $this->settings->set_flashdata('success',"New Amount Charge/Fee successfully saved.");
            else
                $this->settings->set_flashdata('success',"Amount Charge/Fee successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }

    function delete_fee(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `fee_list` where id = '{$id}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Amount Charge/Fee successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function get_fee(){
        extract($_POST);
        $amount = isset($amount) ? $this->conn->real_escape_string($amount) : 0;
        $qry = $this->conn->query("SELECT * FROM `fee_list` WHERE `amount_from` <= '{$amount}' AND `amount_to` >= '{$amount}' AND `status` = 1 ORDER BY unix_timestamp(`date_created`) DESC LIMIT 1");
        if($qry && $qry->num_rows > 0){
            $res = $qry->fetch_array();
            $resp['status'] = 'success';
            $resp['fee'] = $res['fee'];
            $resp['payable'] = floatval($amount) + floatval($res['fee']);
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'No active fee found for the specified amount.';
        }
        return json_encode($resp);
    }

    function toggle_fee(){
        $user = $this->settings->userdata('login_type');
        if($user != 1){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Unauthorized';
            return json_encode($resp);
        }
        extract($_POST);
        if(!isset($id)){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Fee id is required.';
            return json_encode($resp);
        }
        $status = isset($status) ? (int)$status : 0;
        $update = $this->conn->query("UPDATE `fee_list` set `status` = '{$status}' where id = '{$this->conn->real_escape_string($id)}'");
        if($update){
            $resp['status'] = 'success';
            if($status == 1)
                $this->settings->set_flashdata('success',"Fee/Charge successfully enabled.");
            else
                $this->settings->set_flashdata('success',"Fee/Charge successfully disabled.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function save_gateway(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k =>$v){
            if(in_array($k, array('settings'))){
                $v = $this->conn->real_escape_string($v);
            }else{
                $v = $this->conn->real_escape_string($v);
            }
            if(!in_array($k, array('id','csrf_token'))){
                if(!empty($data)) $data .=",";
                $data .= " `{$k}`='{$v}' ";
            }
        }
        if($this->capture_err()) return $this->capture_err();
        if(empty($id)){
            $sql = "INSERT INTO `payment_gateways` set {$data} ";
            $save = $this->conn->query($sql);
        }else{
            $sql = "UPDATE `payment_gateways` set {$data} where id = '{$this->conn->real_escape_string($id)}' ";
            $save = $this->conn->query($sql);
        }
        if($save){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success', empty($id) ? "New Payment Gateway successfully saved." : "Payment Gateway successfully updated.");
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        return json_encode($resp);
    }

    function delete_gateway(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `payment_gateways` where id = '{$this->conn->real_escape_string($id)}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Payment Gateway successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function save_transaction(){
        if(empty($_POST)) return json_encode(['status'=>'failed','msg'=>'No data submitted']);

        // CSRF check for payment transactions
        $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        $expected = hash('sha256', session_id() . '::payment_form');
        if ($csrf_token !== $expected) {
            return json_encode(['status'=>'failed','msg'=>'Invalid request origin. Please refresh and try again.']);
        }

        $required = ['company_id','account_name','account_number','amount_to_pay','gateway_id'];
        foreach($required as $r) if(!isset($_POST[$r]) || trim($_POST[$r])=='') return json_encode(['status'=>'failed','msg'=>"{$r} is required"]);

        $company_id = (int)$_POST['company_id'];
        $gateway_id = (int)$_POST['gateway_id'];
        $account_name = trim($_POST['account_name']);
        $account_number = trim($_POST['account_number']);
        $amount_to_pay = floatval(str_replace([',',' '],'',$_POST['amount_to_pay']));
        $payment_code = isset($_POST['payment_code']) ? trim($_POST['payment_code']) : null;

        // Idempotency check: prevent duplicate payment_code
        if (!empty($payment_code)) {
            $dup = $this->conn->prepare("SELECT id FROM transaction_list WHERE payment_code = ? LIMIT 1");
            $dup->bind_param('s', $payment_code);
            $dup->execute();
            $dres = $dup->get_result();
            if ($dres && $dres->num_rows > 0) {
                $dup->close();
                return json_encode(['status'=>'failed','msg'=>'This payment has already been processed.']);
            }
            $dup->close();
        }

        $gstmt = $this->conn->prepare("SELECT `type`,`status` FROM `payment_gateways` WHERE id = ? LIMIT 1");
        $gstmt->bind_param('i',$gateway_id);
        $gstmt->execute();
        $gres = $gstmt->get_result();
        if(!$gres || $gres->num_rows <= 0) return json_encode(['status'=>'failed','msg'=>'Selected gateway not found']);
        $gdata = $gres->fetch_assoc();
        if(intval($gdata['status']) != 1) return json_encode(['status'=>'failed','msg'=>'Selected gateway is not active']);
        $gtype = $gdata['type'];
        if($gtype == 'manual' && empty($payment_code)) return json_encode(['status'=>'failed','msg'=>'Payment reference is required for manual gateways']);
        $gstmt->close();

        $computed_fee = 0.00;
        $fq = $this->conn->prepare("SELECT fee FROM fee_list WHERE `amount_from` <= ? AND `amount_to` >= ? AND `status` = 1 ORDER BY unix_timestamp(`date_created`) DESC LIMIT 1");
        $fq->bind_param('dd',$amount_to_pay,$amount_to_pay);
        $fq->execute();
        $fres = $fq->get_result();
        if($fres && $fres->num_rows > 0){
            $computed_fee = floatval($fres->fetch_array()['fee']);
        }
        $fq->close();
        $payable_amount = $amount_to_pay + $computed_fee;

        if(empty($_POST['id'])){
            $prefix = substr(str_shuffle(implode('',range('A','Z'))),0,3);
            do{
                $code = $prefix."-".(sprintf("%'.012d",rand(1,999999999999)));
                $check = $this->conn->prepare("SELECT id FROM transaction_list WHERE tracking_code = ? LIMIT 1");
                $check->bind_param('s',$code);
                $check->execute();
                $cres = $check->get_result();
                $exists = ($cres && $cres->num_rows>0);
                $check->close();
            }while($exists);
        }else{
            $code = trim($_POST['tracking_code']);
        }

        $user_id = (int)$this->settings->userdata('id');

        if(empty($_POST['id'])){
            $status = 'completed';
            $is_notified = 0;
            $col_exists = false;
            $col_q = $this->conn->query("SHOW COLUMNS FROM `transaction_list` LIKE 'is_notified'");
            if($col_q && $col_q->num_rows > 0) $col_exists = true;
            if($col_exists){
                $stmt = $this->conn->prepare("INSERT INTO transaction_list (tracking_code,company_id,gateway_id,account_name,account_number,amount_to_pay,payable_amount,fee,payment_code,user_id,is_notified,`status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param('siissdddsiss', $code, $company_id, $gateway_id, $account_name, $account_number, $amount_to_pay, $payable_amount, $computed_fee, $payment_code, $user_id, $is_notified, $status);
            }else{
                $stmt = $this->conn->prepare("INSERT INTO transaction_list (tracking_code,company_id,gateway_id,account_name,account_number,amount_to_pay,payable_amount,fee,payment_code,user_id) VALUES (?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param('siissdddsi', $code, $company_id, $gateway_id, $account_name, $account_number, $amount_to_pay, $payable_amount, $computed_fee, $payment_code, $user_id);
            }
            if($stmt->execute()){
                $new_id = $stmt->insert_id;
                $resp['status'] = 'success';
                $resp['id'] = $new_id;
                $resp['payable_amount'] = $payable_amount;
                $this->settings->set_flashdata('success'," Transaction's Details Successfully saved.");
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'An error occurred. Error: '.$stmt->error;
            }
            $stmt->close();
        }else{
            $id = (int)$_POST['id'];
            $stmt = $this->conn->prepare("UPDATE transaction_list SET tracking_code=?, company_id=?, gateway_id=?, account_name=?, account_number=?, amount_to_pay=?, payable_amount=?, fee=?, payment_code=? WHERE id = ?");
            $stmt->bind_param('siissdddsi', $code, $company_id, $gateway_id, $account_name, $account_number, $amount_to_pay, $payable_amount, $computed_fee, $payment_code, $id);
            if($stmt->execute()){
                $resp['status'] = 'success';
                $resp['id'] = $id;
                $resp['payable_amount'] = $payable_amount;
                $this->settings->set_flashdata('success'," Transaction's Details Successfully updated.");
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'An error occurred. Error: '.$stmt->error;
            }
            $stmt->close();
        }
        return json_encode($resp);
    }

    function delete_transaction(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM `transaction_list` where id = '{$this->conn->real_escape_string($id)}'");
        if($del){
            $resp['status'] = 'success';
            $this->settings->set_flashdata('success',"Transaction's Details Successfully deleted.");
        }else{
            $resp['status'] = 'failed';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function get_unread_transactions_count(){
        $user = $this->settings->userdata('login_type');
        if($user != 1){
            return json_encode(['status'=>'failed','msg'=>'Unauthorized']);
        }
        $qry = $this->conn->query("SELECT COUNT(*) as cnt FROM `transaction_list` WHERE `is_notified` = 0");
        $cnt = 0;
        if($qry){
            $res = $qry->fetch_assoc();
            $cnt = isset($res['cnt']) ? intval($res['cnt']) : 0;
        }
        return json_encode(['status'=>'success','count'=>$cnt]);
    }

    function mark_transaction_read(){
        $user = $this->settings->userdata('login_type');
        if($user != 1){
            return json_encode(['status'=>'failed','msg'=>'Unauthorized']);
        }
        extract($_POST);
        if(isset($id) && trim($id) !== ''){
            $parts = array_filter(array_map('trim', explode(',', $id)));
            $clean = array_map('intval', $parts);
            if(count($clean) <= 0) return json_encode(['status'=>'failed','msg'=>'No valid ids provided']);
            $in = implode(',', $clean);
            $q = $this->conn->query("UPDATE `transaction_list` SET `is_notified` = 1 WHERE id IN ({$in})");
        }else{
            $q = $this->conn->query("UPDATE `transaction_list` SET `is_notified` = 1");
        }
        if($q){
            return json_encode(['status'=>'success']);
        }else{
            return json_encode(['status'=>'failed','error'=>$this->conn->error]);
        }
    }
}
