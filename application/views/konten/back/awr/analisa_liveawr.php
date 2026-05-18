<?php
$this->load->view('konten/back/shared/analisa_livedata', array(
	'tabel_livedata' => $this->session->userdata('tabel') ?: 'weather_station',
	'back_url' => 'station_cuaca/analisa',
	'exclude_alias' => array(),
	'mqtt_broker' => 'mqtt.beacontelemetry.com',
	'log_label' => 'LiveData AWR',
));
