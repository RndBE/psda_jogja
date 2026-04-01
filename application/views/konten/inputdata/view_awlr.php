<?php
	$query_inf=$this->db->query('select * from t_informasi where logger_id = "'.$this->session->userdata('log_awlr').'"');
	foreach($query_inf->result() as $inf)
	{
		$sn = $inf->serial_number ;
	}

?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PUSDA-Jatim-AWLR</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

</head>
<body class="text-bg-light px-3">
	<main>
		<h3 class="fw-bold mb-3 mt-3 text-center">Data Input AWLR</h3> 
				<hr/>
		<div class="container-md px-3">
			<section class="mb-3"> 
				
				<div class="row gx-md-3 justify-content-center "> 
					<div class="col-lg-4"> 
						<ul class="list-group list-group-item-primary p-0">
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span class="fw-bold">ID Logger</span>
								<span><?= $this->session->userdata('log_awlr') ?></span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span class="fw-bold">Tanggal</span>
								<span><?= $this->session->userdata('tgl_awlr') ?></span>
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span class="fw-bold">Serial Number</span>
								<span><?= (isset($sn)) ? $sn : '-' ?></span>
							</li>
						</ul>
					</div> 
					<div class="col-lg-5 "> 
						<div class="card mb-2" >
							<div class="card-body ">
								<!--<form class="row g-3">-->
								<?php echo form_open('datamasuk/sesi_loggerawlr','class="row g-3 align-items-center "');?>
								<div class="col-3 ">
									<label class="text-md-start">ID Logger</label>
								</div>
								<div class="col-6">

									<input type="text" name="logger_id" class="form-control form-control-sm">
								</div>
								<div class="col-3">
									<div class="d-grid gap-2">
										<input class="btn btn-primary btn-sm" name="btnlog" value="Cari" type="submit"/>
									</div>
								</div>
								<!--</form>-->
								<?php echo form_close();?>
							</div>
						</div>	
						<div class="card mb-2">
							<div class="card-body justify-content-between align-items-center">
								<!--<form class="row g-3">-->
								<?php echo form_open('datamasuk/tgl_awlr','class="row g-3"');?>
								<div class="col-3">
									<label class="text-md-start">Tanggal</label>
								</div>
									<div class="col-6">
										
										<input type="date" id="tgl" name="tgl" class="form-control form-control-sm">
									</div>
									<div class="col-3">
										<div class="d-grid gap-2">
										<input class="btn btn-primary btn-sm" name="btntgl" value="Cari" type="submit"/>
										</div>
									</div>
								<!--</form>-->
								<?php echo form_close();?>
							</div>
						</div>
					
					</div> 
					
					<div class="col-lg-3 "> 
						<?php echo form_open('datamasuk/data_awlr');?>
						<div class="d-grid gap-2 col-5  mx-auto">
							<button class="btn btn-primary btn-sm" name="btnrefresh" type="submit"><div class="d-flex justify-content-center"><span class="material-symbols-outlined mx-2" >refresh</span> Refresh</div></button>

						</div>
						<?php echo form_close();?>
					</div> 
			
				</div> 
			</section>
	
		</div>
	</main>
	<div class="container-fluid px-3">
		<div class="table-responsive">
			<table class="table table-sm table-bordered"> 
				<thead class="table-secondary fw-bold">
					<tr>
						<td class="text-nowrap">Id Logger</td>
						<td>Waktu</td>
						<td>Sensor1</td>
						<td>Sensor2</td>
						<td>Sensor3</td>
						<td>Sensor4</td>
						<td>Sensor5</td>
						<td>Sensor6</td>
						<td>Sensor7</td>
						<td>Sensor8</td>
						<td>Sensor9</td>
						<td>Sensor10</td>
						<td>Sensor11</td>
						<td>Sensor12</td> 
						<td>Sensor13</td>
						<td>Sensor14</td>
						<td>Sensor15</td>
						<td>Sensor16</td>
						<td colspan="2"><center>Aksi</center></td>
					</tr>
				</thead>
				<tbody class="h6">
				<?php      
				foreach($data_awlr->result() as $row){
				?>
				<tr>
					<td><?php echo $row->code_logger ?></td>
					<td class="text-nowrap"><?php echo $row->waktu ?></td>
					<td><?php echo $row->sensor1 ?></td>
					<td><?php echo $row->sensor2 ?></td>
					<td><?php echo $row->sensor3 ?></td>
					<td><?php echo $row->sensor4 ?></td>
					<td><?php echo $row->sensor5 ?></td>
					<td><?php echo $row->sensor6 ?></td>
					<td><?php echo $row->sensor7 ?></td>
					<td><?php echo $row->sensor8 ?></td>
					<td><?php echo $row->sensor9 ?></td>
					<td><?php echo $row->sensor10 ?></td>
					<td><?php echo $row->sensor11 ?></td>
					<td><?php echo $row->sensor12 ?></td> 
					<td><?php echo $row->sensor13 ?></td>
					<td><?php echo $row->sensor14 ?></td>
					<td><?php echo $row->sensor15 ?></td>
					<td><?php echo $row->sensor16 ?></td>
					<td><?php echo anchor('datamasuk/edit_awlr/'.$row->id,'Sunting ','class="btn btn-outline-primary btn-sm"'); ?></td>
					<td><?php echo anchor('datamasuk/hapus_awlr/'.$row->id,' Hapus','class="btn btn-outline-danger btn-sm"'); ?></td>

				</tr>

				<?php    
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
 
</body>
</html>