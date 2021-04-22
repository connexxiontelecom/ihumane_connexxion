<?php $CI =& get_instance();
	$CI->load->model('biometric');
?>
<script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/push_notification.js"></script>
<nav class="navbar navbar-expand-lg main-navbar">
	<a href="<?php echo site_url('employee_main'); ?>" class="navbar-brand sidebar-gone-hide">iHumane</a>
	<div class="navbar-nav">
		<a href="#" class="nav-link sidebar-gone-show" data-toggle="sidebar"><i class="fas fa-bars"></i></a>
	</div>
	<div class="nav-collapse d-sm-none d-md-block">
		<a class="sidebar-gone-show nav-collapse-toggle nav-link" href="#">
			<i class="fas fa-ellipsis-v"></i>
		</a>
		<ul class="navbar-nav">
			<li class="nav-item <?php echo $this->uri->segment(1) != 'documents' && $this->uri->segment(1) != 'view_document' ? 'active' : '' ?>"><a href="<?php echo site_url('employee_main'); ?>" class="nav-link">App</a></li>
			<li class="nav-item <?php echo $this->uri->segment(1) == 'documents' || $this->uri->segment(1) == 'view_document' ? 'active' : '' ?>"><a href="<?php echo site_url('documents'); ?>" class="nav-link">Documents</a></li>
      <li class="nav-item dropdown dropdown-list-toggle">
        <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle">Help</a>
        <ul class="dropdown-menu dropdown-menu-sm">
          <li class="dropdown-title">Help Center</li>
          <li><a href="https://docs.ihumane.net/" target="_blank" class="dropdown-item">IHUMANE Documentation</a></li>
          <li><a href="https://ihumane.net" target="_blank" class="dropdown-item">Visit Site</a></li>
          <li><a href="mailto:support@ihumane.net" class="dropdown-item">Contact Support</a></li>
        </ul>
	
		 <?php $date = date('Y-m-d', time());
		 
		
		  $check_login = $CI->biometric->check_clock_in($employee_id, $date);
		  if(empty($check_login)): ?>
			<li class="nav-item dropdown dropdown-list-toggle">
				<form method="post" action="<?php echo site_url('employee_clockin') ?>">
					<input type="hidden" name="employee_id" value="<?php echo $employee_id ?>">
					
					<button type="submit"  class="btn btn-primary">Clock In</button>
				</form>
			 
			</li>
		<?php else: ?>
			
			
			
			<li class="nav-item dropdown dropdown-list-toggle">
				<form method="post" action="<?php echo site_url('employee_clockout') ?>">
					<input type="hidden" name="employee_id" value="<?php echo $employee_id ?>">
			
					<button type="submit" class="btn btn-primary">Clock out</button>
				</form>
			
			</li>
			
			<?php endif;   ?>
      </li>
		</ul>
	</div>

	<ul class="navbar-nav navbar-right" style="margin-left: 50%;">
		<div id="notifications">
		<?php $count = count($notifications); ?>
		<li  class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg <?php if ($count > 0){echo "beep"; } ?>"><i class="far fa-bell"></i></a>
			<div class="dropdown-menu dropdown-list dropdown-menu-right">
				<div class="dropdown-header">Notifications
					<?php if($count > 0): ?>
						<div class="float-right">
							<a href="<?php echo site_url('clear_notifications')."/".$employee->employee_id;?>">Mark All As Read</a>
						</div>
					<?php endif; ?>
				</div>
				<?php if(!empty($notifications)): ?>
				<div class="dropdown-list-content dropdown-list-icons">
					<?php foreach ($notifications as $notification): ?>

					<a id="<?php echo $notification->notification_id; ?>" href="<?php echo site_url()."view_notification/".$notification->notification_id; ?>" class="dropdown-item dropdown-item-unread">
						<div class="dropdown-item-icon bg-primary text-white">
							<i class="fas fa-code"></i>
						</div>
						<div class="dropdown-item-desc">
							<?php echo $notification->notification_type; ?>
							<div class="time text-primary"><?php echo $notification->notification_date; ?></div>
						</div>
					</a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
        <?php if(empty($notifications)): ?>
        <div class="dropdown-footer text-center">
          No Notifications
        </div>
        <?php endif;?>
			</div>
		</li>
		</div>
		<li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
				<img alt="image" src="<?php echo base_url(); ?>/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
				<div class="d-sm-none d-lg-inline-block">Hi,  <?php echo $user_data->user_name; ?></div></a>
			<div class="dropdown-menu dropdown-menu-right">
				<div class="dropdown-title">Logged in For <br> <?php echo timespan($this->session->userdata('login_time'), time(), 2)?></div>
				<a href="<?php echo base_url('personal_information') ?>" class="dropdown-item has-icon">
					<i class="far fa-user"></i> Profile
				</a>
				<a href="<?php echo base_url('change_password') ?>" class="dropdown-item has-icon">
					<i class="fas fa-lock"></i> Change Password
				</a>
				<a href="<?php echo base_url('employee_history') ?>" class="dropdown-item has-icon">
					<i class="fas fa-bolt"></i> Activities
				</a>
				<?php if($user_data->user_type == 3): ?>
					<a href="<?php echo base_url(); ?>" class="dropdown-item has-icon">
						<i class="fas fa-arrow-circle-left"></i> Switch to Admin
					</a>
				<?php endif; ?>
				<div class="dropdown-divider"></div>
				<a href="<?php echo site_url('logout'); ?>" class="dropdown-item has-icon text-danger">
					<i class="fas fa-sign-out-alt"></i> Logout
				</a>
			</div>
		</li>
	</ul>
</nav>
<script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/modules/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/push.js/bin/push.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.playSound.js"></script>
<script>
	$(document).ready(function () {

		setInterval(timestamp, 1000);
		var check = 0;
		var checks = 1;
		function timestamp() {
			
			
			var employee_id = <?php echo $employee->employee_id; ?>;
			$.ajax({
				type: "POST",
				url: '<?php echo site_url('get_notifications'); ?>',
				data: {employee_id: employee_id},
				success: function (data) {

					var data = JSON.parse(data);
					
					
					if(data.length > 0) {

						var i = data.length - 1;

						var datum = new Date(data[0].notification_date);

						var now = new Date();

						var seconds = moment(now).diff(moment(datum), 'seconds');
						
						check ++;
						// if (seconds < 10) {
						//console.log(check);
						if((checks - check)%30 == 0){

							var notification_id = data[0].notification_id;
							Push.create("iHumane", {
								body: data[i].notification_type,
								icon: "https://app.ihumane.net//assets/img/ihumane-logo-1.png",
								timeout: 4000,
								onClick: function () {

									location.href = '<?php echo site_url()?>/view_notifications/' + notification_id;
								}
							});
							$.playSound("<?php echo base_url('assets/notification/insight.mp3'); ?>");
							//console.log('i throe0');
							
							
						}
					}

				},
				error: function () {
					console.log(this.error);
				}
			});
		}
	})

</script>


