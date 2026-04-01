<div class="collapse navbar-collapse" id="navbar-menu">
	<div class="navbar navbar-light">
		<div class="container-xl">
			<ul class="navbar-nav">
				<li class="nav-item <?php if($this->uri->segment(1)=='beranda'){ echo 'active';} ?>">
					<?php echo anchor('beranda','<span class="nav-link-icon d-md-none d-lg-inline-block">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="5 12 3 12 12 3 21 12 19 12" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                    </span>
                    <span class="nav-link-title">
                      Beranda
                    </span>','class="nav-link"')?>

				</li>

				<li class="nav-item <?php if($this->uri->segment(1)=='analisa'){ echo 'active';} ?>">
					<?php echo anchor('analisa','<span class="nav-link-icon d-md-none d-lg-inline-block">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-dots" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                         <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                         <path d="M3 3v18h18"></path>
                         <circle cx="9" cy="9" r="2"></circle>
                         <circle cx="19" cy="7" r="2"></circle>
                         <circle cx="14" cy="15" r="2"></circle>
                         <line x1="10.16" y1="10.62" x2="12.5" y2="13.5"></line>
                         <path d="M15.088 13.328l2.837 -4.586"></path>
                      </svg>
                    </span>
                    <span class="nav-link-title">
                      Analisa
                    </span>','class="nav-link"')?>

				</li>

				<?php if($this->session->userdata('leveluser') != 'Tamu'){ ?>
				<li class="nav-item <?= ($this->uri->segment(1)=='komparasi') ? 'active' : ''?>">
					<?php echo anchor('komparasi','<span class="nav-link-icon d-md-none d-lg-inline-block">
						 <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
             <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
             <rect x="3" y="12" width="6" height="8" rx="1"></rect>
             <rect x="9" y="8" width="6" height="12" rx="1"></rect>
             <rect x="15" y="4" width="6" height="16" rx="1"></rect>
             <line x1="4" y1="20" x2="18" y2="20"></line>
          </svg>
						</span>
						<span class="nav-link-title">
							Komparasi
						</span>','class="nav-link"')?>
				</li>

				<?php } ?>
				<?php if($this->session->userdata('leveluser') != 'Tamu'){ ?>
				<li class="nav-item <?= ($this->uri->segment(1)=='monitoring') ? 'active' : ''?>">
					<?php echo anchor('monitoring','<span class="nav-link-icon d-md-none d-lg-inline-block">
						 <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-text" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
   <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
   <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
   <path d="M9 9l1 0"></path>
   <path d="M9 13l6 0"></path>
   <path d="M9 17l6 0"></path>
</svg>
						</span>
						<span class="nav-link-title">
							Monitoring
						</span>','class="nav-link"')?>
				</li>
				<?php } ?>
<li class="nav-item <?= ($this->uri->segment(1)=='informasi') ? 'active' : ''?>">
					<?php echo anchor('informasi','<span class="nav-link-icon d-md-none d-lg-inline-block">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-square-rounded" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
   <path d="M12 9h.01"></path>
   <path d="M11 12h1v4h1"></path>
   <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>
</svg>

                    </span>
                    <span class="nav-link-title">
                        Informasi
                    </span>','class="nav-link"')?>


				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
								<polyline points="7 11 12 16 17 11"></polyline>
								<line x1="12" y1="4" x2="12" y2="16"></line>
							</svg>
						</span>
						<span class="nav-link-title">
							Unduh
						</span>
					</a>
					<div class="dropdown-menu">
						<a href="<?= base_url()?>datapos"><div class="dropdown-item">Download Data</div></a>
						<?php echo anchor('unduh/go-hidro_1.3.3.apk','Android App','class="dropdown-item"','target="_blank');?>
						<?php echo anchor('https://apps.apple.com/id/app/go-hidro/id6456266862','IOS App','class="dropdown-item"','target="_blank');?>
					</div>
				</li>
				

				<li class="nav-item">
					<?php echo anchor('login/logout','<span class="nav-link-icon d-md-none d-lg-inline-block">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                         <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                         <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                         <path d="M7 12h14l-3 -3m0 6l3 -3"></path>
                      </svg>

                    </span>
                    <span class="nav-link-title">
                        Keluar
                    </span>','class="nav-link"')?>


				</li>

			</ul>
		</div>
	</div>
</div>