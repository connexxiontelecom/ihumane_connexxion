<?php


class Employee extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('form_validation');
		$this->load->library('user_agent');
		$this->load->library('session');
		$this->load->helper('security');
		$this->load->helper('string');
		$this->load->helper('array');
		$this->load->model('users');
		$this->load->model('employees');
		$this->load->model('hr_configurations');
		$this->load->model('logs');
	}

	public function employee()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				if ($permission->employee_management == 1):
					$data['notifications'] = $this->employees->get_notifications(0);
					
						$employees = $this->employees->view_employees();
						
						foreach ($employees as $employee):
							
							if($employee->employee_stop_date == '0000-00-00' && $employee->employee_status == 0):
								
								$termination = $this->employees->get_employee_termination($employee->employee_id);
							
								if(!empty($termination)):
									
									$employee_data = array(
										'employee_stop_date' => $termination->termination_effective_date
									);
									 $this->employees->update_employee($employee->employee_id, $employee_data);
									
									else:
									
									$resignations = $this->employees->get_employee_resignations($employee->employee_id);
									
									if(!empty($resignations)):
										foreach ($resignations as $resignation):
											
											if($resignation->resignation_status == 1):
												$employee_data = array(
													'employee_stop_date' => $resignation->resignation_effective_date
												);
												 $this->employees->update_employee($employee->employee_id, $employee_data);
												endif;
											
											endforeach;
									endif;
									
									
									endif;
							
								
								endif;
							
							endforeach;
					

					$data['employees'] = $this->employees->view_employees();
					$data['user_data'] = $this->users->get_user($username);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/employee', $data);
				else:

					redirect('/access_denied');
				endif;
			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function new_employee()
	{
		error_reporting(0);
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;


				if ($permission->employee_management == 1):

//				$data['employees'] = $this->users->view_employees();
					$data['notifications'] = $this->employees->get_notifications(0);

					$data['grades'] = $this->hr_configurations->view_grades();
					$data['roles'] = $this->hr_configurations->view_job_roles();
					$data['qualifications'] = $this->hr_configurations->view_qualifications();
					$data['banks'] = $this->hr_configurations->view_banks();
					$data['locations'] = $this->hr_configurations->view_locations();
					$data['health_insurances'] = $this->hr_configurations->view_health_insurances();
					$data['pensions'] = $this->hr_configurations->view_pensions();
					$data['subsidiarys'] = $this->hr_configurations->view_subsidiarys();
					$employee_unique_id = "ihumane_" . random_string('alnum', 3);

					$employee_check = $this->employees->get_employee($employee_unique_id);

					while (!empty($employee_check)):
						$employee_check = $this->employees->get_employee($employee_unique_id);
					endwhile;

					$errormsg = ' ';
					$error_msg = array('error' => $errormsg);
					$data['error'] = $errormsg;

					$data['unique_id'] = $employee_unique_id;
					$data['user_data'] = $this->users->get_user($username);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/new_employee', $data);

				else:

					redirect('/access_denied');

				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function add_employee()
	{
		error_reporting(0);
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):


			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;
			$data['notifications'] = $this->employees->get_notifications(0);
			if ($permission->employee_management == 1):
				$config['upload_path'] = 'uploads/employee_passports';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size'] = '8000000';
				$config['max_width'] = '102452';
				$config['max_height'] = '768555';
				//$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;

				$this->load->library('upload', $config);
				$upload = $this->upload->do_upload('employee_passport');

				if (!$upload):
					echo $this->upload->display_errors();
					die();
				else:
					$file_data = $this->upload->data();
					$employee_passport_name = $file_data['file_name'];
				endif;


				$config2['upload_path'] = 'uploads/employee_nysc';
				$config2['allowed_types'] = 'gif|jpg|png|pdf|jpeg';
				$config2['max_size'] = '8000000';
				$config2['max_width'] = '102452';
				$config2['max_height'] = '768555';
				//$config2['overwrite'] = TRUE;
				$config2['encrypt_name'] = TRUE;

				$this->upload->initialize($config2);
				$upload = $this->upload->do_upload('employee_nysc');

				if (!$upload):
					$employee_nysc_name = 'n/a';

//					echo $this->upload->display_errors();
//					die();
				else:
					$file_data = $this->upload->data();
					$employee_nysc_name = $file_data['file_name'];
				endif;

				$employee_unique_id = $this->input->post('employee_unique_id');
				$employee_first_name = $this->input->post('employee_first_name');
				$employee_last_name = $this->input->post('employee_last_name');
				$employee_other_name = $this->input->post('employee_other_name');
				$employee_personal_email = $this->input->post('employee_personal_email');
				$employee_official_email = $this->input->post('employee_official_email');
				$employee_dob = $this->input->post('employee_dob');
				$employee_phone_number = $this->input->post('employee_phone_number');
				$employee_address = $this->input->post('employee_address');
				$employee_grade = $this->input->post('employee_grade');
				$employee_job_role = $this->input->post('employee_job_role');
				$employee_qualification = $this->input->post('employee_qualification');
				$employee_location = $this->input->post('location');
				$check_experience = $this->input->post('check_experience');
				$company_name = $this->input->post('company_name');
				$job_description = $this->input->post('job_description');
				$experience_start_date = $this->input->post('experience_start_date');
				$experience_end_date = $this->input->post('experience_end_date');
				$employee_account_number = $this->input->post('employee_account_number');
				$employee_bank = $this->input->post('employee_bank');
				$nysc_pass_out = $this->input->post('nysc_pass_out');
				$employment_start_date = $this->input->post('employment_start_date');
				$employment_stop_date = $this->input->post('employment_stop_date');
				$employment_status = $this->input->post('employment_status');
				$employee_others = $this->input->post('employee_others');
				$employee_subsidiary = $this->input->post('subsidiary');
				$employee_pensionable = $this->input->post('employee_pensionable');
				$employee_state_of_origin = $this->input->post('employee_state_of_origin');
				$employee_lga = $this->input->post('employee_lga');
				$employee_marital = $this->input->post('employee_marital');
				$employee_spouse_name = $this->input->post('employee_spouse_name');
				$employee_spouse_phone_number = $this->input->post('employee_spouse_phone_number');
				$employee_ailments = $this->input->post('employee_ailments');
				$employee_blood = $this->input->post('employee_blood');
				$employee_genotype = $this->input->post('employee_genotype');
				$employee_next_of_kin_name = $this->input->post('employee_next_of_kin_name');
				$employee_next_of_kin_phone_number = $this->input->post('employee_next_of_kin_phone_number');
				$employee_next_of_kin_address = $this->input->post('employee_next_of_kin_address');
				$employee_emergency_name = $this->input->post('employee_emergency_name');
				$employee_emergency_phone = $this->input->post('employee_emergency_phone');
				$employee_username = $this->input->post('employee_username');
				$employee_password = $this->input->post('employee_password');

				if ($employee_pensionable == 1):
					$employee_pension_number = $this->input->post('employee_pension_number');
					$employee_pension_id = $this->input->post('employee_pension_id');
				else:
					$employee_pension_number = null;
					$employee_pension_id = null;

				endif;


				$employee_hmo_number = $this->input->post('employee_hmo_number');
				$employee_hmo_id = $this->input->post('employee_hmo_id');
				$employee_paye_number = $this->input->post('employee_paye_number');

			if($employee_username == $employee_unique_id):
				$employee_data = array(
					'employee_unique_id' => $employee_unique_id,
					'employee_first_name' => $employee_first_name,
					'employee_other_name' => $employee_other_name,
					'employee_last_name' => $employee_last_name,
					'employee_dob' => $employee_dob,
					'employee_personal_email' => $employee_personal_email,
					'employee_official_email' => $employee_official_email,
					'employee_phone_number' => $employee_phone_number,
					'employee_qualification' => json_encode($employee_qualification),
					'employee_address' => $employee_address,
					'employee_location_id' => $employee_location,
					'employee_job_role_id' => $employee_job_role,
					'employee_grade_id' => $employee_grade,
					'employee_account_number' => $employee_account_number,
					'employee_bank_id' => $employee_bank,
					'employee_nysc_details' => $nysc_pass_out,
					'employee_employment_date' => $employment_start_date,
					'employee_stop_date' => $employment_stop_date,
					'employee_status' => $employment_status,
					'employee_passport' => $employee_passport_name,
					'employee_pension_number' => $employee_pension_number,
					'employee_pensionable' => $employee_pensionable,
					'employee_pension_id' => $employee_pension_id,
					'employee_hmo_number' => $employee_hmo_number,
					'employee_hmo_id' => $employee_hmo_id,
					'employee_nysc_document' => $employee_nysc_name,
					'employee_state' => $employee_state_of_origin,
					'employee_lga' => $employee_lga,
					'employee_marital_status' => $employee_marital,
					'employee_spouse_name' => $employee_spouse_name,
					'employee_spouse_phone_number' => $employee_spouse_phone_number,
					'employee_next_of_kin_name' => $employee_next_of_kin_name,
					'employee_next_of_kin_address' => $employee_next_of_kin_address,
					'employee_next_of_kin_phone_number' => $employee_next_of_kin_phone_number,
					'employee_ailments' => $employee_ailments,
					'employee_blood' => $employee_blood,
					'employee_genotype' => $employee_genotype,
					'employee_emergency_name' => $employee_emergency_name,
					'employee_emergency_contact' => $employee_emergency_phone,
					'employee_paye_number' => $employee_paye_number,
					'employee_subsidiary_id' => $employee_subsidiary

				);

				$employee_name = $employee_last_name . " " . $employee_first_name;

				$user_array = array(
					'user_username' => $employee_username,
					'user_email' => $employee_official_email,
					'user_password' => password_hash($employee_password, PASSWORD_BCRYPT),
					'user_name' => $employee_name,
					'user_type' => 2,
					'user_status' => 1
				);

				$permission_array = array(
					'username' => $employee_username,
					'employee_management' => 0,
					'payroll_management' => 0,
					'biometrics' => 0,
					'user_management' => 0,
					'configuration' => 0,
					'hr_configuration' => 0,
					'payroll_configuration' => 0
				);


				$permission_array = $this->security->xss_clean($permission_array);

				$user_array = $this->security->xss_clean($user_array);

				$employee_data = $this->security->xss_clean($employee_data);

				$query = $this->users->add($user_array, $permission_array);

				$employee_id = $this->employees->add_employee($employee_data);
				$k = 0;

				while ($k < count($employee_others)):
					$employee_other_document_name = $employee_others[$k];

					$others_array = array(
						'other_document_employee_id' => $employee_id,
						'other_document_name' => $employee_other_document_name
					);

					$this->employees->insert_other_document($others_array);
					$k++;
				endwhile;

				$i = 0;
				while ($i < count($company_name)):

					if ($company_name[$i] == ''):
					else:
						$new_array = array(
							'employee_id' => $employee_id,
							'company_name' => $company_name[$i],
							'job_description' => $job_description[$i],
							'start_date' => $experience_start_date[$i],
							'end_date' => $experience_end_date[$i]
						);
						$new_array = $this->security->xss_clean($new_array);
						$this->employees->add_work_experience($new_array);
					endif;
					$i++;
				endwhile;

						if (isset($employee_id)):
							$log_array = array(
								'log_user_id' => $this->users->get_user($username)->user_id,
								'log_description' => "Added New Employee"
							);

							$this->logs->add_log($log_array);

							$employee_history_array = array(
								'employee_history_employee_id' => $employee_id,
								'employee_history_details' => "You were Hired",
								'employee_history_date' => $employment_start_date
							);

							$this->employees->insert_employee_history($employee_history_array);

							$msg = array(
								'msg' => 'New Employee Added Successfully',
								'location' => site_url('employee'),
								'type' => 'success'

							);
							$this->load->view('swal', $msg);
						else:
							$msg = array(
								'msg' => 'An Error Occurred',
								'location' => site_url('new_employee'),
								'type' => 'success'

							);
							$this->load->view('swal', $msg);

						endif;

				else:
					$msg = array(
						'msg' => 'Please Ensure Employee Unique ID is same  as Employee Login',
						'location' => site_url('new_employee'),
						'type' => 'success'

					);
					$this->load->view('swal', $msg);

					endif;
			else:

				redirect('/access_denied');

			endif;

			else:

				redirect('error_404');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function employee_upload_others()
	{

		$config['upload_path'] = 'uploads/employee_others';
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf';
		$config['max_size'] = '8000000';
		$config['max_width'] = '102452';
		$config['max_height'] = '768555';
		$config['encrypt_name'] = TRUE;
		//$config['overwrite'] = TRUE;

		$this->load->library('upload', $config);
		$this->upload->do_upload('file');


		$file_data = $this->upload->data();
		echo $file_data['file_name'];

	}

	public function view_employee()
	{
		$employee_id = $this->uri->segment(2);


		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;


				if ($permission->employee_management == 1):


					$errormsg = ' ';
					$error_msg = array('error' => $errormsg);
					$data['error'] = $errormsg;
					$data['user_data'] = $this->users->get_user($username);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['notifications'] = $this->employees->get_notifications(0);
					$data['grades'] = $this->hr_configurations->view_grades();
					$data['roles'] = $this->hr_configurations->view_job_roles();
					$data['qualifications'] = $this->hr_configurations->view_qualifications();
					$data['banks'] = $this->hr_configurations->view_banks();
					$data['locations'] = $this->hr_configurations->view_locations();
					$data['health_insurances'] = $this->hr_configurations->view_health_insurances();
					$data['pensions'] = $this->hr_configurations->view_pensions();
					$data['subsidiarys'] = $this->hr_configurations->view_subsidiarys();

					$data['work_experiences'] = $this->employees->get_work_experience($employee_id);

					$data['employee'] = $this->employees->get_employee($employee_id);

					$data['other_documents'] = $this->employees->get_other_document($employee_id);


					if (empty($data['employee'])):

						redirect('/access_denied');
					else:

						$this->load->view('employee/view_employee', $data);
					endif;
				else:

					redirect('/access_denied');

				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function update_employee()
	{

		$employee_id = $this->uri->segment(2);


		$username = $this->session->userdata('user_username');

		if (isset($username)):


			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;


				if ($permission->employee_management == 1):

					$data['notifications'] = $this->employees->get_notifications(0);
					$errormsg = ' ';
					$error_msg = array('error' => $errormsg);
					$data['error'] = $errormsg;
					$data['user_data'] = $this->users->get_user($username);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$data['grades'] = $this->hr_configurations->view_grades();
					$data['roles'] = $this->hr_configurations->view_job_roles();
					$data['qualifications'] = $this->hr_configurations->view_qualifications();
					$data['banks'] = $this->hr_configurations->view_banks();
					$data['locations'] = $this->hr_configurations->view_locations();
					$data['health_insurances'] = $this->hr_configurations->view_health_insurances();
					$data['pensions'] = $this->hr_configurations->view_pensions();
					$data['subsidiarys'] = $this->hr_configurations->view_subsidiarys();

					$data['work_experiences'] = $this->employees->get_work_experience($employee_id);

					$data['employee'] = $this->employees->get_employee($employee_id);

					$data['other_documents'] = $this->employees->get_other_document($employee_id);


					if (empty($data['employee'])):

						redirect('/access_denied');
					else:

						$this->load->view('employee/update_employee', $data);
					endif;
				else:

					redirect('access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function edit_employee()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):

			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;
			$data['configuration'] = $permission->configuration;


			if ($permission->employee_management == 1):

				$data['notifications'] = $this->employees->get_notifications(0);
				$employee_unique_id = $this->input->post('employee_unique_id');
				$employee_first_name = $this->input->post('employee_first_name');
				$employee_last_name = $this->input->post('employee_last_name');
				$employee_other_name = $this->input->post('employee_other_name');
				$employee_personal_email = $this->input->post('employee_personal_email');
				$employee_official_email = $this->input->post('employee_official_email');
				$employee_dob = $this->input->post('employee_dob');
				$employee_phone_number = $this->input->post('employee_phone_number');
				$employee_address = $this->input->post('employee_address');
				$employee_grade = $this->input->post('employee_grade');
				$employee_job_role = $this->input->post('employee_job_role');
				$employee_qualification = $this->input->post('employee_qualification');
				$employee_location = $this->input->post('location');
				$check_experience = $this->input->post('check_experience');
				$company_name = $this->input->post('company_name');
				$job_description = $this->input->post('job_description');
				$experience_start_date = $this->input->post('experience_start_date');
				$experience_end_date = $this->input->post('experience_end_date');
				$employee_account_number = $this->input->post('employee_account_number');
				$employee_bank = $this->input->post('employee_bank');
				$nysc_pass_out = $this->input->post('nysc_pass_out');
				$employment_start_date = $this->input->post('employment_start_date');
				$employment_stop_date = $this->input->post('employment_stop_date');
				$employment_status = $this->input->post('employment_status');
				$employee_others = $this->input->post('employee_others');
				$employee_pensionable = $this->input->post('employee_pensionable');
				$employee_subsidiary = $this->input->post('subsidiary');
				$employee_state_of_origin = $this->input->post('employee_state_of_origin');
				$employee_lga = $this->input->post('employee_lga');
				$employee_marital = $this->input->post('employee_marital');
				$employee_spouse_name = $this->input->post('employee_spouse_name');
				$employee_spouse_phone_number = $this->input->post('employee_spouse_phone_number');
				$employee_ailments = $this->input->post('employee_ailments');
				$employee_blood = $this->input->post('employee_blood');
				$employee_genotype = $this->input->post('employee_genotype');
				$employee_next_of_kin_name = $this->input->post('employee_next_of_kin_name');
				$employee_next_of_kin_phone_number = $this->input->post('employee_next_of_kin_phone_number');
				$employee_next_of_kin_address = $this->input->post('employee_next_of_kin_address');
				$employee_emergency_name = $this->input->post('employee_emergency_name');
				$employee_emergency_phone = $this->input->post('employee_emergency_phone');
				$employee_id = $this->input->post('employee_id');

				if ($employee_pensionable == 1):
					$employee_pension_number = $this->input->post('employee_pension_number');
					$employee_pension_id = $this->input->post('employee_pension_id');
				else:
					$employee_pension_number = null;
					$employee_pension_id = null;

				endif;

				$employee_hmo_number = $this->input->post('employee_hmo_number');
				$employee_hmo_id = $this->input->post('employee_hmo_id');
				$employee_paye_number = $this->input->post('employee_paye_number');



				$config['upload_path'] = 'uploads/employee_passports';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size'] = '8000000';
				$config['max_width'] = '102452';
				$config['max_height'] = '768555';
				//$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;

				$this->load->library('upload', $config);
				$upload = $this->upload->do_upload('employee_passport');

				if (!$upload):

					$employee_passport_name = $this->employees->get_employee($employee_id)->employee_passport;
				else:
					$file_data = $this->upload->data();
					$employee_passport_name = $file_data['file_name'];
				endif;

				if(!empty($employee_others)):

				$k = 0;
				while ($k < count($employee_others)):
					$employee_other_document_name = $employee_others[$k];

					$others_array = array(
						'other_document_employee_id' => $employee_id,
						'other_document_name' => $employee_other_document_name
					);

					$this->employees->insert_other_document($others_array);
					$k++;
				endwhile;

				endif;


				$employee_data = array(
					'employee_unique_id' => $employee_unique_id,
					'employee_first_name' => $employee_first_name,
					'employee_other_name' => $employee_other_name,
					'employee_last_name' => $employee_last_name,
					'employee_dob' => $employee_dob,
					'employee_personal_email' => $employee_personal_email,
					'employee_official_email' => $employee_official_email,
					'employee_phone_number' => $employee_phone_number,
					'employee_qualification' => json_encode($employee_qualification),
					'employee_address' => $employee_address,
					'employee_location_id' => $employee_location,
					'employee_job_role_id' => $employee_job_role,
					'employee_grade_id' => $employee_grade,
					'employee_account_number' => $employee_account_number,
					'employee_bank_id' => $employee_bank,
					'employee_nysc_details' => $nysc_pass_out,
					'employee_employment_date' => $employment_start_date,
					'employee_stop_date' => $employment_stop_date,
					'employee_pensionable' => $employee_pensionable,
					'employee_pension_number' => $employee_pension_number,
					'employee_pension_id' => $employee_pension_id,
					'employee_hmo_number' => $employee_hmo_number,
					'employee_hmo_id' => $employee_hmo_id,
					'employee_status' => $employment_status,
					'employee_paye_number' => $employee_paye_number,
					'employee_passport' => $employee_passport_name,
					'employee_state' => $employee_state_of_origin,
					'employee_lga' => $employee_lga,
					'employee_marital_status' => $employee_marital,
					'employee_spouse_name' => $employee_spouse_name,
					'employee_spouse_phone_number' => $employee_spouse_phone_number,
					'employee_next_of_kin_name' => $employee_next_of_kin_name,
					'employee_next_of_kin_address' => $employee_next_of_kin_address,
					'employee_next_of_kin_phone_number' => $employee_next_of_kin_phone_number,
					'employee_ailments' => $employee_ailments,
					'employee_blood' => $employee_blood,
					'employee_genotype' => $employee_genotype,
					'employee_emergency_name' => $employee_emergency_name,
					'employee_emergency_contact' => $employee_emergency_phone,
					'employee_subsidiary_id' => $employee_subsidiary

				);

				$employee_data = $this->security->xss_clean($employee_data);
				$this->employees->delete_work_experience($employee_id);
				$i = 0;
				while ($i < count($company_name)):

					if ($company_name[$i] == ''):
					else:
						$new_array = array(
							'employee_id' => $employee_id,
							'company_name' => $company_name[$i],
							'job_description' => $job_description[$i],
							'start_date' => $experience_start_date[$i],
							'end_date' => $experience_end_date[$i]
						);
						$new_array = $this->security->xss_clean($new_array);
						$this->employees->add_work_experience($new_array);
					endif;
					$i++;
				endwhile;


				$query = $this->employees->update_employee($employee_id, $employee_data);


				if ($query == true):
					$log_array = array(
						'log_user_id' => $this->users->get_user($username)->user_id,
						'log_description' => "Update Employee Record"
					);

					$this->logs->add_log($log_array);

					$notification_data = array(
						'notification_employee_id'=> $employee_id,
						'notification_link'=> 'personal_information',
						'notification_type' => 'Information Updated',
						'notification_status'=> 0
					);

					$this->employees->insert_notifications($notification_data);

					$msg = array(
						'msg' => 'Employee Updated Successfully',
						'location' => site_url('employee'),
						'type' => 'success'

					);
					$this->load->view('swal', $msg);
				else:
					$msg = array(
						'msg' => 'An Error Occurred',
						'location' => site_url('new_employee'),
						'type' => 'success'

					);
					$this->load->view('swal', $msg);

				endif;
			else:

				redirect('/access_denied');

			endif;
			else:

				redirect('error_404');
				endif;
		else:
			redirect('/login');
		endif;


	}

	public function employee_transfer()
	{


		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;


				if ($permission->employee_management == 1):

					$errormsg = ' ';
					$error_msg = array('error' => $errormsg);
					$data['error'] = $errormsg;
					$data['user_data'] = $this->users->get_user($username);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['notifications'] = $this->employees->get_notifications(0);
					$data['grades'] = $this->hr_configurations->view_grades();
					$data['roles'] = $this->hr_configurations->view_job_roles();
					$data['qualifications'] = $this->hr_configurations->view_qualifications();
					$data['banks'] = $this->hr_configurations->view_banks();
					$data['locations'] = $this->hr_configurations->view_locations();
					$data['health_insurances'] = $this->hr_configurations->view_health_insurances();
					$data['pensions'] = $this->hr_configurations->view_pensions();
					$data['subsidiarys'] = $this->hr_configurations->view_subsidiarys();

					$data['transfers'] = $this->employees->get_employees_transfers();


					$this->load->view('employee/employee_transfer', $data);

				else:

					redirect('/access_denied');

				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function new_employee_transfer()
	{


		$username = $this->session->userdata('user_username');

		if (isset($username)):

			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$errormsg = ' ';
					$error_msg = array('error' => $errormsg);
					$data['error'] = $errormsg;
					$data['user_data'] = $this->users->get_user($username);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$data['grades'] = $this->hr_configurations->view_grades();
					$data['roles'] = $this->hr_configurations->view_job_roles();
					$data['qualifications'] = $this->hr_configurations->view_qualifications();
					$data['banks'] = $this->hr_configurations->view_banks();
					$data['locations'] = $this->hr_configurations->view_locations();
					$data['health_insurances'] = $this->hr_configurations->view_health_insurances();
					$data['pensions'] = $this->hr_configurations->view_pensions();
					$data['subsidiarys'] = $this->hr_configurations->view_subsidiarys();

					//$data['transfers'] = $this->employees->get_employees_transfers();
					$data['employees'] = $this->employees->view_employees();

					$this->load->view('employee/new_employee_transfer', $data);

				else:

					redirect('/access_denied');

				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function add_new_employee_transfer()
	{

		//error_reporting(0);
		$username = $this->session->userdata('user_username');



		if (isset($username)):

			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):

			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;

			$data['notifications'] = $this->employees->get_notifications(0);
			if ($permission->employee_management == 1):
				$employee_id = $this->input->post('employee_id');
				$transfer_type = $this->input->post('transfer_type');



				$subsidiary_id = $this->input->post('subsidiary_id');


				$location_id = $this->input->post('location_id');

				$employee = $this->employees->get_employee($employee_id);


				if(empty($employee_id) || $transfer_type == null):
					redirect('error_404');
				else:
				if ($transfer_type == 0):
					$transfer_from = $employee->employee_location_id;
					$transfer_to = $location_id;

					if ($transfer_from == $transfer_to):

						$msg = array(
							'msg' => 'Employee already in this branch',
							'location' => site_url('employee_transfer'),
							'type' => 'warning'

						);
						$this->load->view('swal', $msg);

					endif;

					if ($transfer_from != $transfer_to):

						$employee_data = array(
							'employee_location_id' => $location_id
						);
						$transfer_array = array(
							'transfer_employee_id' => $employee_id,
							'transfer_type' => $transfer_type,
							'transfer_from' => $transfer_from,
							'transfer_to' => $transfer_to


						);

						$transfer_array = $this->security->xss_clean($transfer_array);

						$query = $this->employees->insert_transfer($transfer_array);


						$query = $this->employees->update_employee($employee_id, $employee_data);

						if ($query == true):
							$log_array = array(
								'log_user_id' => $this->users->get_user($username)->user_id,
								'log_description' => "Initiated Employee Transfer"
							);

							$this->logs->add_log($log_array);

							$employee_history_array = array(
								'employee_history_employee_id' => $employee_id,
								'employee_history_details' => 'Transfer'

							);

							$this->employees->insert_employee_history($employee_history_array);

							$notification_data = array(
								'notification_employee_id'=> $employee_id,
								'notification_link'=> 'my_transfer',
								'notification_type' => 'New Transfer',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);

							$msg = array(
								'msg' => 'Employee Transfer Successful',
								'location' => site_url('employee_transfer'),
								'type' => 'success'

							);
							$this->load->view('swal', $msg);
						else:
//					$msg = array(
//						'msg'=> 'An Error Occurred',
//						'location' => site_url('new_employee'),
//						'type' => 'success'
//
//					);
//					$this->load->view('swal', $msg);

						endif;

					endif;

				endif;

				if ($transfer_type == 1):
					$transfer_from = $employee->employee_subsidiary_id;
					$transfer_to = $subsidiary_id;
					if ($transfer_from == $transfer_to):

						$msg = array(
							'msg' => 'Employee already in this subsidiary',
							'location' => site_url('employee_transfer'),
							'type' => 'warning'

						);
						$this->load->view('swal', $msg);
					endif;

					if ($transfer_from != $transfer_to):

						$employee_data = array(
							'employee_subsidiary_id' => $subsidiary_id
						);

						$transfer_array = array(
							'transfer_employee_id' => $employee_id,
							'transfer_type' => $transfer_type,
							'transfer_from' => $transfer_from,
							'transfer_to' => $transfer_to


						);

						$transfer_array = $this->security->xss_clean($transfer_array);

						$query = $this->employees->insert_transfer($transfer_array);


						$query = $this->employees->update_employee($employee_id, $employee_data);

						if ($query == true):
							$log_array = array(
								'log_user_id' => $this->users->get_user($username)->user_id,
								'log_description' => "Initiated Employee Transfer"
							);

							$this->logs->add_log($log_array);

							$msg = array(
								'msg' => 'Employee Transfer Successful',
								'location' => site_url('employee_transfer'),
								'type' => 'success'

							);
							$this->load->view('swal', $msg);
						else:
//					$msg = array(
//						'msg'=> 'An Error Occurred',
//						'location' => site_url('new_employee'),
//						'type' => 'success'
//
//					);
//					$this->load->view('swal', $msg);

						endif;

					endif;
				endif;

				endif;
			//$this->load->view('employee/new_employee_transfer',$data);


			else:

				redirect('/access_denied');

			endif;
			else:
				redirect('error_404');
				endif;
		else:
			redirect('/login');
		endif;

	}


	public function employee_leave()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):


					$errormsg = ' ';
					$error_msg = array('error' => $errormsg);
					$data['error'] = $errormsg;
					$data['user_data'] = $this->users->get_user($username);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();


					$data['leaves'] = $this->employees->get_employees_leaves();


					$this->load->view('employee/employee_leave', $data);
				else:

					redirect('access_denied');

				endif;


			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function new_employee_leave()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;


				if ($permission->employee_management == 1):

					$errormsg = ' ';
					$error_msg = array('error' => $errormsg);
					$data['error'] = $errormsg;
					$data['user_data'] = $this->users->get_user($username);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['notifications'] = $this->employees->get_notifications(0);
					$data['leaves'] = $this->hr_configurations->view_leaves();
					$data['employees'] = $this->employees->view_employees();


					$this->load->view('employee/new_employee_leave', $data);
				else:
					redirect('/access_denied');

				endif;


			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function add_new_employee_leave()
	{


		$username = $this->session->userdata('user_username');

		if (isset($username)):

			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):

			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;
			$data['notifications'] = $this->employees->get_notifications(0);

			if ($permission->employee_management == 1):
				$employee_id = $this->input->post('employee_id');
				$leave_id = $this->input->post('leave_id');
				$start_date = $this->input->post('start_date');
				$end_date = $this->input->post('end_date');


				$check_existing_leaves = $this->employees->check_existing_employee_leaves($employee_id);


				if (!empty($check_existing_leaves)):
					$count = 0;
					foreach ($check_existing_leaves as $check_existing_leave):

						if ($check_existing_leave->leave_status == 0 || $check_existing_leave->leave_status == 1):
							$count++;
						endif;

					endforeach;

					if ($count > 0):

						$msg = array(
							'msg' => 'Employee Already Has Existing Leave',
							'location' => site_url('employee_leave'),
							'type' => 'error'

						);
						$this->load->view('swal', $msg);
					else:

						$leave_array = array(
							'leave_employee_id' => $employee_id,
							'leave_leave_type' => $leave_id,
							'leave_start_date' => $start_date,
							'leave_end_date' => $end_date,
							'leave_status' => 1

						);

						$leave_array = $this->security->xss_clean($leave_array);
						$query = $this->employees->insert_leave($leave_array);

						if ($query == true):

							$log_array = array(
								'log_user_id' => $this->users->get_user($username)->user_id,
								'log_description' => "Initiated Employee Transfer"
							);

							$this->logs->add_log($log_array);

							$employee_history_array = array(
								'employee_history_employee_id' => $employee_id,
								'employee_history_details' => 'Leave Application'

							);

							$this->employees->insert_employee_history($employee_history_array);

							$notification_data = array(
								'notification_employee_id'=> $employee_id,
								'notification_link'=> 'my_leave',
								'notification_type' => 'New Leave Application by Administrator',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);

							$msg = array(
								'msg' => 'Leave Application Successful, Automatically approved because you are Administrator',
								'location' => site_url('employee_leave'),
								'type' => 'success'

							);
							$this->load->view('swal', $msg);

						endif;

					endif;
				else:

					$leave_array = array(
						'leave_employee_id' => $employee_id,
						'leave_leave_type' => $leave_id,
						'leave_start_date' => $start_date,
						'leave_end_date' => $end_date,
						'leave_status' => 1

					);

					$leave_array = $this->security->xss_clean($leave_array);
					$query = $this->employees->insert_leave($leave_array);

					if ($query == true):

						$log_array = array(
							'log_user_id' => $this->users->get_user($username)->user_id,
							'log_description' => "Initiated Employee Transfer"
						);

						$this->logs->add_log($log_array);

						$employee_history_array = array(
							'employee_history_employee_id' => $employee_id,
							'employee_history_details' => 'Leave Application'

						);

						$this->employees->insert_employee_history($employee_history_array);

						$msg = array(
							'msg' => 'Leave Application Successful, Automatically approved because you are Administrator',
							'location' => site_url('employee_leave'),
							'type' => 'success'

						);
						$this->load->view('swal', $msg);
					else:


					endif;

				endif;

			else:

				redirect('/access_denied');

			endif;
			else:
				redirect('error_404');
				endif;
		else:
			redirect('/login');
		endif;

	}

	public function extend_leave()
	{

		$leave_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);
					$data['leave'] = $this->employees->get_leave($leave_id);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/extend_leave', $data);
				else:

					redirect('/access_denied');
				endif;
			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function extend_employee_leave()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):

			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;
			$data['notifications'] = $this->employees->get_notifications(0);

			if ($permission->employee_management == 1):
				$leave_id = $this->input->post('leave_id');
				$leaf = $this->employees->get_leave($leave_id);

				$end_date = $this->input->post('end_date');


				if (empty($leave_id) || empty($end_date)):

					redirect('error_404');

				else:
					if ($end_date < date('yy-m-d')):


					else:
						$leave_array = array(

							'leave_end_date' => $end_date
						);

						$query = $this->employees->update_leave($leave_id, $leave_array);

						if ($query == true):

							$log_array = array(
								'log_user_id' => $this->users->get_user($username)->user_id,
								'log_description' => "Updated Employee Leave"
							);

							$this->logs->add_log($log_array);

							$employee_history_array = array(
								'employee_history_employee_id' => $leaf->leave_employee_id,
								'employee_history_details' => 'Leave Updated'

							);

							$this->employees->insert_employee_history($employee_history_array);

							$notification_data = array(
								'notification_employee_id'=> $leaf->leave_employee_id,
								'notification_link'=> 'my_leave',
								'notification_type' => 'Leave Extended',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);

							$msg = array(
								'msg' => 'Leave Updated',
								'location' => site_url('employee_leave'),
								'type' => 'success'

							);
							$this->load->view('swal', $msg);

						else:


						endif;

					endif;

				endif;

			else:

				redirect('/access_denied');

			endif;
			else:
				redirect('error_404');
				endif;

			else:
			redirect('/login');
		endif;

	}

	public function approve_employee_leave()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;
			$data['notifications'] = $this->employees->get_notifications(0);

			if ($permission->employee_management == 1):
				if ($this->agent->referrer() !== site_url('employee_leave')):

					redirect('error_404');

				else:


					$leave_id = $this->uri->segment(2);

					if (empty($leave_id)):

						redirect('error_404');

					else:
						$leaf = $this->employees->get_leave($leave_id);

						if (!empty($leaf)):

							$leave_array = array(

								'leave_status' => 1
							);

							$query = $this->employees->update_leave($leave_id, $leave_array);

							if ($query == true):

								$log_array = array(
									'log_user_id' => $this->users->get_user($username)->user_id,
									'log_description' => "Approved Employee Leave"
								);

								$this->logs->add_log($log_array);

								$employee_history_array = array(
									'employee_history_employee_id' => $leaf->leave_employee_id,
									'employee_history_details' => 'Leave Updated'

								);

								$this->employees->insert_employee_history($employee_history_array);

								$notification_data = array(
									'notification_employee_id'=> $leaf->leave_employee_id,
									'notification_link'=> 'my_leave',
									'notification_type' => 'Leave Application Approved',
									'notification_status'=> 0
								);

								$this->employees->insert_notifications($notification_data);

								$msg = array(
									'msg' => 'Leave Approved',
									'location' => site_url('employee_leave'),
									'type' => 'success'

								);
								$this->load->view('swal', $msg);

							else:


							endif;

						else:

							redirect('error_404');
						endif;


					endif;

				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function discard_employee_leave()
	{
		$username = $this->session->userdata('user_username');
		$data['notifications'] = $this->employees->get_notifications(0);
		if (isset($username)):
			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;


			if ($permission->employee_management == 1):
				if ($this->agent->referrer() !== site_url('employee_leave')):

					redirect('error_404');

				else:


					$leave_id = $this->uri->segment(2);

					if (empty($leave_id)):

						redirect('error_404');

					else:
						$leaf = $this->employees->get_leave($leave_id);

						if (!empty($leaf)):

							$leave_array = array(

								'leave_status' => 3
							);

							$query = $this->employees->update_leave($leave_id, $leave_array);

							if ($query == true):

								$log_array = array(
									'log_user_id' => $this->users->get_user($username)->user_id,
									'log_description' => "Discarded Employee Leave"
								);

								$this->logs->add_log($log_array);

								$employee_history_array = array(
									'employee_history_employee_id' => $leaf->leave_employee_id,
									'employee_history_details' => 'Leave Discarded'

								);

								$this->employees->insert_employee_history($employee_history_array);

								$notification_data = array(
									'notification_employee_id'=> $leaf->leave_employee_id,
									'notification_link'=> 'my_leave',
									'notification_type' => 'Leave Application Discarded',
									'notification_status'=> 0
								);

								$this->employees->insert_notifications($notification_data);

								$msg = array(
									'msg' => 'Leave Discarded',
									'location' => site_url('employee_leave'),
									'type' => 'success'

								);
								$this->load->view('swal', $msg);

							else:


							endif;

						else:

							redirect('error_404');
						endif;


					endif;

				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}


	public function employee_appraisal()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):


					$data['user_data'] = $this->users->get_user($username);
					$data['appraisals'] = $this->employees->get_appraisals();
					$data['employees'] = $this->employees->view_employees();
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/employee_appraisal', $data);
				else:

					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}


	public function new_employee_appraisal()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):


					$data['user_data'] = $this->users->get_user($username);
					//$data['appraisals'] = $this->employees->get_appraisals();
					$data['employees'] = $this->employees->view_employees();
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/new_employee_appraisal', $data);
				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}



	public function add_new_employee_appraisal()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):

			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):

			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;
			$data['notifications'] = $this->employees->get_notifications(0);

			if ($permission->employee_management == 1):

				$data['user_data'] = $this->users->get_user($username);
				//$data['appraisals'] = $this->employees->get_appraisals();
				$employee_id = $this->input->post('employee_id');
				$supervisor_id = $this->input->post('supervisor_id');
				$start_date = $this->input->post('start_date');
				$end_date = $this->input->post('end_date');

				if ($employee_id == $supervisor_id):
					$msg = array(
						'msg' => 'Employee and supervisor cannot be the same',
						'location' => site_url('employee_appraisal'),
						'type' => 'error'

					);
					$this->load->view('swal', $msg);

				else:
					$check = $this->employees->check_employee_appraisal($employee_id);
					if (!empty($check)):

						$msg = array(
							'msg' => 'Employee has pending appraisal',
							'location' => site_url('employee_appraisal'),
							'type' => 'error'

						);
						$this->load->view('swal', $msg);
					else:

						$appraisal_array = array(
							'employee_appraisal_employee_id' => $employee_id,
							'employee_appraisal_period_from' => $start_date,
							'employee_appraisal_period_to' => $end_date,
							'employee_appraisal_supervisor_id' => $supervisor_id,
							'employee_appraisal_status' => 0
						);

						$appraisal_array = $this->security->xss_clean($appraisal_array);
						$appraisal_id = $this->employees->insert_appraisal($appraisal_array);

						//1 == employee comment 2 == qunatitative 3 == qualitative, 4 == supervisor

						$self_assessments_questions = $this->hr_configurations->view_self_assessments();


						foreach ($self_assessments_questions as $self_assessments_question):

							$self_question_array = array(
								'employee_appraisal_result_appraisal_id' => $appraisal_id,
								'employee_appraisal_result_type' => 1,
								'employee_appraisal_result_question' => $self_assessments_question->self_appraisee_question,


							);
							$self_question_array = $this->security->xss_clean($self_question_array);
							$this->employees->insert_question_result($self_question_array);

						endforeach;

						$employee_job_role_id = $this->employees->get_employee($employee_id)->employee_job_role_id;


						$quantitative_assessment_questions = $this->hr_configurations->view_quantitative_assessments($employee_job_role_id);

						foreach ($quantitative_assessment_questions as $quantitative_assessment_question):

							$quantitative_question_array = array(
								'employee_appraisal_result_appraisal_id' => $appraisal_id,
								'employee_appraisal_result_type' => 2,
								'employee_appraisal_result_question' => $quantitative_assessment_question->quantitative_question,


							);
							$quantitative_question_array = $this->security->xss_clean($quantitative_question_array);
							$this->employees->insert_question_result($quantitative_question_array);

						endforeach;


						$qualitative_assessments_questions = $this->hr_configurations->view_qualitative_assessments();

						foreach ($qualitative_assessments_questions as $qualitative_assessments_question):

							$qualitative_question_array = array(
								'employee_appraisal_result_appraisal_id' => $appraisal_id,
								'employee_appraisal_result_type' => 3,
								'employee_appraisal_result_question' => $qualitative_assessments_question->qualitative_question,


							);
							$qualitative_question_array = $this->security->xss_clean($qualitative_question_array);
							$this->employees->insert_question_result($qualitative_question_array);

						endforeach;


						$supervisor_assessments_questions = $this->hr_configurations->view_supervisor_assessments();

						foreach ($supervisor_assessments_questions as $supervisor_assessments_question):

							$supervisor_question_array = array(
								'employee_appraisal_result_appraisal_id' => $appraisal_id,
								'employee_appraisal_result_type' => 4,
								'employee_appraisal_result_question' => $supervisor_assessments_question->supervisor_appraisee_question,


							);
							$supervisor_question_array = $this->security->xss_clean($supervisor_question_array);
							$query = $this->employees->insert_question_result($supervisor_question_array);

						endforeach;

						if ($query == true):



							$notification_data = array(
								'notification_employee_id'=> $employee_id,
								'notification_link'=> 'appraisals',
								'notification_type' => 'Appraisal Started',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);

							$notification_data = array(
								'notification_employee_id'=> $supervisor_id,
								'notification_link'=> 'appraise_employee',
								'notification_type' => 'New Employee to be Appraised',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);
							$msg = array(
								'msg' => 'Appraisal Started',
								'location' => site_url('employee_appraisal'),
								'type' => 'success'
							);
							$this->load->view('swal', $msg);

						else:


						endif;

					endif;

				endif;

				$data['employees'] = $this->employees->view_employees();
				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();

				$this->load->view('employee/new_employee_appraisal', $data);


			else:

				redirect('/access_denied');

			endif;
			else:
				redirect('error_404');
				endif;
		else:
			redirect('/login');
		endif;

	}

	public function check_appraisal_result(){
		$username = $this->session->userdata('user_username');
		$appraisal_id = $this->uri->segment(2);
		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$questions = $this->employees->get_appraisal_questions($appraisal_id);

					if(empty($questions)):

						redirect('error_404');

					else:

						$data['user_data'] = $this->users->get_user($username);

						//$data['employee'] = $this->employees->get_employee_by_unique($username);

						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();

						//$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

						$data['questions'] = $questions;

						$data['appraisal_id'] = $appraisal_id;



					//$this->load->view('employee_self_service/appraisal_result', $data);



						$this->load->view('employee/appraisal_result', $data);

						endif;
				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function reassign_supervisor(){
		$username = $this->session->userdata('user_username');
		$appraisal_id = $this->uri->segment(2);
		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$questions = $this->employees->get_appraisal_questions($appraisal_id);

					if(empty($questions)):

						redirect('error_404');

					else:

						$data['user_data'] = $this->users->get_user($username);

						//$data['employee'] = $this->employees->get_employee_by_unique($username);

						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();

						//$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

						$data['questions'] = $questions;
						$data['employees'] = $this->employees->view_employees();
						$data['appraisal_id'] = $appraisal_id;
						$data['appraisal_detail'] = $this->employees->get_appraisal($appraisal_id);



						//$this->load->view('employee_self_service/appraisal_result', $data);



						$this->load->view('employee/reassign_supervisor', $data);

					endif;
				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function update_employee_appraisal(){
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$permission = $this->users->check_permission($username);
			$data['employee_management'] = $permission->employee_management;
			$data['payroll_management'] = $permission->payroll_management;
			$data['biometrics'] = $permission->biometrics;
			$data['user_management'] = $permission->user_management;
			$data['configuration'] = $permission->configuration;
			$data['payroll_configuration'] = $permission->payroll_configuration;
			$data['hr_configuration'] = $permission->hr_configuration;
			$data['notifications'] = $this->employees->get_notifications(0);

			if ($permission->employee_management == 1):

				$method = $this->input->server('REQUEST_METHOD');

				if($method == 'POST' || $method == 'Post' || $method == 'post'):

				$data['user_data'] = $this->users->get_user($username);
				//$data['appraisals'] = $this->employees->get_appraisals();
				$employee_id = $this->input->post('employee_id');
				$supervisor_id = $this->input->post('supervisor_id');
				$appraisal_id  = $this->input->post('appraisal_id');


				if ($employee_id == $supervisor_id):
					$msg = array(
						'msg' => 'Employee and supervisor cannot be the same',
						'location' => site_url('employee_appraisal'),
						'type' => 'error'

					);
					$this->load->view('swal', $msg);

				else:


						$appraisal_array = array(

							'employee_appraisal_supervisor_id' => $supervisor_id,
							'employee_appraisal_status' => 0,
							'employee_appraisal_self' => 0
						);

				$query = $this->employees->update_appraisal($appraisal_id, $appraisal_array);



						if ($query == true):



							$notification_data = array(
								'notification_employee_id'=> $employee_id,
								'notification_link'=> 'appraisals',
								'notification_type' => 'Appraisal Supervisor Updated',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);

							$notification_data = array(
								'notification_employee_id'=> $supervisor_id,
								'notification_link'=> 'appraise_employee',
								'notification_type' => 'New Employee to be Appraised',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);
							$msg = array(
								'msg' => 'Appraisal Supervisor Updated',
								'location' => site_url('employee_appraisal'),
								'type' => 'success'
							);
							$this->load->view('swal', $msg);

						else:


						endif;



				endif;


				else:
					redirect('error_404');
					endif;
			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}


	public function terminate_employee()
	{
		$employee_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):
					$employee = $this->employees->get_employee($employee_id);

					if($employee->employee_status == 0 || $employee->employee_status == 3):
						$msg = array(
							'msg' => 'Employee Not Active',
							'location' => site_url('employee'),
							'type' => 'error'
						);
						$this->load->view('swal', $msg);

					endif;

					if($employee->employee_status == 1 || $employee->employee_status == 2):
					$data['user_data'] = $this->users->get_user($username);
					$data['employee'] = $employee;
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['employee_id'] = $employee_id;

					$this->load->view('employee/terminate_employee', $data);

					endif;
				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function terminate()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):

			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				$data['notifications'] = $this->employees->get_notifications(0);
				if ($permission->employee_management == 1):

					$termination_employee_id = $this->input->post('termination_employee_id');
					$termination_reason = $this->input->post('termination_reason');
					$termination_effective_date = $this->input->post('termination_effective_date');

					$_termination_effective_date = strtotime($termination_effective_date);
					$_now = time();

					if($_termination_effective_date <= $_now):

						$msg = array(
							'msg' => 'Choose a date greater than today',
							'location' => site_url('terminate_employee')."/".$termination_employee_id,
							'type' => 'error'
						);
						$this->load->view('swal', $msg);


					else:

						$employee_data = array(
							'employee_status' => 0
						);

					$query = $this->employees->update_employee($termination_employee_id, $employee_data);




					$termination_array = array(
					'termination_employee_id' => $termination_employee_id,
					'termination_reason' => $termination_reason,
					'termination_effective_date' => $termination_effective_date
					);

					$termination_array = $this->security->xss_clean($termination_array);

					$query = $this->employees->insert_termination($termination_array);


					if($query == true):



						$notification_data = array(
							'notification_employee_id'=> $termination_employee_id,
							'notification_link'=> 'employee_resignation',
							'notification_type' => 'Termination Notice',
							'notification_status'=> 0
						);

						$this->employees->insert_notifications($notification_data);

						$msg = array(
							'msg' => 'Employee Terminated',
							'location' => site_url('employee'),
							'type' => 'success'
						);
						$this->load->view('swal', $msg);


						else:

							echo "An Error Occurred";
							endif;

					endif;

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
			else:
				redirect('error_404');
				endif;
		else:
			redirect('/login');
		endif;

	}

	public function terminations()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				$data['notifications'] = $this->employees->get_notifications(0);
				if ($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);
					$data['terminations'] = $this->employees->get_terminations();

					$this->load->view('employee/terminations', $data);

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function resignations()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);
					$data['resignations'] = $this->employees->get_resignations();

					$this->load->view('employee/resignations', $data);

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function approve_resignation()
	{
		$resignation_id = $this->uri->segment(2);
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$check = $this->employees->get_resignation($resignation_id);

					if(empty($check)):

						redirect('error_404');

					else:

						if($check->resignation_status == 1 || $check->resignation_status == 2):

							$msg = array(
								'msg' => 'Resignation Status already Updated',
								'location' => site_url('resignations'),
								'type' => 'error'
							);
							$this->load->view('swal', $msg);
							endif;
						if($check->resignation_status == 0 ):

							$resignation_array = array(
								'resignation_status' => 1,

							);

							$resignation_array = $this->security->xss_clean($resignation_array);

							$query = $this->employees->update_resignation($resignation_id, $resignation_array);

							if($query == true):

								$resignation = $this->employees->get_resignation($resignation_id);

								$notification_data = array(
									'notification_employee_id'=> $resignation->resignation_employee_id,
									'notification_link'=> 'employee_resignation',
									'notification_type' => 'Resignation Approved',
									'notification_status'=> 0
								);

								$this->employees->insert_notifications($notification_data);

								$msg = array(
									'msg' => 'Resignation Approved',
									'location' => site_url('resignations'),
									'type' => 'success'
								);
								$this->load->view('swal', $msg);

							else:



							endif;

						endif;


						endif;

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function discard_resignation()
	{
		$resignation_id = $this->uri->segment(2);
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				$data['notifications'] = $this->employees->get_notifications(0);
				if ($permission->employee_management == 1):

					$check = $this->employees->get_resignation($resignation_id);

					if(empty($check)):

						redirect('error_404');

					else:

						if($check->resignation_status == 1 || $check->resignation_status == 2):

							$msg = array(
								'msg' => 'Resignation Status already Updated',
								'location' => site_url('resignations'),
								'type' => 'error'
							);
							$this->load->view('swal', $msg);
						endif;
						if($check->resignation_status == 0 ):

							$resignation_array = array(
								'resignation_status' => 2,

							);

							$resignation_array = $this->security->xss_clean($resignation_array);

							$query = $this->employees->update_resignation($resignation_id, $resignation_array);

							if($query == true):
								$resignation = $this->employees->get_resignation($resignation_id);

								$notification_data = array(
									'notification_employee_id'=> $resignation->resignation_employee_id,
									'notification_link'=> 'employee_resignation',
									'notification_type' => 'Resignation Discarded',
									'notification_status'=> 0
								);

								$this->employees->insert_notifications($notification_data);
								$msg = array(
									'msg' => 'Resignation Discarded',
									'location' => site_url('resignations'),
									'type' => 'success'
								);
								$this->load->view('swal', $msg);

							else:



							endif;

						endif;


					endif;

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;
	}


	public function query_employee()
	{
		$employee_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);

					$data['employee'] = $this->employees->get_employee($employee_id);

					if(!empty($data['employee'])):

					$data['queries'] = $this->employees->get_queries_employee($employee_id);
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/queries', $data);

					else:

						redirect('error_404');

					endif;

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function new_query()
	{
		$employee_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				$data['notifications'] = $this->employees->get_notifications(0);
				if ($permission->employee_management == 1):

					$method = $this->input->server('REQUEST_METHOD');
				if($method == 'POST' || $method == 'Post' || $method == 'post'):

					extract($_POST);

					$query_array = array(
						'query_employee_id' => $employee_id,
						'query_subject' => $query_subject,
						'query_body' => $query_body,
						'query_type' => $query_type,
						'query_date' => date("Y-m-d")

					);

					$query_array = $this->security->xss_clean($query_array);
					$query = $this->employees->insert_query($query_array);

					if($query == true):

						$notification_data = array(
							'notification_employee_id'=> $employee_id,
							'notification_link'=> 'my_queries',
							'notification_type' => 'New Query',
							'notification_status'=> 0
						);

					$this->employees->insert_notifications($notification_data);

						$msg = array(
							'msg' => 'Query Submitted',
							'location' => site_url('query_employee').'/'.$employee_id,
							'type' => 'success'
						);
						$this->load->view('swal', $msg);


						else:

							endif;
					else:

						redirect('error_404');
						endif;
				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function view_query()
	{
		$query_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);



					$query = $this->employees->get_query($query_id);



					if(!empty($query)):

						$data['employee'] = $this->employees->get_employee($query->query_employee_id);

						$data['query'] = $this->employees->get_query($query_id);
						$data['responses'] = $this->employees->get_query_response($query_id);
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();

						$this->load->view('employee/view_query', $data);

					else:

						redirect('error_404');

					endif;

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function new_response()
	{

		$query_id = $_GET['query_id'];
		$query_response = $_GET['query_response'];
		$query_responder_id = $_GET['query_responder_id'];

		$employee_id = $this->employees->get_query($query_id)->query_employee_id;


		$response_array = array(

		'query_response_query_id' => $query_id,
		'query_response_responder_id' => $query_responder_id,
		'query_response_body' => $query_response

		);

		if($query_responder_id == 0):
			$notification_data = array(
				'notification_employee_id'=> $employee_id ,
				'notification_link'=> 'view_my_query/'.$query_id,
				'notification_type' => 'Response to an Open Query',
				'notification_status'=> 0
			);

			$this->employees->insert_notifications($notification_data);

			else:
				$notification_data = array(
					'notification_employee_id'=> 0,
					'notification_link'=> 'view_query/'.$query_id,
					'notification_type' => 'Response to an Open Query',
					'notification_status'=> 0
				);

				$this->employees->insert_notifications($notification_data);

				endif;



		echo $this->employees->insert_query_response($response_array);


	}

	public function close_query()
	{
		$query_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);



					$query = $this->employees->get_query($query_id);



					if(!empty($query)):

						$query_array = array(

							'query_status' => 0

						);

						$query_array = $this->security->xss_clean($query_array);
						$query = $this->employees->update_query($query_id, $query_array);

						if($query == true):
							$queri = $this->employees->get_query($query_id);

							$notification_data = array(
								'notification_employee_id'=> $queri->query_employee_id,
								'notification_link'=> 'my_queries',
								'notification_type' => 'Query Closed',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);

							$msg = array(
								'msg' => 'Query closed',
								'location' => site_url('view_query').'/'.$query_id,
								'type' => 'success'
							);
							$this->load->view('swal', $msg);


						else:

						endif;



					else:

						redirect('error_404');

					endif;

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function memo()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);


						$data['memos'] = $this->employees->get_memos();
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();

						$this->load->view('employee/memos', $data);





				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function add_memo()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):

			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

						extract($_POST);

						$memo_array = array(
						'memo_subject' => $memo_subject,
						'memo_body' => $memo_body

						);


					$memo_array = $this->security->xss_clean($memo_array);
					$query = $this->employees->insert_memo($memo_array);

					if($query == true):

						$employees = $this->employees->view_employees();

					foreach ($employees as $employee):

						$notification_data = array(
							'notification_employee_id'=> $employee->employee_id,
							'notification_link'=> 'my_memos',
							'notification_type' => 'New Announcement',
							'notification_status'=> 0
						);

						$this->employees->insert_notifications($notification_data);

					endforeach;
						$msg = array(
							'msg' => 'Memo Sent',
							'location' => site_url('memo'),
							'type' => 'success'
						);
						$this->load->view('swal', $msg);


					else:

					endif;


				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;

			else:

				redirect('error_404');

				endif;
		else:
			redirect('/login');
		endif;

	}

	public function update_memo()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				$data['notifications'] = $this->employees->get_notifications(0);
				if ($permission->employee_management == 1):

					$method = $this->input->server('REQUEST_METHOD');
					if($method == 'POST' || $method == 'Post' || $method == 'post'):

						extract($_POST);

						$memo_array = array(
							'memo_subject' => $memo_subject,
							'memo_body' => $memo_body

						);


						$memo_array = $this->security->xss_clean($memo_array);
						$query = $this->employees->update_memo($memo_id, $memo_array);

						if($query == true):

							$msg = array(
								'msg' => 'Memo Updated',
								'location' => site_url('memo'),
								'type' => 'success'
							);
							$this->load->view('swal', $msg);


						else:

						endif;
					else:

						redirect('error_404');
					endif;






				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function specific_memo()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				$data['notifications'] = $this->employees->get_notifications(0);
				if ($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);





					$data['memos'] = $this->employees->get_specific_memos();
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/specific_memos', $data);





				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}

	public function new_specific_memo(){
		$username = $this->session->userdata('user_username');
		//$employee_id = $this->uri->segment(2);

		if(isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if($user_type == 1 || $user_type == 3):

				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);
				if($permission->employee_management == 1):

					$data['user_data'] = $this->users->get_user($username);

					$data['departments'] = $this->hr_configurations->view_departments();
					$data['employees'] = $this->employees->view_employees();

					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/new_specific_memo', $data);

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function add_specific_memo()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):

			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				$data['notifications'] = $this->employees->get_notifications(0);
				if ($permission->employee_management == 1):

					extract($_POST);


					if($category == 1):

						$employees = $this->employees->get_employees_by_department($department_id);
						foreach ($employees as $employee):
							$memo_array = array(
								'specific_memo_employee_id' => $employee->employee_id,
								'specific_memo_subject' => $memo_subject,
								'specific_memo_body' => $memo_body,

							);
							$memo_array = $this->security->xss_clean($memo_array);
							//print_r($variational_payment);
							$query = $this->employees->insert_specific_memo($memo_array);





								$notification_data = array(
									'notification_employee_id'=> $employee->employee_id,
									'notification_link'=> 'my_specific_memos',
									'notification_type' => 'New Memo',
									'notification_status'=> 0
								);

								$this->employees->insert_notifications($notification_data);



						endforeach;

						endif;

					if($category == 2):

						$employees = $this->input->post('employee_ids');
						foreach ($employees as $employee):
							$memo_array = array(
								'specific_memo_employee_id' => $employee,
								'specific_memo_subject' => $memo_subject,
								'specific_memo_body' => $memo_body,

							);
							$memo_array = $this->security->xss_clean($memo_array);

							//print_r($variational_payment);
							$query = $this->employees->insert_specific_memo($memo_array);

							$notification_data = array(
								'notification_employee_id'=> $employee,
								'notification_link'=> 'my_specific_memos',
								'notification_type' => 'New Memo',
								'notification_status'=> 0
							);

							$this->employees->insert_notifications($notification_data);
						endforeach;
					endif;



				if($query == true):

						$msg = array(
							'msg' => 'Memo Sent',
							'location' => site_url('specific_memo'),
							'type' => 'success'
						);
						$this->load->view('swal', $msg);


					else:

					endif;


				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;

			else:
				redirect('error_404');
				endif;
		else:
			redirect('/login');
		endif;

	}



	public function update_specific_memo()
	{

		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);

				if ($permission->employee_management == 1):

					$method = $this->input->server('REQUEST_METHOD');
					if($method == 'POST' || $method == 'Post' || $method == 'post'):

						extract($_POST);

						$memo_array = array(
							'specific_memo_subject' => $memo_subject,
							'specific_memo_body' => $memo_body

						);


						$memo_array = $this->security->xss_clean($memo_array);
						$query = $this->employees->update_specific_memo($memo_id, $memo_array);

						if($query == true):

							$msg = array(
								'msg' => 'Memo Updated',
								'location' => site_url('specific_memo'),
								'type' => 'success'
							);
							$this->load->view('swal', $msg);


						else:

						endif;
					else:

						redirect('error_404');
					endif;






				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;

	}


	public function employee_trainings(){
		$username = $this->session->userdata('user_username');
		//$employee_id = $this->uri->segment(2);

		if(isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if($user_type == 1 || $user_type == 3):

				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);
				if($permission->employee_management == 1):


					$data['user_data'] = $this->users->get_user($username);

					$data['trainings'] = $this->employees->get_trainings();
					//$data['employees'] = $this->employees->view_employees();

					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/employee_trainings', $data);

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}


	public function new_employee_training(){
		$username = $this->session->userdata('user_username');
		//$employee_id = $this->uri->segment(2);

		if(isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if($user_type == 1 || $user_type == 3):

				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);
				if($permission->employee_management == 1):


					$data['user_data'] = $this->users->get_user($username);

					$data['trainings'] = $this->hr_configurations->view_trainings();
					$data['employees'] = $this->employees->view_employees();

					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee/new_employee_training', $data);

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function add_new_employee_training(){
		$username = $this->session->userdata('user_username');
		//$employee_id = $this->uri->segment(2);

		if(isset($username)):

			$method = $this->input->server('REQUEST_METHOD');

			if($method == 'POST' || $method == 'Post' || $method == 'post'):
			$user_type = $this->users->get_user($username)->user_type;

			if($user_type == 1 || $user_type == 3):
				$data['notifications'] = $this->employees->get_notifications(0);
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;

				if($permission->employee_management == 1):

					extract($_POST);

					$employee_training_array = array(
						'employee_training_employee_id' => $employee_id,
						'employee_training_training_id' => $training_id,
						'employee_training_start_date' => $start_date,
						'employee_training_end_date' => $end_date

					);

					$employee_training_array = $this->security->xss_clean($employee_training_array);

					$employee_training_id = $this->employees->insert_employee_training($employee_training_array);

					$questions = $this->hr_configurations->view_training_questions($training_id);

					$check = 0;

					foreach ($questions as $question):

						$result_array = array(
							'training_result_employee_id' => $employee_id,
							'training_result_training_id' => $training_id,
							'training_result_employee_training_id' =>$employee_training_id,
							'training_result_question_id' => $question->training_question_id,
							'training_result_correct_answer' => $question->training_question_correct,
							'training_result_answer' => 'E'
						);

						$result_array = $this->security->xss_clean($result_array);

						$result_id = $this->employees->insert_training_result($result_array);

						if(isset($result_id)):
							$check ++;
							endif;

						endforeach;

					if ($check > 0):

						$notification_data = array(
							'notification_employee_id'=> $employee_id,
							'notification_link'=> 'my_trainings',
							'notification_type' => 'New Training',
							'notification_status'=> 0
						);

						$this->employees->insert_notifications($notification_data);

						$log_array = array(
							'log_user_id' => $this->users->get_user($username)->user_id,
							'log_description' => "Updated Training "
						);

						$this->logs->add_log($log_array);


						$msg = array(
							'msg' => 'Training Initiated Successfully',
							'location' => site_url('employee_trainings'),
							'type' => 'success'

						);
						$this->load->view('swal', $msg);
					else:

						$this->employees->delete_employee_training($training_id);
						$msg = array(
							'msg' => 'No Questions for the Training',
							'location' => site_url('employee_trainings'),
							'type' => 'error'

						);
						$this->load->view('swal', $msg);

					endif;


				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;

			else:
				redirect('error_404');
				endif;
		else:
			redirect('/login');
		endif;


	}

	public function view_training_result(){
		$username = $this->session->userdata('user_username');
		$employee_training_id = $this->uri->segment(2);
		$data['notifications'] = $this->employees->get_notifications(0);
		if(isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if($user_type == 1 || $user_type == 3):
				$permission = $this->users->check_permission($username);
				$data['employee_management'] = $permission->employee_management;
				$data['payroll_management'] = $permission->payroll_management;
				$data['biometrics'] = $permission->biometrics;
				$data['user_management'] = $permission->user_management;
				$data['configuration'] = $permission->configuration;
				$data['payroll_configuration'] = $permission->payroll_configuration;
				$data['hr_configuration'] = $permission->hr_configuration;
				$data['notifications'] = $this->employees->get_notifications(0);
				if($permission->employee_management == 1):

					if(empty($employee_training_id)):

						redirect('error_404');

					else:

						//$check_existing_training = $this->hr_configurations-> view_training($training_id);

						$check_existing_employee_training = $this->employees-> get_employee_training_($employee_training_id);

						if(empty($check_existing_employee_training)):

							redirect('error_404');

						else:

							$data['user_data'] = $this->users->get_user($username);
							$data['employee_training'] = $check_existing_employee_training;
							$data['csrf_name'] = $this->security->get_csrf_token_name();
							$data['csrf_hash'] = $this->security->get_csrf_hash();




							$this->load->view('employee/test_result', $data);

						endif;

					endif;

				else:
					redirect('/access_denied');
				endif;

			else:

				redirect('/access_denied');

			endif;
		else:
			redirect('/login');
		endif;


	}

	public function view_notifications(){
		$username = $this->session->userdata('user_username');
		if(isset($username)):
			$notification_id = $query_id = $this->uri->segment(2);

			$notification = $this->employees->get_notification($notification_id);
			if(empty($notification)):

				redirect('error_404');
			else:

					$notification_data = array(
						'notification_status'=> 1
					);

					$this->employees->update_notification($notification_id, $notification_data);
					redirect($notification->notification_link);

			endif;

		else:
			redirect('error_404');
		endif;
	}

	public function clear_notifications(){

		$username = $this->session->userdata('user_username');
		if(isset($username)):
			$notification_id = $query_id = $this->uri->segment(2);


			if($notification_id == 'a'):
				$notifications = $this->employees->get_notifications(0);
			if(!empty($notifications)):
				foreach ($notifications as $notification):

					$notification_data = array(
						'notification_status'=> 1
					);

					$this->employees->update_notification($notification->notification_id, $notification_data);
					endforeach;
				redirect($this->agent->referrer());
					else:
						redirect('error_404');
						endif;

				else:

					$notifications = $this->employees->get_notifications($notification_id);
				if(!empty($notifications)):
					foreach ($notifications as $notification):

						$notification_data = array(
							'notification_status'=> 1
						);

						$this->employees->update_notification($notification->notification_id, $notification_data);
					endforeach;
					redirect($this->agent->referrer());
					else:
						redirect('error_404');
					endif;
				endif;
			$notification = $this->employees->get_notification($notification_id);


		else:
			redirect('error_404');
		endif;
	}

	public function get_employees() {
    	  echo json_encode($this->employees->view_employees());
  }

  public function employee_queries() {
    $username = $this->session->userdata('user_username');
    if(isset($username)):
      $user_type = $this->users->get_user($username)->user_type;
      if($user_type == 1 || $user_type == 3):
        $permission = $this->users->check_permission($username);
        $data['employee_management'] = $permission->employee_management;
        $data['payroll_management'] = $permission->payroll_management;
        $data['biometrics'] = $permission->biometrics;
        $data['user_management'] = $permission->user_management;
        $data['configuration'] = $permission->configuration;
        $data['payroll_configuration'] = $permission->payroll_configuration;
        $data['hr_configuration'] = $permission->hr_configuration;
        $data['notifications'] = $this->employees->get_notifications(0);
        if($permission->employee_management == 1):
          $data['user_data'] = $this->users->get_user($username);
          $data['queries'] = $this->employees->get_queries();
          $data['csrf_name'] = $this->security->get_csrf_token_name();
          $data['csrf_hash'] = $this->security->get_csrf_hash();
          $this->load->view('employee/employee_queries', $data);
        else:
          redirect('/access_denied');
        endif;
      else:
        redirect('/access_denied');
      endif;
    else:
        redirect('/login');
    endif;
  }
}
