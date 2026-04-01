<div class="container-xl">
	<!-- Page title -->
	<div class="page-header d-print-none">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">
					<?php echo ucfirst($this->uri->segment(1))?>
				</h2>
			</div>
		</div>
	</div>
</div>
<div class="page-body ">
	<div class="container-xl ">
		<div class="row">
			<div class="col-md-2">
				<div class="card border-0">
					<div class="card-body p-0">
						<ul class="list-group w-100" data-bs-toggle="tabs" role="tablist">
							<li class="list-group-item py-0 px-0" role="presentation">
								<a href="#tabs-home-7" class="nav-link active w-100 d-flex justify-content-center py-3" data-bs-toggle="tab" aria-selected="true" role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map me-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
										<path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13"></path>
										<path d="M9 4v13"></path>
										<path d="M15 7v13"></path>
									</svg>
									Logger Pos Pemantauan</a>
							</li>
							<li class="list-group-item py-0 px-0" role="presentation">
								<a href="#tabs-home-2" class="nav-link w-100 d-flex justify-content-center py-3" data-bs-toggle="tab" aria-selected="true" role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-location-pin me-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
										<path d="M12 18l-2 -4l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5l-2.901 8.034"></path>
										<path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z"></path>
										<path d="M19 18v.01"></path>
									</svg>
									Daftar Pos Pemantauan</a>
							</li>
							<li class="list-group-item py-0 px-0" role="presentation">
								<a href="#tabs-activity-7" class="nav-link  w-100 d-flex justify-content-center py-3" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1"><!-- Download SVG icon from http://tabler-icons.io/i/activity -->
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cloud-rain me-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
										<path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7"></path>
										<path d="M11 13v2m0 3v2m4 -5v2m0 3v2"></path>
									</svg>
									Indikator Curah Hujan</a>
							</li>
							<li class="list-group-item py-0 px-0" role="presentation">
								<a href="#tabs-activity-3" class="nav-link  w-100 d-flex justify-content-center py-3" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1"><!-- Download SVG icon from http://tabler-icons.io/i/activity -->
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-address-book me-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
										<path d="M20 6v12a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2z"></path>
										<path d="M10 16h6"></path>
										<path d="M13 11m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
										<path d="M4 8h3"></path>
										<path d="M4 12h3"></path>
										<path d="M4 16h3"></path>
									</svg>
									Kontak DPUPESMDM DIY</a>
							</li>
						</ul>
					</div>

				</div>
			</div>
			<div class="col-md-10">
				<div class="card mt-3 mt-md-0">
					<div class="card-body">
						<div class="tab-content">
							<div class="tab-pane  active show" id="tabs-home-7" role="tabpanel">
								<h3>Pengenalan Logger Pos Pemantauan</h3>
								<div>Beberapa jenis Pos Pemantaun yang terdapat dalam Go Hidro</div>
								<div class="row mt-3">
									<div class="col-md-4">
										<div class="card">
											<div class="card-header px-3 py-2 d-flex justify-content-center">
												<h3 class="card-title"><span class="fw-bold">Stasiun Cuaca AWR </span>(Automatic Weather Recorder)</h3>
											</div>
											<div class="card-body px-3">
												Merupakan alat untuk melakukan pengukuran cuaca dengan beberapa parameter utama seperti : 
												<ul class="mt-1 px-3">
													<li class="mb-1">
														Kecepatan Angin <br/>
														Satuan yang mengukur kecepatan aliran udara dari tekanan tinggi ke tekanan rendah dan menggunakan satuan km/jam
													</li>
													<li class="mb-1">
														Arah Angin <br/>
														Satuan yang mengukur derajat arah angin akan berhembus
													</li>
													<li class="mb-1">
														Temperatur Udara <br/>
														Keadaan udara pada waktu dan tempat tertentu dan diukur menggunakan satuan derajat Celcius
													</li>
													<li class="mb-1">
														Tekanan Udara <br/>
														Tekanan yang ada pada suatu lokasi yang disebabkan oleh berat dari udara yang ditarik oleh gravitasi ke permukaan bumi dan diukur menggunakan satuan Hecto Pascal (hPA)
													</li>
													<li class="mb-1">
														Kelembaban Udara <br/>
														Banyak sedikitnya konsentrasi kandungan uap air di dalam udara dan diukur menggunakan satuan Persen (%)
													</li>
												</ul>

											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="card mt-3 mt-md-0">
											<div class="card-header px-3 py-2 d-flex justify-content-center">
												<h3 class="card-title"><span class="fw-bold">Curah Hujan ARR </span>(Automatic Rain Recorder)</h3>
											</div>
											<div class="card-body px-3">
												Merupakan alat untuk melakukan pengukuran Curah Hujan dengan beberapa parameter utama seperti : 
												<ul class="mt-1 px-3">
													<li class="mb-1">
														Curah Hujan (mm) <br/>
														Ketinggian air hujan yang terkumpul dalam penakar hujan pada tempat yang datar, tidak menyerap, tidak meresap dan tidak mengalir. Unsur hujan 1 (satu) milimeter artinya dalam luasan satu meter persegi pada tempat yang datar tertampung air hujan setinggi satu milimeter atau tertampung air hujan sebanyak satu liter.
													</li>
												</ul>
												Untuk standar pengukuran curah hujan dapat dilihat di menu indikator curah hujan
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="card mt-3 mt-md-0 h-100">
											<div class="card-header px-3 py-2 d-flex justify-content-center">
												<h3 class="card-title"><span class="fw-bold">Duga Air AWLR </span>(Automatic Water Level Recorder)</h3>
											</div>
											<div class="card-body px-3">
												Merupakan alat untuk mengukur dan memantau kondisi dan tinggi muka air pada sungai atau waduk dengan beberapa parameter utama seperti : 
												<ul class="mt-1 px-3">
													<li class="mb-1">
														Tinggi Muka Air <br/>
														elevasi permukaan air (water level) pada suatu penampang melintang sungai terhadap suatu titik tetap yang elevasinya telah diketahui, diukur menggunakan satuan Meter (m)
													</li>
													<li class="mb-1">
														Debit <br/>
														Besaran atau volume air yang mengalir di suatu wilayah tertentu, diukur menggunakan satuan liter/detik
													</li>
												</ul>

											</div>
										</div>
									</div>
								</div>
								<h3 class="mt-5 mt-md-3">Keterangan Dashboard</h3>
								<div class="row mt-2">
									<div class="col-md-6">
										<div class="p-4 border border-1">
											<img src="<?= base_url() ?>image/indikator_dashboard.png" class="img-fluid"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="card">
											<div class="card-header">
												<h3 class="card-title">Arti Keterangan</h3>
											</div>
											<div class="card-body">
												<ul class="px-3">
													<li class="mb-1">Nama Pos Pemantauan <br />
														Merupakan nama dari pos tempat alat / sensor berada
													</li>
													<li class="mb-1">Nama Parameter  <br />
														Merupakan nama dari parameter yang terdapat pada pembacaan sensor
													</li>
													<li class="mb-1">Koneksi dan Data Terakhir  <br />
														Koneksi ke sensor atau alat ditandai dengan warna, apabila berwarna hijau menandakan alat terhubung dengan server dan apabila berwarna hitam menandakan alat terputus dengan server. Sedangkan waktu menunjukkan kapan terakhir kali alat mengirim data ke server.
													</li>
													<li class="mb-1">Nilai  <br />
														Merupakan hasil pembacaan dari sensor
													</li>
													<li class="mb-1">Satuan  <br />
														Merupakan satuan yang terdapat pada tiap tiap parameter
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane " id="tabs-home-2" role="tabpanel">
								<h3>Daftar Pos Telemetri</h3>
								<div class="card">
									<div class="card-body">
										<table class="table table-bordered">
											<thead>
												<tr>
													<th width="20px">No</th>
													
													<th>Nama Pos</th>
													<th>Alamat</th>
													<th>Lokasi</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($kab as $key=>$k): ?>
												<tr>
													<td class="align-middle" colspan="5"><h3 class="mb-0"><?= $key ?></h3></td>
												</tr>
												<?php 
	$i = 1;
														foreach($k as $s): ?>
												<tr>
													<td class="align-middle text-center"><?= $i++ ?></td>
													
													<td class="align-middle"><?= $s['nama_pos'] ?></td>
													<td class="align-middle"><?= $s['alamat'] ?></td>
													
													<td><a class="btn btn-primary" target="_blank" href="<?= $s['gmaps'] ?>"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-location me-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
														<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
														<path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
														</svg>Google Map</a></td>
												</tr>
												<?php endforeach; ?>
												<?php endforeach; ?>

											</tbody>
										</table>

									</div>

								</div>



							</div>
							<div class="tab-pane " id="tabs-activity-7" role="tabpanel">
								<h3>Indikator Curah Hujan</h3>
								<div>Satu milimeter hujan berarti air hujan yang turun di wilayah seluas satu meter persegi akan memiliki ketinggian satu milimeter jika air hujan tidak meresap, mengalir, atau menguap. Ambang batas nilai yang digunakan untuk menentukan intensitas hujan sebagai berikut:</div>
								<div class="row mt-3">
									<div class="col-md-6">
										<div class="card">
											<div class="card-header d-flex justify-content-center py-2">
												<h3 class="card-title">Indikator Curah Hujan Per Jam</h3>
											</div>
											<div class="card-body">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th>Curah Hujan</th>
															<th>Status</th>
															<th>Warna</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td class="align-middle">0 mm/jam</td>
															<td class="align-middle">Berawan / Tidak Hujan</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-hijau.png" /></td>
														</tr>
														<tr>
															<td class="align-middle">0.1 – 5 mm/jam</td>
															<td class="align-middle">Hujan Ringan</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-biru.png" /></td>
														</tr>
														<tr>
															<td class="align-middle">5 – 10 mm/jam</td>
															<td class="align-middle">Hujan Sedang</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-kuning.png" /></td>
														</tr>
														<tr>
															<td class="align-middle">10 – 20 mm/jam</td>
															<td class="align-middle">Hujan Lebat</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-oranye.png" /></td>
														</tr>
														<tr>
															<td class="align-middle">> 20 mm/jam</td>
															<td class="align-middle">Hujan Sangat Lebat</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-merah.png" /></td>
														</tr>
													</tbody>
												</table>

											</div>

										</div>
									</div>
									<div class="col-md-6">
										<div class="card mt-3 mt-md-0">
											<div class="card-header d-flex justify-content-center py-2">
												<h3 class="card-title">Indikator Curah Hujan Per Hari</h3>
											</div>
											<div class="card-body">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th>Curah Hujan</th>
															<th>Status</th>
															<th>Warna</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td class="align-middle">0 mm/hari</td>
															<td class="align-middle">Berawan / Tidak Hujan</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-hijau.png" /></td>
														</tr>
														<tr>
															<td class="align-middle">0.5 – 20 mm/hari</td>
															<td class="align-middle">Hujan Ringan</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-biru.png" /></td>
														</tr>
														<tr>
															<td class="align-middle">20 – 50 mm/hari</td>
															<td class="align-middle">Hujan Sedang</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-kuning.png" /></td>
														</tr>
														<tr>
															<td class="align-middle">50 – 100 mm/hari</td>
															<td class="align-middle">Hujan Lebat</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-oranye.png" /></td>
														</tr>
														<tr>
															<td class="align-middle">> 100 mm/hari</td>
															<td class="align-middle">Hujan Sangat Lebat</td>
															<td><img width="35" src="https://pusdajatim.monitoring4system.com/pin_marker/kotak-merah.png" /></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="tabs-activity-3" role="tabpanel">
								<h3 class="mb-2">Dinas Pekerjaan Umum Perumahan dan Energi Sumber Daya Mineral</h3>
								<h4 class="fw-normal">Untuk informasi lebih lanjut bisa menghubungi Bidang Sumber Daya Air dan Drainase </h4>

								<div class="row mt-3">

									<div class="col-md-6">
										<div class="card">
											<div class="card-header d-flex justify-content-center py-2">
												<h3 class="card-title">Peta Lokasi</h3>
											</div>
											<div class="card-body">
												<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.0323208508707!2d110.3603801143567!3d-7.786397979422886!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5824ccc169f7%3A0x43b18867d78d6c59!2sDinas%20Pekerjaan%20Umum%2C%20Perumahan%20Dan%20Energi%20Sumber%20Daya%20Mineral!5e0!3m2!1sid!2sid!4v1576459326022!5m2!1sid!2sid" width="100%" height="390" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
											</div>
										</div>

									</div>
									<div class="col-md-6">
										<div class="card mt-3 mt-md-0">
											<div class="card-header d-flex justify-content-center py-2">
												<h3 class="card-title">Kontak Kami</h3>
											</div>
											<div class="card-body">
												<div class="row">
													<div class="col-4 col-md-3 d-flex">
														<span><svg xmlns="http://www.w3.org/2000/svg" class="me-2 icon icon-tabler icon-tabler-building-bank" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
															<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
															<path d="M3 21l18 0"></path>
															<path d="M3 10l18 0"></path>
															<path d="M5 6l7 -3l7 3"></path>
															<path d="M4 10l0 11"></path>
															<path d="M20 10l0 11"></path>
															<path d="M8 14l0 3"></path>
															<path d="M12 14l0 3"></path>
															<path d="M16 14l0 3"></path>
															</svg></span><h4 class="mb-0">Alamat</h4>
													</div>
													<div class="col-8 col-md-9"><h4 class="mb-0 fw-normal">: Jl.Bumijo No.5, Yogyakarta</h4></div>	
												</div>
												<div class="row mt-3">
													<div class="col-4 col-md-3 d-flex">
														<span><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail me-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
															<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
															<path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
															<path d="M3 7l9 6l9 -6"></path>
															</svg></span><h4 class="mb-0">Email</h4>
													</div>
													<div class="col-8 col-md-9"><h4 class="mb-0 fw-normal">: sdadrainasediy@gmail.com</h4></div>	
												</div>
												<div class="row mt-3">
													<div class="col-4 col-md-3 d-flex">
														<span><svg xmlns="http://www.w3.org/2000/svg" class="me-2 icon icon-tabler icon-tabler-phone" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
															<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
															<path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
															</svg></span><h4 class="mb-0">No Telefon</h4>
													</div>
													<div class="col-8 col-md-9"><h4 class="mb-0 fw-normal">: +62 274 589091 / +62 274 589074</h4></div>	
												</div>

											</div>
										</div>

									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>