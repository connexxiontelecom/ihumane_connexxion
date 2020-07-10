
<?php include(APPPATH.'\views\stylesheet.php');
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
					<h1>APPRAISAL -> <?php echo $CI->employees->get_appraisal($appraisal_id)->employee_last_name." ". $CI->employees->get_appraisal($appraisal_id)->employee_first_name ?> </h1>
					<div class="section-header-breadcrumb">
						<div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>">Dashboard</a></div>
						<div class="breadcrumb-item">Appraise Employees</div>
					</div>
				</div>
				<div class="section-body">
					<div class="section-title">All About Responding to Appraisal Questions</div>
					<p class="section-lead">You can answer the questions to complete the form</p>
					<div class="row">
						<div class="col-12">
							<div class="card mb-0">
								<div class="card-body">
									<ul class="nav nav-pills" role="tablist">
										<li class="nav-item waves-effect waves-light">
											<a class="nav-link active" data-toggle="tab" href="#self" role="tab"> <i class="fas fa-user"></i> Self Performance</a>
										</li>


									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-4">
						<div class="col-12">
							<form method="post" action="<?php echo site_url('answer_questions_self'); ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
								<div class="card card-primary">
									<div class="card-header">
										<h4>Appraisal Form</h4>
									</div>
									<div class="card-body">
										<div class="tab-content">
											<div class="tab-pane active p-3" id="quantitative" role="tabpanel">





												<!--												--><?php //if($error != ' '): ?>
<!--													<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">-->
<!--														<button type="button" class="close" data-dismiss="alert" aria-label="Close">-->
<!--															<span aria-hidden="true">&times;</span>-->
<!--														</button>-->
<!--														<i class="mdi mdi-close-circle font-32"></i><strong class="pr-1">Error !</strong> --><?php //echo $error; ?><!--.-->
<!--													</div>-->
<!--												--><?php //endif; ?>
												<div class="modal-body">

													<?php foreach ($questions as $question):
														if($question->employee_appraisal_result_type == 1):
															?>

															<div class="form-group mb-0">
																<label><?php echo $question->employee_appraisal_result_question; ?></label>
																<textarea class="form-control" style="resize: vertical" cols="10" rows="5" name="<?php echo $question->employee_appraisal_result_id ?>" required=""></textarea>
																<p class="form-text text-muted">Enter response</p>

															</div>
															<!--														<div class="invalid-feedback">-->
															<!--															What do you wanna say?-->
															<!--														</div>-->




														<?php
														endif;
													endforeach; ?>


													<input type="hidden" name="<?php echo $csrf_name;?>" value="<?php echo $csrf_hash;?>" />
													<input type="hidden" name="appraisal_id" value="<?php echo $appraisal_id;?>" />
													<div class="card-footer text-right bg-whitesmoke">
														<button type="submit" class="btn btn-primary">Submit</button>
														<input type="reset" class="btn btn-secondary">

												</div>
											</div>


											</div>

										</div>
									</div>

								</div>
							</form>
						</div>
					</div>
				</div>
			</section>
		</div>

		<?php include(APPPATH.'\views\footer.php'); ?>
	</div>
</div>

<?php include(APPPATH.'\views\js.php'); ?>
</body>
</html>










