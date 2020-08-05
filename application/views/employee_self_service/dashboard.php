
<?php
	include(APPPATH.'/views/stylesheet.php');
	$CI =& get_instance();
	$CI->load->model('hr_configurations');
	$CI->load->model('payroll_configurations');
	$location = 'Abuja';
	$key = '4bbd388761052d71725bcd55680d1d0c';
	$url = "https://api.openweathermap.org/data/2.5/weather?q=".$location."&appid=".$key."&units=metric";
//"https://api.openweathermap.org/data/2.5/weather?q=Abuja&appid=4bbd388761052d71725bcd55680d1d0c&units=metric"
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
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
          <h1>Dashboard</h1>
          <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo site_url('employee_main'); ?>">Dashboard</a></div>
          </div>
        </div>
        <div class="section-body">
          <div class="row">
            <div class="col-12 mb-4">
              <div class="hero bg-primary text-white">
                <div class="hero-inner">
                  <h2> <?php
                    $dob = $employee->employee_dob;
                    $date = DateTime::createFromFormat("Y-m-d", $dob);
                    $_dob = $date->format("m")."-".$date->format("d");
                    $today = date('Y-m-d');
                    $dates = DateTime::createFromFormat("Y-m-d", $today);
                    $_today = $dates->format("m")."-".$dates->format("d");
                    if($_dob == $_today){ echo "Happy Birthday,"; } else {
                      echo "Welcome,";
                    } ?> <?php echo $user_data->user_name; ?> </h2>
									<p class="lead">The self-service portal provides you with access to the full range of human resource functions and responsibilities</p>
                  <p class="lead" id="timestamp"></p>
                </div>
                <?php if($_dob == $_today){ ?>
                  <canvas id="birthday" style="height: 50vh; display: inherit"></canvas>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
              <div class="card">
                <div class="card-header">
                  <h4>Recent Announcements</h4>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled list-unstyled-border">
                    <?php if(!empty($memos)):
                      $count = 1;
                    foreach($memos as $memo):
                    ?>
                    <li class="media">
                      <div class="media-body">
                        <small class="float-right text-primary"><?php echo date('F j, Y', strtotime($memo->memo_date)); ?></small>
                        <div class="media-title"><?php echo $memo->memo_subject; ?></div>
                        <span class="text-small text-muted"><?php echo $memo->memo_body; ?></span>
                      </div>
                    </li>
                    <?php
                    if($count == 5 ):
                      break;
                    endif;
                    $count++;
                    endforeach;
                    endif;
                    ?>
                  </ul>
                  <div class="text-center pt-1 pb-1">
                    <a href="<?php echo site_url('my_memos'); ?>" class="btn btn-primary btn-lg btn-round">
                      View All
                    </a>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header">
                  <h4>Recent Memos</h4>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled list-unstyled-border">
                    <?php if(!empty($specific_memos)):
                      $count = 1;
                      foreach($specific_memos as $memo):
                        ?>
                        <li class="media">
                          <div class="media-body">
                            <small class="float-right text-primary"><?php echo date('F j, Y', strtotime($memo->specific_memo_date)); ?></small>
                            <div class="media-title"><?php echo $memo->specific_memo_subject; ?></div>
                            <span class="text-small text-muted"><?php echo $memo->specific_memo_body; ?></span>
                          </div>
                        </li>
                        <?php
                        if($count == 5 ):
                          break;
                        endif;
                        $count++;
                      endforeach;
                    endif;
                    ?>
                  </ul>
                  <div class="text-center pt-1 pb-1">
                    <a href="<?php echo site_url('my_specific_memos'); ?>" class="btn btn-primary btn-lg btn-round">
                      View All
                    </a>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header">
                  <h4>Recent Queries</h4>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled list-unstyled-border">
                    <?php if(!empty($queries)):
                      $count = 0;
                      foreach($queries as $query):
                        ?>
                        <li class="media">
                          <div class="media-body">
                            <small class="float-right text-primary"><?php echo date('F j, Y', strtotime($query->query_date)); ?></small>
                            <div class="media-title"><?php echo $query->query_subject; ?> - <?php if($query->query_status == 1){ echo "Opened"; } if($query->query_status == 0){ echo "Closed"; } ?></div>
                            <span class="text-small text-muted"><?php echo strip_tags($query->query_body); ?></span>
                          </div>
                        </li>
                        <?php
                        if($count == 5 ):
                          break;
                        endif;

                        $count++;
                      endforeach;
                    endif;
                    ?>
                  </ul>
                  <div class="text-center pt-1 pb-1">
                    <a href="<?php echo site_url('my_queries'); ?>" class="btn btn-primary btn-lg btn-round">
                      View All
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
              <div class="card">
                <div class="card-header">
                  <h4>Calendar</h4>
                </div>
                <div class="card-body">
                  <div class="fc-overflow">
                    <div id="myEvent"></div>
                  </div>
                </div>
              </div>
<!--              <script src="https://apps.elfsight.com/p/platform.js" defer></script>-->
<!--              <div class="elfsight-app-94db080b-fd1a-434a-a153-c96187c02ee5"></div>-->
							<?php if($response): $response = json_decode($response); //print_r($response); ?>
								<div class="card">
									<div class="card-header">
										<h4>Weather</h4>
									</div>
									<div class="card-body">
										<ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
											<li class="media">
												<div class="media-body">
													<div class="media-title"><?php echo $location?></div>
													<div class="text-job text-muted"><?php echo $response->weather[0]->description?></div>
													<h2><?php echo $response->main->temp?>&#176;</h2>
												</div>
											</li>
											<li class="media">
												<div class="media-body">
													<div class="media-title"><?php echo date('g:i a', $response->sys->sunrise);?></div>
													<div class="text-job text-muted">Sunrise</div>
												</div>
												<div class="media-body">
													<div class="media-title"><?php echo date('g:i a', $response->sys->sunset);?></div>
													<div class="text-job text-muted">Sunset</div>
												</div>
												<div class="media-body">
													<div class="media-title"><?php echo $response->visibility ?> m</div>
													<div class="text-job text-muted">Visibility</div>
												</div>
											</li>
											<li class="media">
												<div class="media-body">
													<div class="media-title"><?php echo $response->main->temp_min?>&#176;</div>
													<div class="text-job text-muted">Min Temp</div>
												</div>
												<div class="media-body">
													<div class="media-title"><?php echo $response->main->temp_max?>&#176;</div>
													<div class="text-job text-muted">Max Temp</div>
												</div>
												<div class="media-body">
													<div class="media-title"><?php echo $response->main->feels_like?>&#176;</div>
													<div class="text-job text-muted">Feels Like</div>
												</div>
											</li>
											<li class="media">
												<div class="media-body">
													<div class="media-title"><?php echo $response->main->pressure?> hPa</div>
													<div class="text-job text-muted">Pressure</div>
												</div>
												<div class="media-body">
													<div class="media-title"><?php echo $response->main->humidity?> %</div>
													<div class="text-job text-muted">Humidity</div>
												</div>
												<div class="media-body">
													<div class="media-title"><?php echo $response->wind->speed?> mps</div>
													<div class="text-job text-muted">Wind Speed</div>
												</div>
											</li>

										</ul>
									</div>
								</div>
							<?php endif;?>
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
	$('title').html('Dashboard - IHUMANE');

	$(document).ready(function() {
		setInterval(timestamp, 1000);
		function timestamp() {
			$.ajax({
				url: '<?php echo site_url('timestamp')?>',
				success: function (data) {
					$('#timestamp').html(data);
				}
			})
		}
	});
	// helper functions
	const PI2 = Math.PI * 2
	const random = (min, max) => Math.random() * (max - min + 1) + min | 0
	const time = _ => new Date().getTime()
	// container
	class Birthday {
		constructor() {
			this.resize()
			// create a lovely place to store the firework
			this.fireworks = []
			this.counter = 0
		}
		resize() {
			this.width = canvas.width = window.innerWidth
			let center = this.width / 2 | 0
			this.spawnA = center - center / 4 | 0
			this.spawnB = center + center / 4 | 0
			this.height = canvas.height = window.innerHeight
			this.spawnC = this.height * .1
			this.spawnD = this.height * .5
		}
		onClick(evt) {
			let x = evt.clientX || evt.touches && evt.touches[0].pageX
			let y = evt.clientY || evt.touches && evt.touches[0].pageY
			let count = random(3,5)
			for(let i = 0; i < count; i++) this.fireworks.push(new Firework(
					random(this.spawnA, this.spawnB),
					this.height,
					x,
					y,
					random(0, 260),
					random(30, 110)))
			this.counter = -1
		}
		update(delta) {
			ctx.globalCompositeOperation = 'hard-light'
			ctx.fillStyle = `rgba(20,20,20,${ 7 * delta })`
			ctx.fillRect(0, 0, this.width, this.height)

			ctx.globalCompositeOperation = 'lighter'
			for (let firework of this.fireworks) firework.update(delta)

			// if enough time passed... create new new firework
			this.counter += delta * 3 // each second
			if (this.counter >= 1) {
				this.fireworks.push(new Firework(
						random(this.spawnA, this.spawnB),
						this.height,
						random(0, this.width),
						random(this.spawnC, this.spawnD),
						random(0, 360),
						random(30, 110)))
				this.counter = 0
			}

			// remove the dead fireworks
			if (this.fireworks.length > 1000) this.fireworks = this.fireworks.filter(firework => !firework.dead)

		}
	}

	class Firework {
		constructor(x, y, targetX, targetY, shade, offsprings) {
			this.dead = false
			this.offsprings = offsprings

			this.x = x
			this.y = y
			this.targetX = targetX
			this.targetY = targetY

			this.shade = shade
			this.history = []
		}
		update(delta) {
			if (this.dead) return

			let xDiff = this.targetX - this.x
			let yDiff = this.targetY - this.y
			if (Math.abs(xDiff) > 3 || Math.abs(yDiff) > 3) { // is still moving
				this.x += xDiff * 2 * delta
				this.y += yDiff * 2 * delta

				this.history.push({
					x: this.x,
					y: this.y
				})

				if (this.history.length > 20) this.history.shift()

			} else {
				if (this.offsprings && !this.madeChilds) {

					let babies = this.offsprings / 2
					for (let i = 0; i < babies; i++) {
						let targetX = this.x + this.offsprings * Math.cos(PI2 * i / babies) | 0
						let targetY = this.y + this.offsprings * Math.sin(PI2 * i / babies) | 0

						birthday.fireworks.push(new Firework(this.x, this.y, targetX, targetY, this.shade, 0))

					}

				}
				this.madeChilds = true
				this.history.shift()
			}

			if (this.history.length === 0) this.dead = true
			else if (this.offsprings) {
				for (let i = 0; this.history.length > i; i++) {
					let point = this.history[i]
					ctx.beginPath()
					ctx.fillStyle = 'hsl(' + this.shade + ',100%,' + i + '%)'
					ctx.arc(point.x, point.y, 1, 0, PI2, false)
					ctx.fill()
				}
			} else {
				ctx.beginPath()
				ctx.fillStyle = 'hsl(' + this.shade + ',100%,50%)'
				ctx.arc(this.x, this.y, 1, 0, PI2, false)
				ctx.fill()
			}

		}
	}

	let canvas = document.getElementById('birthday')
	var ctx = canvas.getContext('2d')

	let then = time()

	let birthday = new Birthday
	window.onresize = () => birthday.resize()
	document.onclick = evt => birthday.onClick(evt)
	document.ontouchstart = evt => birthday.onClick(evt)

	;(function loop(){
		requestAnimationFrame(loop)

		let now = time()
		let delta = now - then

		then = now
		birthday.update(delta / 1000)


	})()

</script>
