<?php
	include(APPPATH.'/views/stylesheet.php');
	$CI =& get_instance();
	$CI->load->model('hr_configurations');
	$CI->load->model('payroll_configurations');
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
					<h1>New Task</h1>
					<div class="section-header-breadcrumb">
						<div class="breadcrumb-item active"><a href="<?php echo site_url('employee_main'); ?>">Dashboard</a></div>
						<div class="breadcrumb-item">New Task</div>
					</div>
				</div>
				<div class="section-body">
					<div class="section-title">All About Assigning Tasks</div>
					<p class="section-lead">You can assigning tasks here</p>
					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h4>New Task</h4>
								</div>
								<div class="card-body">
									<form method="post">
									<div class="form-group row mb-4">
										<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Title</label>
										<div class="col-sm-12 col-md-7">
											<input type="text" class="form-control" name="task_title" required>
										</div>
									</div>
									<div class="form-group row mb-4">
										<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Employees</label>
										<div class="col-sm-12 col-md-7">
											<select class="select2 form-control" name="task_employee_id">
												<option disabled selected>--select employee--</option>
												<?php foreach ($employees as $employee):
													if($employee->employee_status != 3 || $employee->employee_status != 0 ):
													?>
													
													<option value="<?php echo $employee->employee_id ?>"> <?php echo $employee->employee_unique_id." (".$employee->employee_last_name." ".$employee->employee_first_name.")"; ?> </option>
												<?php endif; endforeach; ?>
											</select>
										</div>
									</div>
									
									<div class="form-group row mb-4">
										<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Participants</label>
										<div class="col-sm-12 col-md-7">
											<select class="select2 form-control" name="task_participants[]" multiple>
												<?php foreach ($employees as $employee):
												if($employee->employee_status != 3 || $employee->employee_status != 0 ):
												?>
												
												<option value="<?php echo $employee->employee_id ?>"> <?php echo $employee->employee_unique_id." (".$employee->employee_last_name." ".$employee->employee_first_name.")"; ?> </option>
												<?php endif; endforeach; ?>
											</select>
										</div>
									</div>
									<div class="form-group row mb-4">
										<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Content</label>
										<div class="col-sm-12 col-md-7">
											<textarea class="summernote-simple" name="task_contents"></textarea>
										</div>
									</div>
									
									
									<div class="form-group row mb-4">
										<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Priority</label>
										<div class="col-sm-12 col-md-7">
											<select class="form-control selectric" name="task_priority">
												<option value="1">High </option>
												<option value="2">Medium</option>
												<option value="3">Low</option>
											</select>
										</div>
									</div>
									<div class="form-group row mb-4">
										<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Task Start Date:</label><span style="color: red"> *</span>
										<div  class="col-sm-12 col-md-7">
											
											<input type="text" class="form-control datepicker"  name="task_start_date" required/>
											<div class="invalid-feedback">
												please fill in a start date
											</div>
										</div>
<!--										<div class="col-sm-6">-->
<!--											<label>Appraisal End Duration</label><span style="color: red"> *</span>-->
<!--											<input type="text" class="form-control datepicker"  name="end_date" required/>-->
<!--											<div class="invalid-feedback">-->
<!--												please fill in an end date-->
<!--											</div>-->
<!--										</div>-->
									</div>
									
									<div class="form-group row mb-4">
										<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Task End Date</label><span style="color: red"> *</span>
										<div  class="col-sm-12 col-md-7">
											
											<input type="text" class="form-control datepicker"  name="task_end_date" required/>
											<div class="invalid-feedback">
												please fill in a start date
											</div>
										</div>
										<!--										<div class="col-sm-6">-->
										<!--											<label>Appraisal End Duration</label><span style="color: red"> *</span>-->
										<!--											<input type="text" class="form-control datepicker"  name="end_date" required/>-->
										<!--											<div class="invalid-feedback">-->
										<!--												please fill in an end date-->
										<!--											</div>-->
										<!--										</div>-->
									</div>
										<input type="hidden" name="<?php echo $csrf_name;?>" value="<?php echo $csrf_hash;?>" />
									<div class="form-group row mb-4">
										<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
										<div class="col-sm-12 col-md-7">
											<button type="submit" class="btn btn-primary">Assign Task</button>
										</div>
									</div>
									</form>
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
	$('title').html('New Task - IHUMANE');
</script>









