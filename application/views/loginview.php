
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
		<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
		<title>Dinas PUPESDM Daerah Istimewa Yogyakarta</title>
		<style>
			@import url('https://rsms.me/inter/inter.css');
			:root {
				--tblr-font-sans-serif: Inter,-apple-system,BlinkMacSystemFont,San Francisco,Segoe UI,Roboto,Helvetica Neue,sans-serif !important;
			}
		</style>
		<!-- CSS files -->
		<link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/6/65/Yogyakarta_Logo.svg">
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-flags.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-payments.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-vendors.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/demo.min.css" rel="stylesheet"/>
	</head>
	<body  class=" border-top-wide border-primary d-flex flex-column" >
		<div class="page page-center">
			<div class="container-tight py-auto my-auto ">
				<div class="text-center mb-4">
					<a href="#" class="navbar-brand navbar-brand-autodark"><img src="<?=base_url() ?>image/pupesdm.svg"  alt="" ></a>
				</div>
				<?php echo form_open('login/validasi_login','id="loginform" autocomplete="off" class="card card-md"') ?>

				<div class="card-body">

					<div class="mb-3">
						<label class="form-label">Nama Pengguna</label>
						<input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo set_value('username')?>" autocomplete="off">
					</div>
					<div class="mb-2">
						<label class="form-label">
							Kata Sandi
						</label>
						<div class="input-group input-group-flat">
							<input type="password" id="typepass" name="password"  class="form-control"  placeholder="Kata Sandi" value="<?php echo set_value('password')?>" autocomplete="off">
							<span class="input-group-text">
								<a href="#" id="btneye" class="link-secondary" onclick="show()" title="Tampilkan kata sandi" ><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
									<img id="imgeye" src="<?php echo base_url()?>image/template/eye.svg" height="24" width="24" alt=""></a>
							</span>
						</div>
					</div>

					<div class="form-footer d-flex justify-content-between">
						<a href="<?= base_url() ?>login/login_tamu" class="btn btn-secondary w-100 me-3">Masuk Sebagai Tamu</a>
						<button type="submit" class="btn btn-info w-100">Masuk</button>
					</div>
				</div>
				<?php echo form_close();?>

				<?php echo form_error('username');?>
				<?php echo form_error('password');?>
				<?php echo $this->session->flashdata('message');?>
			</div>
		</div>
		<!-- Libs JS -->
		<!-- Tabler Core -->
		<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js" defer></script>
		<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/demo.min.js" defer></script>
		<script type="text/javascript">
			function show() {
				var temp = document.getElementById("typepass");
				var imgeye=document.getElementById("imgeye");
				var btneye=document.getElementById("btneye");
				if (temp.type === "password") {
					temp.type = "text";
					imgeye.src= "<?php echo base_url()?>image/template/eye-off.svg";
					btneye.title="Sembunyikan kata sandi";
				}
				else {
					temp.type = "password";
					imgeye.src= "<?php echo base_url()?>image/template/eye.svg";
					btneye.title="Tampilkan kata sandi";
				}
			}
		</script>
	</body>
</html>




