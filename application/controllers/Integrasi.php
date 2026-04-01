<?php

class Integrasi extends CI_Controller
{


	public function param_belongs_to_logger()
	{
		$id_param = $this->input->get('id_param');
		$id_logger = $this->input->get('id_logger');

		echo (bool) $this->db->where('id', $id_param)
			->where('logger_code', $id_logger)
			->count_all_results('t_sensor');
	}

	function analisapertanggal()
	{
		$idlogger = $this->input->get('idlogger');
		$idsensor = $this->input->get('idsensor');
		$tabel = $this->input->get('tabel');
		$tanggal = $this->input->get('tanggal');

		$data = array();
		$min = array();
		$max = array();
		$tb_main = $this->db->where('code_logger', $idlogger)->get('t_logger')->row();
		$qparam = $this->db->query("SELECT * FROM t_sensor where id='" . $idsensor . "'")->result();
		foreach ($qparam as $param) {
			if ($tabel == 't_klimatologi' && $param->kolom_sensor == 'sensor8') {
				$namaSensor = 'Akumulasi_' . $param->nama_parameter;
				$select = 'sum(' . $param->kolom_sensor . ')as ' . $namaSensor;
			} elseif ($tabel == 'arr' && $param->kolom_sensor == 'sensor9') {
				$namaSensor = 'Akumulasi_' . $param->nama_parameter;
				$select = 'sum(' . $param->kolom_sensor . ')as ' . $namaSensor;
			} elseif ($param->tipe_graf == 'column') {
				$namaSensor = 'Akumulasi_' . $param->nama_parameter;
				$select = 'sum(' . $param->kolom_sensor . ')as ' . $namaSensor;
			} else {
				$namaSensor = 'Rerata_' . $param->nama_parameter;
				$select = 'avg(' . $param->kolom_sensor . ')as ' . $namaSensor;
			}
			$sensor = $param->kolom_sensor;
			$satuan = $param->satuan;
			$namaparameter = $param->nama_parameter;
		}

		$query_data = $this->db->query("SELECT waktu," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tb_main->tabel_main . " where code_logger='" . $idlogger . "' and waktu >= '" . $tanggal . " 00:00' and waktu <= '" . $tanggal . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu);");

		$hsl = $query_data->result();
		foreach ($hsl as $datalog) {
			$h = $datalog->$namaSensor;
			$max_value = $datalog->max;
			$min_value = $datalog->min;

			$waktu[] = date('Y-m-d H', strtotime($datalog->waktu)) . ":00";
			$data[] = number_format($h, 2, '.', '');
			$min[] = number_format($min_value, 2, '.', '');
			$max[] = number_format($max_value, 2, '.', '');
		}
		if ($hsl) {
			$stts = 'sukses';
			$dataAnalisa = array(
				'status' => 'sukses',
				'idLogger' => $idlogger,
				'nosensor' => $sensor,
				'namaSensor' => $namaSensor,
				'satuan' => $satuan,
				'waktu' => $waktu,
				'tipegraf' => $param->tipe_graf,
				'data' => $data,
				'datamin' => $min,
				'datamax' => $max,
			);
		} else {
			$stts = 'error';
			$dataAnalisa = null;
		}
		echo json_encode(
			array(
				'status' => $stts,
				'data' => $dataAnalisa
			)
		);
	}


	function komparasi()
	{
		$id_logger_real = $this->input->post('id_logger');
		$tanggal = $this->input->post('tanggal');
		$session_logger = json_decode($this->input->post('session_logger'));

		$is_single = count($session_logger) === 1;
		$logger = $this->db
			->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
			->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')
			->where('code_logger', $id_logger_real)
			->get('t_logger')->row();

		$logger_category = $this->db
			->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')
			->where('t_logger.icon', $logger->icon)
			->where('t_logger.user_id', '4')
			->get('t_logger')->result_array();

		$parameter = $this->db
			->where('logger_code', $id_logger_real)
			->where('parameter_utama', '1')
			->get('t_sensor')->row();
		$list_category = array_map(function ($row) use ($session_logger) {

			return [
				'id_logger' => $row['code_logger'] . '_psda',
				'kategori_log' => $row['katlog_id'],
				'nama_lokasi' => $row['nama_lokasi'],
			];
		}, array_filter($logger_category, function ($row) use ($session_logger) {
			return !in_array($row['code_logger'] . '_psda', $session_logger);
		}));

		$list_category[] = [
			'id_logger' => $id_logger_real . '_psda',
			'kategori_log' => $logger->katlog_id,
			'nama_lokasi' => $logger->nama_lokasi,
		];

		$is_spline = $parameter->satuan != 'mm';
		$agg_func = $is_spline ? 'sum' : 'avg';

		$sensor_name = ($is_spline ? 'Rerata_' : 'Akumulasi_') . $parameter->alias_sensor;

		$query = $this->db->query("
                SELECT 
                    waktu,
                    HOUR(waktu)  as jam,
                    DAY(waktu)   as hari,
                    MONTH(waktu) as bulan,
                    YEAR(waktu)  as tahun,
                    {$agg_func}({$parameter->field_sensor}) as {$sensor_name}
                FROM {$logger->tabel}
                WHERE code_logger='{$id_logger_real}'
                  AND waktu BETWEEN '{$tanggal} 00:00' AND '{$tanggal} 23:59'
                GROUP BY YEAR(waktu), MONTH(waktu), DAY(waktu), HOUR(waktu)
                ORDER BY waktu ASC
            ");

		$data_chart = [];
		$data_value = [];
		foreach ($query->result() as $row) {
			$val = number_format($row->$sensor_name, 3, '.', '');
			$data_chart[] = "[ Date.UTC({$row->tahun}," . ($row->bulan - 1) . ",{$row->hari},{$row->jam}),{$val} ]";
			$data_value[] = [
				'nilai' => $val,
				'waktu' => $row->waktu,
				'jam' => $row->jam,
			];
		}
		for ($i = 0; $i < 24; $i++) {
			if (array_search($i, array_column($data_value, 'jam')) === false) {
				$jam = str_pad($i, 2, '0', STR_PAD_LEFT);
				$data_value[] = [
					'nilai' => '-',
					'jam' => $jam,
					'waktu' => "{$tanggal} {$jam}:00:00"
				];
			}
		}
		array_multisort(array_column($data_value, 'waktu'), SORT_ASC, $data_value);

		$y_axis = ($is_single || $is_spline) ? 0 : 1;
		if ($logger->icon == 'ws') {
			$kat_name = 'AWS';
		} elseif ($logger->icon == 'arr') {
			$kat_name = 'ARR';
		} else {
			$kat_name = 'AWLR';
		}
		$selected = [
			'id_logger' => $id_logger_real . '_psda',
			'nama_kategori' => $kat_name,
			'nama_lokasi' => $logger->nama_lokasi,
			'list_kategori' => $list_category,
			'y_axis' => $y_axis,
			'parameter' => $parameter,
			'nama_chart' => $sensor_name,
			'data' => $data_chart,
			'data_nilai' => $data_value,
			'tipe_graf' => $is_spline ? 'spline' : 'column',
			'satuan' => $parameter->satuan
		];
		echo json_encode($selected);
	}

	function get_logger()
	{
		$id_kategori = $this->input->post('id_kategori');
		$sess = json_decode($this->input->post('logger_komparasi'));

		if ($id_kategori === 'arr') {
			$data = $this->db->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_logger.icon', 'arr')->where('t_logger.user_id', '4')->get('t_logger')->result_array();
		} elseif ($id_kategori === 'awr') {
			$data = $this->db->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_logger.icon', 'ws')->where('t_logger.user_id', '4')->get('t_logger')->result_array();
		} else {
			$data = $this->db->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_logger.katlog_id', '8')->where('t_logger.user_id', '4')->get('t_logger')->result_array();
		}
		$list = '';
		foreach ($data as $key => $val) {
			if (!in_array($val["code_logger"] . '_psda', $sess)) {
				$list .= '<option value="' . $val["code_logger"] . '_psda">' . $val["nama_lokasi"] . '</option>';
			}
		}
		echo $list;
	}

	public function monitoring()
	{
		$id_kategori = $this->input->get('id_kategori');
		$dari = $this->input->get('dari');
		$sampai = $this->input->get('sampai');

		$kategoriConfig = [
			'1' => ['tabel' => 'weather_station', 'sensors' => ['sensor9', 'sensor8'], 'type' => 'sum', 'icon' => 'ws'],
			'2' => ['tabel' => 'weather_station', 'sensors' => ['sensor9', 'sensor8'], 'type' => 'sum', 'icon' => 'arr'],
			'default' => ['tabel' => 'awlr', 'sensors' => ['sensor1'], 'type' => 'avg', 'icon' => null]
		];
		$cfg = $kategoriConfig[$id_kategori] ?? $kategoriConfig['default'];
		$kat = $this->db->get_where('kategori_logger', ['id_katlogger' => $id_kategori])->row();

		$this->db->select('t_logger.code_logger, t_logger.nama_logger, t_logger.icon, t_lokasi.nama_lokasi')
			->join('t_lokasi', 't_logger.lokasi_id=t_lokasi.id_lokasi')
			->where('t_logger.user_id', '4')
			->order_by('code_logger', 'asc');
		if (!empty($cfg['icon']))
			$this->db->where('t_logger.icon', $cfg['icon']);
		if ($id_kategori !== '1' && $id_kategori !== '2')
			$this->db->where('katlog_id', $id_kategori);
		$loggers = $this->db->get('t_logger')->result_array();

		$result = [];
		foreach ($loggers as $val) {
			$sensor = null;
			foreach ($cfg['sensors'] as $f) {
				$sensor = $this->db->get_where('t_sensor', ['logger_code' => $val['code_logger'], 'field_sensor' => $f])->row();
				if ($sensor)
					break;
			}
			if (!$sensor)
				continue;

			$select = $cfg['type'] . '(' . $sensor->field_sensor . ') as nilai';
			$query_data = $this->db->query("
            SELECT HOUR(waktu) as jam, $select
            FROM {$cfg['tabel']}
            WHERE code_logger='{$val['code_logger']}'
            AND waktu BETWEEN '$dari' AND '$sampai'
            GROUP BY HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu)
            ORDER BY waktu ASC
        ")->result_array();

			$hours = array_column($query_data, 'jam');
			for ($i = 0; $i < 24; $i++) {
				if (!in_array($i, $hours)) {
					$query_data[] = ['jam' => sprintf("%02d", $i), 'nilai' => '-'];
				}
			}
			array_multisort(array_column($query_data, 'jam'), SORT_ASC, $query_data);

			foreach ($query_data as $k => $row) {
				if ($row['nilai'] !== '-') {
					$row['nilai'] = number_format($row['nilai'], 2);
					if ($id_kategori == '1' || $id_kategori == '2') {
						$row['warna'] =
							$row['nilai'] < 0.1 ? 'white' :
							($row['nilai'] < 1 ? '#70cddd' :
								($row['nilai'] < 5 ? '#35549d' :
									($row['nilai'] < 10 ? '#fef216' :
										($row['nilai'] < 20 ? '#f47e2c' : '#ed1c24'))));
					} else {
						$row['warna'] = 'white';
					}
				} else {
					$row['warna'] = 'white';
				}
				$query_data[$k] = $row;
			}

			$result[] = [
				'id_logger' => $val['code_logger'],
				'icon' => $val['icon'],
				'nama_logger' => $val['nama_lokasi'],
				'data' => $query_data,
				'id_param' => $sensor->id,
				'tabel' => $cfg['tabel'],
				'controller' => $kat->controller
			];
		}
		echo json_encode(['nama_logger' => $kat->nama_kategori, 'data_rekap' => $result]);
	}

	public function get_default_param_for_logger($id_logger)
	{

		$row = $this->db->where('logger_code', $id_logger)
			->order_by('id', 'ASC')
			->limit(1)
			->get('t_sensor')
			->row();
		echo $row ? $row->id : null;
	}
	function plataran()
	{
		$data = $this->db->from('menu_logger')->join('kategori_logger', 'kategori_logger.id_katlogger = menu_logger.katlog_id')->where('user_id', '4')->where('id_katlogger', '8')->order_by('id_katlogger')->get()->result();
		$cek = $this->db->query("SELECT * FROM t_logger WHERE user_id = '4' and icon = 'ws' GROUP BY icon")->result_array();
		foreach ($data as $key => $row) {
			if ($row->temp_tabel) {
				$dataMenu[] = array(
					'id_katlogger' => $row->id_katlogger,
					'menu' => $row->nama_kategori,
					'nama_kategori' => $row->nama_kategori,
					'tabel' => $row->temp_tabel,
					'controller' => $row->controller,
					'tabel_besar' => $row->tabel
				);
			} else {
				$dataMenu[] = array(
					'id_katlogger' => $row->id_katlogger,
					'menu' => $row->nama_kategori,
					'nama_kategori' => $row->nama_kategori,
					'tabel' => $row->tabel,
					'controller' => $row->controller,
					'tabel_besar' => $row->tabel,
				);
			}
		}
		$dataMenu[] = array(
			'id_katlogger' => '2',
			'menu' => 'Curah Hujan',
			'nama_kategori' => 'Curah Hujan',
			'tabel' => 'temp_weather_station',
			'controller' => 'curah_hujan',
			'tabel_besar' => 'weather_station'
		);
		array_multisort(array_column($dataMenu, "id_katlogger"), SORT_ASC, $dataMenu);
		foreach ($dataMenu as $key => $kat) {
			$tabel = $kat['tabel'];
			$nama_kat = $kat['nama_kategori'];
			$kategori[$nama_kat]['nama'] = $kat['nama_kategori'];
			if ($kat['id_katlogger'] == '2') {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.user_id', '4')->where('t_logger.icon', 'arr')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			} elseif ($kat['id_katlogger'] == '1') {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.katlog_id', $kat['id_katlogger'])->where('t_logger.user_id', '4')->where('t_logger.icon', 'ws')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			} else {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.katlog_id', $kat['id_katlogger'])->where('t_logger.user_id', '4')->where('t_logger.id_logger', '10114')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			}
			$kategori[$nama_kat]['logger'] = $data_logger;
			foreach ($data_logger as $k2 => $lg) {
				$waktu = $this->db->where('code_logger', $lg['code_logger'])->get($tabel)->row();
				if ($waktu) {
					$kategori[$nama_kat]['logger'][$k2]['waktu'] = $waktu->waktu;
					if ($waktu->sensor13 == '1') {
						$sdcard = 'OK';
					} else {
						$sdcard = 'Bermasalah';
					}
					$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
					$kategori[$nama_kat]['logger'][$k2]['status_sd'] = $sdcard;
					$id_logger = $lg['code_logger'];
					$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $id_logger))->row();
					if ($cek_perbaikan) {
						$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Perbaikan';
						$kategori[$nama_kat]['logger'][$k2]['color'] = '#7e6126';
					} else {
						if ($waktu->waktu > $awal) {
							$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Koneksi Terhubung';
							$kategori[$nama_kat]['logger'][$k2]['color'] = '#2fb344';
						} else {
							$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Terputus';
							$kategori[$nama_kat]['logger'][$k2]['color'] = '#181823';
						}
					}
					$kategori[$nama_kat]['logger'][$k2]['id_logger'] = $id_logger;
					$kategori[$nama_kat]['logger'][$k2]['status_aset'] = 'DPUPESDM DIY';
					if ($id_logger == '10114' and $kat['id_katlogger'] == '2') {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' and alias_sensor != 'Kedalaman_Air_Sumur' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					} elseif ($id_logger == '10114' and $kat['id_katlogger'] == '13') {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' and alias_sensor != 'Curah_Hujan' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					} else {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					}

					$kategori[$kat['nama_kategori']]['logger'][$k2]['param'] = $parameter_sensor;
					foreach ($parameter_sensor as $k3 => $param) {
						if ($id_logger == '10114' and $kat['id_katlogger'] == '13') {
							$get = 'id_logger=' . $id_logger . '&id_param=' . $param['id'] . '_psda';
						} else {
							$get = 'id_logger=' . $id_logger . '&id_param=' . $param['id'] . '_psda';
						}

						$kolom = $param['field_sensor'];
						if ($param['set_debit'] == '1') {
							$q_datasheet = $this->db->get_where('datasheet_debit', array('id_logger' => $id_logger))->row();

							$datadebit = $q_datasheet->c * pow(($waktu->$kolom + $q_datasheet->a), $q_datasheet->b);
							$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nilai'] = number_format($datadebit, 3);

						} else {
							$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nilai'] = number_format($waktu->$kolom, 3);
						}
						$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nama_parameter'] = $param['alias_sensor'];
						$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['link'] = 'https://bbwsso.monitoring4system.com/analisa/set_sensordash?' . $get;
					}
				} else {
					$kategori[$nama_kat]['logger'][$k2]['waktu'] = '';
					$kategori[$nama_kat]['logger'][$k2]['param'] = [];
				}
			}
		}
		echo json_encode($kategori);
	}

	function beranda_req()
	{

		$cek = $this->db->query("SELECT * FROM t_logger WHERE user_id = '4' and icon = 'ws' GROUP BY icon")->result_array();

		$data = $this->db->from('menu_logger')->join('kategori_logger', 'kategori_logger.id_katlogger = menu_logger.katlog_id')->where('user_id', '4')->order_by('id_katlogger')->get()->result();
		foreach ($data as $key => $row) {
			if ($row->temp_tabel) {
				$dataMenu[] = array(
					'id_katlogger' => $row->id_katlogger,
					'menu' => $row->nama_kategori,
					'nama_kategori' => $row->nama_kategori,
					'tabel' => $row->temp_tabel,
					'controller' => $row->controller,
					'tabel_besar' => $row->tabel
				);
			} else {
				$dataMenu[] = array(
					'id_katlogger' => $row->id_katlogger,
					'menu' => $row->nama_kategori,
					'nama_kategori' => $row->nama_kategori,
					'tabel' => $row->tabel,
					'controller' => $row->controller,
					'tabel_besar' => $row->tabel,
				);
			}
		}
		$dataMenu[] = array(
			'id_katlogger' => '2',
			'menu' => 'Curah Hujan',
			'nama_kategori' => 'Curah Hujan',
			'tabel' => 'temp_weather_station',
			'controller' => 'curah_hujan',
			'tabel_besar' => 'weather_station'
		);
		array_multisort(array_column($dataMenu, "id_katlogger"), SORT_ASC, $dataMenu);

		header('Content-Type: application/json');

		$raw = file_get_contents("php://input");
		$json = json_decode($raw, true);

		if (!isset($json['logger_array']) || !is_array($json['logger_array'])) {
			echo json_encode([
				"status" => false,
				"message" => "logger_array harus berupa array"
			]);
			return;
		}

		$log_array = $json['logger_array'];

		foreach ($dataMenu as $key => $kat) {
			$tabel = $kat['tabel'];
			$nama_kat = $kat['nama_kategori'];
			$kategori[$nama_kat]['nama'] = $kat['nama_kategori'];
			if ($kat['id_katlogger'] == '2') {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.user_id', '4')->where('t_logger.icon', 'arr')->where_in('t_logger.code_logger', $log_array)->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			} elseif ($kat['id_katlogger'] == '1') {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.katlog_id', $kat['id_katlogger'])->where('t_logger.user_id', '4')->where_in('t_logger.code_logger', $log_array)->where('t_logger.icon', 'ws')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			} else {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.katlog_id', $kat['id_katlogger'])->where('t_logger.user_id', '4')->where_in('t_logger.code_logger', $log_array)->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			}
			$kategori[$nama_kat]['logger'] = $data_logger;
			foreach ($data_logger as $k2 => $lg) {
				$waktu = $this->db->where('code_logger', $lg['code_logger'])->get($tabel)->row();
				if ($waktu) {
					$kategori[$nama_kat]['logger'][$k2]['waktu'] = $waktu->waktu;
					if ($waktu->sensor13 == '1') {
						$sdcard = 'OK';
					} else {
						$sdcard = 'Bermasalah';
					}
					$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
					$kategori[$nama_kat]['logger'][$k2]['status_sd'] = $sdcard;
					$id_logger = $lg['code_logger'];
					$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $id_logger))->row();
					if ($cek_perbaikan) {
						$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Perbaikan';
						$kategori[$nama_kat]['logger'][$k2]['color'] = '#7e6126';
					} else {
						if ($waktu->waktu > $awal) {
							$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Koneksi Terhubung';
							$kategori[$nama_kat]['logger'][$k2]['color'] = '#2fb344';
						} else {
							$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Terputus';
							$kategori[$nama_kat]['logger'][$k2]['color'] = '#181823';
						}
					}
					$kategori[$nama_kat]['logger'][$k2]['id_logger'] = $id_logger;
					$kategori[$nama_kat]['logger'][$k2]['status_aset'] = 'DPUPESDM DIY';
					if ($id_logger == '10114' and $kat['id_katlogger'] == '2') {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' and alias_sensor != 'Kedalaman_Air_Sumur' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					} elseif ($id_logger == '10114' and $kat['id_katlogger'] == '13') {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' and alias_sensor != 'Curah_Hujan' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					} else {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					}

					$kategori[$kat['nama_kategori']]['logger'][$k2]['param'] = $parameter_sensor;
					foreach ($parameter_sensor as $k3 => $param) {
						if ($id_logger == '10114' and $kat['id_katlogger'] == '13') {
							$get = 'id_logger=' . $id_logger . '&id_param=' . $param['id'] . '_psda';
						} else {
							$get = 'id_logger=' . $id_logger . '&id_param=' . $param['id'] . '_psda';
						}

						$kolom = $param['field_sensor'];
						if ($param['set_debit'] == '1') {
							$q_datasheet = $this->db->get_where('datasheet_debit', array('id_logger' => $id_logger))->row();

							$datadebit = $q_datasheet->c * pow(($waktu->$kolom + $q_datasheet->a), $q_datasheet->b);
							$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nilai'] = number_format($datadebit, 3);

						} else {
							$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nilai'] = number_format($waktu->$kolom, 3);
						}
						$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nama_parameter'] = $param['alias_sensor'];
						$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['link'] = 'https://bbwsso.monitoring4system.com/analisa/set_sensordash?' . $get;
					}
				} else {
					$kategori[$nama_kat]['logger'][$k2]['waktu'] = '';
					$kategori[$nama_kat]['logger'][$k2]['param'] = [];
				}
			}
		}
		echo json_encode($kategori);
	}

	function beranda()
	{
		$data = $this->db->from('menu_logger')->join('kategori_logger', 'kategori_logger.id_katlogger = menu_logger.katlog_id')->where('user_id', '4')->order_by('id_katlogger')->get()->result();
		$cek = $this->db->query("SELECT * FROM t_logger WHERE user_id = '4' and icon = 'ws' GROUP BY icon")->result_array();
		foreach ($data as $key => $row) {
			if ($row->temp_tabel) {
				$dataMenu[] = array(
					'id_katlogger' => $row->id_katlogger,
					'menu' => $row->nama_kategori,
					'nama_kategori' => $row->nama_kategori,
					'tabel' => $row->temp_tabel,
					'controller' => $row->controller,
					'tabel_besar' => $row->tabel
				);
			} else {
				$dataMenu[] = array(
					'id_katlogger' => $row->id_katlogger,
					'menu' => $row->nama_kategori,
					'nama_kategori' => $row->nama_kategori,
					'tabel' => $row->tabel,
					'controller' => $row->controller,
					'tabel_besar' => $row->tabel,
				);
			}
		}
		$dataMenu[] = array(
			'id_katlogger' => '2',
			'menu' => 'Curah Hujan',
			'nama_kategori' => 'Curah Hujan',
			'tabel' => 'temp_weather_station',
			'controller' => 'curah_hujan',
			'tabel_besar' => 'weather_station'
		);
		array_multisort(array_column($dataMenu, "id_katlogger"), SORT_ASC, $dataMenu);
		foreach ($dataMenu as $key => $kat) {
			$tabel = $kat['tabel'];
			$nama_kat = $kat['nama_kategori'];
			$kategori[$nama_kat]['nama'] = $kat['nama_kategori'];
			if ($kat['id_katlogger'] == '2') {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.user_id', '4')->where('t_logger.icon', 'arr')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			} elseif ($kat['id_katlogger'] == '1') {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.katlog_id', $kat['id_katlogger'])->where('t_logger.user_id', '4')->where('t_logger.icon', 'ws')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			} else {
				$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.katlog_id', $kat['id_katlogger'])->where('t_logger.user_id', '4')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
			}
			$kategori[$nama_kat]['logger'] = $data_logger;
			foreach ($data_logger as $k2 => $lg) {
				$waktu = $this->db->where('code_logger', $lg['code_logger'])->get($tabel)->row();
				if ($waktu) {
					$kategori[$nama_kat]['logger'][$k2]['waktu'] = $waktu->waktu;
					if ($waktu->sensor13 == '1') {
						$sdcard = 'OK';
					} else {
						$sdcard = 'Bermasalah';
					}
					$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
					$kategori[$nama_kat]['logger'][$k2]['status_sd'] = $sdcard;
					$id_logger = $lg['code_logger'];
					$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $id_logger))->row();
					if ($cek_perbaikan) {
						$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Perbaikan';
						$kategori[$nama_kat]['logger'][$k2]['color'] = '#7e6126';
					} else {
						if ($waktu->waktu > $awal) {
							$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Koneksi Terhubung';
							$kategori[$nama_kat]['logger'][$k2]['color'] = '#2fb344';
						} else {
							$kategori[$nama_kat]['logger'][$k2]['status_logger'] = 'Terputus';
							$kategori[$nama_kat]['logger'][$k2]['color'] = '#181823';
						}
					}
					$kategori[$nama_kat]['logger'][$k2]['id_logger'] = $id_logger;
					$kategori[$nama_kat]['logger'][$k2]['status_aset'] = 'DPUPESDM DIY';
					if ($id_logger == '10114' and $kat['id_katlogger'] == '2') {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' and alias_sensor != 'Kedalaman_Air_Sumur' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					} elseif ($id_logger == '10114' and $kat['id_katlogger'] == '13') {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' and alias_sensor != 'Curah_Hujan' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					} else {
						$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					}

					$kategori[$kat['nama_kategori']]['logger'][$k2]['param'] = $parameter_sensor;
					foreach ($parameter_sensor as $k3 => $param) {
						if ($id_logger == '10114' and $kat['id_katlogger'] == '13') {
							$get = 'id_logger=' . $id_logger . '&id_param=' . $param['id'] . '_psda';
						} else {
							$get = 'id_logger=' . $id_logger . '&id_param=' . $param['id'] . '_psda';
						}

						$kolom = $param['field_sensor'];
						if ($param['set_debit'] == '1') {
							$q_datasheet = $this->db->get_where('datasheet_debit', array('id_logger' => $id_logger))->row();

							$datadebit = $q_datasheet->c * pow(($waktu->$kolom + $q_datasheet->a), $q_datasheet->b);
							$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nilai'] = number_format($datadebit, 3);

						} else {
							$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nilai'] = number_format($waktu->$kolom, 3);
						}
						$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['nama_parameter'] = $param['alias_sensor'];
						$kategori[$nama_kat]['logger'][$k2]['param'][$k3]['link'] = 'https://bbwsso.monitoring4system.com/analisa/set_sensordash?' . $get;
					}
				} else {
					$kategori[$nama_kat]['logger'][$k2]['waktu'] = '';
					$kategori[$nama_kat]['logger'][$k2]['param'] = [];
				}
			}
		}
		echo json_encode($kategori);
	}

	public function horizontal()
	{
		$id_kategori = $this->input->get('id_kategori');
		$tanggal_rekap = $this->input->get('dari');
		$tanggal_rekap2 = $this->input->get('sampai');

		// ==== Ambil daftar logger ====
		$this->db->select('t_logger.code_logger as id_logger, t_logger.nama_logger,
                       kategori_logger.tabel, t_logger.katlog_id, kategori_logger.controller')
			->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
			->where('t_logger.user_id', '4');

		if ($id_kategori === 'arr') {
			$this->db->where('t_logger.icon', 'arr');
		} elseif ($id_kategori === 'awr') {
			$this->db->where('t_logger.icon', 'ws');
		} else {
			$this->db->where('t_logger.katlog_id', $id_kategori);
		}

		$data_logger = $this->db->get('t_logger')->result_array();
		if (!$data_logger) {
			echo json_encode([]);
			return;
		}

		$tabel_data = $data_logger[0]['tabel'];
		$logger_meta = [];
		$loggers = [];
		foreach ($data_logger as $L) {
			$logger_meta[$L['id_logger']] = [
				'katlog_id' => (string) $L['katlog_id'],
				'controller' => $L['controller'],
				'nama' => $L['nama_logger'],
			];
			$loggers[] = $L['id_logger'];
		}

		// ==== Sensor utama ====
		$sensors = $this->db->where_in('logger_code', $loggers)
			->where('parameter_utama', '1')
			->get('t_sensor')
			->result();
		$sensor_map = [];
		foreach ($sensors as $s) {
			$sensor_map[$s->logger_code] = [
				'field' => $s->field_sensor,
				'id' => $s->id
			];
		}

		// ==== CASE expr untuk agregasi ====
		$cases = [];
		foreach ($loggers as $code) {
			if (!isset($sensor_map[$code]))
				continue;
			$col = $sensor_map[$code]['field'];
			$cases[] = "WHEN code_logger='{$code}' THEN {$col}";
		}
		if (!$cases) {
			echo json_encode([]);
			return;
		}
		$case_expr = "(CASE " . implode(' ', $cases) . " END)";

		$query_data = $this->db->select("
        code_logger,
        DATE_FORMAT(waktu,'%Y-%m-%d %H:00:00') as jam,
        SUM($case_expr) as sum_val,
        AVG($case_expr) as avg_val
    ", false)
			->from($tabel_data)
			->where_in('code_logger', $loggers)
			->where("waktu >=", $tanggal_rekap)
			->where("waktu <=", $tanggal_rekap2)
			->group_by(['code_logger', 'jam'])
			->order_by('code_logger ASC, jam ASC')
			->get()->result_array();

		// ==== Mapping hasil ====
		$rekap_map = [];
		foreach ($query_data as $row) {
			$code = $row['code_logger'];
			$jam = $row['jam'];
			$kat = $logger_meta[$code]['katlog_id'] ?? '';
			if ($kat === '8') {
				$nilai = $row['avg_val'];  // biarin mentah dulu
			} else {
				$nilai = $row['sum_val'];
			}
			$rekap_map[$code][$jam] = is_null($nilai) ? null : (float) $nilai;
		}

		// ==== Susun data ====
		$start = strtotime($tanggal_rekap);
		$end = strtotime($tanggal_rekap2);
		$sr = [];
		$data_rekap = [];

		foreach ($loggers as $code) {
			if (!isset($sensor_map[$code]))
				continue;
			$sensor_id = $sensor_map[$code]['id'];
			$result = [];
			$total = 0;

			for ($ts = $start; $ts <= $end; $ts += 3600) {
				$date = date('Y-m-d H:00:00', $ts);

				$nilai = isset($rekap_map[$code][$date]) ? $rekap_map[$code][$date] : null;

				if ($nilai === null) {
					$result[] = ['waktu' => $date, 'nilai' => '-', 'warna' => '#D5F0C1'];
				} else {
					$v = (float) $nilai;
					$result[] = ['waktu' => $date, 'nilai' => $v, 'warna' => '#D5F0C1'];
					$total += $v;
				}

				$sr[date('Y-m-d', $ts)][] = date('H:00', $ts);
			}

			$data_rekap[] = [
				'id_logger' => $code,
				'nama_logger' => $logger_meta[$code]['nama'],
				'controller' => $logger_meta[$code]['controller'],
				'id_param' => $sensor_id,
				'link' => 'https://bbwsso.monitoring4system.com/analisa/set_sensordash?id_logger=' . $code . '&id_param=' . $sensor_id . '_psda',
				'data' => $result,
				'total' => $total,
				'tabel' => in_array($id_kategori, ['1', '2']) ? $logger_meta[$code]['controller'] : 'awlr'
			];
		}

		$jumlah_jam = array_sum(array_map('count', $sr));

		// ==== Warna hanya untuk ARR & AWR ====
		if ($id_kategori == 'arr' or $id_kategori == 'awr') {
			$warnaJam = [
				[0, 0, '#D5F0C1'],
				[0.1, 1, '#70cddd'],
				[1, 5, '#35549d'],
				[5, 10, '#fef216'],
				[10, 20, '#f47e2c'],
				[20, INF, '#ed1c24']
			];
			$warnaTotal = [
				[0, 0, '#98bc85'],
				[0.1, 5, '#70cddd'],
				[5, 20, '#35549d'],
				[20, 50, '#fef216'],
				[50, 100, '#f47e2c'],
				[100, INF, '#ed1c24']
			];
			foreach ($data_rekap as &$dtr) {
				foreach ($dtr['data'] as &$row) {
					if ($row['nilai'] === '-') {
						$row['warna'] = '#D5F0C1';
						continue;
					}
					$v = (float) $row['nilai'];
					$row['warna'] = '#D5F0C1';
					foreach ($warnaJam as $r) {
						if ($v >= $r[0] && $v < $r[1]) {
							$row['warna'] = $r[2];
							break;
						}
					}
				}
				$dtr['warna'] = '#98bc85';
				$vt = (float) $dtr['total'];
				foreach ($warnaTotal as $r) {
					if ($vt >= $r[0] && $vt < $r[1]) {
						$dtr['warna'] = $r[2];
						break;
					}
				}
			}
			unset($dtr, $row);
		}

		$nama_kategori = $this->db->select('nama_kategori')
			->get_where('kategori_logger', ['id_katlogger' => $id_kategori])
			->row()->nama_kategori ?? null;

		$data = [
			'data_rekap' => $data_rekap,
			'hari' => $sr,
			'jam' => $jumlah_jam,
			'nama_logger' => $nama_kategori
		];
		echo json_encode($data);
	}

	public function vertikal()
	{
		$id_kategori = $this->input->get('id_kategori');
		$tanggal_rekap = $this->input->get('dari');
		$tanggal_rekap2 = $this->input->get('sampai');

		$data['data_rekap'] = [];

		if ($id_kategori === 'arr') {
			$data['logger'] = $this->db->select('t_logger.code_logger as id_logger, t_logger.nama_logger, kategori_logger.tabel, t_logger.katlog_id, kategori_logger.controller')
				->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
				->where('t_logger.icon', 'arr')->where('t_logger.user_id', '4')->get('t_logger')->result_array();
		} elseif ($id_kategori === 'awr') {
			$data['logger'] = $this->db->select('t_logger.code_logger as id_logger, t_logger.nama_logger, kategori_logger.tabel, t_logger.katlog_id, kategori_logger.controller')
				->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
				->where('t_logger.icon', 'ws')->where('t_logger.user_id', '4')->get('t_logger')->result_array();
		} else {
			$data['logger'] = $this->db->select('t_logger.code_logger as id_logger, t_logger.nama_logger, kategori_logger.tabel, t_logger.katlog_id, kategori_logger.controller')
				->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
				->where('t_logger.katlog_id', $id_kategori)->where('t_logger.user_id', '4')->get('t_logger')->result_array();
		}

		if (!$data['logger']) {
			echo json_encode(['data_rekap' => [], 'new' => [], 'hari' => [], 'jam' => 0]);
			return;
		}

		$loggers = array_column($data['logger'], 'id_logger');
		$tabel = $data['logger'][0]['tabel'];
		$meta = [];
		foreach ($data['logger'] as $L) {
			$meta[$L['id_logger']] = [
				'katlog_id' => (string) $L['katlog_id'],
				'controller' => $L['controller'],
				'nama' => $L['nama_logger']
			];
		}

		$sensors = $this->db->where_in('logger_code', $loggers)->where('parameter_utama', '1')->get('t_sensor')->result();
		$sensor_map = [];
		foreach ($sensors as $s) {
			$sensor_map[$s->logger_code] = [
				'field' => $s->field_sensor,
				'id' => $s->id
			];
		}

		$cases = [];
		foreach ($loggers as $code) {
			if (!isset($sensor_map[$code]))
				continue;
			$col = $sensor_map[$code]['field'];
			$cases[] = "WHEN code_logger='{$code}' THEN {$col}";
		}
		$case_expr = $cases ? "(CASE " . implode(' ', $cases) . " END)" : null;

		$query_data = [];
		if ($case_expr) {
			$query_data = $this->db->select("

        code_logger,
        DATE_FORMAT(waktu,'%Y-%m-%d %H:00:00') as jam,
        SUM($case_expr) as sum_val,
        AVG($case_expr) as avg_val
    ", false)->from($tabel)
				->where_in('code_logger', $loggers)
				->where("waktu >=", $tanggal_rekap)
				->where("waktu <=", $tanggal_rekap2)
				->group_by(['code_logger', 'jam'])
				->order_by('code_logger ASC, jam ASC')
				->get()->result_array();
		}

		$rekap_map = [];
		foreach ($query_data as $row) {
			$code = $row['code_logger'];
			$jam = $row['jam'];
			$kat = $meta[$code]['katlog_id'] ?? '';

			$nilai = ($id_kategori == '8') ? $row['avg_val'] : $row['sum_val'];
			$rekap_map[$code][$jam] = is_null($nilai) ? null : (float) $nilai;
		}

		$start = strtotime($tanggal_rekap);
		$end = strtotime($tanggal_rekap2);
		$sr = [];
		$data['data_rekap'] = [];

		foreach ($loggers as $code) {
			$sensor_id = isset($sensor_map[$code]) ? $sensor_map[$code]['id'] : null;
			$result = [];
			$total = 0.0;

			for ($ts = $start; $ts <= $end; $ts += 3600) {
				$date = date('Y-m-d H:00:00', $ts);
				$nilai = isset($rekap_map[$code][$date]) ? $rekap_map[$code][$date] : null;

				if ($nilai === null) {
					$result[] = ['waktu' => $date, 'nilai' => '-'];
				} else {
					$v = (float) $nilai;
					$result[] = ['waktu' => $date, 'nilai' => $v];
					$total += $v;
				}

				$sr[date('Y-m-d', $ts)][] = date('Y-m-d H:00', $ts);
			}

			$data['data_rekap'][] = [
				'id_logger' => $code,
				'nama_logger' => $meta[$code]['nama'],
				'controller' => $meta[$code]['controller'],
				'data' => $result,
				'id_param' => $sensor_id,
				'total' => $total,
				'tabel' => $meta[$code]['controller']
			];
		}

		$jumlah_jam = array_sum(array_map('count', $sr));

		if ($id_kategori === 'awr' || $id_kategori === 'arr') {
			foreach ($data['data_rekap'] as $key => $dtr) {
				$total_hujan = 0.0;
				foreach ($dtr['data'] as $k2 => $dt_q) {
					if ($dt_q['nilai'] !== '-') {
						$v = (float) $dt_q['nilai'];
						$total_hujan += $v;
						if ($v <= 0.0)
							$data['data_rekap'][$key]['data'][$k2]['warna'] = '#D5F0C1';
						elseif ($v >= 0.1 && $v < 1)
							$data['data_rekap'][$key]['data'][$k2]['warna'] = '#70cddd';
						elseif ($v >= 1 && $v < 5)
							$data['data_rekap'][$key]['data'][$k2]['warna'] = '#35549d';
						elseif ($v >= 5 && $v < 10)
							$data['data_rekap'][$key]['data'][$k2]['warna'] = '#fef216';
						elseif ($v >= 10 && $v < 20)
							$data['data_rekap'][$key]['data'][$k2]['warna'] = '#f47e2c';
						elseif ($v >= 20)
							$data['data_rekap'][$key]['data'][$k2]['warna'] = '#ed1c24';
						else
							$data['data_rekap'][$key]['data'][$k2]['warna'] = '#D5F0C1';
					} else {
						$data['data_rekap'][$key]['data'][$k2]['warna'] = '#D5F0C1';
					}
				}
				if ($total_hujan <= 0)
					$wrn = '#98bc85';
				elseif ($total_hujan >= 0.1 && $total_hujan < 5)
					$wrn = '#70cddd';
				elseif ($total_hujan >= 5 && $total_hujan < 20)
					$wrn = '#35549d';
				elseif ($total_hujan >= 20 && $total_hujan < 50)
					$wrn = '#fef216';
				elseif ($total_hujan >= 50 && $total_hujan < 100)
					$wrn = '#f47e2c';
				elseif ($total_hujan >= 100)
					$wrn = '#ed1c24';
				$data['data_rekap'][$key]['warna'] = $wrn;
				$data['data_rekap'][$key]['total'] = $total_hujan;
			}
		}

		$result = [];
		foreach ($data['data_rekap'] as $logger) {
			foreach ($logger['data'] as $entry) {
				$timestamp = substr($entry['waktu'], 0, 16);
				$result[$timestamp][] = [
					'nilai' => $entry['nilai'],
					'warna' => isset($entry['warna']) ? $entry['warna'] : '#D5F0C1'
				];
			}
		}

		$data['new'] = $result;
		$data['hari'] = $sr;
		$data['jam'] = $jumlah_jam;
		echo json_encode($data);
	}


	public function peta_lokasi()
	{
		$id_kategori = $this->session->userdata('id_kategori');
		$ktg = $this->db->get('kategori_logger')->result_array();

		$das = $this->db->get('das_diy')->result_array();
		foreach ($das as $key => $ds) {
			$das[$key]['logger'] = [];
			$data_logger = $this->db->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_lokasi.das', $ds['nama_das'])->where('t_logger.user_id', '4')->where('t_logger.nama_logger !=', 'AWLR Plataran')->order_by('code_logger')->get('t_logger')->result_array();
			foreach ($data_logger as $k => $log) {
				$tabel = $log['temp_tabel'];
				$id_logger = $log['code_logger'];
				$temp_data = $this->db->where('code_logger', $id_logger)->get($tabel)->row();
				$cek_perbaikan = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();

				$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
				if ($temp_data->waktu >= $awal) {
					$color = "green";
					$status_logger = "Koneksi Terhubung";
				} else {
					$color = "red";
					$status_logger = "Koneksi Terputus";
				}
				if ($cek_perbaikan) {
					$color = "#A16D28";
					$status_logger = "Perbaikan";
				}
				if ($temp_data->sensor13 == '1') {
					$sdcard = 'OK';
				} else {
					$sdcard = 'Bermasalah';
				}

				$param = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
				foreach ($param as $ky => $val) {
					$get = '&id_param=' . $val['id'];
					$kolom = $val['field_sensor'];
					$param[$ky]['nilai'] = $temp_data->$kolom;
					$param[$ky]['kolom_sensor'] = $kolom;
					$param[$ky]['nama_parameter'] = $val['alias_sensor'];
					$param[$ky]['link'] = base_url() . 'analisa/set_sensordash?' . $get;
				}
				$das[$key]['logger'][$k] = [
					'id_logger' => $id_logger,
					'nama_lokasi' => $log['nama_lokasi'],
					'waktu' => $temp_data->waktu,
					'color' => $color,
					'status_logger' => $status_logger,
					'status_sd' => $sdcard,
					'param' => $param,
				];
			}
		}
		$list_das = [];
		foreach ($das as $w => $v) {
			$list_das[$v['nama_das']] = $v;
		}
		$data['data_konten'] = $list_das;

		$kategori = array();
		$query_kategori = $this->db->query('select * from kategori_logger');
		$marker = [];
		foreach ($query_kategori->result() as $kat) {
			$tabel = $kat->tabel;
			$tabel_temp = $kat->temp_tabel;
			$query_lokasilogger = $this->db->query("select * from t_logger inner join t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi  join t_informasi on t_logger.code_logger = t_informasi.logger_id where katlog_id='$kat->id_katlogger' and  t_logger.user_id = 4 and  t_logger.nama_logger != 'AWLR Plataran'");
			foreach ($query_lokasilogger->result() as $loklogger) {
				$id_logger = $loklogger->code_logger;
				$icon = $loklogger->icon;
				$parameter = array();
				$id_param = $this->db->where('logger_code', $id_logger)->where('parameter_utama', '1')->limit(1)->get('t_sensor')->row();
				$query_data = $this->db->query('select * from ' . $tabel_temp . ' where code_logger="' . $id_logger . '"')->result();
				foreach ($query_data as $dt) {
					$waktu = $dt->waktu;
					$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));

					if ($icon == 'awlr') {
						$controller = $kat->controller;
						$kat_group = 'awlr';
						$data_p = $dt->sensor1;
						$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
						if ($perb) {
							$icon_marker = base_url() . 'pin_marker/awlr-iri-coklat.png';
							$status = '<p style="color:brown;margin-bottom:0px">Perbaikan</p>';
							$statlog = 'Perbaikan';
							$statuspantau = "Perbaikan";
							$anim = "";
						} else {
							if ($waktu >= $awal) {
								$icon_marker = base_url() . 'pin_marker/awlr-iri-hijau.png';
								$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
								$statlog = 'Koneksi Terhubung';
								$statuspantau = "Koneksi Terhubung";
								$anim = "";
							} else {
								$icon_marker = base_url() . 'pin_marker/awlr-iri-hitam.png';
								$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
								$statlog = 'Koneksi Terputus';
								$statuspantau = "Koneksi Terputus";
								$anim = "google.maps.Animation.BOUNCE";
							}
						}
					} else if ($icon == 'arr') {
						$controller = 'arr';
						$kat_group = 'arr';
						$sen = $this->db->where('field_sensor', 'sensor9')->where('logger_code', $id_logger)->get('t_sensor')->row();
						$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
						if ($sen) {
							$query_akumulasi = $this->db->query('select sum(sensor9) as sensor9 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
							$data_p = $query_akumulasi->sensor9;
						} else {
							$query_akumulasi = $this->db->query('select sum(sensor8) as sensor8 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
							$data_p = $query_akumulasi->sensor8;
						}

						if ($perb) {
							$icon_marker = base_url() . 'pin_marker/arr-iri-coklat.png';
							$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
							$statlog = 'Perbaikan';
							$statuspantau = "Perbaikan";
							$anim = "";
						} else {
							if ($waktu >= $awal) {
								if ($data_p <= 0) {
									$icon_marker = base_url() . 'pin_marker/arr_hijau.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'th';
									$statuspantau = "Tidak Hujan";
									$anim = "";
								} elseif ($data_p >= 0.1 and $data_p < 1) {
									$icon_marker = base_url() . 'pin_marker/arr_biru.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'sr';
									$statuspantau = "Hujan Sangat Ringan";
									$anim = "";
								} elseif ($data_p >= 1 and $data_p < 5) {
									$icon_marker = base_url() . 'pin_marker/arr_nila.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'r';
									$statuspantau = "Hujan Ringan";
									$anim = "";
								} elseif ($data_p >= 5 and $data_p < 10) {
									$icon_marker = base_url() . 'pin_marker/arr_kuning.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 's';
									$statuspantau = "Hujan Sedang";
									$anim = "";
								} elseif ($data_p >= 10 and $data_p < 20) {
									$icon_marker = base_url() . 'pin_marker/arr_oranye.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'l';
									$statuspantau = "Hujan Lebat";
									$anim = "google.maps.Animation.BOUNCE";
								} elseif ($data_p >= 20) {
									$icon_marker = base_url() . 'pin_marker/arr_merah.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'sl';
									$statuspantau = "Hujan Sangat Lebat";
									$anim = "google.maps.Animation.BOUNCE";
								}
								$statlog = 'Koneksi Terhubung';
							} else {
								$icon_marker = base_url() . 'pin_marker/arr_hitam.png';
								$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
								$statlog = 'Koneksi Terputus';
								$statuspantau = "Koneksi Terputus";
								$anim = "google.maps.Animation.BOUNCE";
							}
						}
					} else {
						$controller = $kat->controller;
						$kat_group = 'awr';
						$sen = $this->db->where('field_sensor', 'sensor9')->where('logger_code', $id_logger)->get('t_sensor')->row();
						$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
						if ($sen) {
							$query_akumulasi = $this->db->query('select sum(sensor9) as sensor9 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
							$data_p = $query_akumulasi->sensor9;
						} else {
							$query_akumulasi = $this->db->query('select sum(sensor8) as sensor8 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
							$data_p = $query_akumulasi->sensor8;
						}
						if ($perb) {
							$icon_marker = base_url() . 'pin_marker/awr_coklat.png';
							$status = '<p style="color:brown;margin-bottom:0px">Koneksi Terputus</p>';
							$statlog = 'Perbaikan';
							$statuspantau = "Perbaikan";
							$anim = "";
						} else {
							if ($waktu >= $awal) {
								if ($data_p >= 0 and $data_p < 0.1) {
									$icon_marker = base_url() . 'pin_marker/awr_hijau.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'Koneksi Terhubung';
									$statuspantau = "Tidak Hujan";
									$anim = "";
								} elseif ($data_p >= 0.1 and $data_p < 1) {
									$icon_marker = base_url() . 'pin_marker/awr_biru.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'Koneksi Terhubung';
									$statuspantau = "Hujan Sangat Ringan";
									$anim = "";
								} elseif ($data_p >= 1 and $data_p < 5) {
									$icon_marker = base_url() . 'pin_marker/awr_nila.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'Koneksi Terhubung';
									$statuspantau = "Hujan Ringan";
									$anim = "";
								} elseif ($data_p >= 5 and $data_p < 10) {
									$icon_marker = base_url() . 'pin_marker/awr_kuning.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'Koneksi Terhubung';
									$statuspantau = "Hujan Sedang";
									$anim = "";
								} elseif ($data_p >= 10 and $data_p < 20) {
									$icon_marker = base_url() . 'pin_marker/awr_oranye.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'Koneksi Terhubung';
									$statuspantau = "Hujan Lebat";
									$anim = "google.maps.Animation.BOUNCE";
								} elseif ($data_p >= 20) {
									$icon_marker = base_url() . 'pin_marker/awr_merah.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'Koneksi Terhubung';
									$statuspantau = "Hujan Sangat Lebat";
									$anim = "google.maps.Animation.BOUNCE";
								}
							} else {
								$icon_marker = base_url() . 'pin_marker/awr_hitam.png';
								$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
								$statlog = 'Koneksi Terputus';
								$statuspantau = "Koneksi Terputus";
								$anim = "google.maps.Animation.BOUNCE";
							}
						}
					}
					$status_sd = 'OK';

				}
				$get = 'id_logger=' . $loklogger->code_logger . '&id_param=' . $id_param->id . '_psda';
				$link = 'https://bbwsso.monitoring4system.com/analisa/set_sensordash?' . $get;

				$marker[] = [
					'nama_das' => $loklogger->das,
					'id_kategori' => $kat->id_katlogger,
					'id_logger' => $loklogger->code_logger,
					'category' => $kat_group,
					'category_group' => $statuspantau,
					'koneksi' => $statlog,
					'status_aset' => 'DPUPESDM DIY',
					'status_sd' => $status_sd,
					'latitude' => $loklogger->latitude,
					'longitude' => $loklogger->longitude,
					'foto_pos' => '<div class="d-flex w-100 justify-content-center mb-2 mt-3"><div style="background:url(https://dpupesdm.monitoring4system.com/image/foto_pos/' . $loklogger->foto_pos . ');width:300px;height:200px;background-size:cover;background-position:center" class"img-fluid"></div></div>',
					'nama_pic' => $loklogger->nama_pic,
					'no_pic' => '-',
					'nama_lokasi' => $loklogger->nama_lokasi,
					'icon' => $icon_marker,
					'id_param' => $id_param->id,
					'link' => $link,
					'anim' => $anim
				];
			}
		}

		$data['das'] = $das;
		$data['marker'] = $marker;
		echo json_encode($data);
	}

	public function pilihparameter($idlogger)
	{
		$data = array();
		$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlogger . "' ORDER BY CAST(SUBSTRING(field_sensor,7) AS UNSIGNED)");
		foreach ($q_parameter->result() as $param) {
			$data[] = array(
				'idParameter' => $param->id,
				'namaParameter' => $param->alias_sensor,
				'fieldParameter' => $param->field_sensor
			);
		}
		$data_param = json_encode($data);
		return json_decode($data_param);
	}

	public function pilihpos()
	{
		$data = array();
		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where t_logger.user_id = 4 order by code_logger asc");

		foreach ($q_pos->result() as $pos) {
			$data[] = array(
				'idLogger' => $pos->code_logger . '_psda',
				'namaPos' => $pos->nama_lokasi
			);
		}
		$data_pos = json_encode($data);
		return json_decode($data_pos);
	}

	public function api_pilihpos()
	{
		$data = array();
		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where t_logger.user_id = 4 order by code_logger asc");

		foreach ($q_pos->result() as $pos) {
			$data[] = array(
				'idLogger' => $pos->code_logger . '_psda',
				'namaPos' => $pos->nama_lokasi
			);
		}
		$data_pos = json_encode($data);
		echo $data_pos;
	}



	function analisapertanggal2()
	{
		$idsensor = $this->input->get('idsensor');
		$tanggal = $this->input->get('tanggal');

		$data = array();
		$min = array();
		$max = array();

		$qparam = $this->db->join('t_logger', 't_logger.code_logger = t_sensor.logger_code')->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')->join('t_informasi', 't_informasi.logger_id = t_logger.code_logger')->where('t_sensor.id', $idsensor)->get('t_sensor')->row();

		$id_kategori = $qparam->id_katlogger;
		$temp_tabel = $qparam->temp_tabel;
		$id_logger = $qparam->code_logger;
		$tabel = $qparam->tabel;
		$sensor = $qparam->field_sensor;
		$satuan = $qparam->satuan;
		$namaparameter = $qparam->alias_sensor;

		$qstatus = $this->db->where('code_logger', $id_logger)->get($temp_tabel)->row();
		$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
		$waktu = $qstatus->waktu ?? null;
		$perbaikan = $this->db->get_where('t_perbaikan', ['id_logger' => $id_logger])->row();

		if ($waktu && $waktu >= $awal) {
			$color = "green";
			$status_logger = "Koneksi Terhubung";
		} else {
			$color = "dark";
			$status_logger = "Koneksi Terputus";
		}
		if ($perbaikan) {
			$stts = '1';
			$status_logger = "Perbaikan";
		} else {
			$stts = '0';
		}
		$temp_data = [
			'nama_lokasi' => $qparam->nama_lokasi,
			'color' => $color,
			'status_logger' => $status_logger,
			'stts' => $stts,
		];

		if ($sensor == 'sensor9' or $sensor == 'sensor8') {
			$tpg = 'column';
			$namaSensor = 'Akumulasi_' . $namaparameter;
			$select = 'sum(' . $sensor . ')as ' . $namaSensor;
		} else {
			$tpg = 'spline';
			$namaSensor = 'Rerata_' . $namaparameter;
			$select = 'avg(' . $sensor . ')as ' . $namaSensor;
		}

		$query_data = $this->db->query("SELECT  HOUR(waktu) AS jam, DAY(waktu) AS hari, MONTH(waktu) AS bulan, YEAR(waktu) AS tahun,waktu," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . "  USE INDEX (waktu) where code_logger='" . $id_logger . "' and waktu >= '" . $tanggal . " 00:00' and waktu <= '" . $tanggal . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu);");

		$akumulasi_hujan = 0;
		$hsl = $query_data->result();
		$fmtTbl = function ($r, $h, $min, $max) {
			return [
				'waktu' => date('H', strtotime($r->waktu)) . ':00:00',
				'dta' => number_format($h, 2, '.', ''),
				'min' => number_format($min, 2, '.', ''),
				'max' => number_format($max, 2, '.', '')
			];
		};
		$fmtPoint = function ($r, $val) {
			return "[ Date.UTC($r->tahun," . ($r->bulan - 1) . ",$r->hari,$r->jam)," . number_format($val, 3, '.', '') . "]";
		};
		$fmtRange = function ($r, $min, $max) {
			return "[ Date.UTC($r->tahun," . ($r->bulan - 1) . ",$r->hari,$r->jam)," . $min . "," . $max . "]";
		};
		$tooltip = "Waktu %d-%m-%Y %H:%M";
		$data = [];
		$range = [];
		$data_tabel = [];
		foreach ($hsl as $r) {
			$h = $r->$namaSensor;
			$min_data = $r->min;
			$max_data = $r->max;
			$data[] = $fmtPoint($r, $h);
			$range[] = $fmtRange($r, $min_data, $max_data);
			$data_tabel[] = $fmtTbl($r, $h, $min_data, $max_data);
			if ($satuan == 'mm') {
				$akumulasi_hujan += $h;
			}
		}

		$dataAnalisa = [
			'idParam' => $idsensor,
			'idLogger' => $id_logger,
			'namaSensor' => $namaparameter,
			'satuan' => $satuan,
			'tipe_grafik' => $tpg,
			'data' => $data,
			'data_tabel' => $data_tabel,
			'nosensor' => $sensor,
			'range' => $range,
			'tooltip' => $tooltip,
			'tooltipper' => $tooltip,
			'mode_data' => 'hari',
			'pada' => $tanggal,
			'dari' => $tanggal,
			'sampai' => $tanggal,
			'akumulasi_hujan' => $akumulasi_hujan,
		];

		$dataAnalisa = array(
			'informasi' => $qparam,
			'data_sensor' => $dataAnalisa,
			'pilih_pos' => $this->pilihpos(),
			'temp_data' => $temp_data,
			'pilih_parameter' => $this->pilihparameter($id_logger),
		);

		echo json_encode($dataAnalisa);
	}

	function analisaperbulan2()
	{
		$idsensor = $this->input->get('idsensor');
		$tanggal = $this->input->get('tanggal');

		$data = array();
		$min = array();
		$max = array();

		$qparam = $this->db->join('t_logger', 't_logger.code_logger = t_sensor.logger_code')->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')->join('t_informasi', 't_informasi.logger_id = t_logger.code_logger')->where('t_sensor.id', $idsensor)->get('t_sensor')->row();

		$id_kategori = $qparam->id_katlogger;
		$temp_tabel = $qparam->temp_tabel;
		$id_logger = $qparam->code_logger;
		$tabel = $qparam->tabel;
		$sensor = $qparam->field_sensor;
		$satuan = $qparam->satuan;
		$namaparameter = $qparam->alias_sensor;

		$qstatus = $this->db->where('code_logger', $id_logger)->get($temp_tabel)->row();
		$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
		$waktu = $qstatus->waktu ?? null;
		$perbaikan = $this->db->get_where('t_perbaikan', ['id_logger' => $id_logger])->row();

		if ($waktu && $waktu >= $awal) {
			$color = "green";
			$status_logger = "Koneksi Terhubung";
		} else {
			$color = "dark";
			$status_logger = "Koneksi Terputus";
		}
		if ($perbaikan) {
			$stts = '1';
			$status_logger = "Perbaikan";
		} else {
			$stts = '0';
		}
		$temp_data = [
			'nama_lokasi' => $qparam->nama_lokasi,
			'color' => $color,
			'status_logger' => $status_logger,
			'stts' => $stts,
		];

		if ($sensor == 'sensor9' or $sensor == 'sensor8') {
			$tpg = 'column';
			$namaSensor = 'Akumulasi_' . $namaparameter;
			$select = 'sum(' . $sensor . ')as ' . $namaSensor;
		} else {
			$tpg = 'spline';
			$namaSensor = 'Rerata_' . $namaparameter;
			$select = 'avg(' . $sensor . ')as ' . $namaSensor;
		}

		$query_data = $this->db->query("SELECT DAY(waktu) AS hari, MONTH(waktu) AS bulan, YEAR(waktu) AS tahun,waktu," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . "  USE INDEX (waktu) where code_logger='" . $id_logger . "' and waktu >= '" . $tanggal . "-01 00:00' and waktu <= '" . $tanggal . "-31 23:59' group by DAY(waktu),MONTH(waktu),YEAR(waktu);");

		$akumulasi_hujan = 0;
		$hsl = $query_data->result();
		$fmtTbl = function ($r, $h, $min, $max) {
			return [
				'waktu' => date('Y-m-d', strtotime($r->waktu)),
				'dta' => number_format($h, 2, '.', ''),
				'min' => number_format($min, 2, '.', ''),
				'max' => number_format($max, 2, '.', '')
			];
		};
		$fmtPoint = function ($r, $val) {
			return "[ Date.UTC($r->tahun," . ($r->bulan - 1) . ",$r->hari)," . number_format($val, 3, '.', '') . "]";
		};
		$fmtRange = function ($r, $min, $max) {
			return "[ Date.UTC($r->tahun," . ($r->bulan - 1) . ",$r->hari)," . $min . "," . $max . "]";
		};
		$tooltip = "Tanggal %d-%m-%Y";
		$data = [];
		$range = [];
		$data_tabel = [];
		foreach ($hsl as $r) {
			$h = $r->$namaSensor;
			$min_data = $r->min;
			$max_data = $r->max;
			$data[] = $fmtPoint($r, $h);
			$range[] = $fmtRange($r, $min_data, $max_data);
			$data_tabel[] = $fmtTbl($r, $h, $min_data, $max_data);
			if ($satuan == 'mm') {
				$akumulasi_hujan += $h;
			}
		}

		$dataAnalisa = [
			'idParam' => $idsensor,
			'idLogger' => $id_logger,
			'namaSensor' => $namaparameter,
			'satuan' => $satuan,
			'tipe_grafik' => $tpg,
			'data' => $data,
			'data_tabel' => $data_tabel,
			'nosensor' => $sensor,
			'range' => $range,
			'tooltip' => $tooltip,
			'tooltipper' => $tooltip,
			'mode_data' => 'bulan',
			'pada' => $tanggal,
			'dari' => $tanggal,
			'sampai' => $tanggal,
			'akumulasi_hujan' => $akumulasi_hujan,
		];

		$dataAnalisa = array(
			'informasi' => $qparam,
			'data_sensor' => $dataAnalisa,
			'pilih_pos' => $this->pilihpos(),
			'temp_data' => $temp_data,
			'pilih_parameter' => $this->pilihparameter($id_logger),
		);

		echo json_encode($dataAnalisa);
	}




	function analisapertahun()
	{
		$idsensor = $this->input->get('idsensor');
		$tanggal = $this->input->get('tahun');

		$data = array();
		$min = array();
		$max = array();

		$qparam = $this->db->join('t_logger', 't_logger.code_logger = t_sensor.logger_code')->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')->where('t_sensor.id', $idsensor)->get('t_sensor')->row();
		$idlogger = $qparam->code_logger;
		$tabel = $qparam->tabel;
		$sensor = $qparam->field_sensor;
		$satuan = $qparam->satuan;
		$namaparameter = $qparam->alias_sensor;

		if ($sensor == 'sensor9' or $sensor == 'sensor8') {
			$tpg = 'column';
			$namaSensor = 'Akumulasi_' . $namaparameter;
			$select = 'sum(' . $sensor . ')as ' . $namaSensor;
		} else {
			$tpg = 'spline';
			$namaSensor = 'Rerata_' . $namaparameter;
			$select = 'avg(' . $sensor . ')as ' . $namaSensor;
		}
		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal,MONTH(waktu) as bulan," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . " USE INDEX (waktu) where code_logger='" . $idlogger . "' and waktu >= '" . $tanggal . "-01-01 00:00' and waktu <= '" . $tanggal . "-12-31 23:59' group by MONTH(waktu),YEAR(waktu);");

		if ($query_data->result_array()) {
			foreach ($query_data->result() as $datalog) {
				$waktu[] = date('Y-m', strtotime($datalog->waktu));
				$data2[] = number_format($datalog->$namaSensor, 3);
				$min2[] = number_format($datalog->min, 3);
				$max2[] = number_format($datalog->max, 3);
			}
			$stts = 'sukses';
			$debit = 'sukses';

		} else {
			$stts = 'error';
			$debit = 'error';
		}
		$dataAnalisa = array(
			'status' => 'sukses',
			'idLogger' => $idlogger,
			'nosensor' => $sensor,
			'namaSensor' => $namaSensor,
			'satuan' => $satuan,
			'waktu' => $waktu,
			'tipegraf' => $tpg,
			'data' => $data2,
			'datamin' => $min2,
			'datamax' => $max2,
		);
		echo json_encode(
			array(
				'debit' => $debit,
				'status' => $stts,
				'data' => $dataAnalisa
			)
		);
	}

	function analisapertahun2()
	{
		$idsensor = $this->input->get('idsensor');
		$tanggal = $this->input->get('tahun');

		$data = array();
		$min = array();
		$max = array();

		$qparam = $this->db->join('t_logger', 't_logger.code_logger = t_sensor.logger_code')->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')->join('t_informasi', 't_informasi.logger_id = t_logger.code_logger')->where('t_sensor.id', $idsensor)->get('t_sensor')->row();

		$id_kategori = $qparam->id_katlogger;
		$temp_tabel = $qparam->temp_tabel;
		$id_logger = $qparam->code_logger;
		$tabel = $qparam->tabel;
		$sensor = $qparam->field_sensor;
		$satuan = $qparam->satuan;
		$namaparameter = $qparam->alias_sensor;

		$qstatus = $this->db->where('code_logger', $id_logger)->get($temp_tabel)->row();
		$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
		$waktu = $qstatus->waktu ?? null;
		$perbaikan = $this->db->get_where('t_perbaikan', ['id_logger' => $id_logger])->row();

		if ($waktu && $waktu >= $awal) {
			$color = "green";
			$status_logger = "Koneksi Terhubung";
		} else {
			$color = "dark";
			$status_logger = "Koneksi Terputus";
		}
		if ($perbaikan) {
			$stts = '1';
			$status_logger = "Perbaikan";
		} else {
			$stts = '0';
		}
		$temp_data = [
			'nama_lokasi' => $qparam->nama_lokasi,
			'color' => $color,
			'status_logger' => $status_logger,
			'stts' => $stts,
		];

		if ($sensor == 'sensor9' or $sensor == 'sensor8') {
			$tpg = 'column';
			$namaSensor = 'Akumulasi_' . $namaparameter;
			$select = 'sum(' . $sensor . ')as ' . $namaSensor;
		} else {
			$tpg = 'spline';
			$namaSensor = 'Rerata_' . $namaparameter;
			$select = 'avg(' . $sensor . ')as ' . $namaSensor;
		}

		$query_data = $this->db->query("SELECT MONTH(waktu) AS bulan, YEAR(waktu) AS tahun,waktu," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . "  USE INDEX (waktu) where code_logger='" . $id_logger . "' and waktu >= '" . $tanggal . "-01-01 00:00' and waktu <= '" . $tanggal . "-12-31 23:59' group by MONTH(waktu),YEAR(waktu) ;");

		$akumulasi_hujan = 0;
		$hsl = $query_data->result();
		$fmtTbl = function ($r, $h, $min, $max) {
			return [
				'waktu' => date('Y-m', strtotime($r->waktu)),
				'dta' => number_format($h, 2, '.', ''),
				'min' => number_format($min, 2, '.', ''),
				'max' => number_format($max, 2, '.', '')
			];
		};
		$fmtPoint = function ($r, $val) {
			return "[ Date.UTC($r->tahun," . ($r->bulan - 1) . ")," . number_format($val, 3, '.', '') . "]";
		};
		$fmtRange = function ($r, $min, $max) {
			return "[ Date.UTC($r->tahun," . ($r->bulan - 1) . ")," . $min . "," . $max . "]";
		};
		$tooltip = "Tanggal %d-%m-%Y";
		$data = [];
		$range = [];
		$data_tabel = [];
		foreach ($hsl as $r) {
			$h = $r->$namaSensor;
			$min_data = $r->min;
			$max_data = $r->max;
			$data[] = $fmtPoint($r, $h);
			$range[] = $fmtRange($r, $min_data, $max_data);
			$data_tabel[] = $fmtTbl($r, $h, $min_data, $max_data);
			if ($satuan == 'mm') {
				$akumulasi_hujan += $h;
			}
		}

		$dataAnalisa = [
			'idParam' => $idsensor,
			'idLogger' => $id_logger,
			'namaSensor' => $namaparameter,
			'satuan' => $satuan,
			'tipe_grafik' => $tpg,
			'data' => $data,
			'data_tabel' => $data_tabel,
			'nosensor' => $sensor,
			'range' => $range,
			'tooltip' => $tooltip,
			'tooltipper' => $tooltip,
			'mode_data' => 'tahun',
			'pada' => $tanggal,
			'dari' => $tanggal,
			'sampai' => $tanggal,
			'akumulasi_hujan' => $akumulasi_hujan,
		];

		$dataAnalisa = array(
			'informasi' => $qparam,
			'data_sensor' => $dataAnalisa,
			'pilih_pos' => $this->pilihpos(),
			'temp_data' => $temp_data,
			'pilih_parameter' => $this->pilihparameter($id_logger),
		);

		echo json_encode($dataAnalisa);
	}


	function analisaperrange2()
	{
		$idsensor = $this->input->get('idsensor');
		$dari = $this->input->get('dari');
		$sampai = $this->input->get('sampai');
		$data = array();
		$min = array();
		$max = array();

		$qparam = $this->db->join('t_logger', 't_logger.code_logger = t_sensor.logger_code')->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')->join('t_informasi', 't_informasi.logger_id = t_logger.code_logger')->where('t_sensor.id', $idsensor)->get('t_sensor')->row();

		$id_kategori = $qparam->id_katlogger;
		$temp_tabel = $qparam->temp_tabel;
		$id_logger = $qparam->code_logger;
		$tabel = $qparam->tabel;
		$sensor = $qparam->field_sensor;
		$satuan = $qparam->satuan;
		$namaparameter = $qparam->alias_sensor;

		$qstatus = $this->db->where('code_logger', $id_logger)->get($temp_tabel)->row();
		$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));
		$waktu = $qstatus->waktu ?? null;
		$perbaikan = $this->db->get_where('t_perbaikan', ['id_logger' => $id_logger])->row();

		if ($waktu && $waktu >= $awal) {
			$color = "green";
			$status_logger = "Koneksi Terhubung";
		} else {
			$color = "dark";
			$status_logger = "Koneksi Terputus";
		}
		if ($perbaikan) {
			$stts = '1';
			$status_logger = "Perbaikan";
		} else {
			$stts = '0';
		}
		$temp_data = [
			'nama_lokasi' => $qparam->nama_lokasi,
			'color' => $color,
			'status_logger' => $status_logger,
			'stts' => $stts,
		];

		if ($sensor == 'sensor9' or $sensor == 'sensor8') {
			$tpg = 'column';
			$namaSensor = 'Akumulasi_' . $namaparameter;
			$select = 'sum(' . $sensor . ')as ' . $namaSensor;
		} else {
			$tpg = 'spline';
			$namaSensor = 'Rerata_' . $namaparameter;
			$select = 'avg(' . $sensor . ')as ' . $namaSensor;
		}

		$query_data = $this->db->query("SELECT  HOUR(waktu) AS jam, DAY(waktu) AS hari, MONTH(waktu) AS bulan, YEAR(waktu) AS tahun,waktu," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $tabel . "  USE INDEX (waktu) where code_logger='" . $id_logger . "' and waktu >= '" . $dari . "' and waktu <= '" . $sampai . "' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu;");

		$akumulasi_hujan = 0;
		$hsl = $query_data->result();
		$fmtTbl = function ($r, $h, $min, $max) {
			return [
				'waktu' => date('Y-m-d H', strtotime($r->waktu)) . ':00:00',
				'dta' => number_format($h, 2, '.', ''),
				'min' => number_format($min, 2, '.', ''),
				'max' => number_format($max, 2, '.', '')
			];
		};
		$fmtPoint = function ($r, $val) {
			return "[ Date.UTC($r->tahun," . ($r->bulan - 1) . ",$r->hari,$r->jam)," . number_format($val, 3, '.', '') . "]";
		};
		$fmtRange = function ($r, $min, $max) {
			return "[ Date.UTC($r->tahun," . ($r->bulan - 1) . ",$r->hari,$r->jam)," . $min . "," . $max . "]";
		};
		$tooltip = "Waktu %d-%m-%Y %H:%M";
		$data = [];
		$range = [];
		$data_tabel = [];
		foreach ($hsl as $r) {
			$h = $r->$namaSensor;
			$min_data = $r->min;
			$max_data = $r->max;
			$data[] = $fmtPoint($r, $h);
			$range[] = $fmtRange($r, $min_data, $max_data);
			$data_tabel[] = $fmtTbl($r, $h, $min_data, $max_data);
			if ($satuan == 'mm') {
				$akumulasi_hujan += $h;
			}
		}

		$dataAnalisa = [
			'idParam' => $idsensor,
			'idLogger' => $id_logger,
			'namaSensor' => $namaparameter,
			'satuan' => $satuan,
			'tipe_grafik' => $tpg,
			'data' => $data,
			'data_tabel' => $data_tabel,
			'nosensor' => $sensor,
			'range' => $range,
			'tooltip' => $tooltip,
			'tooltipper' => $tooltip,
			'mode_data' => 'range',
			'pada' => $dari,
			'dari' => $dari,
			'sampai' => $sampai,
			'akumulasi_hujan' => $akumulasi_hujan,
		];

		$dataAnalisa = array(
			'informasi' => $qparam,
			'data_sensor' => $dataAnalisa,
			'pilih_pos' => $this->pilihpos(),
			'temp_data' => $temp_data,
			'pilih_parameter' => $this->pilihparameter($id_logger),
		);

		echo json_encode($dataAnalisa);
	}

	// ======================== API cek_hujan ========================
	public function cek_hujan()
	{
		header('Content-Type: application/json');

		$filter = $this->input->get('filter');
		if (!$filter) {
			$filter = 'hujan_saja';
		}

		// ---- 1. Ambil semua logger curah hujan (ARR + AWS) ----
		$loggers = $this->db
			->select('t_logger.code_logger, t_logger.icon, t_lokasi.nama_lokasi, t_lokasi.latitude, t_lokasi.longitude')
			->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')
			->where('t_logger.user_id', '4')
			->where_in('t_logger.icon', ['arr', 'ws'])
			->order_by('t_logger.code_logger', 'asc')
			->get('t_logger')
			->result_array();

		$now_hour = date('Y-m-d H');   // e.g. 2026-03-04 10
		$today = date('Y-m-d');     // e.g. 2026-03-04
		$satu_jam_lalu = date('Y-m-d H:i', strtotime('-1 hour'));

		$result = [];

		foreach ($loggers as $lg) {
			$id_logger = $lg['code_logger'];
			$icon = $lg['icon'];

			// ---- 2. Tentukan sensor curah hujan (sensor9 fallback sensor8) ----
			$sensor = $this->db->get_where('t_sensor', [
				'logger_code' => $id_logger,
				'field_sensor' => 'sensor9'
			])->row();

			if (!$sensor) {
				$sensor = $this->db->get_where('t_sensor', [
					'logger_code' => $id_logger,
					'field_sensor' => 'sensor8'
				])->row();
			}

			if (!$sensor)
				continue; // skip jika tidak ada sensor curah hujan

			$kolom = $sensor->field_sensor;

			// ---- 3. Curah hujan jam ini (HH:00 sampai sekarang) ----
			$q_jam = $this->db->query(
				"SELECT IFNULL(SUM($kolom), 0) AS ch_jam 
				 FROM weather_station 
				 WHERE code_logger = '$id_logger' 
				   AND waktu >= '$now_hour:00'"
			)->row();
			$ch_jam = $q_jam ? (float) $q_jam->ch_jam : 0;

			// ---- 4. Curah hujan harian (00:00 sampai 23:59) ----
			$q_hari = $this->db->query(
				"SELECT IFNULL(SUM($kolom), 0) AS ch_hari 
				 FROM weather_station 
				 WHERE code_logger = '$id_logger' 
				   AND waktu >= '$today 00:00' 
				   AND waktu <= '$today 23:59'"
			)->row();
			$ch_hari = $q_hari ? (float) $q_hari->ch_hari : 0;

			// ---- 5. Waktu terakhir dari temp_weather_station ----
			$temp_data = $this->db
				->where('code_logger', $id_logger)
				->get('temp_weather_station')
				->row();

			$waktu_terakhir = $temp_data ? $temp_data->waktu : null;

			// ---- 6. Status koneksi ----
			$cek_perbaikan = $this->db
				->get_where('t_perbaikan', ['id_logger' => $id_logger])
				->row();

			if ($cek_perbaikan) {
				$status_koneksi = 'Perbaikan';
			} elseif ($waktu_terakhir && $waktu_terakhir >= $satu_jam_lalu) {
				$status_koneksi = 'On';
			} else {
				$status_koneksi = 'Off';
			}

			// ---- 7. Mapping icon ke kategori ----
			if ($icon === 'arr') {
				$kategori = 'ARR';
			} elseif ($icon === 'ws') {
				$kategori = 'AWS';
			} else {
				$kategori = strtoupper($icon);
			}

			// ---- 8. Filter hujan_saja ----
			if ($filter === 'hujan_saja' && $ch_jam <= 0) {
				continue;
			}

			$result[] = [
				'id_logger' => $id_logger,
				'lokasi' => $lg['nama_lokasi'],
				'kategori' => $kategori,
				'latitude' => $lg['latitude'] ? (float) $lg['latitude'] : null,
				'longitude' => $lg['longitude'] ? (float) $lg['longitude'] : null,
				'curah_hujan_jam' => number_format($ch_jam, 2, '.', ''),
				'curah_hujan_harian' => number_format($ch_hari, 2, '.', ''),
				'waktu_terakhir' => $waktu_terakhir,
				'status_koneksi' => $status_koneksi,
			];
		}

		// ---- 9. Sort by curah_hujan_jam descending ----
		usort($result, function ($a, $b) {
			return (float) $b['curah_hujan_jam'] <=> (float) $a['curah_hujan_jam'];
		});

		echo json_encode([
			'status' => 'sukses',
			'waktu' => date('Y-m-d H:i'),
			'total_pos' => count($result),
			'data' => $result,
		]);
	}

	// ======================== API cek_hujan_historis ========================
	public function cek_hujan_historis()
	{
		header('Content-Type: application/json');

		$tanggal = $this->input->get('tanggal');
		$filter = $this->input->get('filter');
		if (!$filter) {
			$filter = 'hujan_saja';
		}

		// ---- Validasi parameter tanggal ----
		if (!$tanggal) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Parameter tanggal wajib diisi (format: YYYY-MM-DD)',
			]);
			return;
		}

		$dt = DateTime::createFromFormat('Y-m-d', $tanggal);
		if (!$dt || $dt->format('Y-m-d') !== $tanggal) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD',
			]);
			return;
		}

		// ---- 1. Ambil semua logger curah hujan (ARR + AWS) ----
		$loggers = $this->db
			->select('t_logger.code_logger, t_logger.icon, t_lokasi.nama_lokasi')
			->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')
			->where('t_logger.user_id', '4')
			->where_in('t_logger.icon', ['arr', 'ws'])
			->order_by('t_logger.code_logger', 'asc')
			->get('t_logger')
			->result_array();

		$result = [];

		foreach ($loggers as $lg) {
			$id_logger = $lg['code_logger'];
			$icon = $lg['icon'];

			// ---- 2. Tentukan sensor curah hujan (sensor9 fallback sensor8) ----
			$sensor = $this->db->get_where('t_sensor', [
				'logger_code' => $id_logger,
				'field_sensor' => 'sensor9'
			])->row();

			if (!$sensor) {
				$sensor = $this->db->get_where('t_sensor', [
					'logger_code' => $id_logger,
					'field_sensor' => 'sensor8'
				])->row();
			}

			if (!$sensor)
				continue;

			$kolom = $sensor->field_sensor;

			// ---- 3. Curah hujan harian (00:00 s/d 23:59) ----
			$q_hari = $this->db->query(
				"SELECT IFNULL(SUM($kolom), 0) AS ch_hari 
				 FROM weather_station 
				 WHERE code_logger = '$id_logger' 
				   AND waktu >= '$tanggal 00:00' 
				   AND waktu <= '$tanggal 23:59'"
			)->row();
			$ch_hari = $q_hari ? (float) $q_hari->ch_hari : 0;

			// ---- 4. Waktu terakhir pada tanggal tersebut ----
			$q_waktu = $this->db->query(
				"SELECT waktu 
				 FROM weather_station 
				 WHERE code_logger = '$id_logger' 
				   AND waktu >= '$tanggal 00:00' 
				   AND waktu <= '$tanggal 23:59' 
				 ORDER BY waktu DESC 
				 LIMIT 1"
			)->row();
			$waktu_terakhir = $q_waktu ? $q_waktu->waktu : null;

			// ---- 5. Mapping icon ke kategori ----
			if ($icon === 'arr') {
				$kategori = 'ARR';
			} elseif ($icon === 'ws') {
				$kategori = 'AWS';
			} else {
				$kategori = strtoupper($icon);
			}

			// ---- 6. Filter hujan_saja ----
			if ($filter === 'hujan_saja' && $ch_hari <= 0) {
				continue;
			}

			$result[] = [
				'id_logger' => $id_logger,
				'lokasi' => $lg['nama_lokasi'],
				'kategori' => $kategori,
				'curah_hujan_harian' => number_format($ch_hari, 2, '.', ''),
				'waktu_terakhir' => $waktu_terakhir,
			];
		}

		// ---- 7. Sort by curah_hujan_harian descending ----
		usort($result, function ($a, $b) {
			return (float) $b['curah_hujan_harian'] <=> (float) $a['curah_hujan_harian'];
		});

		echo json_encode([
			'status' => 'sukses',
			'tanggal' => $tanggal,
			'total_pos' => count($result),
			'data' => $result,
		]);
	}
}