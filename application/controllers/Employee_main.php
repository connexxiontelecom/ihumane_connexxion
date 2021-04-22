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
			$this->load->model('chats');
			$this->load->model('biometric');
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
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						
						
						$data['notifications_counts'] = $this->session->userdata('notification_counts');
						
						if( count($this->employees->get_notifications($employee_id)) > $this->session->userdata('notification_counts') ):
							$dat = array(
									'notification_counts'=> count($this->employees->get_notifications($employee_id)),
							);
							$this->session->set_userdata($dat);
						
						endif;
						
						$data['employee'] = $this->employees->get_employee_by_unique($username);
						$data['memos'] = $this->employees->get_memos();
						$data['specific_memos'] = $this->employees->get_my_memo($employee_id);
						$data['appraisals'] = $this->employees->get_employee_appraisal($employee_id);
						$data['appraisees'] = $this->employees->get_appraise_employees($employee_id);
						$data['trainings'] = $this->employees->get_employee_training($employee_id);
						
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();
						
						$data['is_payroll_ready'] = $this->is_payroll_ready($employee_id);
						$data['employee_id'] = $employee_id;
						$tasks = $this->employees->view_assigned_task($employee_id);
						
						$all_tasks = $this->employees->view_all_tasks();
						
						$r = array();
						
						$i = 0;
						if(!empty($all_tasks)):
							foreach ($all_tasks as $all_task):
								$participants = json_decode($all_task->task_participants);
								if(!empty($participants)):
									if((in_array($employee_id, $participants))):
										$r[$i] = $all_task;
										$i++;
									endif;
								endif;
							
							
							endforeach;
						endif;
						
						$data['tasks'] = $tasks;
						$data['taskss'] = $r;
						
						
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
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						$data['employee_id'] = $employee_id;
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
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					$data['histories'] = $this->employees->view_employee_history($employee_id);
					
					$data['employee_id'] = $employee_id;
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
					$data['employee'] = $this->employees->get_employee_by_unique($username);
					
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					$data['leaves'] = $this->employees-> check_existing_employee_leaves($employee_id);
					
					$data['employee_id'] = $employee_id;
					
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
					//$data['leaves'] = $this->hr_configurations->view_leaves();
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					//$data['employees'] = $this->employees->view_employees();
					$data['employee_id'] = $employee_id;
					
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
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
					
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
									
									$employee_history_array = array(
											'employee_history_employee_id' => $employee_id,
											'employee_history_details' =>'Leave Application'
									
									);
									
									$this->employees->insert_employee_history($employee_history_array);
									
									$notification_data = array(
											'notification_employee_id'=> 0,
											'notification_link'=> 'employee_leave',
											'notification_type' => 'New Leave Application',
											'notification_status'=> 0
									);
									
									$this->employees->insert_notifications($notification_data);
									
									
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
								
								$notification_data = array(
										'notification_employee_id'=> 0,
										'notification_link'=> 'employee_leave',
										'notification_type' => 'New Leave Application',
										'notification_status'=> 0
								);
								
								$this->employees->insert_notifications($notification_data);
								
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
					redirect('error_404');
				
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
					
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					$data['employee_id'] = $employee_id;
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
					
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					$data['histories'] = $this->employees->view_employee_history($employee_id);
					$data['appraisals'] = $this->employees->get_appraise_employees($employee_id);
					
					$data['employee_id'] = $employee_id;
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
					
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					$data['questions'] = $this->employees->get_appraisal_questions($appraisal_id);
					$data['appraisal_id'] = $appraisal_id;
					$data['employee_id'] = $employee_id;
					
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
			$method = $this->input->server('REQUEST_METHOD');
			
			if($method == 'POST' || $method == 'Post' || $method == 'post'):
				
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
						
						$notification_data = array(
								'notification_employee_id'=> 0,
								'notification_link'=> 'employee_appraisal',
								'notification_type' => 'Supervisor Completed Appraisal',
								'notification_status'=> 0
						);
						
						$this->employees->insert_notifications($notification_data);
						
						$msg = array(
								'msg'=> 'Appraisal Completed',
								'location' => site_url('employee_main'),
								'type' => 'success'
						
						);
						$this->load->view('swal', $msg);
					endif;
				
				endif;
			else:
				redirect('error_404');
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
					
					
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					$data['questions'] = $this->employees->get_appraisal_questions($appraisal_id);
					$data['appraisal_id'] = $appraisal_id;
					$data['employee_id'] = $employee_id;
					
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
			
			$method = $this->input->server('REQUEST_METHOD');
			
			if($method == 'POST' || $method == 'Post' || $method == 'post'):
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
						
						$notification_data = array(
								'notification_employee_id'=> 0,
								'notification_link'=> 'employee_appraisal',
								'notification_type' => 'Employee Completed Appraisal',
								'notification_status'=> 0
						);
						
						$this->employees->insert_notifications($notification_data);
						$msg = array(
								'msg'=> 'Appraisal Completed',
								'location' => site_url('employee_main'),
								'type' => 'success'
						
						);
						$this->load->view('swal', $msg);
					endif;
				
				endif;
			else:
				redirect('error_404');
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
						
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						
						$data['questions'] = $questions;
						
						$data['appraisal_id'] = $appraisal_id;
						$data['employee_id'] = $employee_id;
						
						
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
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['min_payroll_year'] = $this->salaries->view_min_payroll_year();
					$data['employee_id'] = $employee_id;
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
				
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
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
							$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
							$data['notifications'] = $this->employees->get_notifications($employee_id);
							$data['employee'] = $this->employees->get_employee_by_unique($username);
							$data['employee_id'] = $employee_id;
							
							$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
							
							if(empty($check)):
								
								$this->salaries->optimize_emolument_report();
								
								
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
								
								$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
								$data['employee'] = $this->employees->get_employee($employee_id);
								$data['notifications'] = $this->employees->get_notifications($employee_id);
								$data['emoluments'] = $this->salaries->view_emolument_sheet();
								$data['month'] = $month;
								$data['year'] = $year;
								$data['emoluments'] = $this->salaries->view_emolument_sheet();
								$data['employee_id'] = $employee_id;
								$this->load->view('employee_self_service/_pay_slip', $data);
							
							else:
								
								$this->salaries->clear_emolument();
								$this->salaries->optimize_emolument_report();
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
								
								$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
								$data['employee'] = $this->employees->get_employee($employee_id);
								$data['notifications'] = $this->employees->get_notifications($employee_id);
								$data['emoluments'] = $this->salaries->view_emolument_sheet();
								$data['month'] = $month;
								$data['year'] = $year;
								$data['employee_id'] = $employee_id;
								
								$this->load->view('employee_self_service/_pay_slip', $data);
							
							endif;
						
						endif;
					
					
					elseif($user_type == 1):
						
						redirect('/access_denied');
					
					endif;
				else:
					redirect('error_404');
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
					
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					$data['employee_id'] = $employee_id;
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
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
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
				
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
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
						$reason = $this->input->post('reason');
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
										'loan_reason' => $reason,
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
									
									$notification_data = array(
											'notification_employee_id'=> 0,
											'notification_link'=> 'loans',
											'notification_type' => 'New Loan Application',
											'notification_status'=> 0
									);
									
									$this->employees->insert_notifications($notification_data);
									
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
					
					redirect('error_404');
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
						
						if($check_resignation_attempt->resignation_status == 0):
							
							$count++;
						
						endif;
					
					
					endforeach;
					
					if($count > 0):
						
						$msg = array(
								'msg'=> 'You have a Pending Resignation',
								'location' => site_url('employee_main'),
								'type' => 'warning'
						
						);
						$this->load->view('swal', $msg);
					
					else:
						
						
						$data['employee_id'] = $this->employees->get_employee_by_unique($username)->employee_id;
						$data['resignations'] = $this->employees->get_resignations();
						$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();
						//$data['payroll'] = $this->payroll_configurations->get_payroll_month_year();
						$data['employee_id'] = $employee_id;
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
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
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
								$notification_data = array(
										'notification_employee_id'=> 0,
										'notification_link'=> 'resignations',
										'notification_type' => 'New Resignation Notice',
										'notification_status'=> 0
								);
								
								$this->employees->insert_notifications($notification_data);
								$msg = array(
										'msg' => 'Employment Resignation Notice Sent',
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
					redirect('error_404');
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
					
					
					$data['notifications'] = $this->employees->get_notifications($employee_id);
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
						
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();
						$data['employee_id'] = $employee_id;
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
					
					
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					$data['memos'] = $this->employees->get_memos();
					
					$this->load->view('employee_self_service/my_memos', $data);
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function my_specific_memos(){
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				//$data['employees'] = $this->employees->view_employees();
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
					$data['user_data'] = $this->users->get_user($username);
					
					$data['employee'] = $this->employees->get_employee_by_unique($username);
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					
					
					$data['employee_id'] = $employee_id;
					
					
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					$data['memos'] = $this->employees->get_my_memo($employee_id);
					
					$this->load->view('employee_self_service/my_specific_memos', $data);
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function view_notification(){
			$username = $this->session->userdata('user_username');
			if(isset($username)):
				$notification_id = $query_id = $this->uri->segment(2);
				
				$notification = $this->employees->get_notification($notification_id);
				if(empty($notification)):
					
					redirect('error_404');
				else:
					
					
					
					
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					if($employee_id == $notification->notification_employee_id):
						
						$notification_data = array(
								'notification_status'=> 1
						);
						
						$this->employees->update_notification($notification_id, $notification_data);
						redirect($notification->notification_link);
					else:
						redirect('error_404');
					endif;
				endif;
			
			else:
				redirect('error_404');
			endif;
		}
		
		
		public function my_trainings(){
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				//$data['employees'] = $this->employees->view_employees();
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
					$data['user_data'] = $this->users->get_user($username);
					
					$data['employee'] = $this->employees->get_employee_by_unique($username);
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					
					
					$data['employee_id'] = $employee_id;
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					$data['trainings'] = $this->employees->get_employee_training($employee_id);
					
					
					
					$this->load->view('employee_self_service/my_trainings', $data);
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function begin_training(){
			
			$training_id = $this->uri->segment(2);
			$employee_training_id = $this->uri->segment(3);
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				//$data['employees'] = $this->employees->view_employees();
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
					if(empty($training_id)):
						
						redirect('error_404');
					
					else:
						
						$check_existing_training = $this->hr_configurations-> view_training($training_id);
						
						$check_existing_employee_training = $this->employees-> get_employee_training_($employee_training_id);
						
						if(empty($check_existing_training)):
							
							redirect('error_404');
						
						else:
							
							if(empty($check_existing_employee_training)):
								
								redirect('error_404');
							
							else:
								
								if(!empty($check_existing_employee_training->employee_training_status)):
									
									redirect('error_404');
								
								else:
									
									
									$data['user_data'] = $this->users->get_user($username);
									//$data['employees'] = $this->employees->get_employee_by_salary_setup();
									$data['training'] = $check_existing_training;
									$data['training_materials'] = $this->hr_configurations->view_training_materials($training_id);
									
									
									$data['csrf_name'] = $this->security->get_csrf_token_name();
									$data['csrf_hash'] = $this->security->get_csrf_hash();
									$data['employee_training_id'] = $employee_training_id;
									
									
									
									
									
									
									$data['employee'] = $this->employees->get_employee_by_unique($username);
									$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
									
									
									$data['employee_id'] = $employee_id;
									$data['notifications'] = $this->employees->get_notifications($employee_id);
									
									
									
									$this->load->view('employee_self_service/view_training', $data);
								
								endif;
							
							endif;
						endif;
					
					endif;
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		
		public function start_test(){
			
			$training_id = $this->uri->segment(2);
			$employee_training_id = $this->uri->segment(3);
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				//$data['employees'] = $this->employees->view_employees();
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
					if(empty($training_id)):
						
						redirect('error_404');
					
					else:
						
						$check_existing_training = $this->hr_configurations-> view_training($training_id);
						
						$check_existing_employee_training = $this->employees-> get_employee_training_($employee_training_id);
						
						if(empty($check_existing_training)):
							
							redirect('error_404');
						
						else:
							
							if(empty($check_existing_employee_training)):
								
								redirect('error_404');
							
							else:
								
								$employee_training_array = array(
										'employee_training_status' => 1,
										'employee_training_date' => date("Y-m-d H:i:s")
								
								);
								
								$query = $this->employees->update_employee_training($employee_training_id, $employee_training_array);
								
								
								$data['user_data'] = $this->users->get_user($username);
								//$data['employees'] = $this->employees->get_employee_by_salary_setup();
								$data['training'] = $check_existing_training;
								$data['questions'] = $this->hr_configurations->view_training_questions($training_id);
								
								
								
								$time = $this->session->userdata('exam_time');
								if(isset($time)):
									$data['exam_time'] = $time;
								
								
								else:
									
									$time = $check_existing_training->training_duration_exam;
									$data_time = array(
											'exam_time' => $time
									);
									$this->session->set_userdata($data_time);
									$data['exam_time'] = $time;
								endif;
								
								
								$data['csrf_name'] = $this->security->get_csrf_token_name();
								$data['csrf_hash'] = $this->security->get_csrf_hash();
								$data['employee_training_id'] = $employee_training_id;
								$data['employee'] = $this->employees->get_employee_by_unique($username);
								$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
								$data['employee_id'] = $employee_id;
								$data['notifications'] = $this->employees->get_notifications($employee_id);
								
								
								
								$this->load->view('employee_self_service/start_test', $data);
							
							endif;
						endif;
					
					endif;
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function score_test(){
			
			$this->session->unset_userdata('exam_time');
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
					
					extract($_POST);
					
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					
					$questions = $this->hr_configurations->view_training_questions($training_id);
					
					$score =0;
					
					foreach ($questions as $question):
						$given_answer = $this->input->post($question->training_question_id);
						if(empty($given_answer)):
							$given_answer = 'E';
						endif;
						
						if($given_answer == $question->training_question_correct):
							$score++;
						endif;
						
						$answer_array = array(
								'training_result_answer' => $given_answer,
						);
						
						$this->employees->update_result($training_id, $employee_training_id, $answer_array);
					
					endforeach;
					
					$total_score = ($score/count($questions) * 100);
					
					$employee_training_array = array(
							'employee_training_score' => $total_score,
							'employee_training_status' => 1,
							'employee_training_date' => date("Y-m-d H:i:s")
					
					);
					
					$query = $this->employees->update_employee_training($employee_training_id, $employee_training_array);
					
					if($query == true):
						$notification_data = array(
								'notification_employee_id'=> $employee_id,
								'notification_link'=> 'my_trainings',
								'notification_type' => 'Training Completed, Result Ready',
								'notification_status'=> 0
						);
						
						$this->employees->insert_notifications($notification_data);
						
						$notification_data = array(
								'notification_employee_id'=> 0,
								'notification_link'=> 'employee_trainings',
								'notification_type' => 'Employee Completed Training',
								'notification_status'=> 0
						);
						
						$this->employees->insert_notifications($notification_data);
						$msg = array(
								'msg' => 'Test Complete with a score of  '.$total_score.'%',
								'location' =>  site_url('my_trainings'),
								'type' => 'success'
						);
						$this->load->view('exam_swal', $msg);
					
					
					else:
						
						echo "An Error Occurred";
					endif;
				
				
				
				
				
				//echo $total_score." %";
				
				else:
					redirect('error_404');
				
				endif;
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function check_training_result(){
			
			$employee_training_id = $this->uri->segment(2);
			
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				//$data['employees'] = $this->employees->view_employees();
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
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
							$data['employee'] = $this->employees->get_employee_by_unique($username);
							$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
							$data['employee_id'] = $employee_id;
							$data['notifications'] = $this->employees->get_notifications($employee_id);
							
							//print_r($check_existing_employee_training);
							
							$this->load->view('employee_self_service/test_result', $data);
						
						endif;
					
					endif;
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function update_time(){
			
			$minutes = $_GET['minutes'];
			$this->session->set_userdata('exam_time', $minutes);
		}
		
		public function my_chat(){
			
			
			
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				//$data['employees'] = $this->employees->view_employees();
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
					
					$data['user_data'] = $this->users->get_user($username);
					
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['employee'] = $this->employees->get_employee_by_unique($username);
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['employee_id'] = $employee_id;
					$data['users'] = $this->users->view_users();
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					//print_r($check_existing_employee_training);
					
					$this->load->view('employee_self_service/chat', $data);
				
				
				
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function send_chat(){
			
			$sender_id = $_GET['sender_id'];
			$reciever_id = $_GET['reciever_id'];
			$message = $_GET['message'];
			
			$chat_array = array(
					
					'chat_sender_id' => $sender_id,
					'chat_reciever_id' => $reciever_id,
					'chat_body' => $message,
			);
			
			
			$employee_n = $this->employees->get_employee($sender_id);
			$name = $employee_n->employee_first_name." ".$employee_n->employee_last_name;
			
			$notification_data = array(
					'notification_employee_id'=> $reciever_id,
					'notification_link'=> 'my_chat',
					'notification_type' => 'New Message from '.$name,
					'notification_status'=> 0
			);
			
			$this->employees->insert_notifications($notification_data);
			
			echo $this->chats->add_chat($chat_array);
			
			
			
		}
		
		public function get_chats(){
			$sender_id = $_GET['sender_id'];
			$reciever_id = $_GET['reciever_id'];
			
			$chats = $this->chats->get_chat();
			
			$employee = $this->employees->get_employee($sender_id);
			$employee_details = $this->employees->get_employee($reciever_id);
			
			foreach ($chats as $chat):
				if($chat->chat_sender_id == $employee->employee_id && $chat->chat_reciever_id == $employee_details->employee_id): ?>
					<div class="chat-item chat-right" style="">
						<img height="30" width="30" class="rounded" src="<?php echo base_url(); ?>uploads/employee_passports/<?php echo $employee->employee_passport; ?>">
						<div class="chat-details">
							<div class="chat-text"><?php echo $chat->chat_body; ?></div>
							<div class="chat-time"><?php echo date('F j, Y g:i a', strtotime($chat->chat_time)); ?></div>
						</div>
					</div>
				<?php
				endif;
				if($chat->chat_sender_id == $employee_details->employee_id && $chat->chat_reciever_id == $employee->employee_id):?>
					<div class="chat-item chat-left" style="">
						<img height="30" width="30" class="rounded" src="<?php echo base_url(); ?>uploads/employee_passports/<?php echo $employee_details->employee_passport; ?>">
						<div class="chat-details">
							<div class="chat-text"><?php echo $chat->chat_body; ?></div>
							<div class="chat-time"><?php echo date('F j, Y g:i a', strtotime($chat->chat_time)); ?></div>
						</div>
					</div>
				
				<?php
				endif;
			
			endforeach;
		}
		
		public function get_online(){
			
			
			$employee_id = $_GET['sender_id'];
			$users = $this->users->view_users();
			foreach ($users as $user):
				if($user->user_type == 2 || $user->user_type == 3):
					if(!empty($user->user_token)):
						$employee_details = @$this->employees->get_employee_by_unique($user->user_username);
						if($employee_details->employee_id !== $employee_id):
							
							?>
							
							<li class="media">
								<a class="link" href="#" data-rel="<?php echo $employee_details->employee_id; ?>">
									<img alt="image" class="mr-3 rounded-circle" width="50" src="<?php echo base_url(); ?>uploads/employee_passports/<?php echo $employee_details->employee_passport; ?>">
									<div class="media-body">
										<div class="mt-0 mb-1 font-weight-bold"><?php echo $employee_details->employee_first_name." ". $employee_details->employee_last_name; ?></div>
										
										<div class="text-success text-small font-600-bold"><i class="fas fa-circle"></i> Online</div>
									
									
									</div>
								</a>
							</li>
						
						<?php
						
						endif;
					endif;
				endif;
			endforeach;
			
			foreach ($users as $user):
				if($user->user_type == 2 || $user->user_type == 3):
					if(empty($user->user_token)):
						$employee_details = @$this->employees->get_employee_by_unique($user->user_username);
						if($employee_details->employee_id !== $employee_id):
							
							?>
							
							<li class="media">
								<a class="link" href="#" data-rel="<?php echo $employee_details->employee_id; ?>">
									<img alt="image" class="mr-3 rounded-circle" width="50" src="<?php echo base_url(); ?>uploads/employee_passports/<?php echo $employee_details->employee_passport; ?>">
									<div class="media-body">
										<div class="mt-0 mb-1 font-weight-bold"><?php echo $employee_details->employee_first_name." ". $employee_details->employee_last_name; ?></div>
										
										<div class="text-small font-weight-600 text-muted"><i class="fas fa-circle"></i> Offline</div>
									
									
									</div>
								</a>
							</li>
						
						<?php
						
						endif;
					endif;
				endif;
			endforeach;
		}
		
		public function documents(){
			
			
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				//$data['employees'] = $this->employees->view_employees();
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
					
					$data['user_data'] = $this->users->get_user($username);
					
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['employee'] = $this->employees->get_employee_by_unique($username);
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['employee_id'] = $employee_id;
					$data['documents'] = $this->hr_configurations->view_hr_documents();
					$data['employee_id'] = $employee_id;
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					//print_r($check_existing_employee_training);
					
					$this->load->view('employee_self_service/documents', $data);
				
				
				
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
			
		}
		
		public function view_document(){
			
			$document_id = $this->uri->segment(2);
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
					if(empty($document_id)):
						
						redirect('error_404');
					
					else:
						
						$check_existing_document = $this->hr_configurations-> view_hr_document($document_id);
						
						if(empty($check_existing_document)):
							
							redirect('error_404');
						
						else:
							$data['user_data'] = $this->users->get_user($username);
							
							$data['csrf_name'] = $this->security->get_csrf_token_name();
							$data['csrf_hash'] = $this->security->get_csrf_hash();
							$data['employee'] = $this->employees->get_employee_by_unique($username);
							$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
							$data['employee_id'] = $employee_id;
							$data['document'] = $check_existing_document;
							
							
							$data['notifications'] = $this->employees->get_notifications($employee_id);
							
							//print_r($check_existing_employee_training);
							
							$this->load->view('employee_self_service/view_document', $data);
						
						endif;
					
					endif;
				
				
				
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
			
			
		}
		
		public function change_password(){
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				
				
				$user_type = $this->users->get_user($username)->user_type;
				
				
				if($user_type == 2 || $user_type == 3):
					
					
					
					$data['user_data'] = $this->users->get_user($username);
					
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['employee'] = $this->employees->get_employee_by_unique($username);
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['employee_id'] = $employee_id;
					
					
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					//print_r($check_existing_employee_training);
					
					$this->load->view('employee_self_service/change_password', $data);
				
				
				
				
				
				elseif($user_type == 1):
					
					redirect('/access_denied');
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
			
			
		}
		
		public function change_password_(){
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
					
					$user_type = $this->users->get_user($username)->user_type;
					
					
					if($user_type == 2 || $user_type == 3):
						
						
						extract($_POST);
						
						if($password === $confirm_password):
							
							$user_array = array(
									
									'user_password'=> password_hash($password, PASSWORD_BCRYPT),
							
							);
							
							$user_array = $this->security->xss_clean($user_array);
							$query_user = $this->users->update_user($user_id, $user_array);
							
							if(($query_user == true)):
								
								$msg = array(
										'msg'=> 'Password Reset',
										'location' => site_url('employee_main'),
										'type' => 'success'
								
								);
								$this->load->view('swal', $msg);
							
							else:
								$msg = array(
										'msg'=> 'An Error Occurred',
										'location' => site_url('employee_main'),
										'type' => 'error'
								
								);
								$this->load->view('swal', $msg);
							endif;
						
						
						
						else:
							$msg = array(
									'msg'=> 'Passwords do not match',
									'location' => site_url('employee_main'),
									'type' => 'error'
							
							);
							$this->load->view('swal', $msg);
						
						endif;
					
					
					
					
					
					elseif($user_type == 1):
						
						redirect('/access_denied');
					
					endif;
				else:
					
					redirect('error_404');
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
			
			
		}
		
		public function get_notifications(){
			
			$employee_id = $this->input->post('employee_id');
			
			echo json_encode($this->employees->get_notifications($employee_id));
		}
		
		public function is_payroll_ready($employee_id) {
			$salaries = $this->salaries->get_employee_salary($employee_id);
			return !empty($salaries);
		}
		
		public function get_income_payments() {
			$username = $this->session->userdata('user_username');
			if(isset($username)):
				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
				$income_payments = $this->payroll_configurations->get_income_payments();
				$income_payments_ids = array();
				foreach ($income_payments as $income_payment) {
					$income_payments_ids[] = $income_payment->payment_definition_id;
				}
				if(!empty($income_payments_ids)):
					$income = $this->salaries->get_employee_salaries_by_payment_id($employee_id, $income_payments_ids);
					$json_response = array(
							'success' => true,
							'income' => $income
					);
					echo json_encode($json_response);
				else:
					$json_response = array(
							'success' => false
					);
					echo json_encode($json_response);
				endif;
			endif;
		}
		
		public function get_deduction_payments() {
			$username = $this->session->userdata('user_username');
			if(isset($username)):
				$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
				$deduction_payments = $this->payroll_configurations->get_deduction_payments();
				$deduction_payments_ids = array();
				foreach ($deduction_payments as $deduction_payment) {
					$deduction_payments_ids[] = $deduction_payment->payment_definition_id;
				}
				if(!empty($deduction_payments_ids)):
					$deductions = $this->salaries->get_employee_salaries_by_payment_id($employee_id, $deduction_payments_ids);
					$json_response = array(
							'success' => true,
							'deductions' => $deductions
					);
					echo json_encode($json_response);
				else:
					$json_response = array(
							'success' => false
					);
					echo json_encode($json_response);
				endif;
			endif;
		}
		
		public function new_task(){
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
					
					$_POST['task_participants'] = json_encode($_POST['task_participants']);
					$_POST['task_supervisor_id'] = $this->employees->get_employee_by_unique($username)->employee_id;
					
					$v = $this->employees->add_task($_POST);
					
					if($v):
						
						$notification_data = array(
								'notification_employee_id'=> $_POST['task_employee_id'],
								'notification_link'=> 'assigned_tasks',
								'notification_type' => 'You have a new task',
								'notification_status'=> 0
						);
						
						$this->employees->insert_notifications($notification_data);
						
						$participants = json_decode($_POST['task_participants']);
						foreach ($participants as $participant):
							
							$notification_data = array(
									'notification_employee_id'=> $participant,
									'notification_link'=> 'assigned_tasks',
									'notification_type' => 'You have a new task',
									'notification_status'=> 0
							);
							$this->employees->insert_notifications($notification_data);
						
						endforeach;
						
						
						$msg = array(
								'msg' => 'Task Assigned Successfully',
								'location' => site_url('new_task'),
								'type' => 'success'
						
						);
						$this->load->view('swal', $msg);
					
					else:
						
						$msg = array(
								'msg' => 'An Error Occurred',
								'location' => site_url('new_task'),
								'type' => 'error'
						
						);
						$this->load->view('swal', $msg);
					
					
					endif;
				
				
				
				
				endif;
				
				if($method == 'GET' || $method == 'Get' || $method == 'get'):
					
					$user_type = $this->users->get_user($username)->user_type;
					
					
					if($user_type == 2 || $user_type == 3):
						
						$data['user_data'] = $this->users->get_user($username);
						
						$data['employee'] = $this->employees->get_employee_by_unique($username);
						$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
						$data['employees'] = $this->employees->view_employees();
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();
						$data['employee_id'] = $employee_id;
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						
						
						$this->load->view('employee_self_service/new_task', $data);
					
					
					elseif($user_type == 1):
						
						redirect('/access_denied');
					
					endif;
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function my_tasks(){
			
			$username = $this->session->userdata('user_username');
			
			if(isset($username)):
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
					
					$_POST['task_participants'] = json_encode($_POST['task_participants']);
					$_POST['task_supervisor_id'] = $this->employees->get_employee_by_unique($username)->employee_id;
					
					$v = $this->employees->add_task($_POST);
					
					if($v):
						
						$notification_data = array(
								'notification_employee_id'=> $_POST['task_employee_id'],
								'notification_link'=> 'assigned_tasks',
								'notification_type' => 'You have a new task',
								'notification_status'=> 0
						);
						
						$this->employees->insert_notifications($notification_data);
						
						$participants = json_decode($_POST['task_participants']);
						foreach ($participants as $participant):
							
							$notification_data = array(
									'notification_employee_id'=> $participant,
									'notification_link'=> 'assigned_tasks',
									'notification_type' => 'You have a new task',
									'notification_status'=> 0
							);
							$this->employees->insert_notifications($notification_data);
						
						endforeach;
						
						
						$msg = array(
								'msg' => 'Task Assigned Successfully',
								'location' => site_url('new_task'),
								'type' => 'success'
						
						);
						$this->load->view('swal', $msg);
					
					else:
						
						$msg = array(
								'msg' => 'An Error Occurred',
								'location' => site_url('new_task'),
								'type' => 'error'
						
						);
						$this->load->view('swal', $msg);
					
					
					endif;
				
				
				
				
				endif;
				
				if($method == 'GET' || $method == 'Get' || $method == 'get'):
					
					$user_type = $this->users->get_user($username)->user_type;
					
					
					if($user_type == 2 || $user_type == 3):
						
						$data['user_data'] = $this->users->get_user($username);
						
						$data['employee'] = $this->employees->get_employee_by_unique($username);
						$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
						$data['employees'] = $this->employees->view_employees();
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();
						$data['employee_id'] = $employee_id;
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						$data['tasks'] = $this->employees->view_my_task($employee_id);
						
						
						$this->load->view('employee_self_service/my_tasks', $data);
					
					
					elseif($user_type == 1):
						
						redirect('/access_denied');
					
					endif;
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
		}
		
		public function view_task(){
			$username = $this->session->userdata('user_username');
			$task_id = $this->uri->segment(2);
			if(isset($username)):
				$method = $this->input->server('REQUEST_METHOD');
				
				if($method == 'POST' || $method == 'Post' || $method == 'post'):
					
					$_POST['task_participants'] = json_encode($_POST['task_participants']);
					$_POST['task_supervisor_id'] = $this->employees->get_employee_by_unique($username)->employee_id;
					
					$v = $this->employees->add_task($_POST);
					
					if($v):
						
						$notification_data = array(
								'notification_employee_id'=> $_POST['task_employee_id'],
								'notification_link'=> 'assigned_tasks',
								'notification_type' => 'You have a new task',
								'notification_status'=> 0
						);
						
						$this->employees->insert_notifications($notification_data);
						
						$participants = json_decode($_POST['task_participants']);
						foreach ($participants as $participant):
							
							$notification_data = array(
									'notification_employee_id'=> $participant,
									'notification_link'=> 'assigned_tasks',
									'notification_type' => 'You have a new task',
									'notification_status'=> 0
							);
							$this->employees->insert_notifications($notification_data);
						
						endforeach;
						
						
						$msg = array(
								'msg' => 'Task Assigned Successfully',
								'location' => site_url('new_task'),
								'type' => 'success'
						
						);
						$this->load->view('swal', $msg);
					
					else:
						
						$msg = array(
								'msg' => 'An Error Occurred',
								'location' => site_url('new_task'),
								'type' => 'error'
						
						);
						$this->load->view('swal', $msg);
					
					
					endif;
				
				
				
				
				endif;
				
				if($method == 'GET' || $method == 'Get' || $method == 'get'):
					
					$user_type = $this->users->get_user($username)->user_type;
					
					
					if($user_type == 2 || $user_type == 3):
						
						$data['user_data'] = $this->users->get_user($username);
						
						$data['employee'] = $this->employees->get_employee_by_unique($username);
						$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
						$data['employees'] = $this->employees->view_employees();
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();
						$data['employee_id'] = $employee_id;
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						
						$task = $this->employees->view_task($task_id);
						
						//echo $task_id;
						
						if(!empty($task)):
							
							$participants = json_decode($task->task_participants);
							$data['responses'] = $this->employees->get_task_response($task_id);
							
							if(($employee_id == $task->task_employee_id) || ($employee_id == $task->task_supervisor_id) ||(in_array($employee_id, $participants))):
								$data['task'] = $this->employees->view_task($task_id);
								
								
								$this->load->view('employee_self_service/view_task', $data);
							else:
								
								redirect('error_404');
								
								//echo "i failed the or";
							endif;
						
						else:
							
							redirect('error_404');
						endif;
					
					
					elseif($user_type == 1):
						
						redirect('/access_denied');
					
					endif;
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		public function assigned_tasks(){
			$username = $this->session->userdata('user_username');
			$task_id = $this->uri->segment(2);
			if(isset($username)):
				$method = $this->input->server('REQUEST_METHOD');
				
				
				
				if($method == 'GET' || $method == 'Get' || $method == 'get'):
					
					$user_type = $this->users->get_user($username)->user_type;
					
					
					if($user_type == 2 || $user_type == 3):
						
						$data['user_data'] = $this->users->get_user($username);
						
						$data['employee'] = $this->employees->get_employee_by_unique($username);
						$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
						$data['employees'] = $this->employees->view_employees();
						$data['csrf_name'] = $this->security->get_csrf_token_name();
						$data['csrf_hash'] = $this->security->get_csrf_hash();
						$data['employee_id'] = $employee_id;
						$data['notifications'] = $this->employees->get_notifications($employee_id);
						
						$tasks = $this->employees->view_assigned_task($employee_id);
						
						$all_tasks = $this->employees->view_all_tasks();
						
						$r = array();
						
						$i = 0;
						
						foreach ($all_tasks as $all_task):
							$participants = json_decode($all_task->task_participants);
							if((in_array($employee_id, $participants))):
								
								
								
								$r[$i] = $all_task;
								$i++;
							endif;
						
						
						endforeach;
						
						$data['tasks'] = $tasks;
						$data['taskss'] = $r;
						
						
						$this->load->view('employee_self_service/assigned_tasks', $data);
					
					
					elseif($user_type == 1):
						
						redirect('/access_denied');
					
					endif;
				
				endif;
			
			
			else:
				redirect('/login');
			endif;
			
		}
		
		
		public function new_task_response()
		{
			
			$task_id = $_GET['task_id'];
			$task_response = $_GET['task_response'];
			$task_responder_id = $_GET['task_responder_id'];
			
			$task = $this->employees->view_task($task_id);
			
			
			$response_array = array(
					
					'task_response_task_id' => $task_id,
					'task_response_employee_id' => $task_responder_id,
					'task_response_body' => $task_response,
					'task_response_date' => date("Y-m-d H:i:s")
			
			);
			
			
			$participants = json_decode($task->task_participants);
			foreach ($participants as $participant):
				
				$notification_data = array(
						'notification_employee_id'=> $participant,
						'notification_link'=> 'view_task/'.$task_id,
						'notification_type' => 'Response to an Open task',
						'notification_status'=> 0
				);
				$this->employees->insert_notifications($notification_data);
			
			endforeach;
			
			$notification_data = array(
					'notification_employee_id'=> $task->task_supervisor_id,
					'notification_link'=> 'view_task/'.$task_id,
					'notification_type' => 'Response to an Open task',
					'notification_status'=> 0
			);
			$this->employees->insert_notifications($notification_data);
			
			$notification_data = array(
					'notification_employee_id'=> $task->task_employee_id,
					'notification_link'=> 'view_task/'.$task_id,
					'notification_type' => 'Response to an Open task',
					'notification_status'=> 0
			);
			$this->employees->insert_notifications($notification_data);
			
			
			
			
			
			echo $this->employees->insert_task_response($response_array);
			
			
		}
		
		public function close_task()
		{
			$task_id = $this->uri->segment(2);
			
			$username = $this->session->userdata('user_username');
			
			if (isset($username)):
				$user_type = $this->users->get_user($username)->user_type;
				
				if ($user_type == 2 || $user_type == 3):
					$data['employee'] = $this->employees->get_employee_by_unique($username);
					$employee_id = $this->employees->get_employee_by_unique($username)->employee_id;
					$data['employees'] = $this->employees->view_employees();
					$data['csrf_name'] = $this->security->get_csrf_token_name();
					$data['csrf_hash'] = $this->security->get_csrf_hash();
					$data['employee_id'] = $employee_id;
					$data['notifications'] = $this->employees->get_notifications($employee_id);
					
					
					
					$data['user_data'] = $this->users->get_user($username);
					
					
					
					$task = $this->employees->view_task($task_id);
					
					
					
					if(!empty($task) && $task->task_status == 0):
						
						$task_array = array(
								
								'task_status' => 1
						
						);
						
						$task_array = $this->security->xss_clean($task_array);
						$task = $this->employees->update_task($task_id, $task_array);
						
						if($task == true):
							$task = $this->employees->view_task($task_id);
							
							$participants = json_decode($task->task_participants);
							foreach ($participants as $participant):
								
								$notification_data = array(
										'notification_employee_id'=> $participant,
										'notification_link'=> 'view_task/'.$task_id,
										'notification_type' => 'Task Completed',
										'notification_status'=> 0
								);
								$this->employees->insert_notifications($notification_data);
							
							endforeach;
							
							$notification_data = array(
									'notification_employee_id'=> $task->task_supervisor_id,
									'notification_link'=> 'view_task/'.$task_id,
									'notification_type' => 'Task Completed',
									'notification_status'=> 0
							);
							$this->employees->insert_notifications($notification_data);
							
							$notification_data = array(
									'notification_employee_id'=> $task->task_employee_id,
									'notification_link'=> 'view_task/'.$task_id,
									'notification_type' => 'Task Completed',
									'notification_status'=> 0
							);
							$this->employees->insert_notifications($notification_data);
							
							$msg = array(
									'msg' => 'task closed',
									'location' => site_url('view_task').'/'.$task_id,
									'type' => 'success'
							);
							$this->load->view('swal', $msg);
						
						
						else:
							redirect('error_404');
						endif;
					
					
					
					else:
						
						redirect('error_404');
					
					endif;
				
				
				###
				else:
					
					redirect('/access_denied');
				
				endif;
			else:
				redirect('/login');
			endif;
			
		}
		
		public function clockin(){
			$method = $this->input->server('REQUEST_METHOD');
			if($method == 'POST' || $method == 'Post' || $method == 'post'):
				$log = array(
						'employee_biometrics_login_employee_id' => $_POST['employee_id']
				);
				
				$query = $this->biometric->insert_login($log);
				
				if($query == true):
					$msg = array(
							'msg' => 'Clocked in Successful at '.date('Y-m-d H:i:s'),
							'location' => site_url(),
							'type' => 'success'
					);
					$this->load->view('swal', $msg);
				
				endif;
			
			else:
				redirect('error_404');
			
			endif;
		}
		
		public function clockout(){
			$method = $this->input->server('REQUEST_METHOD');
			if($method == 'POST' || $method == 'Post' || $method == 'post'):
				$log = array(
						'employee_clockout_employee_id' => $_POST['employee_id']
				);
				
				$query = $this->biometric->insert_clockout($log);
				
				if($query == true):
					$msg = array(
							'msg' => 'Clocked out Successful at '.date('Y-m-d H:i:s'),
							'location' => site_url(),
							'type' => 'success'
					);
					$this->load->view('swal', $msg);
				
				endif;
			else:
				
				redirect('error_404');
			endif;
		}
	}
