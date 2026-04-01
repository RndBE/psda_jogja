
<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard - DPUPESDM DIY</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
		<link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/6/65/Yogyakarta_Logo.svg">
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-flags.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-payments.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-vendors.min.css" rel="stylesheet"/>
		<link href="https://stesy.beacontelemetry.com/assets/code/demo.min.css" rel="stylesheet"/>
		<script src="https://stesy.beacontelemetry.com/assets/code/tom-select.complete.min.js" defer></script>
		<script src="https://stesy.beacontelemetry.com/assets/code/tabler.min.js" defer></script>
		<script src="https://stesy.beacontelemetry.com/assets/code/demo.min.js" defer></script>
		<script src="<?php echo base_url();?>code/highcharts.js"></script>
		<script src="<?php echo base_url();?>code/highcharts-more.js"></script>
		<script src="<?php echo base_url();?>code/modules/series-label.js"></script>
		<script src="<?php echo base_url();?>code/modules/exporting.js"></script>
		<script src="<?php echo base_url();?>code/modules/export-data.js"></script>
		<script src="<?php echo base_url();?>code/js/themes/grid.js"></script>
		<script
				src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA0za7gSm6K-8eFKK-np3jhyyW5IMRVSb8"
				async
				defer
				></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js" integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<style>
			.accordion {
				border-radius:5px;
				overflow:hidden;
				border: 1px solid white !important;
			}

			.accordion-item {
				border: 1px solid white !important;
			}
			.gm-style-iw {
				width: 350px; 
				max-height: 150px;
			}
			.gm-style-iw-chr{
				position:absolute;
				right:0px
			}
			*::-webkit-scrollbar {
				display: none; /* Chrome, Safari, and Opera */
			}
			#map {
				height: 100%;
				width: 100%;
			}
			html, body {
				height: 100%;
				margin: 0;
				padding: 0;
			}

			.legend-box {
				display: inline-block;
				width: 16px;
				height: 16px;
				margin-right: 6px;
				border-radius: 3px;
			}
			#tes{
				background:linear-gradient(to right,#2f89b9,transparent, #2f89b9);
				justify-content:space-between;
				display:flex;
				align-items:center;
				padding:0px 20px;
				width:calc(100% - 40px);
				height:75px;
				box-sizing: border-box;
				position:absolute;

				border-radius:5px;
				margin-top:20px
			}
			.layer-control {
				background: #fff; padding: 0px 10px; border-radius: 10px;
				display: grid; gap: 6px; user-select: none;
			}
			.layer-control h3 {font-size: 14px;}
			.layer-control label { display: flex; align-items: center; gap: 8px; cursor: pointer}
			#left_map{
				margin-top:110px;
				max-height:80vh;
				overflow-y:scroll;
				scrollbar-width: none;
				overflow-x: hidden;

				position:absolute;
				margin-left:20px;
				border-radius:5px;
				background:linear-gradient(to right, #2f89b9,transparent);
			}
			#filter_small{
				margin-top:-20px;
				position:absolute;
			}

			#right_map{
				margin-top:10px;
				max-height:75vh;
				position:absolute;
				scrollbar-width: none;
				margin-right:20px;
				overflow-y:scroll;
				border-radius:5px;
				background:linear-gradient(to right,transparent, #2f89b9);
				display: flex;
				flex-direction: column;
			}

			#filterlayer {
				border-radius:5px;
				margin-top:110px;
				margin-right:20px;
				min-height:50px;
				background:linear-gradient(to right,transparent, #2f89b9);
			}

			#pilih_kat{
				font-size:14px;
				font-weight:bold;
				color:white;
				background-color:#30348180;
				border:2px solid #FFD61580;
				border-radius:5px;
				padding:10px 10px;
			}
			@keyframes pulseBorder {
				0% {
					box-shadow: 0 0 0px rgba(255, 214, 21, 0.8);
					border-color: rgba(255, 214, 21, 0.8);
				}
				50% {
					box-shadow: 0 0 10px rgba(255, 214, 21, 1);
					border-color: rgba(255, 214, 21, 1);
				}
				100% {
					box-shadow: 0 0 0px rgba(255, 214, 21, 0.8);
					border-color: rgba(255, 214, 21, 0.8);
				}
			}

			.border-pulse {
				animation: pulseBorder 2s cubic-bezier(0.4, 0, 0.2, 1); /* Pulse for 1 second */
			}
			#small_inside{
				border-radius:5px;
				background:linear-gradient(to right,#2f89b9,transparent, #2f89b9);
			}
			#logo_kiri {
				height:55px
			} 
			@media (max-width: 576px) {#logo_kiri {
				height:40px;
				}  
				#tes{
					height:60px;
					padding: 0 10px;
				}
			}

			@media (min-width: 768px) {}

			@media (min-width: 992px) {}

			@media (min-width: 1200px) { 
				#logo_kiri {
					height:55px
				}
			}

			@media (min-width: 1400px) { #logo_kiri {
				height:55px
				}  }
			#filters { font: 14px/1.4 system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
			.cat-row { display:flex; align-items:center; gap:8px; margin-bottom:6px; }
			.subs { margin-left:24px; }
			.cb-row { display:inline-flex; align-items:center; gap:6px; margin:0 10px 8px 0; }
			.count { opacity:.6; }
			.off { opacity:.55; filter:grayscale(1); }
		</style>
	</head>
	<body>
		<div id="map"></div>
		<div class="container-fluid">
			<div class="row justify-content-center">
				<div class="col-xl-3 col-xxl-2 pt-0 d-none d-xl-inline-block" id="left_map">
					<div class="ps-3 pe-2 d-flex justify-content-between align-items-center py-2" style="">
						<h3 class="text-white fw-bold mb-0">List Logger</h3>
						<button id="btn_hide" class="btn btn-outline-light btn-sm py-1 px-3 fw-bold" style="border:solid 2px white;border-radius:5px">Hide</button>
					</div>
					<div class="row gy-2 justify-content-center mt-0 mb-3" id="list_logger">
						<?php foreach($data_konten as $key=>$vl) { ?> 
						<?php if($vl['logger']) { ?>
						<div class="col-auto"><div class="py-2 text-white fw-bold text-center px-3" style="border:solid 2px white;font-size:14px;border-radius:5px">DAS <?= $vl['nama_das']?></div></div>

						<?php foreach($vl['logger'] as $k=> $v){ ?>

						<div class="col-12 px-3">
							<div class="card text-white" style="background:transparent;border:2px solid white;" id="sc_<?= $v['id_logger'] ?>">
								<div class="card-header px-3 py-2 d-flex justify-content-between " style="border-bottom:2px solid white;">
									<div class="d-flex align-items-center"><div class="me-2" style="width:10px;height:10px;border-radius:50%;background-color:<?= $v['color'] ?>;border:1px solid white"></div><p class="mb-0 fw-bold"><?= $v['status_logger'] ?></p></div>
									<p class="mb-0"><?= $v['waktu']?></p>
								</div>
								<div class="card-body px-3 py-2">
									<div class="d-flex justify-content-between align-items-center">
										<p class="fw-bold mb-0 h4"><?= $v['nama_lokasi'] ?></p>
										<div class="badge badge-outline text-white h-100 h6 mb-0 fw-bold">ID : <?= $v['id_logger'] ?></div>
									</div>
									<div class="row justify-content-center mb-2 gy-2 mt-2 ">
										<?php foreach($v['param'] as $y=>$s){ ?> 
										<?php if($s['alias_sensor'] != 'Baterai_Logger' and $s['alias_sensor'] != 'Kelembaban_Logger' and $s['alias_sensor'] != 'Temperatur_Logger'){ ?>
										<div class="col-6 text-center">
											<h6 class="mb-0 fw-bold h3"><?= $s['nilai']?> <?= $s['satuan'] ?></h6>
											<p class="mb-0 h5 fw-normal">
												<a href="<?= $s['link'] ?>"><?= str_replace('_',' ',$s['alias_sensor']) ?></a>
											</p>
										</div>
										<?php } ?>
										<?php } ?>
									</div>

									<?php 
																$param_bt = false;
																foreach($v['param'] as $y=>$s){  
																	if($s['parameter_utama'] == '0'){ 
																		$param_bt = true;
																	}  
																} 
																$found = false;

																foreach ($v['param'] as $item) {
																	if (isset($item['alias_sensor']) && $item['alias_sensor'] === 'Kelembaban_Logger') {
																		$found = true;
																		break;
																	}
																}
									?>
									<?php if($param_bt) { ?>
									<div class="rounded py-0 mt-3" style="border:2px solid white">
										<div class="row gx-0 justify-content-center">
											<?php foreach($v['param'] as $y=>$s){ ?> 
											<?php if($s['alias_sensor'] == 'Baterai_Logger' or $s['alias_sensor'] == 'Kelembaban_Logger' or $s['alias_sensor'] == 'Temperatur_Logger'){ ?>
											<div class="<?= $found ? 'col-4':'col-6' ?>">
												<div class=" d-flex justify-content-center align-items-center w-100 py-1" style="<?= ($s['alias_sensor'] != 'Temperatur_Logger') ? 'border-right:solid 2px white':'' ?>">
													<img  src="https://api.beacontelemetry.com/image/sensor/<?= $s['alias_sensor']?>.svg" style="filter: brightness(0) invert(1);stroke:white;height:16px;color:white" class="text-white me-2 mb-0"/>
													<span class="fw-bold mb-0"><?= $s['nilai'] ?> <?= $s['satuan'] == 'Percent' ? '%':$s['satuan'] ?></span>
												</div>
											</div>
											<?php } ?>

											<?php } ?>

										</div>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } } ?>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class=" px-3 py-2 d-inline-block d-lg-none " id="filter_small">
				<div class="d-flex align-items-center py-2 px-2 " id="small_inside">
					<button class="btn btn-outline-light w-100 fw-bold py-1 pe-1" data-bs-toggle="modal" data-bs-target="#setting_peta" style="border:solid 2px white;border-radius:5px"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg></button>
					<button class="d-lg-none btn btn-outline-light w-100 fw-bold py-1 mx-3 pe-1" id="sm_list" style="border:solid 2px white;border-radius:5px"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-layout-list"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 14m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /></svg></button>
					<button class="d-lg-none btn btn-outline-light w-100 fw-bold py-1  pe-1" id="sm_das" style="border:solid 2px white;border-radius:5px"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-squares"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 10a2 2 0 0 1 2 -2h9a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-9a2 2 0 0 1 -2 -2z" /><path d="M16 8v-3a2 2 0 0 0 -2 -2h-9a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h3" /></svg></button>
				</div>
			</div>
			<div class="col-xl-3 col-xxl-2 d-none d-lg-flex flex-column align-items-center px-2 py-2" id="filterlayer">
				<button class="btn btn-outline-light w-100 fw-bold py-1" data-bs-toggle="modal" data-bs-target="#setting_peta" style="border:solid 2px white;border-radius:5px"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="me-2 icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>Pengaturan Peta</button>
				<button class="d-lg-none btn btn-outline-light w-100 fw-bold py-1 mt-2" data-bs-toggle="modal" data-bs-target="#setting_peta" style="border:solid 2px white;border-radius:5px"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="me-2 icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>List DAS</button>
				<button class="d-lg-none btn btn-outline-light w-100 fw-bold py-1 mt-2" data-bs-toggle="modal" data-bs-target="#setting_peta" style="border:solid 2px white;border-radius:5px"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="me-2 icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>List Logger</button>
			</div>
			<div class="col-xl-3 col-xxl-2 d-xl-inline-block d-none" id="right_map">

				<div class="px-3 pt-2 pb-2 d-flex justify-content-between align-items-center">
					<h3 class="mb-0 fw-bold text-white">Daftar DAS</h3><button id="btn_hide2" class="btn btn-outline-light btn-sm py-1 px-3 fw-bold" style="border:solid 2px white;border-radius:5px">Hide</button>
				</div>
				<div class="px-2 pb-2 text-white" style="overflow-y: scroll;scrollbar-width: none;" id="list_das">
					<div class="accordion mt-2" id="accordion-default">
						<?php foreach($data_konten as $key=>$vl) { ?>
						<div class="accordion-item" >
							<div class="accordion-header">
								<button class="accordion-button collapsed text-white py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $key ?>" aria-expanded="false">
									<?= $vl['nama_das'] ?>
								</button>
							</div>
							<style>
								li {
									margin-bottom: 5px;
								}

								/* Optional: remove margin from last li */
								li:last-child {
									margin-bottom: 0;
								}
							</style>
							<div id="collapse-<?= $key ?>" class="accordion-collapse collapse text-white" data-bs-parent="#accordion-default" style="">
								<div class="accordion-body pt-2 pb-0">
									<?php if ($vl['logger']) { ?>
									<ul class="px-3">
										<?php foreach($vl['logger'] as $k => $v) { ?>
										<li><h4 class="mb-0"><?= $v['nama_lokasi'] ?></h4></li>
										<?php } ?>
									</ul>
									<?php }else{?>
									<h5 class="mb-2"> Tidak Ada Logger</h5>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>

					</div>
				</div>

			</div>
		</div>
		<div style="" id="tes">
			<img src="<?= base_url() ?>image/diy_white3.svg" id="logo_kiri" />
			<button class="btn bg-transparent px-2 text-white  d-lg-none " data-bs-toggle="modal" data-bs-target="#fullModal">
				<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-menu-4 px-0 mx-0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 6h10" /><path d="M4 12h16" /><path d="M7 12h13" /><path d="M7 18h10" /></svg>
			</button>

			<div class="align-items-center d-none d-xl-flex">
				<div class="d-flex flex-column align-items-center">
					<a class="me-3 d-flex align-items-center py-1" style="background:transparent;border:none;color:white;font-weight:bold;font-size:16px" href="<?= base_url() ?>beranda">
						<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 16 16" class="me-2"><path fill="currentColor" d="M6.906.664a1.749 1.749 0 0 1 2.187 0l5.25 4.2c.415.332.657.835.657 1.367v7.019A1.75 1.75 0 0 1 13.25 15h-3.5a.75.75 0 0 1-.75-.75V9H7v5.25a.75.75 0 0 1-.75.75h-3.5A1.75 1.75 0 0 1 1 13.25V6.23c0-.531.242-1.034.657-1.366l5.25-4.2Zm1.25 1.171a.25.25 0 0 0-.312 0l-5.25 4.2a.25.25 0 0 0-.094.196v7.019c0 .138.112.25.25.25H5.5V8.25a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 .75.75v5.25h2.75a.25.25 0 0 0 .25-.25V6.23a.25.25 0 0 0-.094-.195Z"/></svg>
						Dashboard
					</a>
				</div>
				<div class="d-flex flex-column align-items-center">
					<button class="me-3 d-flex align-items-center py-1" style="background:transparent;border:none;color:white;font-weight:bold;font-size:16px">
						<span class="nav-link-icon d-md-none d-lg-inline-block text-white">
							<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-map"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></svg>
						</span>
						Peta Lokasi
					</button>
					<div style="border-bottom:2px solid white;width:40px"></div>
				</div>
				<div class="d-flex flex-column align-items-center">
					<a class="me-3 d-flex align-items-center py-1" style="background:transparent;border:none;color:white;font-weight:bold;font-size:16px" href="<?= base_url() ?>komparasi">
						<span class="nav-link-icon d-md-none d-lg-inline-block text-white">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<rect x="3" y="12" width="6" height="8" rx="1"></rect>
								<rect x="9" y="8" width="6" height="12" rx="1"></rect>
								<rect x="15" y="4" width="6" height="16" rx="1"></rect>
								<line x1="4" y1="20" x2="18" y2="20"></line>
							</svg>
						</span>
						Komparasi
					</a>
				</div>
				<div class="d-flex flex-column align-items-center">
					<a class="me-3 d-flex align-items-center py-1" style="background:transparent;border:none;color:white;font-weight:bold;font-size:16px" href="<?= base_url() ?>monitoring">
						<span class="nav-link-icon d-md-none d-lg-inline-block text-white">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-text" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
								<path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
								<path d="M9 9l1 0"></path>
								<path d="M9 13l6 0"></path>
								<path d="M9 17l6 0"></path>
							</svg>
						</span>
						Monitoring
					</a>
				</div>
				<div class="d-flex flex-column align-items-center">
					<a class="me-3 d-flex align-items-center py-1" style="background:transparent;border:none;color:white;font-weight:bold;font-size:16px" href="<?= base_url() ?>informasi">
						<span class="nav-link-icon d-md-none d-lg-inline-block text-white">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-square-rounded" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M12 9h.01"></path>
								<path d="M11 12h1v4h1"></path>
								<path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>
							</svg>
						</span>
						Informasi
					</a>
				</div>
				<div class="dropdown px-0 me-4">
					<button type="button" class="btn dropdown-toggle text-white bg-transparent border-0 fw-bold px-0" data-bs-toggle="dropdown">
						<!-- SVG icon from http://tabler-icons.io/i/calendar -->
						<svg xmlns="http://www.w3.org/2000/svg" width="22" class="me-2" height="22" viewBox="0 0 26 26"><g fill="none"><path d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z"/><path fill="currentColor" d="M20 14.5a1.5 1.5 0 0 1 1.5 1.5v4a2.5 2.5 0 0 1-2.5 2.5H5A2.5 2.5 0 0 1 2.5 20v-4a1.5 1.5 0 0 1 3 0v3.5h13V16a1.5 1.5 0 0 1 1.5-1.5Zm-8-13A1.5 1.5 0 0 1 13.5 3v9.036l1.682-1.682a1.5 1.5 0 0 1 2.121 2.12l-4.066 4.067a1.75 1.75 0 0 1-2.474 0l-4.066-4.066a1.5 1.5 0 0 1 2.121-2.121l1.682 1.682V3A1.5 1.5 0 0 1 12 1.5Z"/></g></svg>
						<h3 class="mb-0 fw-bold">Unduh</h3>
					</button>
					<div class="dropdown-menu fw-bold border-white">
						<a class="dropdown-item" href="<?= base_url() ?>datapos">
							Unduh Data
						</a>
						<a class="dropdown-item" href="<?= base_url() ?>unduh/go-hidro_1.3.3.apk" target="_blank">
							Android App
						</a>
						<a class="dropdown-item" href="https://apps.apple.com/id/app/go-hidro/id6456266862" target="_blank">
							iOS App
						</a>
					</div>
				</div>
				<a class="me-3 d-flex align-items-center fw-bold" style="background:transparent;border:none;color:white;font-size:16px" href="<?= base_url() ?>login/logout">
					<svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="22" height="22" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z"/><path fill="currentColor" d="M12 2.5a1.5 1.5 0 0 1 0 3H7a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h4.5a1.5 1.5 0 0 1 0 3H7A3.5 3.5 0 0 1 3.5 18V6A3.5 3.5 0 0 1 7 2.5Zm6.06 5.61l2.829 2.83a1.5 1.5 0 0 1 0 2.12l-2.828 2.83a1.5 1.5 0 1 1-2.122-2.122l.268-.268H12a1.5 1.5 0 0 1 0-3h4.207l-.268-.268a1.5 1.5 0 1 1 2.122-2.121Z"/></g></svg>
					Keluar
				</a>
			</div>
		</div>
		<div class="h-50 w-100 d-lg-none" id="bottom_small" style="display:none;border-radius:20px 20px 0px 0px;overflow-y:scroll;background:linear-gradient(to right,#2f89b9,transparent, #2f89b9);">
			<div id="lg_small">
				<div class="px-3 pt-2 pb-2 d-flex justify-content-between align-items-center text-white">
					<h3 class="mb-0 fw-bold">Daftar DAS</h3>
					<button class="btn_hidesmall btn btn-outline-light btn-sm py-1 px-3 fw-bold" style="border:solid 2px white;border-radius:5px">Tutup</button>
				</div>
				<div class="row gy-2 justify-content-center mt-0 mb-3" id="list_logger">
					<?php foreach($data_konten as $key=>$vl) { ?> 
					<?php if($vl['logger']) { ?>
					<div class="col-auto"><div class="py-2 fw-bold text-center text-white px-3" style="border:solid 2px white;font-size:14px;border-radius:5px">DAS <?= $vl['nama_das']?></div></div>

					<?php foreach($vl['logger'] as $k=> $v){ ?>

					<div class="col-12 px-3">
						<div class="card text-white" style="background:transparent;border:2px solid white;" >
							<div class="card-header px-3 py-2 d-flex justify-content-between " style="border-bottom:2px solid white;">
								<div class="d-flex align-items-center"><div class="me-2" style="width:10px;height:10px;border-radius:50%;background-color:<?= $v['color'] ?>;border:1px solid white"></div><p class="mb-0 fw-bold"><?= $v['status_logger'] ?></p></div>
								<p class="mb-0"><?= $v['waktu']?></p>
							</div>
							<div class="card-body px-3 py-2 text-white">
								<div class="d-flex justify-content-between align-items-center">
									<p class="fw-bold mb-0 h4"><?= $v['nama_lokasi'] ?></p>
									<div class="badge badge-outline h-100 h6 mb-0 fw-bold">ID : <?= $v['id_logger'] ?></div>
								</div>
								<div class="row justify-content-center mb-2 gy-2 mt-2 ">
									<?php foreach($v['param'] as $y=>$s){ ?> 
									<?php if($s['alias_sensor'] != 'Baterai_Logger' and $s['alias_sensor'] != 'Kelembaban_Logger' and $s['alias_sensor'] != 'Temperatur_Logger'){ ?>
									<div class="col-6 text-center">
										<h6 class="mb-0 fw-bold h3"><?= $s['nilai']?> <?= $s['satuan'] ?></h6>
										<p class="mb-0 h5 fw-normal">
											<a href="<?= $s['link'] ?>"><?= str_replace('_',' ',$s['alias_sensor']) ?></a>
										</p>
									</div>
									<?php } ?>
									<?php } ?>
								</div>

								<?php 
															$param_bt = false;
															foreach($v['param'] as $y=>$s){  
																if($s['parameter_utama'] == '0'){ 
																	$param_bt = true;
																}  
															} 
															$found = false;

															foreach ($v['param'] as $item) {
																if (isset($item['alias_sensor']) && $item['alias_sensor'] === 'Kelembaban_Logger') {
																	$found = true;
																	break;
																}
															}
								?>
								<?php if($param_bt) { ?>
								<div class="rounded py-0 mt-3" style="border:2px solid white">
									<div class="row gx-0 justify-content-center">
										<?php foreach($v['param'] as $y=>$s){ ?> 
										<?php if($s['alias_sensor'] == 'Baterai_Logger' or $s['alias_sensor'] == 'Kelembaban_Logger' or $s['alias_sensor'] == 'Temperatur_Logger'){ ?>
										<div class="<?= $found ? 'col-4':'col-6' ?>">
											<div class=" d-flex justify-content-center align-items-center w-100 py-1" style="<?= ($s['alias_sensor'] != 'Temperatur_Logger') ? 'border-right:solid 2px white':'' ?>">
												<img  src="https://api.beacontelemetry.com/image/sensor/<?= $s['alias_sensor']?>.svg" style="filter: brightness(0) invert(1);stroke:black;height:16px;color:black" class="text-black me-2 mb-0"/>
												<span class="fw-bold mb-0"><?= $s['nilai'] ?> <?= $s['satuan'] == 'Percent' ? '%':$s['satuan'] ?></span>
											</div>
										</div>
										<?php } ?>

										<?php } ?>

									</div>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } } ?>
					<?php } ?>
				</div>
			</div>
			<div id="das_small">
				<div class="px-3 pt-2 pb-2 d-flex justify-content-between align-items-center">
					<h3 class="mb-0 fw-bold text-white">Daftar DAS</h3>
					<button class="btn_hidesmall btn btn-outline-light btn-sm py-1 px-3 fw-bold" style="border:solid 2px white;border-radius:5px">Tutup</button>
				</div>
				<div class="px-2 pb-2 text-white" style="overflow-y: scroll;scrollbar-width: none;" id="list_das">
					<div class="accordion mt-2" id="accordion-default">
						<?php foreach($data_konten as $key=>$vl) { ?>
						<div class="accordion-item" >
							<div class="accordion-header">
								<button class="accordion-button collapsed text-white py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $key ?>" aria-expanded="false">
									<?= $vl['nama_das'] ?>
								</button>
							</div>
							<style>
								li {
									margin-bottom: 5px;
								}

								/* Optional: remove margin from last li */
								li:last-child {
									margin-bottom: 0;
								}
							</style>
							<div id="collapse-<?= $key ?>" class="accordion-collapse collapse text-white" data-bs-parent="#accordion-default" style="">
								<div class="accordion-body pt-2 pb-0">
									<?php if ($vl['logger']) { ?>
									<ul class="px-3">
										<?php foreach($vl['logger'] as $k => $v) { ?>
										<li><h4 class="mb-0"><?= $v['nama_lokasi'] ?></h4></li>
										<?php } ?>
									</ul>
									<?php }else{?>
									<h5 class="mb-2"> Tidak Ada Logger</h5>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>

					</div>
				</div>
			</div>

		</div>
		<div class="modal fade" id="setting_peta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header py-2">
						<h5 class="modal-title">Pengaturan Peta</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body px-3 py-2">
						<div class="row gy-2">
							<div class="col-12 col-lg">
								<div class="card px-0">
									<div class="card-header py-1 fw-bold bg-light">
										Filter Peta
									</div>
									<div class="card-body pb-0 pt-0 px-2">
										<div id="filters" style="padding:8px; max-width:420px;">
											<label style="font-weight:600; display:block; margin-bottom:6px;">
												<input type="checkbox" id="filter-all" checked>
												Select All
											</label>
											<div id="filters-body"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12 col-lg-auto">
								<div class="card px-0">
									<div class="card-header py-1 fw-bold bg-light">
										Layer Peta
									</div>
									<div class="card-body py-2 px-2">
										<div class="layer-control">
											<label><input type="checkbox" id="layer-das">Daerah Aliran Sungai</label>
											<label><input type="checkbox" id="layer-sungai">Sungai</label>
										</div>
									</div>
								</div>
								<div class="card px-0 mt-2">
									<div class="card-header py-1 fw-bold bg-light">
										Jenis Peta
									</div>
									<div class="card-body py-2 px-2">
										<div class="layer-control">
											<label><input type="radio" name="mapType" value="hybrid" checked>Hybrid</label>
											<label><input type="radio" name="mapType" value="roadmap" >Normal</label>
											<label><input type="radio" name="mapType" value="satellite">Satellite</label>
											<label><input type="radio" name="mapType" value="terrain">Terrain</label>

										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="modal-footer py-1">
						<button type="button" class="btn btn-secondary btm-sm px-2" data-bs-dismiss="modal">Tutup</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade"
			 id="fullModal"
			 tabindex="-1"
			 aria-labelledby="fullModalLabel"
			 aria-hidden="true"
			 data-bs-backdrop="static" 
			 data-bs-keyboard="false">  
			<div class="modal-dialog modal-fullscreen">
				<div class="modal-content">

					<div class="modal-body" style="position:relative">
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="position:absolute;right:10px; top:10px"></button>

						<a class="d-flex align-items-center py-1 text-secondary" style="background:transparent;border:none;font-weight:bold;font-size:16px" href="<?= base_url() ?>beranda">
							<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 16 16" class="me-2"><path fill="currentColor" d="M6.906.664a1.749 1.749 0 0 1 2.187 0l5.25 4.2c.415.332.657.835.657 1.367v7.019A1.75 1.75 0 0 1 13.25 15h-3.5a.75.75 0 0 1-.75-.75V9H7v5.25a.75.75 0 0 1-.75.75h-3.5A1.75 1.75 0 0 1 1 13.25V6.23c0-.531.242-1.034.657-1.366l5.25-4.2Zm1.25 1.171a.25.25 0 0 0-.312 0l-5.25 4.2a.25.25 0 0 0-.094.196v7.019c0 .138.112.25.25.25H5.5V8.25a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 .75.75v5.25h2.75a.25.25 0 0 0 .25-.25V6.23a.25.25 0 0 0-.094-.195Z"/></svg>
							Dashboard
						</a>
						<button class="d-flex align-items-center py-1 px-0 mt-3" style="background:transparent;border:none;font-weight:bold;font-size:16px">
							<span class="nav-link-icon d-md-none d-lg-inline-block text-black">
								<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-map"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></svg>
							</span>
							Peta Lokasi
						</button>
						<div style="border-bottom:2px solid black;width:120px"></div>
						<a class="d-flex align-items-center py-1 mt-3 text-secondary" style="background:transparent;border:none;font-weight:bold;font-size:16px" href="<?= base_url() ?>komparasi">
							<span class="nav-link-icon d-md-none d-lg-inline-block">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<rect x="3" y="12" width="6" height="8" rx="1"></rect>
									<rect x="9" y="8" width="6" height="12" rx="1"></rect>
									<rect x="15" y="4" width="6" height="16" rx="1"></rect>
									<line x1="4" y1="20" x2="18" y2="20"></line>
								</svg>
							</span>
							Komparasi
						</a>
						<a class="d-flex align-items-center py-1 mt-3 text-secondary"  style="background:transparent;border:none;font-weight:bold;font-size:16px" href="<?= base_url() ?>monitoring">
							<span class="nav-link-icon d-md-none d-lg-inline-block ">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-text" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
									<path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
									<path d="M9 9l1 0"></path>
									<path d="M9 13l6 0"></path>
									<path d="M9 17l6 0"></path>
								</svg>
							</span>
							Monitoring
						</a>
						<a class="d-flex align-items-center py-1 mt-3 text-secondary" style="background:transparent;border:none;font-weight:bold;font-size:16px" href="<?= base_url() ?>informasi">
							<span class="nav-link-icon d-md-none d-lg-inline-block">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-square-rounded" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<path d="M12 9h.01"></path>
									<path d="M11 12h1v4h1"></path>
									<path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>
								</svg>
							</span>
							Informasi
						</a>

						<a class="d-flex align-items-center fw-bold mt-3 text-secondary" style="background:transparent;border:none;font-size:16px" href="<?= base_url() ?>login/logout">
							<svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="22" height="22" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z"/><path fill="currentColor" d="M12 2.5a1.5 1.5 0 0 1 0 3H7a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h4.5a1.5 1.5 0 0 1 0 3H7A3.5 3.5 0 0 1 3.5 18V6A3.5 3.5 0 0 1 7 2.5Zm6.06 5.61l2.829 2.83a1.5 1.5 0 0 1 0 2.12l-2.828 2.83a1.5 1.5 0 1 1-2.122-2.122l.268-.268H12a1.5 1.5 0 0 1 0-3h4.207l-.268-.268a1.5 1.5 0 1 1 2.122-2.121Z"/></g></svg>
							Keluar
						</a>
					</div>

				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
		<script>
			$(document).ready(function() {

				$('.btn_hidesmall').click(function() {
					$('#bottom_small').toggle();
				});

				$('#sm_list').click(function() {
					if ($('#bottom_small').is(':hidden')) {
						console.log('wdws');
						$('#bottom_small').toggle();
					}

					$('#lg_small').show();
					$('#das_small').hide();
				});

				$('#sm_das').click(function() {
					if ($('#bottom_small').is(':hidden')) {
						$('#bottom_small').toggle();
					}
					$('#das_small').show();
					$('#lg_small').hide();
				});
				$('#btn_hide').click(function() {
					$('#list_logger').toggle();
					let newText = $(this).text() === 'Show' ? 'Hide' : 'Show';
					$(this).text(newText);
				});

				$('#btn_hide2').click(function() {
					$('#list_das').toggle();
					let newText = $(this).text() === 'Show' ? 'Hide' : 'Show';
					$(this).text(newText);
				});
				const location_new = <?php echo json_encode($marker)?>;
				var awlr_plataran = location_new[41];
				var arr_plataran = location_new[25];

				const LAYERS = {
					das: {
						nama: "DAS",
						url: "https://dpupesdm.monitoring4system.com/image/newest_json.geojson",
						data: null,
						loaded: false
					},
					sungai: {
						nama: "Sungai",
						url: "https://dpupesdm.monitoring4system.com/image/sungai_psda.geojson",
						data: null,
						loaded: false
					}
				};
				let map;
				const CAT_ICONS = {
					arr:  "/pin_marker/arr.png",
					awlr: "/pin_marker/awlr.png",
					awr:  "/pin_marker/arr.png",
					_default: "/pin_marker/arr.png",
				};

				// sub-icon per kategori (fallback ke _default)
				const SUB_ICONS = {
					arr: {
						"tidak hujan": "/pin_marker/kotak-hijau.png",
						"hujan ringan": "/pin_marker/kotak-cyan.png",
						"hujan sedang": "/pin_marker/kotak-nila.png",
						"hujan lebat": "/pin_marker/kotak-kuning.png",
						"hujan sangat ringan": "/pin_marker/kotak-oranye.png",
						"hujan sangat lebat": "/pin_marker/kotak-merah.png",
						"perbaikan": "/pin_marker/kotak-coklat.png",
						"koneksi terputus": "/pin_marker/kotak-hitam.png",
						_default: "/pin_marker/kotak-hijau.png"
					},
					awlr: {
						"koneksi terhubung": "/pin_marker/kotak-hijau.png",
						"koneksi terputus": "/pin_marker/kotak-hitam.png",
						"perbaikan": "/pin_marker/kotak-coklat.png",
						_default: "/pin_marker/kotak-hijau.png"
					},
					awr: {
						"tidak hujan": "/pin_marker/kotak-hijau.png",
						"hujan ringan": "/pin_marker/kotak-cyan.png",
						"hujan sedang": "/pin_marker/kotak-nila.png",
						"hujan lebat": "/pin_marker/kotak-kuning.png",
						"hujan sangat ringan": "/pin_marker/kotak-oranye.png",
						"hujan sangat lebat": "/pin_marker/kotak-merah.png",
						"perbaikan": "/pin_marker/kotak-coklat.png",
						"koneksi terputus": "/pin_marker/kotak-hitam.png",
						_default: "/pin_marker/kotak-hijau.png"
					},
					_default: "/pin_marker/kotak-hijau.png"
				};

				// Master daftar sub agar selalu tampil lengkap
				const ALL_SUBS = {
					arr:  ["Tidak Hujan","Hujan Ringan","Hujan Sedang","Hujan Lebat","Hujan Sangat Ringan","Hujan Sangat Lebat","Perbaikan","Terputus"],
					awlr: ["Koneksi Terhubung","Koneksi Terputus"],
					awr:  ["Tidak Hujan","Hujan Ringan","Hujan Sedang","Hujan Lebat","Perbaikan","Koneksi Terputus"]
				};

				// =================== STATE & UTIL ===================
				const MARKERS = {}; // MARKERS[ncat][nsub] = []
				const CATS    = {}; // CATS[ncat] = Set(nsub)
				const LABEL   = { cats: {}, subs: {} }; // label display


				// =================== HELPERS ===================
				const norm = s => (s ?? "").toString().trim().toLowerCase();

				function setCatLabel(ncat, raw) {
					LABEL.cats[ncat] = LABEL.cats[ncat] || (raw?.toUpperCase() || ncat.toUpperCase());
				}
				function setSubLabel(ncat, nsub, raw) {
					if (!LABEL.subs[ncat]) LABEL.subs[ncat] = {};
					LABEL.subs[ncat][nsub] = LABEL.subs[ncat][nsub] || (raw || nsub);
				}

				function getCatIconUrl(ncat) {
					return CAT_ICONS[ncat] || CAT_ICONS._default;
				}
				function getSubIconUrl(ncat, disp) {
					const byCat = SUB_ICONS[ncat] || {};
					const key   = (disp ?? "").toString().trim().toLowerCase();
					return byCat[key] || byCat._default || SUB_ICONS._default || CAT_ICONS._default;
				}


				function ensureBucket(rawCat, rawSub) {
					const ncat = norm(rawCat || "unknown");
					const nsub = norm(rawSub || "no_group");
					if (!MARKERS[ncat]) MARKERS[ncat] = {};
					if (!MARKERS[ncat][nsub]) MARKERS[ncat][nsub] = [];
					if (!CATS[ncat]) CATS[ncat] = new Set();
					CATS[ncat].add(nsub);
					setCatLabel(ncat, rawCat);
					setSubLabel(ncat, nsub, rawSub);
					return { ncat, nsub };
				}

				function preloadFromAllSubs() {
					for (const [rawCat, subs] of Object.entries(ALL_SUBS)) {
						const ncat = norm(rawCat);
						setCatLabel(ncat, rawCat);
						if (!CATS[ncat]) CATS[ncat] = new Set();
						if (!MARKERS[ncat]) MARKERS[ncat] = {};
						subs.forEach(rawSub => {
							const nsub = norm(rawSub);
							CATS[ncat].add(nsub);
							setSubLabel(ncat, nsub, rawSub);
							if (!MARKERS[ncat][nsub]) MARKERS[ncat][nsub] = [];
						});
					}
				}


				function initMap() {
					preloadFromAllSubs();
					map = new google.maps.Map(document.getElementById("map"), {
						center: { lat: -7.8268087, lng: 110.3877723 },
						zoom: 10,
						disableDefaultUI: true,
						optimized: true,
						tilt: 100,
						mapId: "90f87356969d889c",
						mapTypeId: "hybrid"
					});
					const Z = { das: 1, sungai: 100 }; 
					for (const k of Object.keys(LAYERS)) {
						const layer = new google.maps.Data({ map: null });
						layer.setStyle((feature) => ({
							strokeColor: feature.getProperty("stroke") || (k === "sungai" ? "#00a" : "#0066ff"),
							strokeWeight: feature.getProperty("stroke-width") || (k === "sungai" ? 2.5 : 2),
							strokeOpacity: feature.getProperty("stroke-opacity") ?? 0.95,
							fillColor: feature.getProperty("fill") || "#0088ff",
							fillOpacity: feature.getProperty("fill-opacity") ?? (k === "das" ? 0.12 : 0),
							zIndex: Z[k], 
						}));
						LAYERS[k].data = layer;
					}
					// Checkbox handler
					document.getElementById("layer-das").addEventListener("change", (e) => {
						toggleLayer("das", e.target.checked);
					});
					document.getElementById("layer-sungai").addEventListener("change", (e) => {
						toggleLayer("sungai", e.target.checked);
					});

					// InfoWindow untuk layer DAS (pasang listener ke layer yang benar)
					const infoWindow2 = new google.maps.InfoWindow();
					LAYERS.das.data.addListener("click", (event) => {
						const f = event.feature;
						const urutDas  = f.getProperty("KD_URUTDAS") || "No name available";
						const namaDas  = f.getProperty("NAMA_DAS")   || "No name available";
						const luasDas  = f.getProperty("LUAS_M2")    || "No name available";
						const luasHa   = f.getProperty("LUAS_HA")    || "No name available";

						infoWindow2.setContent(`
        <div class="d-flex justify-content-start mt-2 w-100">
          <h3 class="pt-1 mb-0"><strong>${namaDas}</strong></h3>
			</div>
        <div>
          <table class="table table-bordered mt-3 rounded">
            <tbody>
              <tr><td>Kode Urut</td><td>${urutDas}</td></tr>
              <tr><td>Nama DAS</td><td>${namaDas}</td></tr>
              <tr><td>Luas (m²)</td><td>${luasDas} m²</td></tr>
              <tr><td>Luas (ha)</td><td>${luasHa} ha</td></tr>
			</tbody>
			</table>
			</div>`);
						infoWindow2.setPosition(event.latLng);
						infoWindow2.open(map);
					});

					// Kontrol custom (pastikan elemen ada di DOM)
					const new_element  = document.getElementById('tes');
					const left_element = document.getElementById('left_map');
					const right_element= document.getElementById('right_map');
					const filter_small= document.getElementById('filter_small');
					const bottom_small= document.getElementById('bottom_small');
					if (new_element)  map.controls[google.maps.ControlPosition.TOP_CENTER].push(new_element);
					if (left_element) map.controls[google.maps.ControlPosition.LEFT_TOP].push(left_element);
					if (filter_small) map.controls[google.maps.ControlPosition.LEFT_TOP].push(filter_small);
					if (right_element) map.controls[google.maps.ControlPosition.RIGHT_TOP].push(right_element);	
					if (bottom_small) map.controls[google.maps.ControlPosition.LEFT_CENTER].push(bottom_small);	
					const new_element2 = document.getElementById('filterlayer');

					if (new_element2) {
						const controls = map.controls[google.maps.ControlPosition.RIGHT_TOP];
						const rightIndex = controls.getArray().indexOf(right_element);

						if (rightIndex >= 0) {
							controls.insertAt(rightIndex, new_element2);
						} else {
							controls.insertAt(0, new_element2);
						}
					}


					var currentInfoWindow = null;
					if (Array.isArray(location_new)) {
						location_new.forEach(function(location) {
							const pos = { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) }
							const icon = {
								url: location.icon,
								scaledSize: new google.maps.Size(25, 35),
								labelOrigin: new google.maps.Point(10, -10)
							}
							const anim = (location.anim && location.anim !== '-') ? eval(location.anim) : null

							if (location.id_logger == '10114' && location.category != 'awlr') {
								const baseMarker = new google.maps.Marker({
									position: pos,
									map,
									icon,
									animation: anim || undefined
								})

								let expandedMarkers = []
								let connectingLines = []
								let animating = false

								baseMarker.addListener('click', function() {
									if (animating) return
									if (expandedMarkers.length > 0) {
										expandedMarkers.forEach(m => m.setMap(null))
										connectingLines.forEach(l => l.setMap(null))
										expandedMarkers = []
										connectingLines = []
										return
									}

									animating = true
									map.panTo(baseMarker.getPosition())
									map.setZoom(18)
									
									const offsets = [
										{ name: awlr_plataran.nama_lokasi, icon: awlr_plataran.icon, offset: { lat: 0.0007, lng: -0.0005 } ,status_pantau: awlr_plataran.status_pantau,link : 'https://dpupesdm.monitoring4system.com/analisa/set_sensordash?id_param=649'},
										{ name: arr_plataran.nama_lokasi, icon: arr_plataran.icon, offset: { lat: 0.0007, lng: 0.0005 } ,status_pantau: arr_plataran.status_pantau,link : 'https://dpupesdm.monitoring4system.com/analisa/set_sensordash?id_param=519'}
									]

									offsets.forEach(item => {
										const dest = { lat: pos.lat + item.offset.lat, lng: pos.lng + item.offset.lng }
										const marker = new google.maps.Marker({
											position: pos,
											map,
											icon: {
												url: item.icon,
												scaledSize: new google.maps.Size(25, 35),
												labelOrigin: new google.maps.Point(10, -10)
											}
										})
										expandedMarkers.push(marker)

										const line = new google.maps.Polyline({
											path: [pos, pos],
											geodesic: true,
											strokeColor: '#4285F4',
											strokeOpacity: 0.9,
											strokeWeight: 2,
											map
										})
										connectingLines.push(line)

										const str_analisa = `<a class="d-flex align-items-center" href="${item.link}" target="_blank">Analisa Data</a>`
										const infoWindow = new google.maps.InfoWindow({
											content: `
						<div class="d-flex justify-content-start mt-2 w-100">
							<h3 class="pt-1 mb-0"><strong>${item.name}</strong></h3>
			</div>
						<div>
							<table class="table table-bordered mt-3 rounded">
								<tbody>
									<tr><td>Nama DAS</td><td>${location.nama_das}</td></tr>
									<tr><td>Latitude</td><td>${dest.lat.toFixed(6)}</td></tr>
									<tr><td>Longitude</td><td>${dest.lng.toFixed(6)}</td></tr>
									<tr><td>Status Koneksi</td><td>${location.koneksi}</td></tr>
									<tr><td>Status Pemantauan</td><td>${item.status_pantau}</td></tr>
									<tr><td>Status SD Card</td><td>${location.status_sd}</td></tr>
			</tbody>
			</table>
			</div>
						<div class="d-flex justify-content-center fw-bold">
							<a class="me-3 d-flex align-items-center" href="https://maps.google.com/?q=${dest.lat},${dest.lng}" target="_blank">Menuju Lokasi</a>
							${str_analisa}
			</div>
					`
										})

										marker.addListener('click', function() {
											if (currentInfoWindow) currentInfoWindow.close()
											infoWindow.open(map, marker)
											setTimeout(() => { map.setTilt(60); map.setHeading(map.getHeading()) }, 200)
											currentInfoWindow = infoWindow
										})

										const duration = 600
										const startTime = performance.now()
										function animate(now) {
											const progress = Math.min((now - startTime) / duration, 1)
											const lat = pos.lat + (dest.lat - pos.lat) * progress
											const lng = pos.lng + (dest.lng - pos.lng) * progress
											marker.setPosition({ lat, lng })
											line.setPath([pos, { lat, lng }])
											if (progress < 1) requestAnimationFrame(animate)
										}
										requestAnimationFrame(animate)
									})

									setTimeout(() => animating = false, 650)
								})

								map.addListener('click', function() {
									if (expandedMarkers.length > 0) {
										expandedMarkers.forEach(m => m.setMap(null))
										connectingLines.forEach(l => l.setMap(null))
										expandedMarkers = []
										connectingLines = []
										map.setZoom(13)
										map.panTo(pos)
									}
								})

								const { ncat, nsub } = ensureBucket(location.category, location.category_group)
								MARKERS[ncat][nsub].push(baseMarker)
								return
							}

							if (location.id_logger != '10114') {
								const marker = new google.maps.Marker({
									position: pos,
									map,
									icon,
									animation: anim || undefined
								})

								const { ncat, nsub } = ensureBucket(location.category, location.category_group)
								MARKERS[ncat][nsub].push(marker)

								const str_analisa = `<a class="d-flex align-items-center" href="${location.link}" target="_blank">Analisa Data</a>`
								const infoWindow = new google.maps.InfoWindow({
									content: `
				<div class="d-flex justify-content-start mt-2 w-100">
					<h3 class="pt-1 mb-0"><strong>${location.nama_lokasi}</strong></h3>
			</div>
				<div>
					<table class="table table-bordered mt-3 rounded">
						<tbody>
							<tr><td>Nama DAS</td><td>${location.nama_das}</td></tr>
							<tr><td>Latitude</td><td>${location.latitude}</td></tr>
							<tr><td>Longitude</td><td>${location.longitude}</td></tr>
							<tr><td>Status Koneksi</td><td>${location.koneksi}</td></tr>
							<tr><td>Status Pemantauan</td><td>${location.status_pantau}</td></tr>
							<tr><td>Status SD Card</td><td>${location.status_sd}</td></tr>
			</tbody>
			</table>
			</div>
				<div class="d-flex justify-content-center fw-bold">
					<a class="me-3 d-flex align-items-center" href="https://maps.google.com/?q=${location.latitude},${location.longitude}" target="_blank">Menuju Lokasi</a>
					${str_analisa}
			</div>
			`
								})

								marker.addListener('click', function() {
									if (currentInfoWindow) currentInfoWindow.close()
									map.panTo(marker.getPosition())
									setTimeout(() => { map.setTilt(60); map.setHeading(map.getHeading()) }, 200)
									infoWindow.open(map, marker)
									currentInfoWindow = infoWindow
									scrollToElement(location['id_logger'])
								})
							}
						})
					}

					// ===== [TAMBAHAN] Render checkbox & wiring =====
					function renderFilters() {
						const container = document.getElementById('filters');
						if (!container) return;

						const body = document.createElement('div');
						body.id = 'filters-body';

						const selAll = document.createElement('label');
						selAll.innerHTML = `<input type="checkbox" id="filter-all" checked> Semua Perangkat`;
						selAll.style.display = 'block';
						selAll.style.fontWeight = '600';
						selAll.style.marginBottom = '8px';

						container.innerHTML = '';
						container.appendChild(selAll);
						container.appendChild(body);

						const allCats = Object.keys(CATS).sort();

						allCats.forEach(ncat => {
							const catLabel = LABEL.cats[ncat] || ncat.toUpperCase();
							const catIcon  = getCatIconUrl(ncat);

							const wrap = document.createElement('div');
							wrap.style.marginBottom = '8px';
							wrap.innerHTML = `
      <div class="cat-row">
        <label class="cat-label" style="display:flex;align-items:center;gap:6px;">
          <input type="checkbox" class="cb-cat" data-cat="${ncat}" checked>
          <span style="width:18px;height:18px;display:inline-block;
                       background-image:url('${catIcon}');
                       background-size:contain;background-repeat:no-repeat;background-position:center;"></span>
          <span>${catLabel}</span>
			</label>
			</div>
      <div class="subs"></div>
    `;
							body.appendChild(wrap);
							const subsDiv = wrap.querySelector('.subs');
							[...CATS[ncat]].forEach(nsub => {
								const count = (MARKERS[ncat][nsub] || []).length;
								const disp  = (LABEL.subs[ncat] && LABEL.subs[ncat][nsub]) ? LABEL.subs[ncat][nsub] : nsub;
								const subIcon = getSubIconUrl(ncat, disp);

								const lbl = document.createElement('label');
								lbl.className = 'cb-row';
								lbl.innerHTML = `
        <input type="checkbox" class="cb-sub" data-cat="${ncat}" data-sub="${nsub}" checked>
        <span style="width:16px;height:16px;display:inline-block;
                     background-image:url('${subIcon}');
                     background-size:contain;background-repeat:no-repeat;background-position:center;"></span>
        <span>${disp} <span class="count">(${count})</span></span>
      `;
								subsDiv.appendChild(lbl);
							});
						});
					}

					function updateVisibility() {
						Object.keys(CATS).forEach(ncat => {
							const catOn = document.querySelector(`.cb-cat[data-cat="${ncat}"]`)?.checked;
							[...CATS[ncat]].forEach(nsub => {
								const subOn = document.querySelector(`.cb-sub[data-cat="${ncat}"][data-sub="${nsub}"]`)?.checked;
								(MARKERS[ncat][nsub] || []).forEach(m => m.setMap(catOn && subOn ? map : null));
							});
						});
					}


					function wireFilterEvents() {
						const selAll = document.getElementById('filter-all');
						if (selAll) {
							selAll.addEventListener('change', e => {
								const on = e.target.checked;
								document.querySelectorAll('#filters-body input[type="checkbox"]').forEach(cb => cb.checked = on);
								updateVisibility();
							});
						}

						document.querySelectorAll('.cb-cat').forEach(cb => {
							cb.addEventListener('change', () => {
								// toggle semua sub di kategori saat uncheck/check cat?
								const ncat = cb.dataset.cat;
								const on = cb.checked;
								document.querySelectorAll(`.cb-sub[data-cat="${ncat}"]`).forEach(x => x.checked = on);
								updateVisibility();
								syncGlobalAll();
							});
						});

						document.querySelectorAll('.cb-sub').forEach(cb => {
							cb.addEventListener('change', () => {
								updateVisibility();
								syncGlobalAll();
							});
						});

						function syncGlobalAll() {
							const boxes = [...document.querySelectorAll('#filters-body input[type="checkbox"]')];
							const allOn = boxes.length ? boxes.every(x => x.checked) : true;
							const selAll = document.getElementById('filter-all');
							if (selAll) selAll.checked = allOn;
						}
					}

					renderFilters();
					updateVisibility();
					wireFilterEvents();

				}

				$("input[name='mapType']").on("change", function() {
					const type = $(this).val();
					map.setMapTypeId(type);

					if (type === "roadmap" || type === "terrain") {
						$("#tes").css("background", "#2f89b9");
						$("#left_map").css("background", "#2f89b9");
						$("#right_map").css("background", "#2f89b9");
						$("#filterlayer").css("background", "#2f89b9");
					} else {
						$("#tes").css("background", "linear-gradient(to right,#2f89b9,transparent, #2f89b9)");
						$("#left_map").css("background", "linear-gradient(to right,#2f89b9,transparent)");
						$("#right_map").css("background", "linear-gradient(to right,transparent, #2f89b9)");
						$("#filterlayer").css("background", "linear-gradient(to right,transparent, #2f89b9)");
					}
				});
				function bringToFront(key) {
					const entry = LAYERS[key];
					if (entry && entry.data && entry.data.getMap()) {
						// trik aman untuk menggambar ulang di atas
						entry.data.setMap(null);
						entry.data.setMap(map);
					}
				}

				function toggleLayer(key, visible) {
					if (!map || !(map instanceof google.maps.Map)) return;
					const entry = LAYERS[key];
					if (!entry) return;

					if (visible) {
						const show = () => {
							entry.data.setMap(map);
							if (key === "sungai") bringToFront("sungai");
							if (key === "das" && LAYERS.sungai.data.getMap()) bringToFront("sungai");
						};

						if (!entry.loaded) {
							entry.data.loadGeoJson(entry.url, null, () => {
								entry.loaded = true;
								show();
							});
						} else {
							show();
						}
					} else {
						entry.data.setMap(null);
					}
				}

				if (typeof google !== 'undefined' && google.maps) {
					initMap();
				} else {
					window.onload = initMap;
				}


				function scrollToElement(elementId) {
					const $element = $('#sc_' + elementId);
					if ($element.length) {
						$('#left_map').animate({
							scrollTop: $element.offset().top - $('#left_map').offset().top + $('#left_map').scrollTop()- 20
						}, 600);  
					}
					$element.addClass('border-pulse');

					setTimeout(function() {
						$element.removeClass('border-pulse');
					}, 2000); 
				}



			});
		</script>
	</body>

</html>