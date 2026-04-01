<div class="col-md-9">

	<div class="card">
		<div class="card-body">
			<h2 class="mb-2 mt-2 fw-bold">Tingkat Status ARR</h2>
			<hr class="mb-3 mt-0">

			<div class="card rounded border">
				<div class="card-header py-1 d-flex justify-content-center">
					<h3 class="card-title text-center  py-2">Tingkat Status Per Jam</h3>
				</div>
				<div class="card-body">
					<form action="<?= base_url()?>pengaturan/update_status_arr/1"  method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Tidak Hujan</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-hijau.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class="form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">=</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $jam->hijau?>" type="text" name="hijau" disabled/>
										</div>
									</div>
									
								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Sangat Ringan</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-cyan.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $jam->biru?>" type="text" name="biru" disabled/>
										</div>
									</div>
									
								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Ringan</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-nila.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $jam->biru_tua?>" type="text" name="biru_tua" disabled/>
										</div>
									</div>
									
								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Sedang</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-kuning.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $jam->kuning?>" type="text" name="kuning" disabled/>
										</div>
									</div>
									
								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Lebat</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-oranye.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $jam->oranye?>" type="text" name="oranye" disabled/>
										</div>
									</div>
									
								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Sangat Lebat</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-merah.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $jam->merah?>" type="text" name="merah" disabled/>
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<div class="text-center mt-3 w-100 d-none">
							<button class="btn btn-outline-primary">Ubah</button>
						</div>
					</form>
				</div>
			</div>
			<div class="card rounded border mt-3">
				<div class="card-header py-1 d-flex justify-content-center">
					<h3 class="card-title text-center  py-2">Tingkat Status Per Hari</h3>
				</div>
				<div class="card-body">
					<form action="<?= base_url()?>pengaturan/update_status_arr/2" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Tidak Hujan</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-hijau.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class="form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">=</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $hari->hijau?>" type="text" name="hijau" disabled/>
										</div>
									</div>

								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Sangat Ringan</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-cyan.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $hari->biru?>" type="text" name="biru" disabled/>
										</div>
									</div>

								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Ringan</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-nila.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $hari->biru_tua?>" type="text" name="biru_tua" disabled/>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Sedang</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-kuning.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $hari->kuning?>" type="text" name="kuning" disabled/>
										</div>
									</div>

								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Lebat</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-oranye.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $hari->oranye?>" type="text" name="oranye" disabled/>
										</div>
									</div>

								</div>
							</div>
							<div class="col-xl-2">
								<div class="form-group my-1">
									<label>Hujan Sangat Lebat</label>
									<div class="row gx-2">
										<div class="col-3 d-flex align-items-end">
											<div class="form-control w-100 rounded" style="background-image: url(../pin_marker/kotak-merah.png);background-repeat:no-repeat;background-position: center center;"><span style="opacity:0">w</span></div>
										</div>
										<div class="col-2 d-flex align-items-end">
											<div class=" w-100 form-control border-0 d-flex align-items-center justify-content-center" style=""><h4 class="mb-1 p-0" style="font-size:22px">≥</h4></div>
										</div>
										<div class="col-7">
											<input class="form-control mt-2" value="<?= $hari->merah?>" type="text" name="merah" disabled/>
										</div>
									</div>

								</div>
							</div>
						</div>
						<div class="text-center mt-3 w-100 d-none">
							<button class="btn btn-outline-primary">Ubah</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#example').DataTable(
				scrollX = true
			);
		});
	</script>