<html>

	<head>
		<title>Data Masuk</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

		<style>

		</style>
	</head>

	<body>

		<main>
			<h3 class="fw-bold mb-3 mt-3 text-center">Data Masuk DPUPESDM</h3> 
			<hr/>
			<div class="container-md px-3">
				<section class="mb-3"> 

					<div class="row gx-md-3 justify-content-center "> 
						<div class="col-lg-4"> 
							<ul class="list-group list-group-item-primary p-0">
								<li class="list-group-item d-flex justify-content-between align-items-center">
									<span class="fw-bold">ID Logger</span>
									<span><?= $this->session->userdata('log_id') ?></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center">
									<span class="fw-bold">Tanggal</span>
									<span><?= $this->session->userdata('tgl_search') ?></span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center">
									<span class="fw-bold">Serial Number</span>

								</li>
								<li class="list-group-item d-flex align-items-center justify-content-between">
									<span class="fw-bold">Kelengkapan Data</span>
									<div class="d-flex align-items-center ">
										<span class="badge bg-primary me-2 pb-1"><?= ($data_count == 0) ? '0 %' :  number_format($data_count/$total_minutes*100,2,'.','') . ' %'   ?></span><span><?=  $data_count .' / ' . $total_minutes ?></span>
									</div>
								</li>
							</ul>
						</div> 
						<div class="col-lg-5 mt-2 mt-md-0"> 
							<form action="<?= base_url() ?>datamasuk/tgl_search" method="post">
								<div class="card mb-2" >
									<div class="card-body">
										<!--<form class="row g-3">-->
										<div class="row">
											<div class="col-auto ">
												<label class="text-md-start">ID Logger</label>
											</div>
											<div class="col">
												<select class="form-select" name="logger_id" id="" required>
													<option value="" selected disabled>Pilih ID Logger</option>
													<?php foreach ($list_logger as $ls) { ?>

													<option value="<?= $ls['code_logger'] ?>" <?= ($this->input->get('id_logger') == $ls['code_logger']) ? 'selected' : '' ?>><?= $ls['nama_logger'] . ' - ' . $ls['code_logger']  ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
								</div>	
								<div class="card mb-2">
									<div class="card-body">
										<div class="row">
											<div class="col-auto ">
												<label class="text-md-start">ID Logger</label>
											</div>
											<div class="col">
												<input type="date" id="tgl" name="tgl" class="form-control" value="<?= $this->input->get('tgl') ?>">
											</div>
											<div class="col-auto ">
												<button class="btn btn-primary" type="submit">Cari</button>

											</div>
										</div>
										<!--</form>-->

									</div>
								</div>
							</form>
						</div> 
<!--
						<div class="col-lg-2 mt-2 mt-md-0"> 
							
							<?php echo form_open('datamasuk');?>

							<button class="btn btn-primary" name="btnrefresh" type="submit"><div class="d-flex justify-content-center"><span class="material-symbols-outlined mx-2" >refresh</span> Refresh</div></button>


							<?php echo form_close();?>
						</div> 
-->

					</div> 
				</section>

			</div>

		</main>
		<hr />
		<?php if ($data) { ?>
		<div class="table-responsive px-3">
			<table border="1" cellspacing="1" cellpadding="1" class="table table-bordered table-sm">
				<tr class="fw-bold">
					<?php
	foreach ($key as $r) {
					?>
					<td style="font-size:11px" class=" text-center bg-light">&nbsp;<?= str_replace('_',' ',$r['nama']) ?>&nbsp;</td>
					<?php
		}
					?>
				</tr>
				<tr style="font-size:13px" class="fw-bold text-center">
					<?php
	foreach ($key as $r) {
					?>
					<td class=" bg-light">&nbsp;<?= $r['key'] ?>&nbsp;</td>
					<?php
		}
					?>
				</tr><?php
	foreach ($data as $row) {
				?>
				<tr>
					<?php foreach ($row as $ky => $val) { ?>
					<td  style="font-size:14px;text-align:center" class="text-nowrap"><?= $val ?></td>
					<?php } ?>
				</tr>

				<?php
	}
				?>
			</table>
		</div>
		<?php } else { ?>
		<center> Data Tidak Ditemukan</center>
		<?php } ?>

	</body>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>

</html>