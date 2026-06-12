<?php
require_once('../config.php');
Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}

	public function save_users(){
		extract($_POST);
		$oid = $id;
		$data = '';

		if(isset($oldpassword)){
			$storedHash = $this->settings->userdata('password');
			$passwordOk = false;
			if (password_verify($oldpassword, $storedHash)) {
				$passwordOk = true;
			} elseif (strlen($storedHash) === 32 && ctype_xdigit($storedHash)) {
				if (md5($oldpassword) === $storedHash) {
					$passwordOk = true;
				}
			}
			if (!$passwordOk) {
				return 4;
			}
		}

		$chk = $this->conn->query("SELECT * FROM `users` where username = '" . $this->conn->real_escape_string($username) . "' " . ($id > 0 ? " and id != '" . intval($id) . "' " : ""))->num_rows;
		if($chk > 0){
			return 3;
		}

		foreach($_POST as $k => $v){
			if(in_array($k, array('firstname','middlename','lastname','username','type'))){
				if(!empty($data)) $data .= " , ";
				$data .= " {$k} = '" . $this->conn->real_escape_string($v) . "' ";
			}
		}
		if(!empty($password)){
			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			if(!empty($data)) $data .= " , ";
			$data .= " `password` = '" . $this->conn->real_escape_string($password_hash) . "' ";
		}

		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if($qry){
				$id = $this->conn->insert_id;
				$this->settings->set_flashdata('success','User Details successfully saved.');
				$resp['status'] = 1;
			}else{
				$resp['status'] = 2;
			}
		}else{
			$qry = $this->conn->query("UPDATE users set $data where id = " . intval($id));
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				if($id == $this->settings->userdata('id')){
					foreach($_POST as $k => $v){
						if($k != 'id' && $k != 'password' && $k != 'oldpassword'){
							$this->settings->set_userdata($k, $v);
						}
					}
				}
				$resp['status'] = 1;
			}else{
				$resp['status'] = 2;
			}
		}

		if(isset($resp['status']) && $resp['status'] == 1){
			$data = "";
			foreach($_POST as $k => $v){
				if(!in_array($k, array('id','firstname','middlename','lastname','username','password','type','oldpassword'))){
					if(!empty($data)) $data .= ", ";
					$v = $this->conn->real_escape_string($v);
					$data .= "('" . intval($id) . "','" . $this->conn->real_escape_string($k) . "', '" . $v . "')";
				}
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `user_meta` where user_id = '" . intval($id) . "' ");
				$save = $this->conn->query("INSERT INTO `user_meta` (user_id,`meta_field`,`meta_value`) VALUES {$data}");
				if(!$save){
					$resp['status'] = 2;
					if(empty($oid)){
						$this->conn->query("DELETE FROM `users` where id = '" . intval($id) . "' ");
					}
				}
			}
		}

		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = 'uploads/avatar-'.$id.'.png';
			$dir_path = base_app . $fname;
			$upload = $_FILES['img']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg');
			if(!in_array($type,$allowed)){
				$resp['msg'] .= " But Image failed to upload due to invalid file type.";
			}else{
				$new_height = 200;
				$new_width = 200;
				list($width, $height) = getimagesize($upload);
				$t_image = imagecreatetruecolor($new_width, $new_height);
				imagealphablending($t_image, false);
				imagesavealpha($t_image, true);
				$gdImg = ($type == 'image/png') ? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
				imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				if($gdImg){
					if(is_file($dir_path))
						unlink($dir_path);
					$uploaded_img = imagepng($t_image, $dir_path);
					imagedestroy($gdImg);
					imagedestroy($t_image);
				}else{
					$resp['msg'] .= " But Image failed to upload due to unknown reason.";
				}
			}
			if(isset($uploaded_img)){
				$this->conn->query("UPDATE users set `avatar` = CONCAT('" . $this->conn->real_escape_string($fname) . "','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '" . intval($id) . "' ");
				if($id == $this->settings->userdata('id')){
					$this->settings->set_userdata('avatar', $fname);
				}
			}
		}
		if(isset($resp['msg']))
			$this->settings->set_flashdata('success', $resp['msg']);
		return isset($resp['status']) ? $resp['status'] : 2;
	}

	public function delete_users(){
		extract($_POST);
		$id = intval($id);
		$avatar_qry = $this->conn->query("SELECT avatar FROM users where id = '{$id}'");
		$avatar = $avatar_qry ? $avatar_qry->fetch_array()['avatar'] : '';
		$qry = $this->conn->query("DELETE FROM users where id = '{$id}'");
		if($qry){
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			if(is_file(base_app . $avatar))
				unlink(base_app . $avatar);
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}

	public function save_susers(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','password'))){
				if(!empty($data)) $data .= ", ";
				$data .= " `{$k}` = '" . $this->conn->real_escape_string($v) . "' ";
			}
		}
		if(!empty($password))
			$data .= ", `password` = '" . $this->conn->real_escape_string(password_hash($password, PASSWORD_DEFAULT)) . "' ";

		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = 'uploads/' . strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../' . $fname);
			if($move){
				$data .= " , avatar = '" . $this->conn->real_escape_string($fname) . "' ";
				if(isset($_SESSION['userdata']['avatar']) && is_file('../' . $_SESSION['userdata']['avatar']))
					unlink('../' . $_SESSION['userdata']['avatar']);
			}
		}
		$sql = "UPDATE students set {$data} where id = " . intval($id);
		$save = $this->conn->query($sql);
		if($save){
			$this->settings->set_flashdata('success','User Details successfully updated.');
			foreach($_POST as $k => $v){
				if(!in_array($k, array('id','password'))){
					$this->settings->set_userdata($k, $v);
				}
			}
			if(isset($fname) && isset($move))
				$this->settings->set_userdata('avatar', $fname);
			return 1;
		}else{
			$resp['error'] = $sql;
			return json_encode($resp);
		}
	}
}

$users = new Users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save':
		echo $users->save_users();
		break;
	case 'fsave':
		echo $users->save_fusers();
		break;
	case 'ssave':
		echo $users->save_susers();
		break;
	case 'delete':
		echo $users->delete_users();
		break;
	default:
		break;
}
