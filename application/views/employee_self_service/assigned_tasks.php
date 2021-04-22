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
		
		<!-- Main Content -->
		
		<div class="main-content">
			<section class="section">
				<div class="section-header">
					<h1>Task Assigned to Me</h1>
					<div class="section-header-breadcrumb">
						<div class="breadcrumb-item active"><a href="<?php echo site_url('employee_main'); ?>">Dashboard</a></div>
						<div class="breadcrumb-item">My Tasks</div>
					</div>
				</div>
				<div class="section-body">
					<div class="section-title">All About Managing Tasks assigned to you here</div>
					<p class="section-lead">You can manage tasks assigned to you here</p>
					<div class="row">
						<div class="col-12">
							<div class="card">
								
								
								<div class="card-header">
									<h4 class="d-inline">Tasks</h4>
									
								</div>
								<div class="card-body">
									<ul class="list-unstyled list-unstyled-border">
										<?php foreach ($tasks as $task): ?>
										<li class="media">
											<img class="mr-3 rounded-circle" width="50" src="<?php echo base_url(); ?>assets/img/avatar/avatar-4.png" alt="avatar">
											<div class="media-body">
												
											
												<?php if($task->task_status == 0): ?>
													<div class="badge badge-pill badge-danger mb-1 float-right">Not Finished</div>
												<?php endif; ?>
												
												<?php if($task->task_status == 1): ?>
													<div class="badge badge-pill badge-warning mb-1 float-right">Finished</div>
												<?php endif; ?>
												
												<h6 class="media-title"><a href="<?php echo site_url('view_task').'/'.$task->task_id; ?>"><?=$task->task_title; ?></a></h6>
												<div class="text-small text-muted"><?php $employee = $CI->employees->get_employee($task->task_supervisor_id); echo $employee->employee_last_name." ".$employee->employee_first_name;  ?>  <div class="bullet"></div> <span class="text-primary"><?=$task->task_start_date.' : '.$task->task_end_date; ?></span>
													<div class="bullet"></div>
													<?php if($task->task_priority == 1): ?>
														<div class="badge badge-pill badge-danger mb-1">High</div>
													<?php endif; ?>
													
													<?php if($task->task_priority == 2): ?>
														<div class="badge badge-pill badge-warning mb-1">Medium</div>
													<?php endif; ?>
													
													<?php if($task->task_priority == 3): ?>
														<div class="badge badge-pill badge-success mb-1">low</div>
													<?php endif; ?></div>
											</div>
										</li>
										<?php endforeach; ?>
										
										<?php foreach ($taskss as $task): ?>
											<li class="media">
												<img class="mr-3 rounded-circle" width="50" src="<?php echo base_url(); ?>assets/img/avatar/avatar-4.png" alt="avatar">
												<div class="media-body">
													
													
													<?php if($task->task_status == 0): ?>
														<div class="badge badge-pill badge-danger mb-1 float-right">Not Finished</div>
													<?php endif; ?>
													
													<?php if($task->task_status == 1): ?>
														<div class="badge badge-pill badge-warning mb-1 float-right">Finished</div>
													<?php endif; ?>
													
													<h6 class="media-title"><a href="<?php echo site_url('view_task').'/'.$task->task_id; ?>"><?=$task->task_title; ?></a></h6>
													<div class="text-small text-muted"><?php $employee = $CI->employees->get_employee($task->task_supervisor_id); echo $employee->employee_last_name." ".$employee->employee_first_name;  ?>  <div class="bullet"></div> <span class="text-primary"><?=$task->task_start_date.' : '.$task->task_end_date; ?></span>
														<div class="bullet"></div>
														<?php if($task->task_priority == 1): ?>
															<div class="badge badge-pill badge-danger mb-1">High</div>
														<?php endif; ?>
														
														<?php if($task->task_priority == 2): ?>
															<div class="badge badge-pill badge-warning mb-1">Medium</div>
														<?php endif; ?>
														
														<?php if($task->task_priority == 3): ?>
															<div class="badge badge-pill badge-success mb-1">low</div>
														<?php endif; ?></div>
												</div>
											</li>
										<?php endforeach; ?>
										
									</ul>
								</div>
								<div class="card-footer bg-whitesmoke"></div>
							</div>
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
	$('title').html('My Tasks - IHUMANE');
</script>









