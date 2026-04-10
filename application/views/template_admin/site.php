<!doctype html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />

		<title>Dinas PUPESDM Daerah Istimewa Yogyakarta</title>
		<style>
			@import url('https://rsms.me/inter/inter.css');

			:root {
				--tblr-font-sans-serif: Inter, -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif !important;
			}
		</style>
		<!-- CSS files -->
		<link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/6/65/Yogyakarta_Logo.svg">
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler.min.css" rel="stylesheet" />
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-flags.min.css" rel="stylesheet" />
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-payments.min.css" rel="stylesheet" />
		<link href="https://stesy.beacontelemetry.com/assets/code/tabler-vendors.min.css" rel="stylesheet" />
		<link href="https://stesy.beacontelemetry.com/assets/code/demo.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/loaders.css/0.1.2/loaders.min.css">


		<link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="https://jqueryui.com/resources/demos/style.css">
		<link
			  rel="stylesheet"
			  href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
		<link
			  rel="stylesheet"
			  href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0" />
		<link rel="stylesheet" href="<?php echo base_url() ?>plugin/datetimepicker/build/jquery.datetimepicker.min.css" />
		<style>
			.navbar {
				--tblr-navbar-active-border-color: #058dc7;
			}

			/* Copilot thinking loader */
			.copilot-thinking {
				display: flex;
				align-items: center;
				gap: 12px;
				padding: 14px 20px;
				background: #ffffff;
				border-radius: 20px;
				border-bottom-left-radius: 4px;
				border: 1px solid rgba(0,0,0,0.04);
				box-shadow: 0 2px 8px rgba(0,0,0,0.03);
			}
			.copilot-thinking-icon {
				width: 24px;
				height: 24px;
				position: relative;
				flex-shrink: 0;
			}
			.copilot-thinking-icon svg {
				width: 24px;
				height: 24px;
				position: absolute;
				top: 0; left: 0;
				transition: all 0.35s cubic-bezier(.4,0,.2,1);
				opacity: 0;
				transform: scale(0.3) rotate(-90deg);
				filter: drop-shadow(0 0 4px currentColor);
			}
			.copilot-thinking-icon svg.active {
				opacity: 1;
				transform: scale(1) rotate(0deg);
			}
			.copilot-thinking-icon svg.exit {
				opacity: 0;
				transform: scale(0.3) rotate(90deg);
			}
			.copilot-thinking-text {
				font-size: 0.85rem;
				color: #64748b;
				font-weight: 500;
				white-space: nowrap;
			}

			#btn-suggest:hover span {
				text-decoration: underline;
			}

			.incoming {
				align-items: flex-end;
			}

			.chatbot__button {
				position: fixed;
				bottom: 35px;
				z-index: 999;
				right: 35px;
				min-width: 140px;
				height: 56px;
				display: flex;
				justify-content: center;
				align-items: center;
				background: linear-gradient(135deg, #1A4D96 0%, #2A75DD 100%);
				color: #fff;
				border: none;
				border-radius: 28px;
				outline: none;
				cursor: pointer;
				box-shadow: 0 8px 24px rgba(26, 77, 150, 0.35);
				transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
			}

			.chatbot__button span {
				position: absolute;
				display: flex;
				align-items: center;
				font-size: 1.05rem;
				font-weight: 600;
				transition: opacity 0.2s ease, transform 0.2s ease;
			}

			.show-chatbot .chatbot__button span:first-child,
			.chatbot__button span:last-child {
				opacity: 0;
				transform: scale(0.8);
				pointer-events: none;
			}

			.show-chatbot .chatbot__button span:last-child {
				opacity: 1;
				transform: scale(1);
				pointer-events: auto;
			}

			.chatbot__button:hover {
				transform: translateY(-3px) scale(1.02);
				box-shadow: 0 14px 30px rgba(26, 77, 150, 0.45);
			}

			.chatbot {
				position: fixed;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%) scale(0.9);
				width: 850px;
				height: 720px;
				z-index: 999;
				background-color: #f8fafc;
				border-radius: 24px;
				border: 1px solid rgba(255, 255, 255, 0.6);
				box-shadow: 0 12px 48px rgba(15, 23, 42, 0.15), 0 4px 12px rgba(15, 23, 42, 0.08);
				transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1.1), opacity 0.3s ease;
				overflow: hidden;
				opacity: 0;
				pointer-events: none;
				display: flex;
				flex-direction: column;
				backdrop-filter: blur(10px);
			}

			.show-chatbot .chatbot {
				opacity: 1;
				pointer-events: auto;
				transform: translate(-50%, -50%) scale(1);
			}

			.chatbot__header {
				position: relative;
				background-color: rgba(255, 255, 255, 0.95);
				text-align: center;
				border-bottom: 1px solid rgba(0, 0, 0, 0.04);
				padding: 18px 24px;
				z-index: 2;
				box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
				backdrop-filter: blur(8px);
			}

			.chatbot__header span {
				position: absolute;
				top: 50%;
				right: 24px;
				color: #94a3b8;
				transform: translateY(-50%);
				cursor: pointer;
				transition: color 0.2s, transform 0.2s;
				padding: 4px;
			}

			.chatbot__header span:hover {
				color: #0f172a;
				transform: translateY(-50%) scale(1.1);
			}

			.chatbox__title {
				margin-bottom: 0px;
				font-size: 1.25rem;
				letter-spacing: -0.3px;
			}

			.chatbot__box {
				flex: 1;
				overflow-y: auto;
				padding: 24px 24px 120px;
			}

			.chatbot__box::-webkit-scrollbar { width: 6px; }
			.chatbot__box::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
			.chatbot__box::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

			.chatbot__chat {
				display: flex;
				margin: 20px 0;
				align-items: flex-end;
				animation: fadeIn 0.3s ease;
			}

			@keyframes fadeIn {
				from { opacity: 0; transform: translateY(10px); }
				to { opacity: 1; transform: translateY(0); }
			}

			#btn-suggest span { color: #256fd2; }

			.chatbot__chat p {
				max-width: 82%;
				font-size: 0.98rem;
				line-height: 1.55;
				white-space: pre-wrap;
				padding: 14px 20px;
				border-radius: 20px;
				box-shadow: 0 2px 8px rgba(0,0,0,0.03);
			}

			#btn-suggest.active svg { transform: rotate(180deg); transition: transform 0.3s ease; }

			.chatbot__chat p.error {
				color: #b91c1c;
				background: #fef2f2;
				border: 1px solid #fca5a5;
			}

			.incoming p {
				margin: 0;
				color: #1e293b;
				background: #ffffff;
				border-bottom-left-radius: 4px;
				border: 1px solid rgba(0,0,0,0.04);
			}

			.outgoing p {
				color: #ffffff;
				background: linear-gradient(135deg, #256fd2 0%, #1e5ab8 100%);
				margin: 0;
				border-bottom-right-radius: 4px;
				box-shadow: 0 4px 12px rgba(37, 111, 210, 0.2);
			}

			.incoming span.material-symbols-outlined {
				width: 38px;
				height: 38px;
				line-height: 38px;
				color: #ffffff;
				background: linear-gradient(135deg, #1A4D96 0%, #2A75DD 100%);
				border-radius: 50%;
				text-align: center;
				margin: 0 14px 0 0;
				font-size: 20px;
				box-shadow: 0 4px 12px rgba(26, 77, 150, 0.25);
				flex-shrink: 0;
			}

			.outgoing {
				justify-content: flex-end;
			}

			.textarea-container {
				position: relative;
				width: 100%;
				display: flex;
				align-items: center;
				background: #ffffff;
				border-radius: 28px;
				box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
				border: 1px solid rgba(0, 0, 0, 0.06);
				padding: 8px 16px;
				transition: box-shadow 0.3s ease;
			}

			.textarea-container:focus-within {
				box-shadow: 0 10px 30px rgba(37, 111, 210, 0.15);
				border-color: rgba(37, 111, 210, 0.3);
			}

			.chatbot__input-box {
				position: absolute;
				bottom: 0;
				width: 100%;
				background: linear-gradient(0deg, #f8fafc 80%, rgba(248, 250, 252, 0) 100%);
				padding: 24px 24px 20px;
				border-top: none;
			}

			.textarea-container span {
				color: #94a3b8;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				transition: all 0.2s ease;
				width: 40px;
				height: 40px;
				border-radius: 50%;
				flex-shrink: 0;
			}

			.textarea-container span:hover {
				color: #256fd2;
				background: #f1f5f9;
				transform: scale(1.05);
			}

			#mic-btn { margin-right: 4px; }

			.chatbot__textarea {
				flex: 1;
				max-height: 100px;
				margin: 0;
				font-size: 1rem;
				color: #1e293b;
				border: none;
				outline: none;
				resize: none;
				background: transparent;
				padding: 10px 10px;
				line-height: 1.5;
				display: block;
			}

			.chatbot__textarea::placeholder {
				font-family: inherit;
				color: #94a3b8;
			}

			/* Copilot chart inside bubble */
			.copilot-content-wrap {
				display: flex;
				flex-direction: column;
				max-width: 82%;
			}
			.copilot-chart-wrap {
				background: #ffffff;
				border-radius: 16px;
				padding: 12px;
				margin-bottom: 8px;
				border: 1px solid rgba(0,0,0,0.04);
				box-shadow: 0 2px 8px rgba(0,0,0,0.03);
				width: 100%;
			}
			.copilot-chart-wrap canvas { height: 250px !important; }
			.copilot-csv-link {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				padding: 8px 16px;
				background: linear-gradient(135deg, #256fd2, #1e5ab8);
				color: #fff !important;
				border-radius: 12px;
				font-size: .85rem;
				font-weight: 600;
				text-decoration: none;
				margin-bottom: 8px;
				box-shadow: 0 4px 12px rgba(37,111,210,.2);
				transition: transform .2s;
			}
			.copilot-csv-link:hover { transform: translateY(-1px); }

			/* Mic recording state */
			#mic-btn.recording svg { stroke: #ef4444; }
			#mic-btn.recording { animation: pulse-mic 1s infinite; }
			#mic-btn.processing svg { stroke: #f59e0b; }
			@keyframes pulse-mic { 0%,100%{opacity:1} 50%{opacity:.4} }

			@media (max-width: 992px) {
				.chatbot { width: 90%; }
			}

			@media (max-width: 490px) {
				.chatbot__button { bottom: 20px; right: 20px; }
				.chatbot {
					right: 0;
					bottom: 0;
					width: 100%;
					height: 100%;
					border-radius: 0;
					transform: translateY(100%);
				}
				.show-chatbot .chatbot { transform: translateY(0); }
				.chatbot__header span { display: inline; }
			}
		</style>
		<?php

	if ($this->session->userdata('data') == 'bulan') {
		?>
		<style type="text/css">
			.ui-datepicker-calendar {
				display: none;
			}
		</style>
		<?php

	} elseif ($this->session->userdata('data') == 'tahun') {
		?>
		<style type="text/css">
			.ui-datepicker-calendar {
				display: none;
			}

			.ui-datepicker-prev {
				display: none;
			}

			.ui-datepicker-next {
				display: none;
			}

			.ui-datepicker-month {
				display: none;
			}
		</style>
		<?php
	}
		?>

		<style type="text/css">
			.highcharts-data-table table {
				border-collapse: collapse;
				border-spacing: 0;
				background: white;
				min-width: 100%;
				margin-top: 10px;
				font-family: sans-serif;
				font-size: 0.9em;
			}

			.highcharts-data-table td,
			.highcharts-data-table th,
			.highcharts-data-table caption {
				border: 1px solid silver;
				padding: 0.5em;
			}

			.highcharts-data-table tr:nth-child(even),
			.highcharts-data-table thead tr {
				background: #f8f8f8;
			}

			.highcharts-data-table tr:hover {
				background: #eff;
			}

			.highcharts-data-table caption {
				border-bottom: none;
				font-size: 1.1em;
				font-weight: bold;
				caption-side: top;
			}
		</style>
		<script src="https://stesy.beacontelemetry.com/assets/code/tom-select.base.min.js" defer></script>
		<script src="https://stesy.beacontelemetry.com/assets/code/tabler.min.js" defer></script>
		<script src="https://stesy.beacontelemetry.com/assets/code/demo.min.js" defer></script>

		<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
		<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
		<script src="<?php echo base_url() ?>plugin/datetimepicker/build/jquery.datetimepicker.full.min.js"></script>

		<!-- mQtt -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>



		<script>
			$(function() {
				$('#dptanggal').datetimepicker({
					timepicker: false,
					format: 'Y-m-d',
				});
			});
		</script>
		<script>
			$(function() {
				$("#dpbulan").datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: 'yy-mm',
					onClose: function(dateText, inst) {
						$(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
					}
				});
			});
		</script>
		<script>
			$(function() {
				$("#dptahun").datepicker({
					changeYear: true,
					dateFormat: 'yy',
					onClose: function(dateText, inst) {
						$(this).datepicker('setDate', new Date(inst.selectedYear, 1));
					}
				});
			});
		</script>
		<script>
			$(function() {
				$("#dpdari").datetimepicker({
					format: 'Y-m-d H:i'
				});
			});
		</script>
		<script>
			$(function() {
				$("#dpsampai").datetimepicker({
					format: 'Y-m-d H:i'
				});
			});
		</script>

	</head>

	<body class="layout-fluid" <?php if ($this->uri->segment(2) == "livedata") {
	echo 'onload="init();"';
} ?>>
		<div class="page">
			<header class="navbar navbar-expand-md navbar-light d-print-none" style="background-color:#d4e4ff">
				<div class="container-xl">
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
						<span class="navbar-toggler-icon"></span>
					</button>
					<h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
						<a href=".">
							<div class="d-none d-lg-block">
								<img src="<?php echo base_url() ?>image/pupesdm.svg" height="50" alt="PU SDA">
							</div>
							<div class="d-lg-none">
								<img src="https://upload.wikimedia.org/wikipedia/commons/6/65/Yogyakarta_Logo.svg" height="40" alt="PU SDA">
							</div>
						</a>
					</h1>
					<div class="navbar-nav flex-row order-md-last">
						<div class="nav-item dropdown">
							<div class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu" aria-expanded="false">

								<?php if ($this->session->userdata('leveluser') == 'User') { ?>
								<img src="https://upload.wikimedia.org/wikipedia/commons/6/65/Yogyakarta_Logo.svg" width="110" height="32" alt="PU SDA" class="navbar-brand-image">
								<?php } else { ?>
								<span class="avatar avatar-sm ">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
										<circle cx="9" cy="7" r="4"></circle>
										<path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
										<path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
										<path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
									</svg>
								</span>
								<?php } ?>


								<div class="d-none d-xl-block ps-2">
									<div><?= $this->session->userdata('nama') ?></div>

									<?php if ($this->session->userdata('leveluser') == 'User') { ?>
									<div class="mt-1 small text-muted text-uppercase">
										Admin
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</header>
			<div class="navbar-expand-md">
				<?php $this->load->view('template_admin/menu'); ?>
			</div>
			<div class="page-wrapper">

				<!-- Konten-->
				<?php $this->load->view($konten); ?>
				<!-- end Konten-->
				<footer class="footer footer-transparent d-print-none">
					<div class="container-xl">
						<div class="row text-center align-items-center flex-row-reverse">
							<div class="col-12 mb-3">

								<!-- Histats.com  (div with counter) -->
								<div id="histats_counter"></div>
								<!-- Histats.com  START  (aync)-->
								<script type="text/javascript">
									var _Hasync = _Hasync || [];
									_Hasync.push(['Histats.start', '1,4787566,4,397,112,48,00011101']);
									_Hasync.push(['Histats.fasi', '1']);
									_Hasync.push(['Histats.track_hits', '']);
									(function() {
										var hs = document.createElement('script');
										hs.type = 'text/javascript';
										hs.async = true;
										hs.src = ('//s10.histats.com/js15_as.js');
										(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
									})();
								</script>
								<noscript><a href="/" target="_blank"><img src="//sstatic1.histats.com/0.gif?4787566&101" alt="free web site hit counter" border="0"></a></noscript>
								<!-- Histats.com  END  -->
							</div>
							<div class="col-12 ">
								<ul class="list-inline list-inline-dots mb-0">
									<li class="list-inline-item">
										&copy; PUPESDM DIY 2017 - 2023
									</li>
									<li class="list-inline-item">
										<img src="<?php echo base_url() ?>image/logo_be.png" alt="Beacon Engineering" class="navbar-brand-image">
									</li>
									<li class="list-inline-item">
										<img src="<?php echo base_url() ?>image/logostesy.png" alt="Beacon Engineering" class="navbar-brand-image">
									</li>
								</ul>
							</div>
						</div>
					</div>
				</footer>
			</div>
			<?php if ($this->session->userdata('leveluser') == 'User') { ?>
			<button class="chatbot__button">
				<span class="d-flex fw-bold"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-sparkles me-2">
					<path stroke="none" d="M0 0h24v24H0z" fill="none" />
					<path d="M16 18a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm0 -12a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm-7 12a6 6 0 0 1 6 -6a6 6 0 0 1 -6 -6a6 6 0 0 1 -6 6a6 6 0 0 1 6 6z" />
					</svg>Copilot</span>
				<span class="material-symbols-outlined">close</span>
			</button>
			<?php } ?>

			<div class="chatbot">
				<div class="chatbot__header">
					<div class="d-flex px-3 align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-sparkles" style="color: #256fd2;">
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M16 18a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm0 -12a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm-7 12a6 6 0 0 1 6 -6a6 6 0 0 1 -6 -6a6 6 0 0 1 -6 6a6 6 0 0 1 6 6z" />
						</svg>
						<h5 class="ms-2 chatbox__title text-dark fw-bold mb-0">Copilot</h5>
						<div class="badge text-white ms-3 py-1 px-3 shadow-sm rounded-pill" style="font-size: 0.72rem; font-weight: 600; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">BETA</div>
					</div>

					<span class="material-symbols-outlined">close</span>
				</div>
				<ul class="chatbot__box">
					<li class="chatbot__chat incoming">
						<span class="material-symbols-outlined">smart_toy</span>
						<p>Halo apakah ada yang bisa saya bantu ?</p>
					</li>
				</ul>

				<div class="chatbot__input-box">
					<div class="textarea-container">
						<textarea
								  class="chatbot__textarea"
								  placeholder="Enter a message..."
								  required></textarea>
						<span id="mic-btn" title="Rekam suara (klik mulai/berhenti)">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
								<path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
								<line x1="12" y1="19" x2="12" y2="22"></line>
							</svg>
						</span>
						<span id="send-btn">
							<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send">
								<path stroke="none" d="M0 0h24v24H0z" fill="none" />
								<path d="M10 14l11 -11" />
								<path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
							</svg>
						</span>
					</div>
				</div>
			</div>
		</div>




	</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

function generateRandomCode(length = 5) {
	const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	let result = '';
	for (let i = 0; i < length; i++) result += chars[Math.floor(Math.random() * chars.length)];
	return result;
}

const chatbotToggle = document.querySelector('.chatbot__button');
const sendChatBtn   = document.querySelector('#send-btn');
const chatInput     = document.querySelector('.chatbot__textarea');
const chatBox       = document.querySelector('.chatbot__box');
const chatbotCloseBtn = document.querySelector('.chatbot__header span');
const micBtn        = document.querySelector('#mic-btn');

const formatResponse = (text) => {
	text = text.replace(/\n{2,}/g, '\n');
	text = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
	text = text.replace(/^#### (.*)$/gm, '<b>$1</b>');
	text = text.replace(/^### (.*)$/gm, '<b>$1</b>');
	text = text.replace(/^## (.*)$/gm, '<b>$1</b>');
	text = text.replace(/^# (.*)$/gm, '<b>$1</b>');
	text = text.replace(/\n/g, '<br>');
	return text;
};

let message_history = [];
let userMessage;
const inputInitHeight = chatInput.scrollHeight;

const createChatLi = (message, className) => {
	const chatLi = document.createElement('li');
	chatLi.classList.add('chatbot__chat', className);
	const chatContent = className === 'outgoing'
		? `<p></p>`
		: `<span class="material-symbols-outlined">smart_toy</span><p></p>`;
	chatLi.innerHTML = chatContent;
	chatLi.querySelector('p').textContent = message;
	return chatLi;
};

const random_code = generateRandomCode();
const BASE = '<?php echo base_url(); ?>';

const generateResponse = (incomingChatLi) => {
	const API_URL = BASE + 'copilot/chat';
	message_history.push({ role: 'user', content: userMessage });

	// Buat wrapper untuk chart + teks (belum ditampilkan, loading masih tampil)
	const contentWrap = document.createElement('div');
	contentWrap.className = 'copilot-content-wrap';
	const textP = document.createElement('p');
	contentWrap.appendChild(textP);

	let fullText = '';
	let loadingSwapped = false;

	function swapLoading() {
		if (loadingSwapped) return;
		loadingSwapped = true;
		incomingChatLi.innerHTML = '<span class="material-symbols-outlined">smart_toy</span>';
		incomingChatLi.appendChild(contentWrap);
	}

	fetch(API_URL, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ uuid: random_code, messages: message_history })
	}).then(response => {
		const reader = response.body.getReader();
		const decoder = new TextDecoder();
		let buffer = '';

		function processStream() {
			return reader.read().then(({ done, value }) => {
				if (done) {
					if (fullText) {
						message_history.push({ role: 'assistant', content: fullText });
					}
					chatBox.scrollTo(0, chatBox.scrollHeight);
					return;
				}

				buffer += decoder.decode(value, { stream: true });
				const lines = buffer.split('\n');
				buffer = lines.pop();

				for (const line of lines) {
					const trimmed = line.trim();
					if (!trimmed || !trimmed.startsWith('data: ')) continue;
					const payload = trimmed.slice(6);
					if (payload === '[DONE]') continue;

					try {
						const json = JSON.parse(payload);

						// ── Meta events (chart / csv) ──
						if (json.meta) {
							swapLoading();
							if (json.meta.type === 'chart' && json.meta.datasets) {
								const chartWrap = document.createElement('div');
								chartWrap.className = 'copilot-chart-wrap';
								const canvas = document.createElement('canvas');
								chartWrap.appendChild(canvas);
								contentWrap.insertBefore(chartWrap, textP);
								new Chart(canvas, {
									type: json.meta.chart_type || 'line',
									data: { labels: json.meta.labels, datasets: json.meta.datasets },
									options: {
										responsive: true,
										maintainAspectRatio: false,
										plugins: {
											title: { display: true, text: json.meta.title || '', font: { size: 12 } },
											legend: { labels: { font: { size: 11 } } }
										},
										scales: { x: { ticks: { font: { size: 10 } } }, y: { ticks: { font: { size: 10 } } } }
									}
								});
							}
							if (json.meta.type === 'csv_download') {
								const a = document.createElement('a');
								a.href = BASE + 'copilot/export_csv?id_logger=' + encodeURIComponent(json.meta.id_logger)
									+ '&awal=' + json.meta.awal + '&akhir=' + json.meta.akhir;
								a.target = '_blank';
								a.className = 'copilot-csv-link';
								a.innerHTML = '⬇ Download CSV — ' + (json.meta.nama || json.meta.id_logger);
								contentWrap.insertBefore(a, textP);
							}
							continue;
						}

						// ── Token ──
						if (json.token) {
							swapLoading();
							fullText += json.token;
							textP.innerHTML = formatResponse(fullText);
							chatBox.scrollTo(0, chatBox.scrollHeight);
						}

						// ── Error ──
						if (json.error) {
							swapLoading();
							textP.classList.add('error');
							textP.textContent = 'Error: ' + (json.error.oai_msg || json.error.error || 'Unknown');
						}
					} catch (e) { /* skip parse errors */ }
				}

				return processStream();
			});
		}
		return processStream();
	}).catch(error => {
		swapLoading();
		textP.classList.add('error');
		textP.textContent = 'Gagal terhubung ke server. Coba lagi.';
		console.error(error);
	});
};

const handleChat = () => {
	userMessage = chatInput.value.trim();
	if (!userMessage) return;
	chatInput.value = '';
	chatInput.style.height = `${inputInitHeight}px`;

	chatBox.appendChild(createChatLi(userMessage, 'outgoing'));
	chatBox.scrollTo(0, chatBox.scrollHeight);

	setTimeout(() => {
		const incomingChatLi = document.createElement('li');
		incomingChatLi.classList.add('chatbot__chat', 'incoming');
		incomingChatLi.innerHTML = `
		<span class="material-symbols-outlined">smart_toy</span>
		<div class="copilot-thinking">
			<div class="copilot-thinking-icon">
				<svg data-i="0" class="active" style="color:#256fd2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 18a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm0 -12a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm-7 12a6 6 0 0 1 6 -6a6 6 0 0 1 -6 -6a6 6 0 0 1 -6 6a6 6 0 0 1 6 6z"/></svg>
				<svg data-i="1" style="color:#7c3aed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.5 13a3.5 3.5 0 0 0-3.5-3.5"/><path d="M19 13a7 7 0 0 0-7-7"/><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10h-4"/></svg>
				<svg data-i="2" style="color:#06b6d4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
				<svg data-i="3" style="color:#f59e0b" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
			</div>
			<span class="copilot-thinking-text">Sedang berpikir...</span>
		</div>`;
		chatBox.appendChild(incomingChatLi);
		chatBox.scrollTo(0, chatBox.scrollHeight);

		// Cycle icons
		const iconWrap = incomingChatLi.querySelector('.copilot-thinking-icon');
		const thinkText = incomingChatLi.querySelector('.copilot-thinking-text');
		const labels = ['Sedang berpikir...', 'Menganalisis data...', 'Mencari informasi...', 'Menyusun jawaban...'];
		let idx = 0;
		const cycleTimer = setInterval(() => {
			if (!iconWrap || !document.contains(iconWrap)) { clearInterval(cycleTimer); return; }
			const svgs = iconWrap.querySelectorAll('svg');
			const prev = svgs[idx];
			idx = (idx + 1) % svgs.length;
			const next = svgs[idx];
			prev.classList.remove('active');
			prev.classList.add('exit');
			setTimeout(() => prev.classList.remove('exit'), 350);
			next.classList.add('active');
			if (thinkText) thinkText.textContent = labels[idx];
		}, 1200);

		generateResponse(incomingChatLi);
	}, 300);
};

chatInput.addEventListener('input', () => {
	chatInput.style.height = `${inputInitHeight}px`;
	chatInput.style.height = `${chatInput.scrollHeight}px`;
});

chatInput.addEventListener('keydown', (e) => {
	if (e.key === 'Enter' && !e.shiftKey && window.innerWidth > 800) {
		e.preventDefault();
		handleChat();
	}
});

chatbotToggle.addEventListener('click', () => {
	document.body.classList.toggle('show-chatbot');
	if (document.body.classList.contains('show-chatbot')) {
		setTimeout(() => chatInput.focus(), 300);
	}
});

chatbotCloseBtn.addEventListener('click', () =>
	document.body.classList.remove('show-chatbot')
);

sendChatBtn.addEventListener('click', () => handleChat());

function send_suggestion(pesan) {
	chatInput.value = pesan;
	handleChat();
}

// ─── Voice Recording (Whisper) ───
let mediaRecorder = null;
let audioChunks = [];
let isRecording = false;

if (micBtn) {
	micBtn.addEventListener('click', async () => {
		if (!isRecording) {
			try {
				const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
				mediaRecorder = new MediaRecorder(stream);
				audioChunks = [];
				mediaRecorder.ondataavailable = e => { if (e.data.size > 0) audioChunks.push(e.data); };
				mediaRecorder.onstop = async () => {
					stream.getTracks().forEach(t => t.stop());
					const blob = new Blob(audioChunks, { type: 'audio/webm' });
					const formData = new FormData();
					formData.append('audio', blob, 'recording.webm');

					micBtn.classList.remove('recording');
					micBtn.classList.add('processing');

					try {
						const res = await fetch(BASE + 'copilot/transcribe', { method: 'POST', body: formData });
						const data = await res.json();
						if (data.text) {
							chatInput.value = data.text;
							chatInput.focus();
						}
					} catch (err) { console.error('Transcribe error:', err); }
					micBtn.classList.remove('processing');
				};
				mediaRecorder.start();
				isRecording = true;
				micBtn.classList.add('recording');
			} catch (err) { console.error('Mic access denied:', err); }
		} else {
			mediaRecorder.stop();
			isRecording = false;
		}
	});
}
</script>
