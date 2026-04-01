<style>
	.hide-scrollbar::-webkit-scrollbar {
		display: none;
	}
</style>
<div class="container-xl">
          <!-- Page title -->
          <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">
                  <?php echo ucfirst($this->uri->segment(1))?>
                </h2>
              </div>
            </div>
          </div>
        </div>
        <div class="page-body" >
      <!-- Konten-->
      <div class="container-xl" >
			  <div class="row row-cards hide-scrollbar px-0" style="max-height:70vh; overflow-y:scroll" id="cont">
			  <?php foreach ($data_konten as $key => $kt){?>
				  <div class="col-12">
					  <div class="card">
						  <div class="card-header">
							<h3 class="card-title"><strong><?= $kt->nama_kategori ?></strong> <?= $kt->kepanjangan ?></h3>
						  </div>
						  <div class="card-body">
							<div class="row row-cards">
								<?php foreach($kt->logger as $log){?>
									<div class="col-md-6 col-lg-4">
										<div class="card"> 
										<div class="card-status-top bg-<?= $log->color ?>"></div>
										<div class="ribbon bg-<?= $log->color ?>"> <?= $log->waktu ?>

											<div class="card-actions">
											<div class="dropdown">
												<a href="#" class="btn-icon" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												 <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round">
													   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
													   <line x1="9" y1="6" x2="20" y2="6"></line>
													   <line x1="9" y1="12" x2="20" y2="12"></line>
													   <line x1="9" y1="18" x2="20" y2="18"></line>
													   <line x1="5" y1="6" x2="5" y2="6.01"></line>
													   <line x1="5" y1="12" x2="5" y2="12.01"></line>
													   <line x1="5" y1="18" x2="5" y2="18.01"></line>
													</svg>

												</a>
												<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
													<div class="dropdown-item">
													<strong><a href="#" class="text-reset">Id Logger</a></strong>
													<label class="form-check m-0 ms-auto">
													 <?= $log->id_logger ?>
													</label>
													</div>

													<div class="dropdown-item">
													<strong><a href="#" class="text-reset">Status Logger</a></strong>
													<label class="form-check m-0 ms-auto">
													 <?= $log->status_logger ?>
													</label>
													</div>

													<div class="dropdown-item">
													<strong><a href="#" class="text-reset">Status SDCard</a></strong>
													<label class="form-check m-0 ms-auto">
													 <?= $log->sdcard ?>
													</label>
												</div>

												</div>
												</div>
												</div>
										</div>
										  <div class="card-header">
											  <div class="d-block">
												  <?php if ($log->bidang == 'irigasi') { ?> 
												 	 <h3 class=" mb-0 pb-0">Bidang Irigasi</h3>
												  <?php }else{?>
												  	<h3 class=" mb-0 pb-0">Bidang Hidrologi</h3>
												  <?php } ?>
												  
											  </div>
											
											  
										  </div>
									<div class="card-body p-0">
										<div class="text-center">
											<h3 class="fw-bold my-2"><?= $log->nama_lokasi ?></h3>
										</div>
									<div class="table-responsive">
									<table class="table table-vcenter card-table">
									  <thead>
										<tr>
										  <th>Parameter</th>
										  <th>Nilai Ukur</th>
										</tr>
									  </thead>
									  <tbody>
										  <?php foreach($log->param as $val) { ?>
										  <tr>
											  <td>
												  <?php if($val['nama_parameter'] == 'Akumulasi Curah Hujan'){ ?>
												  <?= $val['nama_parameter'] ?>
													<?php } else{?>
												  <a href="<?= (isset($val['link'])) ? $val['link'] : '' ?>"><?= $val['nama_parameter'] ?></a></td>
											  <?php } ?>
											  <td><?= $val['nilai'] ?> <?= ($val['nilai'] != '-') ? $val['satuan'] : '' ?></td>
										  </tr>
										  <?php } ?>
										<tr>
										</tr>
									  </tbody>
									</table>
									</div>
								   </div>
								   </div>
								   </div>
								<?php } ?>
							</div>
						  </div>
					  </div>
				  </div>
				 
			  <?php } ?>
				  
		  </div>
      </div>
      <!-- end Konten-->
        </div>
<script type="text/javascript">
	'use strict';
(function() {
  window.AutoScroll = function(el, options) {
	  // In case they forgot 'new'
	  if (!(this instanceof AutoScroll)) {
		  return new AutoScroll(el, options);
	  } 
	  
    this.el = el;
    this.speed = null;
    this.isBeingThrown = false;
    this.isMouseOver = false;
    this.isRunning = false;
    this.thrownInterval = null;
	 this.timeout = null;
    this.previousScrollTop = null;

    var defaults = {
      speed: 0,
      pauseBottom: 500,
      pauseStart: 500,
		requestAnimationFrame: true,
		timeoutRate: 10
    };

    if (options && typeof options === 'object') {
      this.options = extendDefaults(defaults, options);
    } else {
      this.options = defaults;
    }

    _init.call(this);
  }

  AutoScroll.prototype.autoScroll = function() {
    if (this.isRunning && !this.isBeingThrown && !this.isMouseOver) {
      if (this.el.scrollTop < this.el.scrollHeight - this.el.offsetHeight) {
			if(this.options.requestAnimationFrame) {
				this.el.scrollTop += this.speed;
				window.requestAnimationFrame(this.autoScroll.bind(this));	
			} else {
				this.el.scrollTop += this.speed;
				if (this.timeout) clearTimeout(this.timeout);
				this.timeout = setTimeout(this.autoScroll.bind(this), this.options.timeoutRate)
			}
      } else {
        this.isRunning = false;
        setTimeout(this.resetScroll.bind(this), this.options.pauseBottom);
      }
    } else {
      return;
    }
  }

  AutoScroll.prototype.startScroll = function() {
    this.isRunning = true;
    this.autoScroll.call(this);
  }

  AutoScroll.prototype.pauseScroll = function() {
    this.isRunning = false;
  }

  AutoScroll.prototype.resetScroll = function() {
    this.el.scrollTop = 0;
    setTimeout(this.startScroll.bind(this), this.options.pauseStart);
  }

  AutoScroll.prototype.mouseEnter = function(e) {
    this.isMouseOver = true;
    this.isRunning = false;
  }

  AutoScroll.prototype.mouseLeave = function(e) {
    this.isMouseOver = false;
    this.isRunning = true;
    this.startScroll.call(this);
  }

  AutoScroll.prototype.mobileTouchStart = function(e) {
    this.isBeingThrown = true;
  }

  AutoScroll.prototype.mobileTouchEnd = function(e) {
    this.thrownInterval = setInterval(this.wasThrown.bind(this), 10);
  }

  AutoScroll.prototype.wasThrown = function() {
    if (this.previousScrollTop !== this.el.scrollTop) this.previousScrollTop = this.el.scrollTop;
    else this.thrownEnd.call(this);
  }

  AutoScroll.prototype.thrownEnd = function() {
    clearInterval(this.thrownInterval);
    this.isBeingThrown = false;
    this.startScroll.call(this);
  }

  // Private Methods
  function _init() {
    this.speed = _setSpeed(this.options.speed);
    _initEvents.call(this);
    setTimeout(this.startScroll.bind(this), this.options.pauseStart);
  }

  function _initEvents() {
    this.el.addEventListener('mouseenter', this.mouseEnter.bind(this));
    this.el.addEventListener('mouseleave', this.mouseLeave.bind(this));
    this.el.addEventListener('touchstart', this.mobileTouchStart.bind(this));
    this.el.addEventListener('touchend', this.mobileTouchEnd.bind(this));
  }

  function _setSpeed(scrollDistance) {
    return Math.ceil(scrollDistance / 60);
  }

  // Utility Methods
  function extendDefaults(source, properties) {
    var property;
    for (property in properties) {
      if (properties.hasOwnProperty(property)) {
        source[property] = properties[property];
      }
    }
    return source;
  }

})();

var element2 = document.getElementById('cont');

var Scroller2 = new AutoScroll(element2, {
  speed: 1
});

</script>