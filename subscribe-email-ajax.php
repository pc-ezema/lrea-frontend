<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');
  
	if ($_POST["email"]) {
		if (isset($_POST['use_new'])) {
			$use_new_form_design = true;
		}
		else {
			$use_new_form_design = false;
		}
		if (!$use_new_form_design) {
			validate('title', 'strlen', 2);
			validate('first_name', 'strlen', 2, 70);
			validate('last_name', 'strlen', 2, 70);
			validate_norequired('phone', 'phone');
		}
		
		validate('email', 'email');

		if (empty($form_errors)) {
			$phone = '';
			if ($_POST['phone']) {
				$phone = $_POST['phone'];
			}

			$response = add_new_email_subscriber($_POST['email'], $time_now, $_POST['title'], $_POST['first_name'], $_POST['last_name'], $phone);
			echo $response;
			exit();
		} 
		else {
			if ($use_new_form_design) {
				if (!empty($form_errors['email'])) {
					$response = array(
						'status' => 'invalid',
						'message' => $form_errors['email']
					);
				}
			}
			else {
				if (!empty($form_errors['title'])) {
					$response = array(
						'status' => 'invalid',
						'message' => '<p class="help-block text-danger">Title must be at least 2 characters</p>'
					);
				} elseif (!empty($form_errors['first_name'])) {
					$response = array(
						'status' => 'invalid',
						'message' => '<p class="help-block text-danger">First Name must be at least 2 characters and maximum of 70</p>'
					);
				} elseif (!empty($form_errors['last_name'])) {
					$response = array(
						'status' => 'invalid',
						'message' => '<p class="help-block text-danger">Last Name must be at least 2 characters and maximum of 70</p>'
					);
				} elseif (!empty($form_errors['email'])) {
					$response = array(
						'status' => 'invalid',
						'message' => $form_errors['email']
					);
				} elseif (!empty($form_errors['phone'])) {
					$response = array(
						'status' => 'invalid',
						'message' => $form_errors['phone']
					);
				}
			}

			echo json_encode($response);
			exit();
		}
	}
?>