<div class="container-xl">
	<!-- Page title -->
	<div class="page-header d-print-none">
		<div class="row g-2 align-items-center">
			<div class="col">
				<h2 class="page-title">
					<?php // echo ucfirst($this->uri->segment(1)) ?>
					Data Pos
				</h2>
			</div>
		</div>
	</div>
</div>
<a id="dlink" style="display:none;"></a>
<div class="page-body">
	<!-- Konten-->
	<div class="container-xl">
		<div class="row row-cards hide-scrollbar px-0 " >
			<div class="card">

				<div class="card-body pt-2 px-3">
					<form id="fetchForm" >
						<div class="col-12 col-xl-8 row align-items-end">
							<div class="col-12 col-md-3">
								<div class="form-group">
									<label class="form-label mt-2">Lokasi Pos</label>
									<?php 
									$this->load->helper('logger');
									$cmblogger=loggercombo();

									?>
									<select type="text" name="id_logger" class="form-select" placeholder="Cari Lokasi Pos"  id="select-pos" value="">
										<option value="">Pilih Pos</option>
										<?php foreach ($pilih_pos as $mnpos) : ?>
										<option value="<?= $mnpos['code_logger'] ?>|<?= $mnpos['nama_lokasi'] ?>"><?= str_replace('_', ' ',$mnpos['nama_lokasi']) ?></option>
										<?php endforeach ?>
									</select>


								</div>
							</div>
							<div class="col-12 col-md-3">
								<div class="form-group">
									<label class="form-label mt-2">Dari</label>
									<input class="form-control" name="awal" placeholder="Dari" id="awal_new" autocomplete="off" required/>
								</div>
							</div>
							<div class="col-12 col-md-3">
								<div class="form-group">
									<label class="form-label mt-2">Sampai</label>
									<input class="form-control" name="akhir" placeholder="Sampai" id="akhir_new"  autocomplete="off" required/>
								</div>
							</div>
							<div class="col-6 col-md-auto d-flex align-items-end mt-3 mt-md-0">
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
							<div class="col-6 col-md-auto d-flex align-items-end mt-3 mt-md-0">
								<button type="button" id="btn-export" class="btn btn-success w-100" >Download<span class="spinner-border spinner-border-sm ms-2" style="display:none" role="status"></span></button> 

							</div>

						</div>
					</form>
				</div>
			</div>
			<?php	echo form_close();?>
			<div class="card">
				<div class="card-header pb-2 pt-3"><h3 class="mb-0" id="head-title"></h3></div>
				<div class="card-body px-3">
					<div class="table-responsive">
						<table class="table table-bordered" id="tabel">
							<thead></thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- end Konten-->
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body px-3 py-3">
				<div class="mb-2 text-center">
					<span class="badge badge-soft me-2">Status: <span id="statusText">idle</span></span>
				</div>

				<div class="progress mt-3" role="progressbar" aria-label="Progress minggu" style="height:30px">
					<div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var tmp;
	function strip(html) {
		tmp = document.createElement("DIV");
		tmp.innerHTML = html;
		console.log(tmp.innerText);
		console.log(tmp.textContent);

		return tmp.textContent || tmp.innerText || "";
	}
	var tableToExcel = (function() {
		var uri = 'data:application/vnd.ms-excel;base64,',
			template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
			base64 = function(s) {
				return window.btoa(unescape(encodeURIComponent(s)))
			},
			format = function(s, c) {
				return s.replace(/{(\w+)}/g, function(m, p) {
					return c[p];
				})
			}
		return function(table, name, filename) {
			if (!table.nodeType) 
				table = $('#'+table).clone();

			var hyperLinks = table.find('a');
			for (i = 0; i < hyperLinks.length; i++) {

				var sp1 = document.createElement("span");
				var sp1_content = document.createTextNode($(hyperLinks[i]).text());
				sp1.appendChild(sp1_content);
				var sp2 = hyperLinks[i];
				var parentDiv = sp2.parentNode;
				parentDiv.replaceChild(sp1, sp2);
			}

			var ctx = {
				worksheet: name || 'Worksheet',
				table: table[0].innerHTML
			}


			document.getElementById("dlink").href = uri + base64(format(template, ctx));
			document.getElementById("dlink").download = filename;
			document.getElementById("dlink").click();

		}
	})()
</script>
<script>
	// @formatter:off
	document.addEventListener("DOMContentLoaded", function () {
		var el;
		window.TomSelect && (new TomSelect(el = document.getElementById('select-pos'), {
			copyClassesToDropdown: false,
			dropdownClass: 'dropdown-menu ts-dropdown',
			optionClass:'dropdown-item',
			controlInput: '<input>',
			render:{
				item: function(data,escape) {
					if( data.customProperties ){
						return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
					}
					return '<div>' + escape(data.text) + '</div>';
				},
				option: function(data,escape){
					if( data.customProperties ){
						return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
					}
					return '<div>' + escape(data.text) + '</div>';
				},
			},
		}));
	});
	$( function() {
		$('#awal_new').datetimepicker(
			{
				timepicker:false,
				format:'Y-m-d',
			});
		$('#akhir_new').datetimepicker(
			{
				timepicker:false,
				format:'Y-m-d',
			});
	} );
	// @formatter:on
</script>
<script>
	// Pastikan jQuery sudah dimuat


	(() => {

		const API_BASE = "https://demo.beacontelemetry.com/go/fetch_progress";

		const $form        = $('#fetchForm');

		const $btnCancel   = $('#btnCancel');
		const $statusText  = $('#statusText');
		const $weekIdxEl   = $('#weekIdx');
		const $weekTotalEl = $('#weekTotal');
		const $progressBar = $('#progressBar');
		const $dataCountEl = $('#dataCount');

		let jqXHR = null;
		let totalWeeksKnown = 0;

		let lastRowsForExport = [];
		
		const exportBtn = document.getElementById('btn-export');
		
		async function downloadExcel({ url = '/export_excel', title = 'Data', rows = [], parameter = null, btn }) {
			if (!Array.isArray(rows) || rows.length === 0) {
				alert('Tidak ada data untuk diexport.');
				return;
			}

			// ---- Tampilkan loading di button ----
			$('.spinner-border').show();
			exportBtn.disabled = true;

			try {
				const fd = new FormData();
				fd.append('title', title);
				fd.append('data', JSON.stringify(rows));
				if (parameter) {
					fd.append('parameter', JSON.stringify(parameter));
				}

				const resp = await fetch(url, { method: 'POST', body: fd });
				if (!resp.ok) {
					const text = await resp.text().catch(() => '');
					throw new Error('Export error: ' + text);
				}

				const blob = await resp.blob();
				const cd = resp.headers.get('Content-Disposition') || '';
				const m = cd.match(/filename="?([^"]+)"?/i);
				const filename = m ? m[1] : (title.replace(/[^\w\- ]/g, '_') + '.xlsx');

				const href = URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.href = href;
				a.download = filename;
				document.body.appendChild(a);
				a.click();
				a.remove();
				setTimeout(() => URL.revokeObjectURL(href), 2000);
			} catch (err) {
				console.error(err);
				alert('Gagal export Excel.');
			} finally {
				// ---- Balikkan button ke normal ----
				$('.spinner-border').hide();
				exportBtn.disabled = false;
			}
		}
		function appendLog(obj) {
			const line = (typeof obj === 'string') ? obj : JSON.stringify(obj);
		}

		function setProgress(current, total) {
			$weekIdxEl.text(current);
			$weekTotalEl.text(total);
			const pct = total > 0 ? Math.round((current / total) * 100) : 0;
			$progressBar.css('width', pct + "%").text(pct + "%");
		}

		function resetUI() {
			$statusText.text("connecting...");
			setProgress(0, 0);
			$dataCountEl.text("0");
			$btnCancel.prop('disabled', false);
			totalWeeksKnown = 0;
		}

		function buildURL(params) {
			const qs = $.param(params);
			return API_BASE + "?" + qs;
		}

		function processEvent(evt) {
			appendLog(evt);

			if (evt._event === "start") {
				totalWeeksKnown = Number(evt.total_weeks || 0);
				setProgress(0, totalWeeksKnown);
				$statusText.text("started");
			}

			if (evt._event === "progress") {
				const idx       = Number(evt.idx || 0);
				const total     = Number(evt.total || 0);
				const collected = Number(evt.rows_collected || 0);

				// simpan total terbaru jika server memperbarui
				if (total > 0) totalWeeksKnown = total;

				setProgress(idx, totalWeeksKnown);
				$dataCountEl.text(String(collected));
				$statusText.text(`Fetching Data`);
			}

			if (evt._event === "week_error") {
				$statusText.text("week error (lihat log)");
			}

			if (evt._event === "complete") {

				$statusText.text("complete");
				$dataCountEl.text(String(evt.total_rows || 0));
				try {
					const pretty = JSON.stringify(evt.data ?? [], null, 2);
					$('#exampleModal').modal('hide');
					renderTableFromData(pretty, '#tabel');
					var nama_pos = $('#select-pos').val().split('|')[1];
					var awal      = $('input[name="awal"]').val();
					var akhir      = $('input[name="akhir"]').val();
					$('#head-title').text('Data '+ nama_pos +' dari '+awal+ ' sampai '+akhir);
				} catch {

				}
				setProgress(totalWeeksKnown, totalWeeksKnown);
			}
		}
		
		
		function renderTableFromData(input, tableSelector = '#tabel') {
			let rows;
			try {
				if (Array.isArray(input)) {
					rows = input;
				} else if (typeof input === 'string') {
					const parsed = JSON.parse(input);
					rows = Array.isArray(parsed) ? parsed : (Array.isArray(parsed?.data) ? parsed.data : []);
				} else if (typeof input === 'object' && input !== null) {
					rows = Array.isArray(input.data) ? input.data : [];
				} else {
					rows = [];
				}
			} catch {
				console.error('JSON parse gagal');
				rows = [];
			}

			// simpan untuk export
			lastRowsForExport = rows;

			if (!rows || rows.length === 0) {
				$(tableSelector + ' thead').html('<tr><th>Tidak ada data</th></tr>');
				$(tableSelector + ' tbody').empty();
				return;
			}

			let keys = Object.keys(rows[0]);
			const hasWaktu = keys.includes('waktu') || keys.includes('Waktu');
			if (hasWaktu) {
				const waktuKey = keys.includes('waktu') ? 'waktu' : 'Waktu';
				keys = [waktuKey, ...keys.filter(k => k !== waktuKey)];
			}

			const escapeHtml = (s) =>
			String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));

			const isNumericLike = (v) => {
				if (v === null || v === '' || typeof v === 'boolean') return false;
				const n = Number(v);
				return Number.isFinite(n);
			};

			const theadHtml = '<tr>' + keys.map(k => `<th>${escapeHtml(k)}</th>`).join('') + '</tr>';
			$(tableSelector + ' thead').html(theadHtml);

			const waktuKeys = new Set(['waktu','Waktu']);
			const tbodyRows = rows.map(row => {
				const tds = keys.map(k => {
					const val = row[k] ?? '';
					if (!waktuKeys.has(k) && isNumericLike(val)) {
						return `<td>${Number(val).toFixed(3)}</td>`;
					}
					return `<td>${escapeHtml(val)}</td>`;
				});
				return `<tr>${tds.join('')}</tr>`;
			});
			$(tableSelector + ' tbody').html(tbodyRows.join(''));
			
		}

		function streamNDJSON(url) {
			let buffer = "";
			let lastIndex = 0;

			jqXHR = $.ajax({
				url: url,
				method: 'GET',
				headers: { 'Accept': 'application/x-ndjson' },
				// Agar bisa proses streaming chunk demi chunk
				xhr: function () {
					const xhr = new window.XMLHttpRequest();
					try { xhr.overrideMimeType('text/plain; charset=UTF-8'); } catch(e) {}

					xhr.addEventListener('progress', function () {
						// ambil tambahan teks baru sejak lastIndex
						const resp = xhr.responseText || "";
						const newText = resp.substring(lastIndex);
						lastIndex = resp.length;

						buffer += newText;
						const lines = buffer.split("\n");
						buffer = lines.pop() || ""; // sisakan partial terakhir

						for (const line of lines) {
							if (!line.trim()) continue;
							try {
								const evt = JSON.parse(line);
								processEvent(evt);
							} catch {
								// bukan JSON valid (mungkin log plain dari server)
								appendLog(line);
							}
						}

						// set status saat mulai menerima data
						if ($statusText.text() === "connecting...") {
							$statusText.text("streaming...");
						}
					});

					xhr.addEventListener('readystatechange', function () {
						// ketika selesai (DONE), flush sisa buffer jika valid
						if (xhr.readyState === 4) {
							if (buffer.trim()) {
								try {
									const evt = JSON.parse(buffer);
									processEvent(evt);
								} catch {
									appendLog(buffer);
								}
							}
						}
					});

					return xhr;
				},
				beforeSend: () => {
					resetUI();
				}
			})
				.done(() => {
				// Jika belum "complete" dari server, tandai ended
				if ($statusText.text() !== "complete") {
					$statusText.text("ended");
				}
			})
				.fail((jq, textStatus, err) => {
				if (textStatus === "abort") {
					$statusText.text("canceled");
					appendLog("Request dibatalkan oleh user.");
				} else {
					$statusText.text("aborted/failed");
					appendLog(`ERR: ${textStatus} ${err || ''}`);
					if (jq && jq.status) appendLog(`HTTP ${jq.status}`);
				}
			})
				.always(() => {
				$btnCancel.prop('disabled', true);
				jqXHR = null;
			});
		}

		$form.on('submit', function (e) {
			e.preventDefault();
			$('#exampleModal').modal('show');
			if (jqXHR) jqXHR.abort();

			const fd = new FormData(this);
			const id_logger = fd.get('id_logger').split("|")[0];
			
			const awal      = fd.get('awal');
			const akhir     = fd.get('akhir');

			const url = buildURL({ id_logger, awal, akhir });
			streamNDJSON(url);
			
		});
		exportBtn?.addEventListener('click', () => {
			// (opsional) bentuk parameter sendiri, misalnya dari kolom yang aktif:
			// Jika tidak perlu, hapus "parameter" dan biarkan server yang generate.
			let parameter = null;
			if (lastRowsForExport.length) {
				const keys = Object.keys(lastRowsForExport[0]).filter(k => k.toLowerCase() !== 'waktu');
				parameter = keys.map(k => ({
					alias_sensor: k,
					satuan: '',          // isi kalau ada
					field_sensor: k
				}));
			}
			//console.log($form);
			var nama_pos = $('#select-pos').val().split('|')[1];
			var awal      = $('input[name="awal"]').val();
			var akhir      = $('input[name="akhir"]').val();
			//$('#head-title').text('Data '+ nama_pos +' dari '+awal+ ' sampai '+akhir);
			downloadExcel({
				url: '<?= site_url("datapos/excel_export"); ?>', // sesuaikan route CI kamu
				title: 'Data '+ nama_pos +' dari '+awal+ ' sampai '+akhir,
				rows: lastRowsForExport,
				parameter,
				exportBtn
			});
		});
		
		$btnCancel.on('click', function () {
			if (jqXHR) {
				jqXHR.abort();
			}
		});
	})();
</script>