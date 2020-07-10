<?php


class Employee_main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->helper('security');
		$this->load->helper('string');
		$this->load->helper('array');
		$this->load->model('users');
		$this->load->model('employees');
		$this->load->model('hr_configurations');
		$this->load->model('logs');
		$this->load->model('salaries');
		$this->load->model('loans');
		$this->load->model('payroll_configurations');
	}

	public function index(){
		$username = $this->session->userdata('user_username');



		if(isset($username)):


//				//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;




				if($user_type == 2 || $user_type == 3):

					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

						$terminations = $this->employees->get_employee_terminations($employee_id);

						if(!empty($terminations)):

							$count_termination = 0;

							foreach ($terminations as $termination):

								if(strtotime($termination->termination_effective_date) <= time()):

									$count_termination++;
									endif;

								endforeach;

							endif;


					$resignations = $this->employees->get_employee_resignations($employee_id);

					if(!empty($resignations)):

						$count_resignation = 0;

						foreach ($resignations as $resignation):

							if($resignation->resignation_status == 1):

							if(strtotime($resignation->resignation_effective_date) <= time()):

								$count_resignation++;
							endif;

							endif;

						endforeach;

					endif;


			if(@$count_termination > 0 || @$count_resignation > 0):

					$employee_data = array(
						'employee_status' => 0,
						'employee_stop_date' => date("Y-m-d")
					);

				$query_ = $this->employees->update_employee($employee_id, $employee_data);

				$user_id = $this->users->get_user($username)->user_id;

				$user_data = array(

					'user_status'=> 0

				);

				$_query = $this->users->update_user($user_id, $user_data);

				if($_query == true && $query_ == true):

					$msg = array(
						'msg' => 'Your Employment has been Terminated',
						'location' => site_url('logout'),
						'type' => 'error'
					);
					$this->load->view('swal', $msg);

					endif;

			else:

				$data['user_data'] = $this->users->get_user($username);
				$data['queries'] = $this->employees->get_queries_employee($employee_id);

				$data['employee'] = $this->employees->get_employee_by_unique($username);
				$data['memos'] = $this->employees->get_memos();

				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();

				$this->load->view('employee_self_service/dashboard', $data);

			endif;

				elseif($user_type == 1):

					redirect('/access_denied');

					endif;



		else:
			redirect('/login');
		endif;

	}

	public function personal_information(){
		$username = $this->session->userdata('user_username');



		if(isset($username)):


//				//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;




			if($user_type == 2 || $user_type == 3):

				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

				$terminations = $this->employees->get_employee_terminations($employee_id);

				if(!empty($terminations)):

					$count_termination = 0;

					foreach ($terminations as $termination):

						if(strtotime($termination->termination_effective_date) <= time()):

							$count_termination++;
						endif;

					endforeach;

				endif;


				$resignations = $this->employees->get_employee_resignations($employee_id);

				if(!empty($resignations)):

					$count_resignation = 0;

					foreach ($resignations as $resignation):

						if($resignation->resignation_status == 1):

							if(strtotime($resignation->resignation_effective_date) <= time()):

								$count_resignation++;
							endif;

						endif;

					endforeach;

				endif;


				if(@$count_termination > 0 || @$count_resignation > 0):

					$employee_data = array(
						'employee_status' => 0,
						'employee_stop_date' => date("Y-m-d")
					);

					$query_ = $this->employees->update_employee($employee_id, $employee_data);

					$user_id = $this->users->get_user($username)->user_id;

					$user_data = array(

						'user_status'=> 0

					);

					$_query = $this->users->update_user($user_id, $user_data);

					if($_query == true && $query_ == true):

						$msg = array(
							'msg' => 'Your Employment has been Terminated',
							'location' => site_url('logout'),
							'type' => 'error'
						);
						$this->load->view('swal', $msg);

					endif;

				else:

					$data['user_data'] = $this->users->get_user($username);

					$data['employee'] = $this->employees->get_employee_by_unique($username);

					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee_self_service/home', $data);

				endif;

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;



		else:
			redirect('/login');
		endif;

	}


	public function employee_history(){
		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);

				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();


				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

				$data['histories'] = $this->employees->view_employee_history($employee_id);


				//$this->load->view('log/view_logs', $data);

				$this->load->view('employee_self_service/employee_history', $data);

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function my_leave(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):

			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$errormsg = ' ';
				$error_msg = array('error' => $errormsg);
				$data['error'] = $errormsg;
				$data['user_data'] = $this->users->get_user($username);
				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();

				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

				$data['leaves'] = $this->employees-> check_existing_employee_leaves($employee_id);



				$this->load->view('employee_self_service/employee_leave',$data);

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;
	}

	public function request_leave(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):

			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$errormsg = ' ';
				$error_msg = array('error' => $errormsg);
				$data['error'] = $errormsg;
				$data['user_data'] = $this->users->get_user($username);
				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();
				$data['employee'] = $this->employees->get_employee_by_unique($username);
				$data['leaves'] = $this->hr_configurations->view_leaves();
				//$data['employees'] = $this->employees->view_employees();


				$this->load->view('employee_self_service/new_employee_leave', $data);
			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;
	}


	public function request_new_leave(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):

			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$employee_id = $this->input->post('employee_id');
				$leave_id = $this->input->post('leave_id');
				$start_date = $this->input->post('start_date');
				$end_date = $this->input->post('end_date');


				$check_existing_leaves = $this->employees->check_existing_employee_leaves($employee_id);



				if(!empty($check_existing_leaves)):
					$count = 0;
					foreach ($check_existing_leaves as $check_existing_leave):

						if($check_existing_leave->leave_status == 0 || $check_existing_leave->leave_status == 1):
							$count++;
						endif;

					endforeach;

					if($count > 0):

						$msg = array(
							'msg'=> 'You have an Existing Leave',
							'location' => site_url('my_leave'),
							'type' => 'error'

						);
						$this->load->view('swal', $msg);
					else:

						$leave_array = array(
							'leave_employee_id'=> $employee_id,
							'leave_leave_type' => $leave_id,
							'leave_start_date' => $start_date,
							'leave_end_date' => $end_date,
							'leave_status' => 0

						);

						$leave_array = $this->security->xss_clean($leave_array);
						$query = $this->employees->insert_leave($leave_array);

						if($query == true):



							$msg = array(
								'msg'=> 'Leave Application Successful',
								'location' => site_url('my_leave'),
								'type' => 'success'

							);
							$this->load->view('swal', $msg);

						endif;

					endif;
				else:

					$leave_array = array(
						'leave_employee_id'=> $employee_id,
						'leave_leave_type' => $leave_id,
						'leave_start_date' => $start_date,
						'leave_end_date' => $end_date,
						'leave_status' => 0

					);

					$leave_array = $this->security->xss_clean($leave_array);
					$query = $this->employees->insert_leave($leave_array);

					if($query == true):

						$log_array = array(
							'log_user_id' => $this->users->get_user($username)->user_id,
							'log_description' => "Initiated Employee Transfer"
						);

						$this->logs->add_log($log_array);

						$employee_history_array = array(
							'employee_history_employee_id' => $employee_id,
							'employee_history_details' =>'Leave Application'

						);

						$this->employees->insert_employee_history($employee_history_array);

						$msg = array(
							'msg'=> 'Leave Application Successful',
							'location' => site_url('my_leave'),
							'type' => 'success'

						);
						$this->load->view('swal', $msg);
					else:


					endif;

				endif;
			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;
	}

	public function appraisals(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);

				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();


				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

				$data['histories'] = $this->employees->view_employee_history($employee_id);
				$data['appraisals'] = $this->employees->get_employee_appraisal($employee_id);


				//$this->load->view('log/view_logs', $data);

				$this->load->view('employee_self_service/employee_appraisal', $data);

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function appraise_employee(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);

				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();


				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

				$data['histories'] = $this->employees->view_employee_history($employee_id);
				$data['appraisals'] = $this->employees->get_appraise_employees($employee_id);


				//$this->load->view('log/view_logs', $data);

				$this->load->view('employee_self_service/appraise_employee', $data);

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}


	public function respond_appraisal_supervisor(){

		$appraisal_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);

				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();


				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;


				$data['questions'] = $this->employees->get_appraisal_questions($appraisal_id);
				$data['appraisal_id'] = $appraisal_id;


				//$this->load->view('log/view_logs', $data);

				$this->load->view('employee_self_service/respond_appraisal_supervisor', $data);

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function answer_questions_supervisor(){

		$appraisal_id = $this->input->post('appraisal_id');

		if(empty($appraisal_id)):

			redirect('error_404');

		else:

				$questions = $this->employees->get_appraisal_questions($appraisal_id);

				$count = 0;
				foreach($questions as $question):

					if($question->employee_appraisal_result_type == 2 || $question->employee_appraisal_result_type == 3 || $question->employee_appraisal_result_type == 4 ):

					$answer = $this->input->post($question->employee_appraisal_result_id);

					$answer_array = array(
						'employee_appraisal_result_answer' => $answer
					);

					$this->employees->answer_question($question->employee_appraisal_result_id, $answer_array);
					$count++;

					endif;
				endforeach;

				if($count >0):

					$appraisal_data = array(
						'employee_appraisal_supervisor'=> 1,
						'employee_appraisal_qualitative '=> 1,
						'employee_appraisal_quantitative'=>1
						);

					$this->employees->update_appraisal($appraisal_id, $appraisal_data);

					$check_appraisal= $this->employees->get_appraisal($appraisal_id);

					if($check_appraisal->employee_appraisal_supervisor == 1 && $check_appraisal->employee_appraisal_qualitative == 1 && $check_appraisal->employee_appraisal_quantitative == 1 && $check_appraisal->employee_appraisal_self == 1 ):
						$appraisal_data = array(

							'employee_appraisal_status'=>1
						);

						$this->employees->update_appraisal($appraisal_id, $appraisal_data);
						endif;

					$msg = array(
						'msg'=> 'Appraisal Completed',
						'location' => site_url('employee_main'),
						'type' => 'success'

					);
					$this->load->view('swal', $msg);
					endif;

		endif;

	}

	public function respond_appraisal_self(){

		$appraisal_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);

				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();


				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;


				$data['questions'] = $this->employees->get_appraisal_questions($appraisal_id);
				$data['appraisal_id'] = $appraisal_id;


				//$this->load->view('log/view_logs', $data);

				$this->load->view('employee_self_service/respond_appraisal_self', $data);

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function answer_questions_self(){

		$appraisal_id = $this->input->post('appraisal_id');

		if(empty($appraisal_id)):

			redirect('error_404');

		else:

			$questions = $this->employees->get_appraisal_questions($appraisal_id);

			$count = 0;
			foreach($questions as $question):

				if($question->employee_appraisal_result_type == 1):

					$answer = $this->input->post($question->employee_appraisal_result_id);

					$answer_array = array(
						'employee_appraisal_result_answer' => $answer
					);

					$this->employees->answer_question($question->employee_appraisal_result_id, $answer_array);
					$count++;

				endif;
			endforeach;

			if($count >0):

				$appraisal_data = array(
					'employee_appraisal_self'=>1
				);

				$this->employees->update_appraisal($appraisal_id, $appraisal_data);

				$check_appraisal= $this->employees->get_appraisal($appraisal_id);

				if($check_appraisal->employee_appraisal_supervisor == 1 && $check_appraisal->employee_appraisal_qualitative == 1 && $check_appraisal->employee_appraisal_quantitative == 1 && $check_appraisal->employee_appraisal_self == 1 ):
					$appraisal_data = array(

						'employee_appraisal_status'=>1
					);

					$this->employees->update_appraisal($appraisal_id, $appraisal_data);
				endif;

				$msg = array(
					'msg'=> 'Appraisal Completed',
					'location' => site_url('employee_main'),
					'type' => 'success'

				);
				$this->load->view('swal', $msg);
			endif;

		endif;

	}



	public function check_appraisal_results(){

		$appraisal_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):


				$questions = $this->employees->get_appraisal_questions($appraisal_id);

				if(empty($questions)):

					redirect('error_404');

				else:

					$data['user_data'] = $this->users->get_user($username);

					$data['employee'] = $this->employees->get_employee_by_unique($username);

					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

					$data['questions'] = $questions;

					$data['appraisal_id'] = $appraisal_id;



					$this->load->view('employee_self_service/appraisal_result', $data);

				endif;

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function pay_slip(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):




					$data['user_data'] = $this->users->get_user($username);

					$data['employee'] = $this->employees->get_employee_by_unique($username);

					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$data['employee_id'] = $this->employees->get_employee_by_unique($username)->employee_id;

					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['min_payroll_year'] = $this->salaries->view_min_payroll_year();

					$this->load->view('employee_self_service/pay_slip', $data);



			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}


	public function pay_slips(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$month = $this->input->post('month');
				$year = $this->input->post('year');


				if(empty($month) || empty($year)):


					redirect('error_404');

				else:

					$check = $this->salaries->view_emolument_sheet();
					$data['payroll_month'] = $month;
					$data['payroll_year'] = $year;
					$data['user_data'] = $this->users->get_user($username);

					$data['employee'] = $this->employees->get_employee_by_unique($username);


					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

					if(empty($check)):


						$payment_definitions = $this->payroll_configurations->view_payment_definitions_order();

						foreach ($payment_definitions as $payment_definition):

							$fields = array(
								'payment_definition_'.$payment_definition->payment_definition_id => array('type' => 'TEXT')
							);

							$this->salaries->new_column($fields);
						endforeach;


						$employees = $this->employees->view_employees();

						foreach ($employees as $employee):
							if($employee->employee_id == $employee_id):
							$emolument_data = array(

								'emolument_report_employee_id' => $employee->employee_id

							);

							$this->salaries->insert_emolument($emolument_data);

							$salaries = $this->salaries->view_salaries_emolument($employee->employee_id, $month, $year);

							foreach ($salaries as $salary):

								$emoluments_data = array(
									'payment_definition_'.$salary->salary_payment_definition_id => $salary->salary_amount

								);
								//print_r($emoluments_data);

								$this->salaries->update_emolument($employee->employee_id, $emoluments_data);

							endforeach;
						endif;
						endforeach;


						$data['emoluments'] = $this->salaries->view_emolument_sheet();

						$this->load->view('employee_self_service/_pay_slip', $data);

					else:

						$this->salaries->clear_emolument();
						$emolument_fields = $this->salaries->view_emolument_fields();

						foreach($emolument_fields as $emolument_field):

							$payment_definition_field = stristr($emolument_field,"payment_definition_");

							if(!empty($payment_definition_field)):

								$this->salaries->remove_field($payment_definition_field);


							endif;

						endforeach;


						$payment_definitions = $this->payroll_configurations->view_payment_definitions_order();

						foreach ($payment_definitions as $payment_definition):

							$fields = array(
								'payment_definition_'.$payment_definition->payment_definition_id => array('type' => 'TEXT')
							);

							$this->salaries->new_column($fields);
						endforeach;


						$employees = $this->employees->view_employees();

						foreach ($employees as $employee):
							if($employee->employee_id == $employee_id):
								$emolument_data = array(

									'emolument_report_employee_id' => $employee->employee_id

								);

								$this->salaries->insert_emolument($emolument_data);

								$salaries = $this->salaries->view_salaries_emolument($employee->employee_id, $month, $year);

								foreach ($salaries as $salary):

									$emoluments_data = array(
										'payment_definition_'.$salary->salary_payment_definition_id => $salary->salary_amount

									);
									//print_r($emoluments_data);

									$this->salaries->update_emolument($employee->employee_id, $emoluments_data);

								endforeach;
							endif;
						endforeach;


						$data['emoluments'] = $this->salaries->view_emolument_sheet();

						$this->load->view('employee_self_service/_pay_slip', $data);

					endif;

				endif;


			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}
	public function my_loan(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);



				$data['employee_id'] = $this->employees->get_employee_by_unique($username)->employee_id;
				$data['loans'] = $this->loans->view_loans();

				$data['employees'] = $this->employees->view_employees();
				$data['payment_definitions'] = $this->payroll_configurations->view_payment_definitions();
				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();
				$data['payroll'] = $this->payroll_configurations->get_payroll_month_year();

				$this->load->view('employee_self_service/my_loan', $data);


			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function my_new_loan(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);



				$data['employee_id'] = $this->employees->get_employee_by_unique($username)->employee_id;



				$data['payment_definitions'] = $this->payroll_configurations->view_payment_definitions();
				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();
				$data['payroll'] = $this->payroll_configurations->get_payroll_month_year();

				$this->load->view('employee_self_service/my_new_loan', $data);


			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}


	public function apply_loan(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);

				$payroll_month = $this->payroll_configurations->get_payroll_month_year()->payroll_month_year_month;
				$payroll_year = $this->payroll_configurations->get_payroll_month_year()->payroll_month_year_year;

				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
				$payment_definition = $this->input->post('payment_definition_id');
				$start_month = $this->input->post('start_month');
				$start_year = $this->input->post('start_year');
				$end_month = $this->input->post('end_month');
				$end_year = $this->input->post('end_year');
				$amount = $this->input->post('amount');
				$monthly_repayment = $this->input->post('repayment_amount');

				if((empty($employee_id))|| (empty($payment_definition)) || (empty($start_month)) || (empty($start_year))
					|| (empty($end_month)) || (empty($end_year)) || (empty($amount))):

					redirect('error_404');

				else:

					$start_date = $start_year."-".$start_month;
					$end_date = $end_year."-".$end_month;
					$payroll_date = $payroll_year."-".$payroll_month;

					$installments = floor((strtotime($end_date) - strtotime($start_date))/ (30*60*60*24))+1;

					//echo $installments;

					if((strtotime($end_date) > strtotime($start_date)) && (strtotime($start_date) > strtotime($payroll_date))):
						$loan_array = array(
							'loan_employee_id'=> $employee_id,
							'loan_payment_definition_id'=>$payment_definition,
							'loan_amount' => $amount,
							'loan_start_year'=> $start_year,
							'loan_start_month' => $start_month,
							'loan_end_year' => $end_year,
							'loan_end_month' => $end_month,
							'loan_installments' => $installments,
							'loan_monthly_repayment' => $monthly_repayment,
							'loan_balance' => $amount,
							'loan_status'=> 2

						);

						$loan_array = $this->security->xss_clean($loan_array);

						//print_r($loan_array);
						$query = $this->loans->add_loan($loan_array);

						if(($query == true)):
							$log_array = array(
								'log_user_id' => $this->users->get_user($username)->user_id,
								'log_description' => "Initiated Loan Application"
							);

							$this->logs->add_log($log_array);
							$msg = array(
								'msg'=> 'Loan Added Successfully',
								'location' => site_url('my_loan'),
								'type' => 'success'

							);
							$this->load->view('swal', $msg);

						else:
							echo "An Error Occurred";
						endif;

					else:
						$msg = array(
							'msg'=> 'Check year and Month Entry',
							'location' => site_url('my_loan'),
							'type' => 'error'

						);
						$this->load->view('swal', $msg);
					endif;




				endif;



			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function employee_resignation()
	{


		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);

				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;

				$check_resignation_attempts = $this->employees->get_employee_resignations($employee_id);

				$count = 0;

				foreach ($check_resignation_attempts as $check_resignation_attempt):

					if($check_resignation_attempt->resignation_status == 0 || $check_resignation_attempt->resignation_status == 2):

						$count++;

						endif;


					endforeach;

					if($count > 0):

						$msg = array(
							'msg'=> 'You have resigned already',
							'location' => site_url('employee_main'),
							'type' => 'warning'

						);
						$this->load->view('swal', $msg);

					else:


				$data['employee_id'] = $this->employees->get_employee_by_unique($username)->employee_id;

				$data['csrf_name'] = $this->security->get_csrf_token_name();
				$data['csrf_hash'] = $this->security->get_csrf_hash();
				//$data['payroll'] = $this->payroll_configurations->get_payroll_month_year();

				$this->load->view('employee_self_service/employee_resignation', $data);

				endif;


			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function resignation()
	{
		$username = $this->session->userdata('user_username');

		if (isset($username)):
			$user_type = $this->users->get_user($username)->user_type;

			if ($user_type == 2 || $user_type == 3):

					$resignation_employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$resignation_reason = $this->input->post('resignation_reason');
					$resignation_effective_date = $this->input->post('resignation_effective_date');

					$_resignation_effective = strtotime($resignation_effective_date);
					$_now = time();

					if($_resignation_effective <= $_now):

						$msg = array(
							'msg' => 'Choose a date greater than today',
							'location' => site_url('employee_resignation'),
							'type' => 'error'
						);
						$this->load->view('swal', $msg);


					else:

						$resignation_array = array(
							'resignation_employee_id' => $resignation_employee_id,
							'resignation_reason' => $resignation_reason,
							'resignation_effective_date' => $resignation_effective_date
						);

						$resignation_array = $this->security->xss_clean($resignation_array);

						$query = $this->employees->insert_resignation($resignation_array);


						if($query == true):

							$msg = array(
								'msg' => 'Employment Termination Notice Sent',
								'location' => site_url('employee_main'),
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
			redirect('/login');
		endif;

	}

	public function my_queries(){


		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);
				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;


				$data['employee_id'] = $employee_id;



				$data['queries'] = $this->employees->get_queries_employee($employee_id);

				$this->load->view('employee_self_service/my_queries', $data);


			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function view_my_query(){

		$query_id = $this->uri->segment(2);

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):



				$query = $this->employees->get_query($query_id);



				if(!empty($query)):

					$data['employee'] = $this->employees->get_employee($query->query_employee_id);

					$data['query'] = $this->employees->get_query($query_id);
					$data['responses'] = $this->employees->get_query_response($query_id);
					$data['user_data'] = $this->users->get_user($username);

					$data['employee'] = $this->employees->get_employee_by_unique($username);



					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();

					$this->load->view('employee_self_service/view_query', $data);

				else:

					redirect('error_404');

				endif;

			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}

	public function my_memos(){

		$username = $this->session->userdata('user_username');

		if(isset($username)):


			//$data['employees'] = $this->employees->view_employees();
			$user_type = $this->users->get_user($username)->user_type;


			if($user_type == 2 || $user_type == 3):

				$data['user_data'] = $this->users->get_user($username);

				$data['employee'] = $this->employees->get_employee_by_unique($username);
				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;


				$data['employee_id'] = $employee_id;



				$data['memos'] = $this->employees->get_memos();

				$this->load->view('employee_self_service/my_memos', $data);


			elseif($user_type == 1):

				redirect('/access_denied');

			endif;


		else:
			redirect('/login');
		endif;

	}


}