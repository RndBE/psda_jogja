<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>
<?php 
if($this->input->get('theme')=='dark')
{
 echo '<script src="https://code.highcharts.com/themes/dark-unica.js"></script>';
}
else
{
  echo '<script src="https://code.highcharts.com/js/themes/grid.js"></script>';
}
?>




<?php
$qstatus=$this->db->query('select waktu from '.$this->session->userdata('tabel').' where code_logger="'.$this->session->userdata('idlogger').'" order by waktu desc limit 1');
foreach($qstatus->result() as $stat)
{
  $awal=date('Y-m-d H:i',(mktime(date('H')-1)));
  $waktuterakhir=$stat->waktu;
  if($waktuterakhir >= $awal)
    {
      $color="green";
      $status_logger="Koneksi Terhubung";
    }
    else{
      $color="red";
      $status_logger="Koneksi Terputus";
    }
}
if($data_sensor== null )
{
  $namasensor='';

}else
{
   $namasensor=str_replace('_', ' ', $data_sensor->{'namaSensor'});
   $satuan=$data_sensor->{'satuan'};
   $tooltip=$data_sensor->{'tooltip'};
   $data = $data_sensor->{'data'};
   $range=$data_sensor->{'range'};
   $nosensor= $data_sensor->{'nosensor'};
   $typegraf=$data_sensor->{'tipe_grafik'};

}

?>


<div class="container-md">
          <div class="page-header d-print-none">
            <div class="row g-3 align-items-center">
              <div class="col-auto">

                <?php echo anchor('analisa','<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-big-left-lines" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
              <div class="col-md-auto ms-auto d-print-none">
                <div class="btn-list">
					<?php $tingkat_status = $this->db->get_where('klasifikasi_tma', array('idlogger'=>$this->session->userdata('idlogger')))->row();				
					
	if(!$tingkat_status)
		{ $zona='';?>
					<div class="card p-0 border-warning" id="warn">
						<div class="card-body ps-2 pe-0 py-1 d-flex align-items-center justify-content-between">
							
							<div class="d-flex">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle me-2 text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
									<circle cx="12" cy="12" r="9"></circle>
									<line x1="12" y1="8" x2="12" y2="12"></line>
									<line x1="12" y1="16" x2="12.01" y2="16"></line>
								</svg>
								<div class="me-3 text-warning">Tingkat Status Belum Diatur</div>
							</div>
							<button onclick="myFunction()" href="#" class="bg-white border-0 text-dark"><!-- Download SVG icon from http://tabler-icons.io/i/x -->
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x text-dark" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
   <line x1="18" y1="6" x2="6" y2="18"></line>
   <line x1="6" y1="6" x2="18" y2="18"></line>
</svg>
							</button>
						</div>
					</div>
					<script type="text/javascript">
						function myFunction() {
						   var element = document.getElementById("warn");
						   element.classList.toggle("d-none");
						}
					</script>
					<?php } else{
						$zona="zones:[
						{
							color:'#4299e1',//waspada
							value:2
							},
							{
							color:'yellow',//siaga
							value:4
							},
							{
							color:'red'
							},
							],";
					}?>
					<!--	<a href="<?= base_url() . 'awlr/set_sensordash2?tabel=awlr&id_param='.$this->session->userdata('id_param') ?>" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
   <rect x="3" y="12" width="6" height="8" rx="1"></rect>
   <rect x="9" y="8" width="6" height="12" rx="1"></rect>
   <rect x="15" y="4" width="6" height="16" rx="1"></rect>
   <line x1="4" y1="20" x2="18" y2="20"></line>
</svg>
							Komparasi</a>
						-->
                  <a class="btn" data-bs-toggle="offcanvas" href="#offcanvasEnd" role="button" aria-controls="offcanvasEnd">
                    <!-- Download SVG icon from http://tabler-icons.io/i/settings -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                     <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                     <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                     <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                     <path d="M11 14h1v4h1"></path>
                     <path d="M12 11h.01"></path>
                  </svg>
                    Informasi
                  </a>
				
				
<!--
                  <a href="#" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-player-play" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 4v16l13 -8z"></path></svg>
                    Live Data
                  </a>
-->
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="page-body">
          <div class="container-xl">
            <div class="row row-cards">
              <div class="col-md-2">
            <div class="row row-cards">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="subheader"><label class="form-label">Pilih Pos AWLR</label></div>
                    <div class="h3 m-0"> 
                       <?php  
                           echo form_open('awlr/set_pos');?>
                            <select type="text" name="pilihpos" class="form-select" placeholder="Pilih Pos AWLR" onchange="this.form.submit()" id="select-pos" value=" ">
                              <option value="">Pilih Pos</option>
                              <?php foreach($pilih_pos as $mnpos ):?>
                                <option value="<?= $mnpos->idLogger ?>" ><?= str_replace('_', ' ', $mnpos->namaPos) ?></option>
                              <?php endforeach ?>
                            </select>
                    <?php echo form_close() ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                     <div class="subheader"><label class="form-label">Pilih Parameter</label></div>
                    <div class="h3 m-0">
                    
                      <?php  
                           echo form_open('awlr/set_parameter');?>
                            <select type="text" name="mnsensor" class="form-select" placeholder="Pilih Parameter"  onchange="this.form.submit()"  id="select-parameter" value=" ">
                              <option value="">Pilih Parameter</option>
                              <?php foreach($pilih_parameter as $mnparameter ):?>
                                    <option value="<?= $mnparameter->idParameter ?>" ><?= str_replace('_', ' ', $mnparameter->namaParameter)?></option>
                                 <?php endforeach ?>
                            </select>
                            <?php echo form_close() ?>
                    
                    </div>
                  </div>
                </div>
              </div>


                  <?php  

                          if($this->session->userdata('data')=='hari')
                          {
                            ?>
                                   <div class="col-md-12">
                                    <div class="card">
                                      <div class="card-body">
                                        <div class="subheader"><label class="form-label">Pilih Tanggal</label></div>
                                        <div class="h3 m-0">

                                          <?php echo form_open('awlr/settgl') ;?>
                                          <div class="row">
                                            <div class="col-12 col-md-12 col-sm-12">
                                            <div class="input-icon">
                                                  <input class="form-control " name="tgl" placeholder="Pilih Tanggal" id="dptanggal" value="" autocomplete="off" required/>
                                                  <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                                                  </span>
                                                </div>
												<div class="form-footer">
                                                <input type="submit" class="btn btn-info w-100" value="Tampil"/>
                                              </div>
                                                 </div>
                                               
                                              </div>
                                          <?php echo form_close() ?>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                     <div class="col-md-12">
                                    <div class="card">
                                      <div class="card-body">
                                         <div class="subheader"><label class="form-label">Analisa dalam</label></div>

                           <?php echo form_open('awlr/sesi_data');?>
                            
                           <div class="mb-3">
                            <div>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="hari" onclick="javascript: submit()" checked />
                                <span class="form-check-label">Hari</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="bulan" onclick="javascript: submit()" />
                                <span class="form-check-label">Bulan</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="tahun" onclick="javascript: submit()" />
                                <span class="form-check-label">Tahun</span>
                              </label>
								<label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="range" onclick="javascript: submit()" />
                                <span class="form-check-label">Rentang Waktu</span>
                              </label>
                             
                            </div>
                          </div>
                    <?php echo form_close() ?>
                    
                   
                  </div>
                </div>
              </div>

                            <?php
                          }
                          elseif($this->session->userdata('data')=='bulan')
                          {
                            ?>
                                   <div class="col-md-12">
                                    <div class="card">
                                      <div class="card-body">
                                        <div class="subheader"><label class="form-label">Pilih Bulan</label></div>
                                        <div class="h3 m-0">
                                           <?php echo form_open('awlr/setbulan') ;?>
                                           <div class="row">
                                             <div class="col-12 col-md-12 col-sm-12">
                                            <div class="input-icon">
                                                  <input type="month" class="form-control " name="bulan" placeholder="Pilih Bulan" id="dpbulan" value="" autocomplete="off" required/>
                                                  <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                                                  </span>
                                                </div>
												  <div class="form-footer">
                                                <input type="submit" class="btn btn-info w-100" value="Tampil"/>
                                              </div>
                                                 </div>
                                             
                                              </div>
                                          <?php echo form_close() ?>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                     <div class="col-md-12">
                                    <div class="card">
                                      <div class="card-body">
                                         <div class="subheader"><label class="form-label">Analisa dalam</label></div>

                           <?php echo form_open('awlr/sesi_data');?>
                            
                           <div class="mb-3">
                            <div>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="hari" onclick="javascript: submit()" />
                                <span class="form-check-label">Hari</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="bulan" onclick="javascript: submit()" checked />
                                <span class="form-check-label">Bulan</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="tahun" onclick="javascript: submit()" />
                                <span class="form-check-label">Tahun</span>
                              </label>
								<label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="range" onclick="javascript: submit()" />
                                <span class="form-check-label">Rentang Waktu</span>
                              </label>
                             
                            </div>
                          </div>
                    <?php echo form_close() ?>
                    
                   
                  </div>
                </div>
              </div>
                            <?php
                          }
                          elseif($this->session->userdata('data')=='tahun')
                          {
                            ?>

                                   <div class="col-md-12">
                                    <div class="card">
                                      <div class="card-body">
                                        <div class="subheader"><label class="form-label">Pilih Tahun</label></div>
                                        <div class="h3 m-0">
                                          <?php echo form_open('awlr/settahun') ;?>
                                          <div class="row">
                                             <div class="col-12 col-md-12 col-sm-12">
                                            <div class="input-icon">
                                                  <input class="form-control" name="tahun" placeholder="Pilih Tahun" id="dptahun" value="" autocomplete="off" required/>
                                                  <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                                                  </span>
                                                </div>
												   <div class="form-footer">
                                                <input type="submit" class="btn btn-info w-100" value="Tampil"/>
                                              </div>
                                              </div>
                                             
                                              </div>
                                          <?php echo form_close() ?>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                     <div class="col-md-12">
                                    <div class="card">
                                      <div class="card-body">
                                         <div class="subheader"><label class="form-label">Analisa dalam</label></div>

                           <?php echo form_open('awlr/sesi_data');?>
                            
                           <div class="mb-3">
                            <div>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="hari" onclick="javascript: submit()" />
                                <span class="form-check-label">Hari</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="bulan" onclick="javascript: submit()" />
                                <span class="form-check-label">Bulan</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="tahun" onclick="javascript: submit()" checked />
                                <span class="form-check-label">Tahun</span>
                              </label>
								    <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="range" onclick="javascript: submit()" />
                                <span class="form-check-label">Rentang Waktu</span>
                              </label>
                             
                            </div>
                          </div>
                    <?php echo form_close() ?>
                    
                   
                  </div>
                </div>
              </div>

                            <?php
                          }
						 elseif($this->session->userdata('data')=='range')
                          {
                            ?>

                                   <div class="col-md-12">
                                    <div class="card">
                                      <div class="card-body">
                                        <div class="subheader"><label class="form-label">Pilih Rentang Waktu</label></div>
                                        <div class="h3 m-0">
                                          <?php echo form_open('awlr/setrange') ;?>
                                          <div class="row">
                                             <div class="col-12 col-md-12 col-sm-12">
												  <div class="row">
                                             <div class="col-12 col-md-12 col-sm-12">
												  <label class="form-label">Dari</label>
                                            <div class="input-icon">
												
                                                  <input class="form-control" name="dari" placeholder="Dari" id="dpdari" value="" autocomplete="off" required/>
                                                  <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                                                  </span>
                                                </div>
													  </div>
													  <div class="col-12 col-md-12 col-sm-12">
														   <label class="form-label mt-2">Sampai</label>
												 <div class="input-icon">
													
                                                  <input class="form-control" name="sampai" placeholder="Sampai" id="dpsampai" value="<?= $this->session->userdata('sampai')?>" autocomplete="off" required/>
                                                  <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                                                  </span>
                                                </div>
													  </div>
												 </div>
												   <div class="form-footer">
                                                <input type="submit" class="btn btn-info w-100" value="Tampil"/>
                                              </div>
                                              </div>
                                             
                                              </div>
                                          <?php echo form_close() ?>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                     <div class="col-md-12">
                                    <div class="card">
                                      <div class="card-body">
                                         <div class="subheader"><label class="form-label">Analisa dalam</label></div>

                           <?php echo form_open('awlr/sesi_data');?>
                            
                           <div class="mb-3">
                            <div>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="hari" onclick="javascript: submit()" />
                                <span class="form-check-label">Hari</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="bulan" onclick="javascript: submit()" />
                                <span class="form-check-label">Bulan</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="tahun" onclick="javascript: submit()"  />
                                <span class="form-check-label">Tahun</span>
                              </label>
                              <label class="form-check">
                                <input class="form-check-input" type="radio" name="data"  value="range" onclick="javascript: submit()" checked />
                                <span class="form-check-label">Rentang Waktu</span>
                              </label>
                            </div>
                          </div>
                    <?php echo form_close() ?>
                    
                   
                  </div>
                </div>
              </div>

                            <?php
                          }
                          ?>

  <!-- IMPORT DATA --------------------------------     
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                     <div class="subheader"><label class="form-label">Import Data</label></div>
                    <div class="h3 m-0">
                    
                      <input type="file" class="form-control" />
                    
                    </div>
                  </div>
                </div>
              </div>
            
         End Import Data ---------------------------->    
				</div>
             </div>
      <div class="col-md-10">
             <div class="row row-cards">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <h3 class="card-title"> </h3>
                   
                    <div id="analisa"></div>
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
                      <div class="offcanvas-header">
                        <h2 class="offcanvas-title" id="offcanvasEndLabel">Informasi Logger</h2>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                      </div>
                      <div class="offcanvas-body">
                        <div>
					<table class="table table-sm table-borderless">
                      <tbody>
                        <?php 
                            $query_informasi=$this->db->query('select * from t_informasi where logger_id="'.$this->session->userdata('idlogger').'"');
                            foreach($query_informasi->result() as $tinfo)
                            {
                        ?>
					<tr> <td  class="fw-bold">Id Logger</td><td class="text-end"><?php  echo $tinfo->logger_id ?></td></tr>
                    <tr> <td  class="fw-bold">Seri Logger</td><td class="text-end"><?php  echo $tinfo->seri ?></td></tr>
					<tr> <td  class="fw-bold">Sensor</td><td class="text-end"><?php  echo $tinfo->sensor ?></td></tr>
					<tr> <td  class="fw-bold">Serial Number</td><td class="text-end"><?php  echo $tinfo->serial_number ?></td></tr>
                          <?php

                        if($this->uri->segment(1)=='awlr')
                        {
                          ?>
                    <tr> <td  class="fw-bold">Elevasi</td><td class="text-end"><?php  echo $tinfo->elevasi ?></td></tr>
                          <?php

                        }

                        ?>
					<tr> <td  class="fw-bold">No. Seluler</td><td class="text-end"><?php  echo $tinfo->nosell ?></td></tr>
					<tr> <td  class="fw-bold">Nama Penjaga</td><td class="text-end"><?php  echo $tinfo->nama_pic ?></td></tr>
					<tr> <td  class="fw-bold">Nomor Penjaga</td><td class="text-end"><?php  echo $tinfo->no_pic ?></td></tr>
					<tr> <td  class="fw-bold">Logger Aktif</td><td class="text-end"><?php  echo $tinfo->tgl_aktif ?></td></tr>
					<tr> <td  class="fw-bold">Masa Garansi</td><td class="text-end"><?php  echo $tinfo->garansi ?></td></tr>
					
                        <?php } ?>
						    </tbody>
                    </table>
                </div>
						  
				
                     
                        
                    
                        
                      </div>
                    </div>

                  </div>
                </div>
              </div>
             </div>
          </div>
        </div>
      </div></div>
       
                           
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
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
      var el;
      window.TomSelect && (new TomSelect(el = document.getElementById('select-parameter'), {
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
    // @formatter:on
  </script>
  <script type="text/javascript">
	<?php if($this->session->userdata('data')=='range') { $title= " dari ". $this->session->userdata('dari')." sampai ".$this->session->userdata('sampai'); }
	else {
	 $title= " pada ". $this->session->userdata('pada'); } ?>
Highcharts.chart('analisa', {
  chart: {
            zoomType: 'xy'
        },

    title: {
            text: "<?php echo $namasensor ?> <?php echo $title ?>"
        },
        subtitle: {
            text: '<?php echo $this->session->userdata('namalokasi') ?> '
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
        yAxis: [ { // Secondary yAxis
          
       tickAmount: 5,
        
            title: {
                text: "<?php echo $namasensor ?>",
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            labels: {
                format: "{value} <?php echo $satuan?>",
                
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
           
        }],
        tooltip: {
             xDateFormat: '<?php echo $tooltip ?>',
            shared: true
        },
	
      /*s  legend: {
            layout: 'vertical',
            align: 'left',
            x: 10,
            verticalAlign: 'top',
            y: 30,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        */
        credits: {
                enabled: false
            },
    exporting: {
        buttons: {
            contextButton: {
                menuItems: ['printChart','separator','downloadPNG', 'downloadJPEG','downloadXLS']
            }
        },
       showTable:true
    },
	<?php if($this->session->userdata('leveluser')=='user'){ ?>
	 navigation: {
        buttonOptions: {
            enabled: false
        }
    },
<?php } ?>
        series: [ {
            name: '<?php echo $namasensor; ?>',
            type: '<?php echo $typegraf; ?>',
            data: <?php echo str_replace('"','',json_encode($data)); ?>,
            zIndex: 1,
        marker: {
            fillColor: 'white',
            lineWidth: 2,
            lineColor: Highcharts.getOptions().colors[0]
        },
            tooltip: {
                valueSuffix: ' <?php echo $satuan; ?>',
                 valueDecimals: 2,
            },
	<?php
	if($data_sensor->{'namaSensor'} == "Rerata_Tinggi_Muka_Air")
	{
		echo $zona;
	}

	?>
        }
			<?php if($typegraf != 'column')
{
	echo ", {
        name: 'Range',
        data: ".str_replace('"','',json_encode($range)).",
        type: 'arearange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: Highcharts.getOptions().colors[0],
        fillOpacity: 0.3,
        zIndex: 0,
        marker: {
            enabled: false
        },
        tooltip: {
                valueSuffix: ' ". $satuan."',
                 valueDecimals: 3,
            }
    }";
}?>
			],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});
    </script>

<div class="modal modal-blur fade" id="modal-lengkung" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pengaturan Lengkung Debit - <?php echo $this->session->userdata('namalokasi'); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
         <?php 
			$query_datasheet=$this->db->query('select * from datasheet_debit where idlogger="'.$this->session->userdata('idlogger').'"');
									foreach($query_datasheet->result() as $dtsheet)
									{
										$a=$dtsheet->a;
										$b=$dtsheet->b;
										$c=$dtsheet->c;
										$tahun=$dtsheet->tahun;
									}
									
			?>
			<?php 						
			echo form_open('awlr/editlengkungdebit');?>
          <div class="modal-body">
			 <h5 class="modal-title"></h5>
            <div class="row">
              <div class="col-lg-4">
                <div class="mb-3">
                  <label class="form-label">A</label>
                  <input type="text" name="a" class="form-control" value="<?php echo $a?>" required />
                </div>
              </div>
			<div class="col-lg-4">
                <div class="mb-3">
                  <label class="form-label">B</label>
                  <input type="text" name="b" class="form-control" value="<?php echo $b?>" required />
                </div>
              </div>
			<div class="col-lg-4">
                <div class="mb-3">
                  <label class="form-label">C</label>
                  <input type="text" name="c" class="form-control" value="<?php echo $c?>" required />
                </div>
              </div>
              <div class="col-lg-12">
				  <label class="form-label">Tahun</label>
				   <div class="input-icon">
                 <input class="form-control " name="tahun" id="dptanggal" value="<?php echo $tahun ?>" autocomplete="off" required/>
                                                  <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                                                  </span>
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
		 <?php 
										
				echo form_close() ?>	
			
        </div>
      </div>
    </div>	

<div class="modal modal-blur fade" id="modal-siaga" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pengaturan Tingkat Siaga - <?php echo $this->session->userdata('namalokasi'); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
         <?php 
			$query_datasheet=$this->db->query('select * from klasifikasi_tma where idlogger="'.$this->session->userdata('idlogger').'"');
									foreach($query_datasheet->result() as $dtsheet)
									{
										$waspada=$dtsheet->siaga2;
										$siaga=$dtsheet->siaga1;
									}
									
			?>
			<?php 						
			echo form_open('awlr/editsiaga');?>
          <div class="modal-body">
			 <h5 class="modal-title"></h5>
            <div class="row">
              <div class="col-lg-6">
                <div class="mb-3">
                  <label class="form-label">Waspada</label>
                  <input type="text" name="waspada" class="form-control" value="<?php echo $waspada ?>" required />
                </div>
              </div>
			<div class="col-lg-6">
                <div class="mb-3">
                  <label class="form-label">Siaga</label>
                  <input type="text" name="siaga" class="form-control" value="<?php echo $siaga ?>" required /> 
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
		 <?php 
										
				echo form_close() ?>	
			
        </div>
      </div>
    </div>	
