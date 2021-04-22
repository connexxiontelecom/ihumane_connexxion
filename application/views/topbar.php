
<nav class="navbar navbar-expand-lg main-navbar">
	<form class="form-inline mr-auto" onsubmit="return false;">
		<ul class="navbar-nav mr-3">
			<li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
			<li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
		</ul>
    <?php if($employee_management == 1):?>
		<div class="search-element">
			<input class="form-control" type="search" placeholder="Search Employees" aria-label="Search" data-width="250" onkeyup="filter(this)">
			<button class="btn" type="submit"><i class="fas fa-search"></i></button>
			<div class="search-backdrop"></div>
			<div class="search-result pl-2 pb-3">
				<div class="search-header mb-2">
					Employees
				</div>
        <div class="search-items" id="search-items" style="max-height: 300px; overflow-y: auto">

        </div>
        <script language="javascript" type="text/javascript">
          function filter(element) {
            let value = $(element).val();
            $("#search-items > .search-item").each(function() {

              if ($(this).text().search(new RegExp(value, "i")) > -1) {
                $(this).show();
              }
              else {
                $(this).hide();
              }
            });
          }
        </script>
			</div>
		</div>
    <?php endif;?>
	</form>
	<ul class="navbar-nav navbar-right">
		<div id="notifications">
			<?php $count = count($notifications); ?>
		<li  class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg <?php if ($count > 0){echo "beep"; } ?>"><i class="far fa-bell"></i></a>
			<div class="dropdown-menu dropdown-list dropdown-menu-right">
				<div class="dropdown-header">Notifications
					<?php if($count > 0): ?>
										<div class="float-right">
											<a href="<?php echo site_url('clear_notifications')."/a"?>">Mark All As Read</a>
										</div>
					<?php endif; ?>
				</div>
				<?php if(!empty($notifications)): ?>

					<div class="dropdown-list-content dropdown-list-icons">
						<?php foreach ($notifications as $notification): ?>

							<a id="<?php echo $notification->notification_id; ?>" href="<?php echo site_url()."view_notifications/".$notification->notification_id; ?>" class="dropdown-item dropdown-item-unread">
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
    <li class="dropdown dropdown-list-toggle">
      <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle">
        <i class="far fa-question-circle"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-sm">
        <li class="dropdown-title">Help Center</li>
        <li><a href="https://docs.ihumane.net/" target="_blank" class="dropdown-item">IHUMANE Documentation</a></li>
        <li><a href="https://ihumane.net" target="_blank" class="dropdown-item">Visit Site</a></li>
        <li><a href="mailto:support@ihumane.net" class="dropdown-item">Contact Support</a></li>
      </ul>
    </li>

		<li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
				<img alt="image" src="<?php echo base_url() ?>/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
				<div class="d-sm-none d-lg-inline-block">Hi, <?php echo $user_data->user_name; ?></div></a>
			<div class="dropdown-menu dropdown-menu-right">
				<div class="dropdown-title">Logged in for <br/><?php echo timespan($this->session->userdata('login_time'), time(), 2)?></div>
				<a href="<?php echo site_url('view_log')?>" class="dropdown-item has-icon">
					<i class="fas fa-bolt"></i> Activities
				</a>
				<a href="#" class="dropdown-item has-icon">
					<i class="fas fa-cog"></i> Settings
				</a>
				<?php if($user_data->user_type == 3): ?>
				<a href="<?php echo base_url('employee_main') ?>" class="dropdown-item has-icon">
					<i class="fas fa-arrow-circle-right"></i> Switch to Self-Service
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
<script src="<?php echo base_url(); ?>assets/bower_components/push.js/bin/push.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.playSound.js"></script>

<script>
	$(document).ready(function () {
		
		var check = 0;
		var checks = 1;
		setInterval(timestamp, 5000);
		function timestamp() {

			var employee_id = 0;
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

						check++;
						// if (seconds < 10) {
						if((checks - check)%30 == 0){

							var notification_id = data[0].notification_id;
							Push.create("iHumane", {
								body: data[i].notification_type,
								icon: "https://app.ihumane.net//assets/img/ihumane-logo-1.png",
								timeout: 4000,
								onClick: function () {
									// window.focus();
									// this.close();
									location.href = '<?php echo site_url()?>/view_notifications/' + notification_id;
								}
							});
							$.playSound("<?php echo base_url('assets/notification/insight.mp3'); ?>");


						}
					}

				},

				error: function () {
					console.log(this.error);
				}
			});
		}
    $.ajax({
      url: '<?php echo site_url('get_employees')?>',
      success: function(employees) {
        let employeeList = JSON.parse(employees)
        for(let i = 0; i<employeeList.length; i++){
          //console.log(employeeList[i])
          $('.search-items').append(`
            <div class="search-item">
              <a href="<?php echo site_url('view_employee').'/'; ?>${employeeList[i].employee_id}">
                <img class="mr-3 rounded" width="30" height="30" src="<?php echo base_url()?>/uploads/employee_passports/${employeeList[i].employee_passport}" alt="product">
                ${employeeList[i].employee_first_name} ${employeeList[i].employee_last_name}
              </a>
            </div>
          `)
        }
        // console.log(JSON.parse(employees));
      }
    });
	})

</script>
