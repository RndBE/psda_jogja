<?php 
$ambilwaktu = mktime(date("H")-1, date("i"), 0, date("m"), date("d"), date("Y"));
$ambilwaktu2=date("Y-m-d H:i",$ambilwaktu);
$query_sensor=$this->db->query('select * from t_sensor where logger_code ="'.$this->session->userdata('idlogger').'" and set_debit != "1"  and alias_sensor != "Curah_Hujan" order by CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)');
foreach($query_sensor->result() as $vardata)
{
	${'data_'.$vardata->field_sensor}=array();
}

if($this->session->userdata('idlogger') == '10114'){
	$query_data=$this->db->query('select * from weather_station where code_logger ="'.$this->session->userdata('idlogger').'" and waktu >= "'.$ambilwaktu2.'"');
}else{
	$query_data=$this->db->query('select * from awlr where code_logger ="'.$this->session->userdata('idlogger').'" and waktu >= "'.$ambilwaktu2.'"');
}

foreach($query_data->result() as $dtsen )
{
	$tahun=date('Y',strtotime($dtsen->waktu));
	$bulan=date('m',strtotime($dtsen->waktu));
	$hari=date('d',strtotime($dtsen->waktu));
	$jam=date('H',strtotime($dtsen->waktu));
	$menit=date('i',strtotime($dtsen->waktu));
	foreach($query_sensor->result() as $vardata2)
	{
		$kolom=$vardata2->field_sensor ;
		${'data_'.$kolom}[]="[ Date.UTC(".$tahun.",".$bulan."-1,".$hari.",".$jam.",".$menit."),". $dtsen->$kolom ."]";
	}
	
	
	$waktuterakhir=$dtsen->waktu;
	if($waktuterakhir >= $ambilwaktu2)
	{
		$color="green";
		$status_logger="Koneksi Terhubung";
	}
	else{
		$color="red";
		$status_logger="Koneksi Terputus";
	}
	//$data_sensor1[] ="[ Date.UTC(".$tahun.",".$bulan."-1,".$hari.",".$jam."-7,".$menit."),". $dtsen->sensor1  ."]";
}

?>


<script type="text/javascript"> 
	var MQTTbroker = 'mqtt.beacontelemetry.com';
	var MQTTport = 8083;
	var MQTTsubTopic = '<?php echo $this->session->userdata('idlogger') ?>';
	<?php
	foreach($query_sensor->result() as $var)
	{

		echo "var chart".$var->alias_sensor.";";
	}
	?>

	var dataTopics = new Array();
	var client = new Paho.MQTT.Client(MQTTbroker, MQTTport,
									  "clientid_" + parseInt(Math.random() * 100, 10));
	client.onMessageArrived = onMessageArrived;
	client.onConnectionLost = onConnectionLost;


	var options = {
		timeout: 3,
		useSSL: true,
		userName : "userlog",
		password : "b34c0n",

		onSuccess: function () {
			console.log("mqtt connected");
			client.subscribe(MQTTsubTopic, {qos: 1});
		},
		onFailure: function (message) {
		}
	};

	function onConnectionLost(responseObject) {
	};
	function onMessageArrived(message) {
		if (dataTopics.indexOf(message.destinationName) < 0){
			dataTopics.push(message.destinationName);
			var y = dataTopics.indexOf(message.destinationName);
			var dataLog = message.payloadString;
			var dataLogObj = JSON.parse(dataLog);
			var tanggal=dataLogObj.tanggal+" "+dataLogObj.jam;
			var jam=dataLogObj.jam;

			var tahun = new Date(tanggal).getFullYear();
			var bulan = new Date(tanggal).getMonth()
			var hari = new Date(tanggal).getDate();
			var jam1 = new Date(tanggal).getHours();
			var menit = new Date(tanggal).getMinutes();

			var waktu = new Date(tahun,bulan,hari,jam1+7,menit,0).getTime();

			<?php 
			foreach($query_sensor->result() as $sensor) 
			{
				echo "var ".$sensor->field_sensor ."= dataLogObj.".$sensor->field_sensor.";";
				echo "var newseries".$sensor->id." = {
                  id: y,
				  name: '".$sensor->alias_sensor ."',//nama Series
                  data: []
                  };

        chart".$sensor->alias_sensor.".redraw(newseries".$sensor->id.");

		  ";
			}
			?>
		};

		var y = dataTopics.indexOf(message.destinationName);
		var dataLog = message.payloadString;
		var dataLogObj = JSON.parse(dataLog);
		var tanggal=dataLogObj.tanggal+' '+dataLogObj.jam;
		var jam=dataLogObj.jam;

		var tahun = new Date(tanggal).getFullYear();
		var bulan = new Date(tanggal).getMonth()
		var hari = new Date(tanggal).getDate();
		var jam1 = new Date(tanggal).getHours();
		var menit = new Date(tanggal).getMinutes();

		var waktu = new Date(tahun,bulan,hari,jam1+7,menit,0).getTime();
		<?php 
		foreach($query_sensor->result() as $sensor2) 
		{
			echo 'var '.$sensor2->field_sensor .'= dataLogObj.'.$sensor2->field_sensor.';';
			echo '
     var plotMqtt'.$sensor2->id.' = [waktu, Number('.$sensor2->field_sensor .')];

      if (isNumber('.$sensor2->field_sensor .')) { 
        plot'.$sensor2->id .'(plotMqtt'.$sensor2->id .', y);	//send it to the plot function
      };';
		}
		?>
	};

	function isNumber(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	};
	function init() {
		Highcharts.setOptions({

			time: {
				useUTC: false
			}

		});


		client.connect(options);
	};

	<?php
	foreach($query_sensor->result() as $plotchart) 
	{
		echo "
      function plot".$plotchart->id."(point, chartno) {
        //console.log(point);

			   var series".$plotchart->id." = chart".$plotchart->alias_sensor.".series[0],
                shift = series".$plotchart->id.".data.length > 5; // shift if the series is
                                                 // longer than 20

            chart".$plotchart->alias_sensor.".series[chartno].addPoint(point, true, shift);
		  };"; }?>
	//settings for the chart
	$(document).ready(function() {
		<?php 
		foreach($query_sensor->result() as $sensor3) 
		{
			echo "

        chart".$sensor3->alias_sensor." = new Highcharts.Chart({
            chart: {
                renderTo: 'container".$sensor3->alias_sensor."',
				zoomType :'xy',
                defaultSeriesType: 'spline',
				styledMode: true,
            },
            title: {
                text: '".str_replace('_', ' ',$sensor3->alias_sensor)."'
            },
            subtitle: {
                                  text: ' '
                          },
            xAxis: [{

				type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
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
                    text: '".$sensor3->alias_sensor."',

                }
            }],
			  plotOptions: {
						series: {
						  dataLabels: {
							enabled: true,
							shape:'square',
							backgroundColor: 'rgba(252, 255, 197, 0.7)',
							borderWidth: 2,
							borderRadius: 5,
							borderColor: '#AAA',
							padding: 5,
							y: -10,
							align:'top',
							style: {
										fontWeight: 'bold'
									},
							formatter: function() {
							  var seriesPoints = this.series.points;
							  if (this.point === seriesPoints[seriesPoints.length - 1]) {
								return 'Waktu : '+Highcharts.dateFormat('%H:%M %d-%m-%Y',this.x)+'<br>".$sensor3->alias_sensor." : '+this.y +' ".$sensor3->satuan."';
							  }
							}
						  }
						}
					  },
			tooltip: {
             xDateFormat: 'Tanggal %d-%m-%Y %H:%M',
            shared: true
        }, credits: {
                enabled: false
            },
            series: [ {
            name: '".$sensor3->alias_sensor."',
            data: [".join(${"data_".$sensor3->field_sensor},',') ."],

            tooltip: {
                valueSuffix: ' ".$sensor3->satuan."',
                 valueDecimals: 3,
            }
         }]
        });";

		}
		?>
	});

</script>

<?php 
$query_lokasi=$this->db->query('select * from t_logger inner join t_lokasi ON t_lokasi.id_lokasi=t_logger.lokasi_id where code_logger="'.$this->session->userdata('idlogger').'"');
foreach($query_lokasi->result() as $lokasi)
{
	$lokasi_logger=$lokasi->nama_lokasi;
}

?>


<div class="container-md">
	<div class="page-header d-print-none">
		<div class="row g-3 align-items-center">
			<div class="col-auto">
				<?php echo anchor('awlr/analisa','<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-big-left-lines" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                 <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                 <path d="M12 15v3.586a1 1 0 0 1 -1.707 .707l-6.586 -6.586a1 1 0 0 1 0 -1.414l6.586 -6.586a1 1 0 0 1 1.707 .707v3.586h3v6h-3z"></path>
                 <path d="M21 15v-6"></path>
                 <path d="M18 15v-6"></path>
              </svg>
') ?>
			</div>
			<div class="col-auto">
				<span class="status-indicator status-<?php echo $color?> status-indicator-animated">
					<span class="status-indicator-circle"></span>
					<span class="status-indicator-circle"></span>
					<span class="status-indicator-circle"></span>
				</span>
			</div>
			<div class="col">
				<h2 class="page-title">
					<?php echo $this->session->userdata('namalokasi'); ?>
				</h2>
				<div class="text-muted">
					<ul class="list-inline list-inline-dots mb-0">
<li class="list-inline-item"><span class="text-<?php echo $color?>"><?php echo $status_logger ?></span></li>
					</ul>
				</div>
			</div>

		</div>
	</div>
</div>


<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">
			<div class="col-md-12">
				<div class="row row-cards">
					<?php foreach($query_sensor->result() as $sensor4) { ?>

					<div class="col-md-6">
						<div class="card">
							<div class="card-body">
								<h3 class="card-title"> </h3>

								<div id="container<?= $sensor4->alias_sensor ?>" ></div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div> 



<script src="<?php echo base_url();?>js/highcharts.js"></script>
<script src="<?php echo base_url();?>js/modules/data.js"></script>
<script src="<?php echo base_url();?>js/modules/exporting.js"></script>
<script src="<?php echo base_url();?>js/highcharts-more.js"></script>
<script src="<?php echo base_url();?>js/themes/grid.js"></script> 
<!--<script src="<?php echo base_url();?>js/themes/dark-unica.js"></script> -->
<script src="<?php echo base_url();?>js/modules/no-data-to-display.js"></script>

