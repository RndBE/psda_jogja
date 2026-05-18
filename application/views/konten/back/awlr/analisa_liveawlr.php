<?php
$this->load->view('konten/back/shared/analisa_livedata', array(
	'tabel_livedata' => $this->session->userdata('idlogger') == '10114' ? 'weather_station' : 'awlr',
	'back_url' => 'awlr/analisa',
	'exclude_alias' => array('Curah_Hujan'),
	'exclude_set_debit' => true,
	'mqtt_broker' => 'mqtt.beacontelemetry.com',
	'log_label' => 'LiveData AWLR',
));
