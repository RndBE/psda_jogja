<?php
$idlogger = $this->session->userdata('idlogger');
$tabel_livedata = isset($tabel_livedata) && $tabel_livedata ? $tabel_livedata : ($this->session->userdata('tabel') ?: 'weather_station');
$back_url = isset($back_url) && $back_url ? $back_url : '';
$exclude_alias = isset($exclude_alias) && is_array($exclude_alias) ? $exclude_alias : array();
$mqtt_broker = isset($mqtt_broker) && $mqtt_broker ? $mqtt_broker : 'mqtt.beacontelemetry.com';
$exclude_set_debit = isset($exclude_set_debit) ? (bool) $exclude_set_debit : false;
$log_label = isset($log_label) && $log_label ? $log_label : 'LiveData';
$ambilwaktu = mktime(date("H") - 1, date("i"), 0, date("m"), date("d"), date("Y"));
$ambilwaktu2 = date("Y-m-d H:i", $ambilwaktu);

$this->db->where('logger_code', $idlogger);
if ($exclude_set_debit) {
	$this->db->where('set_debit !=', '1');
}
if (!empty($exclude_alias)) {
	$this->db->where_not_in('alias_sensor', $exclude_alias);
}
$this->db->order_by('CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)', 'ASC', false);
$query_sensor = $this->db->get('t_sensor');

$series_data = array();
$sensor_configs = array();
foreach ($query_sensor->result() as $sensor) {
	$series_data[$sensor->field_sensor] = array();
	$tipe_grafik = ($sensor->field_sensor == 'sensor9' || $sensor->field_sensor == 'sensor8') ? 'column' : 'spline';
	if (isset($sensor->tipe_graf) && !empty($sensor->tipe_graf)) {
		$tipe_grafik = $sensor->tipe_graf;
	}
	$sensor_configs[] = array(
		'id' => $sensor->id,
		'field' => $sensor->field_sensor,
		'alias' => $sensor->alias_sensor,
		'label' => str_replace('_', ' ', $sensor->alias_sensor),
		'satuan' => $sensor->satuan,
		'tipe_grafik' => $tipe_grafik,
		'container' => 'container-live-' . $sensor->id,
	);
}

$query_data = $this->db
	->where('code_logger', $idlogger)
	->where('waktu >=', $ambilwaktu2)
	->order_by('waktu', 'asc')
	->get($tabel_livedata);

$color = 'red';
$status_logger = 'Koneksi Terputus';
foreach ($query_data->result() as $dtsen) {
	$tahun = date('Y', strtotime($dtsen->waktu));
	$bulan = date('m', strtotime($dtsen->waktu));
	$hari = date('d', strtotime($dtsen->waktu));
	$jam = date('H', strtotime($dtsen->waktu));
	$menit = date('i', strtotime($dtsen->waktu));
	foreach ($sensor_configs as $sensor) {
		$field = $sensor['field'];
		if (isset($dtsen->$field)) {
			$series_data[$field][] = array(
				gmmktime((int) $jam, (int) $menit, 0, (int) $bulan, (int) $hari, (int) $tahun) * 1000,
				(float) $dtsen->$field,
			);
		}
	}
	if ($dtsen->waktu >= $ambilwaktu2) {
		$color = 'green';
		$status_logger = 'Koneksi Terhubung';
	}
}

foreach ($sensor_configs as &$sensor_config) {
	$field = $sensor_config['field'];
	$sensor_config['data'] = $series_data[$field];
}
unset($sensor_config);
?>

<script type="text/javascript">
	var MQTTbroker = '<?= $mqtt_broker ?>';
	var MQTTport = 8083;
	var MQTTsubTopic = '<?= $idlogger ?>';
	var sensorConfigs = <?= json_encode($sensor_configs) ?>;
	var logLabel = <?= json_encode($log_label) ?>;
	var charts = {};
	var dataTopics = [];
	var client = new Paho.MQTT.Client(MQTTbroker, MQTTport, "clientid_" + parseInt(Math.random() * 100, 10));

	client.onMessageArrived = onMessageArrived;
	client.onConnectionLost = onConnectionLost;

	var options = {
		timeout: 3,
		useSSL: true,
		userName: "userlog",
		password: "b34c0n",
		onSuccess: function() {
			console.log("mqtt connected");
			client.subscribe(MQTTsubTopic, {qos: 1});
		},
		onFailure: function() {}
	};

	function onConnectionLost(responseObject) {}

	function getPayloadTime(dataLogObj) {
		var tanggal = dataLogObj.waktu || ((dataLogObj.tanggal || '') + ' ' + (dataLogObj.jam || ''));
		var match = String(tanggal).match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?/);
		if (match) {
			return Date.UTC(
				Number(match[1]),
				Number(match[2]) - 1,
				Number(match[3]),
				Number(match[4]),
				Number(match[5]),
				Number(match[6] || 0)
			);
		}

		return new Date(tanggal).getTime();
	}

	function onMessageArrived(message) {
		var chartno;
		var dataLogObj = JSON.parse(message.payloadString);
		var waktu = getPayloadTime(dataLogObj);
		var addedPoints = [];

		if (dataTopics.indexOf(message.destinationName) < 0) {
			dataTopics.push(message.destinationName);
			chartno = dataTopics.indexOf(message.destinationName);
		} else {
			chartno = dataTopics.indexOf(message.destinationName);
		}

		sensorConfigs.forEach(function(sensor) {
			var value = dataLogObj[sensor.field];
			var numericValue = Number(value);
			if (!isNumber(numericValue) || !charts[sensor.id]) {
				return;
			}
			var series = charts[sensor.id].series[chartno] || charts[sensor.id].series[0];
			var shift = series.data.length > 5;
			series.addPoint([waktu, numericValue], true, shift);
			addedPoints.push({
				sensor: sensor.label,
				field: sensor.field,
				value: numericValue,
				totalPoints: series.data.length
			});
		});

		console.log('[' + logLabel + '] Data masuk', {
			topic: message.destinationName,
			waktu: isNumber(waktu) ? new Date(waktu).toISOString() : null,
			payload: dataLogObj,
			points: addedPoints
		});
	}

	function isNumber(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}

	function init() {
		Highcharts.setOptions({
			time: {
				useUTC: false
			}
		});
		client.connect(options);
	}

	$(document).ready(function() {
		sensorConfigs.forEach(function(sensor) {
			charts[sensor.id] = new Highcharts.Chart({
				chart: {
					renderTo: sensor.container,
					zoomType: 'xy',
					defaultSeriesType: sensor.tipe_grafik,
					styledMode: true
				},
				title: {
					text: sensor.label
				},
				subtitle: {
					text: ' '
				},
				xAxis: [{
					type: 'datetime',
					dateTimeLabelFormats: {
						millisecond: '%H:%M',
						second: '%H:%M',
						minute: '%H:%M',
						hour: '%H:%M',
						day: '%e. %b %y',
						week: '%e. %b %y',
						month: '%b \'%y',
						year: '%Y'
					},
					crosshair: true
				}],
				yAxis: [{
					minPadding: 0.2,
					maxPadding: 0.2,
					title: {
						text: sensor.label
					}
				}],
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true,
							shape: 'square',
							backgroundColor: 'rgba(252, 255, 197, 0.7)',
							borderWidth: 2,
							borderRadius: 5,
							borderColor: '#AAA',
							padding: 5,
							y: -10,
							align: 'top',
							style: {
								fontWeight: 'bold'
							},
							formatter: function() {
								var seriesPoints = this.series.points;
								if (this.point === seriesPoints[seriesPoints.length - 1]) {
									return 'Waktu : ' + Highcharts.dateFormat('%H:%M %d-%m-%Y', this.x) + '<br>' + sensor.label + ' : ' + this.y + ' ' + sensor.satuan;
								}
							}
						}
					}
				},
				tooltip: {
					xDateFormat: 'Tanggal %d-%m-%Y %H:%M',
					shared: true
				},
				credits: {
					enabled: false
				},
				series: [{
					name: sensor.label,
					data: sensor.data,
					tooltip: {
						valueSuffix: ' ' + sensor.satuan,
						valueDecimals: 3
					}
				}]
			});
		});
	});
</script>

<div class="container-md">
	<div class="page-header d-print-none">
		<div class="row g-3 align-items-center">
			<?php if ($back_url) { ?>
			<div class="col-auto">
				<?= anchor($back_url, '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-big-left-lines" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 15v3.586a1 1 0 0 1 -1.707 .707l-6.586 -6.586a1 1 0 0 1 0 -1.414l6.586 -6.586a1 1 0 0 1 1.707 .707v3.586h3v6h-3z"></path><path d="M21 15v-6"></path><path d="M18 15v-6"></path></svg>') ?>
			</div>
			<?php } ?>
			<div class="col-auto">
				<span class="status-indicator status-<?= $color ?> status-indicator-animated">
					<span class="status-indicator-circle"></span>
					<span class="status-indicator-circle"></span>
					<span class="status-indicator-circle"></span>
				</span>
			</div>
			<div class="col">
				<h2 class="page-title">
					<?= $this->session->userdata('namalokasi'); ?>
				</h2>
				<div class="text-muted">
					<ul class="list-inline list-inline-dots mb-0">
						<li class="list-inline-item"><span class="text-<?= $color ?>"><?= $status_logger ?></span></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">
			<?php foreach ($sensor_configs as $sensor) { ?>
				<div class="col-md-6">
					<div class="card">
						<div class="card-body">
							<h3 class="card-title"> </h3>
							<div id="<?= $sensor['container'] ?>"></div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<script src="<?= base_url(); ?>js/highcharts.js"></script>
<script src="<?= base_url(); ?>js/modules/data.js"></script>
<script src="<?= base_url(); ?>js/modules/exporting.js"></script>
<script src="<?= base_url(); ?>js/highcharts-more.js"></script>
<script src="<?= base_url(); ?>js/themes/grid.js"></script>
<script src="<?= base_url(); ?>js/modules/no-data-to-display.js"></script>
