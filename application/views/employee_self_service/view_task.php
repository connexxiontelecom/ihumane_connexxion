
<?php
	include(APPPATH.'/views/stylesheet.php');
	$CI =& get_instance();
	$CI->load->model('hr_configurations');
	$CI->load->model('payroll_configurations');
	$CI->load->model('employees');
?>

<body class="layout-3">
<div id="app">
	<div class="main-wrapper container">
		<div class="navbar-bg"></div>
		<?php include('header.php'); ?>
		<?php include('menu.php'); ?>
		<div class="main-content">
			<section class="section">
				<div class="section-header">
					<div class="section-header-back">
						<a href="<?php echo site_url()?>" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
					</div>
					<h1>View Task</h1>
					<div class="section-header-breadcrumb">
						<div class="breadcrumb-item active"><a href="<?php echo base_url('employee_main'); ?>">Dashboard</a></div>
						
						<div class="breadcrumb-item">View task</div>
					</div>
				</div>
				<div class="section-body">
					<h2 class="section-title">View Task</h2>
					<p class="section-lead">You can update tasks here</p>
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4>Task Thread</h4>
									<?php if(($task->task_status == 0) && ($task->task_supervisor_id == $employee->employee_id)):  ?>
										<div class="card-header-action">
											<a href="<?php echo site_url('close_task').'/'.$task->task_id ?>"  class="btn btn-icon icon-left btn-danger">
												<i class="fa fa-times"></i> Mark task as Finished
											</a>
										</div>
									<?php endif; ?>
								</div>
							
								<div class="card-body">
									<div class="tickets">
										<div class="ticket-items" id="ticket-items">
											<div class="ticket-item active">
												<div class="ticket-title">
													<h4><?php echo $task->task_title; ?></h4>
												</div>
												<div class="ticket-desc">
													<div>Supervisor: <?php $emp = $CI->employees->get_employee($task->task_supervisor_id);
															echo $emp->employee_last_name." ".$emp->employee_first_name; ?></div>
													<div class="bullet"></div> <br>
													<div>Responsible: <?php
															$emp = $CI->employees->get_employee($task->task_employee_id);
															echo $emp->employee_last_name." ".$emp->employee_first_name; ?></div>
													
												</div>
											</div> <br>
											<div class="card-header">
												<h4>Participants:</h4>
											</div>
											
											<?php $participants = json_decode($task->task_participants);
											foreach ($participants as $participant):
												$emp = $CI->employees->get_employee($participant);
												?>
											<div class="ticket-item">
												
												<div class="ticket-desc">
													<div><?=$emp->employee_last_name." ".$emp->employee_first_name; ?></div>
												
													
												</div>
											</div>
											<?php endforeach; ?>
										</div>
										
										
										
										
										
										<div class="ticket-content">
											<div class="ticket-header">
												<div class="ticket-sender-picture img-shadow">
													<img src="<?php echo base_url(); ?>assets/img/avatar/avatar-2.png" class="rounded-circle" width="35" data-toggle="title" title="">
												</div>
												<div class="ticket-detail">
													<div class="ticket-title">
														<h4><?php echo $task->task_title; ?></h4>
													</div>
													<div class="ticket-info">
														
														<div class="bullet"></div>
														<div class="text-primary font-weight-600"><?php echo date('F j, Y', strtotime($task->task_start_date))." -  ".date('F j, Y', strtotime($task->task_end_date));  ?></div>
														<div class="bullet"></div>
														<?php if($task->task_status == 0): ?>
															<div class="text-danger font-weight-600">Not Finished</div>
														<?php elseif ($task->task_status == 1):?>
															<div class="text-success font-weight-600">Closed</div>
														<?php endif;?>
														<div class="bullet"></div>
														<?php if($task->task_priority == 1): ?>
															<div class="badge badge-pill badge-danger mb-1 float-right">High</div>
														<?php endif; ?>
														
														<?php if($task->task_priority == 2): ?>
															<div class="badge badge-pill badge-warning mb-1 float-right">Medium</div>
														<?php endif; ?>
														
														<?php if($task->task_priority == 3): ?>
															<div class="badge badge-pill badge-success mb-1 float-right">low</div>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="ticket-description">
												<?php echo $task->task_contents; ?>
												<div class="ticket-divider"></div>
												<div id="task">
													<?php
														if(!empty($responses)):
															foreach ($responses as $response): ?>
																<div class="row">
																	<?php if($response->task_response_employee_id == $employee->employee_id): ?>
																		<div class="col-10 col-md-10 col-lg-10 offset-2">
																			<!--																	<div class="card card-primary">-->
																			<!--																		<div class="card-header">-->
																			<!--																			<h4>Supervisor's Response - --><?php //echo $response->task_response_date; ?><!--</h4>-->
																			<!--																		</div>-->
																			<!--																		<div class="card-body">-->
																			<!--																			<p>--><?php //echo $response->task_response_body; ?><!--</p>-->
																			<!--																		</div>-->
																			<!--																	</div>-->
																			<div class="card card-warning">
																				<div class="card-body">
																					<div class="list-group">
																						<div class="d-flex w-100 justify-content-between">
																							<h5 class="mb-1">Supervisor</h5>
																							<!--                                          <h5 class="mb-1">--><?php //echo $employee->employee_first_name?><!--</h5>-->
																							<small><?php echo timespan(strtotime($response->task_response_date), now('Africa/Lagos'), 2)?> ago</small>
																						</div>
																						<p class="mb-1"><?php echo $response->task_response_body; ?></p>
																						<small><?php echo date('F j, Y g:i:s a', strtotime($response->task_response_date));?></small>
																					</div>
																				</div>
																			</div>
																		</div>
																	<?php else: ?>
																		<div class="col-10 col-md-10 col-lg-10">
																			<!--																	<div class="card card-primary">-->
																			<!--																		<div class="card-header">-->
																			<!--																			<h4>Your Response - --><?php //echo $response->task_response_date; ?><!--</h4>-->
																			<!--																		</div>-->
																			<!--																		<div class="card-body">-->
																			<!--																			<p>--><?php //echo $response->task_response_body; ?><!--</p>-->
																			<!--																		</div>-->
																			<!--																	</div>-->
																			<div class="card card-primary">
																				<div class="card-body">
																					<div class="d-flex w-100 justify-content-between">
																						<h5 class="mb-1"><?php $emp = $CI->employees->get_employee($response->task_response_employee_id); echo $emp->employee_last_name." ".$emp->employee_first_name; ?></h5>
																						<small><?php echo timespan(strtotime($response->task_response_date), now('Africa/Lagos'), 2)?> ago</small>
																					</div>
																					<p class="mb-1"><?php echo $response->task_response_body; ?></p>
																					<small><?php echo date('F j, Y g:i:s a', strtotime($response->task_response_date));?></small>
																				</div>
																			</div>
																		</div>
																	
																	<?php endif; ?>
																
																</div>
															
															<?php endforeach;
														endif;

													?>
													
													<input type="hidden" id="task_status" value="<?php echo $task->task_status; ?>">
												</div>
												<div id="task_form">
													<?php if($task->task_status == 0):  ?>
														
														<div class="ticket-form" >
															<form action="" id="response_form" method="post">
																<div class="form-group">
																	<textarea class="summernote form-control" id="task_response" name="task_response" placeholder="Type a reply ..."></textarea>
																	<input type="hidden" name="task_id" id="task_id" value="<?php echo $task->task_id; ?>">
																	<input type="hidden" name="task_response_employee_id" id="employee_id" value="<?php echo $employee->employee_id; ?>">
																</div>
																<div class="form-group text-right">
																	<button type="button" id="response_button" class="btn btn-primary btn-lg">
																		Post Update
																	</button>
																</div>
															</form>
														</div>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer bg-whitesmoke"></div>
							
							</div>
						</div>
					</div>
			</section>
		</div>
		<?php include(APPPATH.'/views/footer.php'); ?>
	</div>
</div>

<?php include(APPPATH.'/views/js.php'); ?>
</body>
</html>


<script>
	$('title').html('View task - IHUMANE')
	
	$(document).ready(function(){
		setInterval(timestamp, 1000);
		function timestamp() {
			$("#task").load(location.href + " #task");
			
			var task_status = $("#task_status").val();
			
			if(task_status == 1){
				$("#task_form").hide();
			}
		}
		$("#response_button").click(function(e){
			e.preventDefault();
			
			let task_id = $("#task_id").val();
			let task_response = $("#task_response").val();
			let task_responder_id = $("#employee_id").val();
			
			$.ajax({
				type: "GET",
				url: '<?php echo site_url('new_task_response'); ?>',
				data: {task_id:task_id,task_response:task_response, task_responder_id:task_responder_id},
				success:function(data)
				{
					$("#task").load(location.href + " #task");
				},
				error:function()
				{
					alert(this.error);
					
					//console.log(this.error);
				}
			});
		});
	});
</script>







