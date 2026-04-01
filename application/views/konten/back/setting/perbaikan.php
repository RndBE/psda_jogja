

<div class="col-md-9">
	
	<style>
		.circle {
			width: 12px;
			height: 12px;
			border-radius: 50%;
			box-shadow: 0px 0px 1px 1px #0000001a;
		}

		.pulse-green {
			background: green;
			animation: pulse-animation-green 2s infinite;
		}
		
		.pulse-black {
			background: black;
			animation: pulse-animation-black 2s infinite;
		}
		.pulse-brown {
			background: #876a2f;
			animation: pulse-animation-brown 2s infinite;
		}

		@keyframes pulse-animation-green {
			0% {
				box-shadow: 0 0 0 0px #5bc26d;
			}
			100% {
				box-shadow: 0 0 0 10px rgba(0, 0, 0, 0);
			}
		}
		@keyframes pulse-animation-black {
			0% {
				box-shadow: 0 0 0 0px black;
			}
			100% {
				box-shadow: 0 0 0 10px rgba(0, 0, 0, 0);
			}
		}
		@keyframes pulse-animation-brown {
			0% {
				box-shadow: 0 0 0 0px #876a2f;
			}
			100% {
				box-shadow: 0 0 0 10px rgba(0, 0, 0, 0);
			}
		}
	</style>
	<div class="card">
		<div class="card-body">
			<h2 class="mb-2 mt-2 fw-bold">Perbaikan Logger</h2>
			<hr class="mb-3 mt-0">
			<div class="table-responsive" style="overflow-x:scroll">
				<table id="example" class="cell-border" >
					<thead>
						<tr>
							<th width="10px" class="text-center">No</th>
							<th class="text-center">Nama Pos</th>
							<th class="text-center">Status</th>
							<th class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$i =1;
						foreach ($awlr_stts as $key => $val) : ?>
						<tr>
							<td class="text-center"><?= $i++ ?></td>
							<td style="white-space:nowrap">
								<div class="d-flex align-items-center">
									<?php if($val['status'] == '1'){
											echo '<div class="circle pulse-green " ></div>';
										}elseif($val['status'] == '0'){
											echo '<div class="circle pulse-black " ></div>';
										}else{
											echo '<div class="circle pulse-brown " ></div>';
										}
									?><span class="ps-3"><?= $val['nama_lokasi'] ?></span>
								</div>
								</td>
							<td><?php if($val['status'] == '1'){
									echo 'Koneksi Terhubung';
								}elseif($val['status'] == '0'){
									echo 'Koneksi Terputus';}else{
									echo 'Perbaikan';
								}
								?></td>
							<td>
								<div class="d-flex">
									<?php if($val['status'] == 'Perbaikan') { ?> 
									<button class="btn btn-outline-success me-2 py-1" data-bs-target="#hapus_perbaikan<?= $val['id_logger']?>" data-bs-toggle="modal">Selesai</button>
										
									<?php } else{ ?>
										<button class="btn btn-outline-warning me-2 py-1" data-bs-target="#perbaiki<?= $val['id_logger']?>" data-bs-toggle="modal">Perbaiki</button>
									<?php } ?> 
								</div>

							</td>
						</tr>
						<?php endforeach ?>

					</tbody>
				</table>
			</div>
		</div>

	</div>
</div>

<?php 
	$i =1;foreach ($awlr_stts as $key => $val) : ?>
<div class="modal" tabindex="-1" id="perbaiki<?= $val['id_logger']?>">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Tambah Perbaikan </h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="<?= base_url() ?>pengaturan/tambah_perbaikan/<?= $val['id_logger']?>" method="post" enctype="multipart/form-data">
				<div class="modal-body py-3">
					Ubah Status Menjadi Perbaikan ? <br>
					(<?= $val['nama_lokasi']?>)
				</div>
				<div class="modal-footer ">
					<div class="text-center w-100">
						
						<button type="button" class="btn btn-outline-dark  me-3" data-bs-dismiss="modal">Batal</button>
						<button class="btn btn-outline-warning" type="submit">Perbaiki</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php endforeach ?>
<?php 
	$i =1;foreach ($awlr_stts as $key => $val) : ?>
<div class="modal" tabindex="-1" id="hapus_perbaikan<?= $val['id_logger']?>">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Hapus Perbaikan </h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="<?= base_url() ?>pengaturan/hapus_perbaikan/<?= $val['id_logger']?>" method="post" enctype="multipart/form-data">
				<div class="modal-body py-3">
					Selesai Perbaikan ? <br>
					(<?= $val['nama_lokasi']?>)
				</div>
				<div class="modal-footer ">
					<div class="text-center w-100">
						
						<button type="button" class="btn btn-outline-dark  me-3" data-bs-dismiss="modal">Batal</button>
						<button class="btn btn-outline-success" type="submit">Selesai</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php endforeach ?>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function () {
		$('#example').DataTable(
			scrollX = true
		);
	});
</script>