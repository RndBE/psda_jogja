<?php
//if($this->session->userdata('bidang') == 'hidrologi')
$query_lokasi=$this->db->query('select id_logger,nama_lokasi from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where tabel = "awlr"');

?>
<div class="col-md-9">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title fw-bold">Daftar Rumus Lengkung Debit AWLR</h3>
			<div class="card-actions">

				<a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#modal-tambahrumus" >
					<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
					Tambah Rumus
				</a>
			</div>
		</div>
		<div class="card-body" style="">
			<div class="table-responsive">
				<table id="example" class="cell-border " style="overflow-x:scroll">
					<thead>
						<tr>
							<th class="text-center">No</th>
							<th class="text-center">Nama Pos</th>
							<th class="text-center">Parameter Rumus</th>
							<th class="text-center">Rumus Debit</th>
							<th class="text-center">Tipe</th>
							<th class="text-center">Tanggal Berlaku</th>
							<th class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						foreach ($awlr_stts as $key => $val) : ?>
						<tr>
							<td class="text-center"><?= $i++ ?></td>
							<td><?= $val['nama_logger'] ?></td>
							<td>
								<?php if (isset($val['current'])) { 
	$s = ($val['current']);
	$c = json_decode($s->parameter_rumus);?>
								<ul style="list-style-type: none;" class="m-0 p-0">
									<?php foreach($c as $key => $z){ ?>
									<li><?= $key ?> = <?= $z ?></li>
									<?php } ?>
								</ul>
								<?php }else{
	echo 'Belum Diatur';
}?>								
							</td>
							<td class="text-center"><?= (isset($val['current'])) ? $val['current']->rumus : 'Belum Diatur' ?></td>
							<td class="text-center"><?= (isset($val['current'])) ? $val['current']->jenis_rumus : 'Belum Diatur' ?></td>
							<td class="text-center"><?= (isset($val['current'])) ? $val['current']->tanggal_berlaku  : 'Belum Diatur' ?></td>
							<td>
								<div class="d-flex">
									<button class="btn btn-outline-info me-2 py-1" data-bs-target="#detail_logger<?= $val['id_logger'] ?>" data-bs-toggle="modal">Detail</button>
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
	$i = 1;foreach ($awlr_stts as $key => $val) : ?>
<div class="modal modal-blur fade detail" id="detail_logger<?= $val['id_logger'] ?>" >
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Detail Logger (<?= $val['nama_logger'] ?>)</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body py-3">
				<h4 class="mt-0 pt-0">History Rumus Lengkung</h4>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th>Rumus Lengkung</th>
								<th>Parameter Rumus</th>
								<th>Mulai Berlaku</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<form action="<?= base_url() ?>pengaturan/update_history/<?=$val['id_logger']?>" method="post" enctype="multipart/form-data">
								<?php if (isset($val['history'])) {$i = 1;foreach ($val['history'] as $k =>$dt) : ?>
								<tr>
									<td class="align-middle"><?= $i++ ?></td>
									<td class="text-muted align-middle">
										<?= $dt['rumus'] ?><input type="text" class="d-none" value="<?= $dt['jenis_rumus'] ?>" name="jenis_rumus[<?= $dt['iddatasheet'] ?>]"/>
									</td>
									<?php $a = json_decode($dt['parameter_rumus']) ?>
									<td class="text-muted align-middle">
										<div id="tbr_<?=$dt['iddatasheet']?>_<?= $k ?>" >
											<?php foreach($a as $key =>$r):?>
											<span class="me-1"><?= $key ?></span><span class="me-2">= <?= $r ?></span>
											<?php endforeach?>
										</div>
										<div id="tbr2_<?=$dt['iddatasheet']?>_<?= $k ?>" class="d-flex align-items-center d-none">
											<?php foreach($a as $key =>$r):?>
											<span class="me-2"><?= $key ?> </span><input type="text" name="parameter_rumus[<?= $dt['iddatasheet'] ?>][<?= $key ?>]" class="form-control me-3" value="<?= $r ?>"/>
											<?php endforeach?>
										</div>
									</td>
									<td class="text-muted align-middle">
										<div id="tbt_<?=$dt['iddatasheet']?>_<?= $k ?>">
											<?= $dt['tanggal_berlaku'] ?>
										</div>
										<div id="tbt2_<?=$dt['iddatasheet']?>_<?= $k ?>" class="d-none">
											<input type="date" class="form-control me-3" value="<?= $dt['tanggal_berlaku'] ?>" name="tanggal[<?= $dt['iddatasheet'] ?>]"/>
										</div>
									</td>
									<td class="d-flex align-items-center">
										<button type="button" class="btn btn-success px-2 py-1" onclick="edit_rumus(<?= $dt['iddatasheet']?> , <?= $k ?>)" id="edit_btn_<?=$dt['iddatasheet']?>_<?= $k ?>">Edit</button>
										<button type="button" class="btn btn-danger px-2 py-1 ms-2" onclick="show_modal(<?= $dt['iddatasheet']?> , <?= $k ?>)" id="hapus_btn_<?=$dt['iddatasheet']?>_<?= $k ?>" >Hapus</button>
										<button type="button" class="btn btn-danger px-2 py-1 d-none me-2" onclick="edit_rumus(<?= $dt['iddatasheet']?> , <?= $k ?>)" id="delete_btn_<?=$dt['iddatasheet']?>_<?= $k ?>">Batal</button>
										<button type="submit" class="btn btn-primary px-2 py-1 d-none" id="simpan_btn_<?=$dt['iddatasheet']?>_<?= $k ?>" >Simpan</button>
									</td>
								</tr>
								<?php endforeach ?>
								<?php } else { ?>
								<tr>
									<td colspan="4" class="text-center">Belum ada data</td>
								</tr>
								<?php } ?>
							</form>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer ">
				<div class="text-center w-100">
					<button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endforeach ?>

<div class="modal" id="modal2" role="dialog" aria-hidden="true" >
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			<div class="modal-status bg-danger"></div>
			<div class="modal-body text-center pt-4 pb-1">
				<!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
				<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v2m0 4v.01"></path><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path></svg>
				<h3>Hapus Rumus Lengkung ?</h3>
			</div>
			<div class="modal-footer">
				<div class="w-100">
					<div class="row">
						<div class="col"><a href="#" class="btn btn-secondary w-100" data-bs-dismiss="modal">
							Batal
							</a></div>

						<div class="col">
							<form action="<?= base_url()?>pengaturan/hapus_history" method="post">
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

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	function edit_rumus(key, num){
		console.log(key, num);
		$("#tbr_" + key + '_' + num).toggleClass('d-none');
		$("#tbt_" + key + '_' + num).toggleClass('d-none');
		$("#tbr2_" + key + '_' + num).toggleClass('d-none');
		$("#tbt2_" + key + '_' + num).toggleClass('d-none');
		$("#delete_btn_" + key + '_' + num).toggleClass('d-none');
		$("#edit_btn_" + key + '_' + num).toggleClass('d-none');
		$("#simpan_btn_" + key + '_' + num).toggleClass('d-none');
		$("#hapus_btn_" + key + '_' + num).toggleClass('d-none');
	}
	function show_modal(key, num) {
		$('#id_datasheet').val(key);
		$('#modal2').modal('show');
	}
	$(document).ready(function() {
		$('#example').DataTable(
			scrollX = true
		);
	});
</script>
<div class="modal modal-blur fade" id="modal-tambahrumus" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Tambah Rumus Lengkung Debit AWLR</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<?= form_open('pengaturan/tambah_rumus');?>
			<div class="modal-body py-0">
				<h5 class="modal-title"></h5>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group mb-3">
							<label class="form-label">Pos AWLR</label>
							<select class="form-select" name="idlogger" required>
								<option selected="">Pilih Lokasi Pos AWLR</option>
								<?php foreach($query_lokasi->result() as $lokasi)
{ ?>
								<option value="<?= $lokasi->id_logger;?>"><?= $lokasi->nama_lokasi;?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group mb-3">
							<label class="form-label">Jenis Rumus</label>
							<select class="form-select" name="tipe" required>
								<option selected="">Pilih Tipe Rumus</option>
								<option value="type01">Tipe-01 (C x (H-A)^B)</option>
								<option value="type02">Tipe-02 (C x B x H^3/2)</option>

							</select>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group mb-3">
							<div class="type01 box">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>C</label>
											<input class="form-control mt-1" type="text" name="c"/>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>A</label>
											<input class="form-control mt-1" type="text" name="a"/>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>B</label>
											<input class="form-control mt-1" type="text" name="b"/>
										</div>
									</div>
								</div>

							</div>
							<div class="type02 box">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>C</label>
											<input class="form-control mt-1" type="text" name="c2"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>B</label>
											<input class="form-control mt-1" type="text" name="b2"/>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group mb-3">
							<div class="form-group">
								<label>Tanggal Mulai Berlaku</label>
								<input class="form-control mt-1" type="date" name="tanggal" required/>
							</div>
						</div>
					</div>

				</div>
			</div>


			<div class="modal-footer">
				<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
					Batal
				</a>
				<input type="submit" class="btn btn-primary ms-auto" value="Simpan"/>
			</div>
			<?php echo form_close() ?>	

		</div>
	</div>
</div>	

<script>
	$(document).ready(function(){
		$("select").change(function(){
			$(this).find("option:selected").each(function(){
				var optionValue = $(this).attr("value");
				if(optionValue){
					$(".box").not("." + optionValue).hide();
					$("." + optionValue).show();
				} else{
					$(".box").hide();
				}
			});
		}).change();
	});
</script>
