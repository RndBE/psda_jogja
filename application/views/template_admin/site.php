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

			.incoming .loader-inner.ball-pulse>div {
				width: 7px;
				height: 7px;
				background-color: black;
			}

			.incoming .loader-inner {
				display: flex;
				align-items: center;
				justify-content: center;
				padding: 12px 16px;
				background: #f5f5f5;
				border-radius: 10px;
				max-width: 75%;
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
				min-width: 100px;
				height: 50px;
				display: flex;
				justify-content: center;
				align-items: center;
				background: #227ebb;
				color: #f3f7f8;
				border: none;
				border-radius: 20px;
				outline: none;
				cursor: pointer;
			}

			.chatbot__button span {
				position: absolute;
			}

			#btn-suggest span {
				color: #2e4e77 !important;
			}

			.show-chatbot .chatbot__button span:first-child,
			.chatbot__button span:last-child {
				opacity: 0;
			}

			.show-chatbot .chatbot__button span:last-child {
				opacity: 1;
			}

			.chatbot {
				position: fixed;
				bottom: 10px;
				/* you can adjust this value */
				left: 50%;
				/* center horizontally */
				transform: translateX(-50%) scale(0.5);
				/* center + scale effect */
				width: 900px;
				z-index: 999;
				background-color: white;
				border-radius: 10px;
				border: 1px solid #d3d4d6;
				box-shadow: 0 0 128px 0 rgba(0, 0, 0, 0.1), 0 32px 64px -48px rgba(0, 0, 0, 0.5);
				transition: transform 0.3s ease;
				overflow: hidden;
				opacity: 0;
				pointer-events: none;
			}

			.show-chatbot .chatbot {
				opacity: 1;
				pointer-events: auto;
				transform: translateX(-50%) scale(1);
			}

			.chatbot__header {
				position: relative;
				background-color: white;
				text-align: center;
				border-bottom: 1px solid #d3d4d6;
				padding: 16px 0;
			}

			.chatbot__header span {
				position: absolute;
				top: 50%;
				right: 20px;
				color: black;
				transform: translateY(-50%);
				cursor: pointer;
			}

			.chatbox__title {
				margin-bottom: 0px;
				font-size: 1.1rem;

			}

			.chatbot__box {
				height: 700px;
				overflow-y: auto;
				padding: 10px 20px 100px;
			}

			.chatbot__chat {
				display: flex;
			}

			#btn-suggest span {
				color: #256fd2;
			}

			.chatbot__chat p {
				max-width: 75%;
				font-size: 0.95rem;
				overflow: hidden;
				white-space: pre-wrap;
				color: white;
				background-color: white;
				border-radius: 10px 10px 0 10px;
				padding: 12px 16px;
			}

			#btn-suggest.active svg {
				transform: rotate(180deg);
			}

			.chatbot__chat p.error {
				color: #721c24;
				background: #f8d7da;
			}

			.incoming p {
				margin-top: 0px;
				margin-bottom: 0px;
				color: #202020;
				background: #f5f5f5;
				border-radius: 10px 10px 10px 10px;
			}

			.outgoing p {
				color: #202020;
				background: #f5f5f5;
				border-radius: 10px 10px 10px 10px;
				margin-bottom: 0px;
			}


			.incoming span {
				width: 32px;
				height: 32px;
				line-height: 32px;
				color: #f3f7f8;
				background-color: #227ebb;
				border-radius: 4px;
				text-align: center;
				align-self: flex-end;
				margin: 0 10px 0px 0;
			}

			.outgoing {
				justify-content: flex-end;
				margin: 0px 0;
			}

			.incoming {
				margin: 20px 0;
			}

			.textarea-container {
				position: relative;
				width: 100%;
			}

			.chatbot__input-box {
				position: absolute;
				bottom: 0;
				width: 100%;
				display: block;
				gap: 5px;
				align-items: center;
				border-top: 1px solid #d3d4d6;
				background: white;
				padding: 15px 0px;
			}

			.textarea-container span {
				position: absolute;
				right: 25px;
				top: 50%;
				transform: translateY(-50%);
				color: #999;
				cursor: pointer;
			}

			.chatbot__textarea {
				width: 100%;
				max-height: 40px;
				margin-bottom: 0px;
				font-size: 0.95rem;
				overflow: hidden;
				padding: 0 15px;
				/* remove vertical padding */
				color: #202020;
				border-radius: 10px;
				border: solid 1px #d3d4d6;
				outline: none;
				resize: none;
				background: transparent;
				display: flex;
				/* NEW: use flexbox */
				align-items: center;
				/* NEW: center text vertically */
				line-height: 40px;
				/* NEW: help align text inside */
			}

			.chatbot__textarea::placeholder {
				font-family: 'Poppins', sans-serif;
			}

			.chatbot__input-box span {
				color: #d3d4d6;
				cursor: pointer;
				/* visibility: hidden; */
			}

			@media (max-width: 490px) {
				.chatbot {
					right: 0;
					bottom: 0;
					width: 100%;
					height: 100%;
					border-radius: 0;
				}

				.chatbot__box {
					height: 90%;
				}

				.chatbot__header span {
					display: inline;
				}
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
					<div class="d-flex px-3 align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-sparkles">
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M16 18a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm0 -12a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm-7 12a6 6 0 0 1 6 -6a6 6 0 0 1 -6 -6a6 6 0 0 1 -6 6a6 6 0 0 1 6 6z" />
						</svg>
						<h5 class="ms-2 chatbox__title text-dark fw-bold">Copilot
						</h5>
						<div class="badge bg-azure text-white ms-3 py-1">BETA</div>
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
					<!--
					<div class="px-3 mb-3 border-bottom	">
						<h4 class="fw-normal mb-2" id="btn-suggest" style="width:max-content">
							<span style="color: #256fd2;">Hide Suggested Suggestion</span>
							<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#256fd2">
								<path d="M480-528 296-344l-56-56 240-240 240 240-56 56-184-184Z" />
							</svg>
						</h4>
						<div class="row mb-3" id="question">
							<div class="col-6">
								<div class="card" style="border-radius:10px;cursor:pointer;overflow:hidden" onclick="send_suggestion('Tampilkan data pos AWR Kaliurang')">
									<div class="card-body d-flex justify-content-center py-2">
										Tampilkan data pos AWR Kaliurang
									</div>
								</div>
							</div>
							<div class="col-6">
								<div class="card" style="border-radius:10px;cursor:pointer" onclick="send_suggestion('Tampilkan data pos AWR Tegal')">
									<div class="card-body d-flex justify-content-center py-2">
										Tampilkan data pos AWR Tegal
									</div>
								</div>
							</div>
						</div>
					</div>-->
					<div class="textarea-container px-3">
						<textarea
								  class="chatbot__textarea"
								  placeholder="Enter a message..."
								  required></textarea>
						<span id="send-btn">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send">
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
<script>

	function generateRandomCode(length = 5) {
		const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		let result = '';
		for (let i = 0; i < length; i++) {
			const randomIndex = Math.floor(Math.random() * chars.length);
			result += chars[randomIndex];
		}
		return result;
	}
	
	console.log(generateRandomCode());
	var hide = false;
	$('#btn-suggest').click(function() {
		$(this).toggleClass('active');
		if (hide == true) {
			$(".chatbot__box").css("padding", "10px 20px 150px");
			$('#btn-suggest span').text('Hide Suggested Suggestion');
			$('#question').show();
			hide = false;
		} else {
			$(".chatbot__box").css("padding", "10px 20px 100px");
			$('#btn-suggest span').text('Show Suggested Suggestion');
			hide = true;
			$('#question').hide();
		}
	});

	const chatbotToggle = document.querySelector('.chatbot__button');
	const sendChatBtn = document.querySelector('#send-btn');
	const chatInput = document.querySelector('.chatbot__textarea');
	const chatBox = document.querySelector('.chatbot__box');
	const chatbotCloseBtn = document.querySelector('.chatbot__header span');
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
		const chatContent = className === 'outgoing' ?
			  `<p></p>` :
		`<span class="material-symbols-outlined">smart_toy</span> <p></p>`;
		chatLi.innerHTML = chatContent;
		chatLi.querySelector('p').textContent = message;
		return chatLi;
	};
	const random_code = generateRandomCode();
	const generateResponse = (incomingChatLi) => {
		const API_URL = 'https://dpupesdm.monitoring4system.com/welcome/get_api';
		message_history.push({
			role: 'user',
			content: userMessage
		});

		const requestOptions = {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				uuid : random_code,
				model: 'llama3.1:8b',
				messages: message_history,
				stream: false
			}),
		};

		fetch(API_URL, requestOptions)
			.then(res => res.json())
			.then(data => {
			message_history.push({
				role: 'assistant',
				content: data.message.content
			});
			
			const answer = {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					uuid : random_code,
					answer: data.message.content,
				}),
			}

			
			incomingChatLi.innerHTML = `
          <span class="material-symbols-outlined">smart_toy</span>
          <p>${formatResponse(data.message.content)}</p>`;
			
			fetch("https://dpupesdm.monitoring4system.com/welcome/save_answer", answer);
		})
			.catch(error => {
			incomingChatLi.innerHTML = `
          <span class="material-symbols-outlined">smart_toy</span>
          <p class="error">Oops! Please try again!</p>`;
			console.error(error);
		})
			.finally(() => chatBox.scrollTo(0, chatBox.scrollHeight));
		//console.log(message_history);
	};

	const handleChat = () => {
		userMessage = chatInput.value.trim();
		if (!userMessage) return;
		console.log(userMessage);
		chatInput.value = '';
		chatInput.style.height = `${inputInitHeight}px`;

		chatBox.appendChild(createChatLi(userMessage, 'outgoing'));
		chatBox.scrollTo(0, chatBox.scrollHeight);

		setTimeout(() => {
			const incomingChatLi = document.createElement('li');
			incomingChatLi.classList.add('chatbot__chat', 'incoming');
			incomingChatLi.innerHTML = `
        <span class="material-symbols-outlined">smart_toy</span>
        <div class="loader-inner ball-pulse">
          <div></div>
          <div></div>
          <div></div>
	</div>`;
			chatBox.appendChild(incomingChatLi);
			chatBox.scrollTo(0, chatBox.scrollHeight);
			generateResponse(incomingChatLi);
		}, 600);
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

	chatbotToggle.addEventListener('click', () =>
								   document.body.classList.toggle('show-chatbot')
								  );

	chatbotCloseBtn.addEventListener('click', () =>
									 document.body.classList.remove('show-chatbot')
									);

	sendChatBtn.addEventListener('click', function() {
		handleChat();
	});

	function send_suggestion(pesan) {
		chatInput.value = pesan;
		handleChat();
	}
</script>
