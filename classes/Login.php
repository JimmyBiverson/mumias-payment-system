<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}

	private function migratePassword($user_id, $plainPassword) {
		$newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
		$stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
		$stmt->bind_param('si', $newHash, $user_id);
		$stmt->execute();
		$stmt->close();
	}

	private function authenticateUser($username, $password, $type) {
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? AND `type` = ? LIMIT 1");
		$stmt->bind_param('si', $username, $type);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();
			$storedHash = $user['password'];
			$passwordOk = false;
			if (password_verify($password, $storedHash)) {
				$passwordOk = true;
			} elseif (strlen($storedHash) === 32 && ctype_xdigit($storedHash)) {
				if (md5($password) === $storedHash) {
					$passwordOk = true;
					$this->migratePassword($user['id'], $password);
				}
			}
			if ($passwordOk) {
				foreach ($user as $k => $v) {
					if (!is_numeric($k) && $k != 'password') {
						$this->settings->set_userdata($k, $v);
					}
				}
				$this->settings->set_userdata('login_type', $type);
				return true;
			}
		}
		$stmt->close();
		return false;
	}

	public function login(){
		$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
		$expected = hash('sha256', session_id() . '::admin_login');
		if ($csrf_token !== $expected) {
			return json_encode(array('status' => 'failed', 'msg' => 'Invalid request origin'));
		}
		extract($_POST);
		$success = $this->authenticateUser($username, $password, 1);
		if ($success) {
			return json_encode(array('status' => 'success'));
		} else {
			return json_encode(array('status' => 'incorrect'));
		}
	}

	public function logout(){
		if($this->settings->sess_des()){
			redirect('admin/login.php');
		}
	}

	function login_user(){
		extract($_POST);
		$success = $this->authenticateUser($username, $password, 2);
		if ($success) {
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'incorrect';
		}
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	public function logout_user(){
		if($this->settings->sess_des()){
			redirect('./');
		}
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'login_user':
		echo $auth->login_user();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	case 'logout_user':
		echo $auth->logout_user();
		break;
	default:
		echo $auth->index();
		break;
}
