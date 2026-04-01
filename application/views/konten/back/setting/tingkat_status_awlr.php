

<div class="col-md-9">

	<div class="card">
		<div class="card-body">
			<h2 class="mb-2 mt-2 fw-bold">Tingkat Status AWLR</h2>
			<hr class="mb-3 mt-0">
			<div style="overflow-x:scroll">
				<table id="example" class="cell-border" >
					<thead>
						<tr>
							<th width="10px" class="text-center">No</th>
							<th class="text-center">Nama Pos</th>
							<th class="text-center">Batas Waspada</th>
							<th class="text-center">Batas Siaga</th>
							<th class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$i =1;
						foreach ($awlr_stts as $key => $val) : ?>
						<tr>
							<td class="text-center"><?= $i++ ?></td>
							<td><?= $val['nama_logger'] ?></td>
							<td class="text-center"> <?= (isset($val['siaga2'])) ? '≥ ' . $val['siaga2'] . ' Meter' : 'Belum Diatur' ?></td>
							<td class="text-center"><?= (isset($val['siaga1'])) ? '≥ ' . $val['siaga1'] . ' Meter' : 'Belum Diatur' ?></td>

							<td>
								<div class="d-flex">
									<?php if(isset($val['siaga2']) and isset($val['siaga1'])) { ?> 
										<button class="btn btn-outline-info me-2 py-1" data-bs-target="#edit_logger<?= $val['id_logger']?>" data-bs-toggle="modal">Edit</button>
									<?php } else{ ?>
										<button class="btn btn-outline-success me-2 py-1" data-bs-target="#tambah_rumus<?= $val['id_logger']?>" data-bs-toggle="modal">Tambahkan</button>
									<?php } ?> 
									<?php if(isset($val['siaga2']) and isset($val['siaga1'])) { ?> 
										 <button class="btn btn-outline-danger me-2 py-1" data-bs-target="#hapus_rumus<?= $val['id_logger']?>" data-bs-toggle="modal">Hapus</button>
									<?php }  ?>
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
<div class="modal" tabindex="-1" id="edit_logger<?= $val['id_logger']?>">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Logger (<?= $val['nama_logger']?>)</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="<?= base_url() ?>pengaturan/update_status_awlr/<?= $val['id_logger']?>" method="post" enctype="multipart/form-data">
				<div class="modal-body py-3">
					<div class="row">
						<div class="col-12">
							<div class="form-group mb-3">
								<label>Nama Pos</label>
								<input class="form-control" value="<?= $val['nama_logger']?>" readonly />
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Batas Waspada</label>
								<input class="form-control" value="<?= $val['siaga2']?>" name="siaga2"/>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Batas Siaga</label>
								<input class="form-control" value="<?= $val['siaga1']?>" name="siaga1"/>
							</div>
						</div>
					</div>  
				</div>
				<div class="modal-footer ">
					<div class="text-center w-100">
						<button class="btn btn-outline-primary me-3" type="submit">Simpan Perubahan</button>
						<button type="button" class="btn btn-outline-danger me-auto" data-bs-dismiss="modal">Batal</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php endforeach ?>
<?php 
	$i =1;foreach ($awlr_stts as $key => $val) : ?>
<div class="modal" tabindex="-1" id="tambah_rumus<?= $val['id_logger']?>">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Logger (<?= $val['nama_logger']?>)</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="<?= base_url() ?>pengaturan/tambah_status_awlr/<?= $val['id_logger']?>" method="post" enctype="multipart/form-data">
				<div class="modal-body py-3">
					<div class="row">
						<div class="col-12">
							<div class="form-group mb-3">
								<label>Nama Pos</label>
								<input class="form-control" value="<?= $val['nama_logger']?>" readonly />
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Batas Waspada</label>
								<input class="form-control" value="0" name="siaga2" required/>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Batas Siaga</label>
								<input class="form-control" value="0" name="siaga1" required/>
							</div>
						</div>
					</div>  
				</div>
				<div class="modal-footer ">
					<div class="text-center w-100">
						<button class="btn btn-outline-primary me-3" type="submit">Tambah Rumus</button>
						<button type="button" class="btn btn-outline-danger me-auto" data-bs-dismiss="modal">Batal</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php endforeach ?>
<?php 
	$i =1;foreach ($awlr_stts as $key => $val) : ?>
<div class="modal fade modal-blur" tabindex="-1" id="hapus_rumus<?= $val['id_logger']?>">
	<div class="modal-dialog-centered modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			<div class="modal-status bg-danger"></div>
			<div class="modal-body text-center pt-4 pb-1">
				<!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
				<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v2m0 4v.01"></path><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path></svg>
				<h3>Hapus Tingkat Status ?</h3>
			</div>
			<div class="modal-footer">
				<div class="w-100">
					<div class="row">
						<div class="col"><button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">
							Batal
							</button></div>

						<div class="col">
							<form action="<?= base_url()?>pengaturan/hapus_status_awlr/<?= $val['id_klasifikasi']?>" method="post">
								<input id="id_datasheet" type="text" name="id_datasheet" value="" class="d-none"  />
								<button type="submit" href="#" class="btn btn-danger w-100" >
									Hapus
								</button >
							</form>
						</div>
					</div>
				</div>
			</div>
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