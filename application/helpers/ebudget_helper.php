<?php defined('BASEPATH') OR exit('No direct script access allowed');
// Create by MW 20201201

function check_min_value($v,$x){
	$val = kali_minus($v,$x);
	// $val = custom_format($val);
	$val = custom_format(view_report($val));
	return $val;
}
function check_value($v){
	// $val = kali_minus($v,$x);
	$val = custom_format(view_report($v));
	return $val;
}

function remove_spaces($val){
	return preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $val);
}

function checkRealisasiKolektibilitas($p1,$data){
	if($p1['tahun'] != $p1['tahun_core']):
		$key = multidimensional_search($data, array(
			'tahun_core' => $p1['tahun_core'],
			'id_kolektibilitas' => $p1['id'],
			'parent_index' => $p1['cabang'],
		));
		$d = $data[$key];
	else:
		$key = multidimensional_search($data, array(
			'tahun_core' => $p1['tahun_core'],
			'id_kolektibilitas' => $p1['id'],
			'parent_index' => '0'
		));
		$d = $data[$key];
	endif;
	return $d;
}

function checkMonthAnggaran($anggaran){
	$bulan 	= sprintf('%02d', $anggaran->bulan_terakhir_realisasi);
	$date 	= "01-".$bulan.'-'.$anggaran->tahun_terakhir_realisasi;
	return minusMonth($date,1);
}

function minusMonth($date,$minus){
	$date = date("m-Y", strtotime($date." -".$minus." months"));
	return $date;
}

function insert_formula_kolektibilitas($data,$anggaran){
	$d=[];
	$table = 'tbl_formula_kolektibilitas';
	foreach ($data as $k => $v) {
		$x 		= explode("-", $k);
		$coa 	= $x[0];
		$thn 	= $x[1];
		$sumber_data = $x[2];
		$cabang = $x[3];

		$h = [
			'coa' => $coa,
		];
		$h['kode_anggaran'] 		= $anggaran->kode_anggaran;
		$h['tahun_anggaran'] 		= $anggaran->tahun_anggaran;
		$h['keterangan_anggaran'] 	= $anggaran->keterangan;
		$h['kode_cabang']			= $cabang;
		$h['tahun_core'] 			= $thn;
		$h['changed'] 				= '[]';
		// $h['sumber_data'] 			= $sumber_data;
		foreach ($v as $k2 => $v2) {
			$h[$k2] 					= $v2;
		}
		$ck = get_data($table,[
			'select'	=> 'id',
			'where'		=> "kode_anggaran = '$anggaran->kode_anggaran' and kode_cabang = '$cabang' and coa = '$coa' and tahun_core = '$thn'"
		])->result();
		if(count($ck)<=0):
			insert_data($table,$h);
		endif;
		$d[] = $h;
	}
	// render($d,'json');
}

function update_formula_kolektibilitas($data,$anggaran){
	$kode_anggaran 		= $anggaran->kode_anggaran;
	$tahun_anggaran 	= $anggaran->tahun_anggaran;
	$keterangan_anggaran 	= $anggaran->keterangan;
	$table = 'tbl_formula_kolektibilitas';
	foreach ($data as $k => $v) {
		$x 		= explode('-', $k);
		$id 	= $x[0];
		$coa 	= $x[1];
		$thn 	= $x[2];
		$sumber_data = $x[3];
		$cabang = $x[4];
		if(strlen(strpos($coa,'sumkol123'))>0):
			$ck = get_data($table,[
				'select'	=> 'id',
				'where' 	=> "coa = '$coa' and kode_cabang = '$cabang' and kode_anggaran = '$kode_anggaran' and tahun_core = '$thn'",
			])->result();
			if(count($ck)>0):
				$where = [
                    'coa' => $coa,
                    'tahun_core' => $thn,
                    'kode_cabang' => $cabang,
                    'kode_anggaran' => $kode_anggaran,
                ];
				update_data($table,$v,$where);
			else:
				$h = $v;
				$h['coa'] 					= $coa;
				$h['kode_anggaran'] 		= $anggaran->kode_anggaran;
				$h['tahun_anggaran'] 		= $anggaran->tahun_anggaran;
				$h['keterangan_anggaran'] 	= $anggaran->keterangan;
				$h['kode_cabang']			= $cabang;
				$h['tahun_core'] 			= $thn;
				$h['changed'] 				= '[]';
				insert_data($table,$h);
			endif;
		elseif(strlen(strpos($coa, '_total'))>0):
			$where = [
                'coa' => $coa,
                'tahun_core' => $thn,
                'kode_cabang' => $cabang,
                'kode_anggaran' => $kode_anggaran,
            ];
			update_data($table,$v,$where);
		else:
			update_data($table,$v,'id',$id);
		endif;
	}
}

function filter_money($val){
 	$value = str_replace('.', '', $val);
    $value = str_replace(',', '.', $value);
    if(strlen(strpos($value, '('))>0):
    	$value = str_replace('(', '', $value);
    	$value = str_replace(')', '', $value);
    	$value = '-'.$value;
    endif;
    return $value;
}

function parse_condition($condition){
    $val = '2 == 2 && 13 < 2';
    $condition = "return ".$val.";";
    $test = eval($condition);
    var_dump($test);
}

function arrSumberData(){
	return ['real' => 'Real'];
}

function bgEdit(){
	// return '#f3f088';
	if(setting('warna_inputan')):
		return setting('warna_inputan');
	else:
		return '#f3f088';
	endif;
}

function get_data_core($arr_coa,$arr_tahun_core,$column){
	$CI         = get_instance();
	// data core / history
    $data_core = [];
    foreach ($arr_tahun_core as $v) {
        $tbl_history = 'tbl_history_'.$v;
        $tbl_history_status = true;
        if(!$CI->db->table_exists($tbl_history)):
            $tbl_history_status = false;
        endif;
        if ($tbl_history_status && !$CI->db->field_exists($column, $tbl_history)):
            $tbl_history_status = false;
        endif;
        if($tbl_history_status):
            $data_core[$v] = get_data($tbl_history,[
                'select' => "
                    coalesce(sum(case when bulan = '1' then ".$column." end), 0) as B_01,
                    coalesce(sum(case when bulan = '2' then ".$column." end), 0) as B_02,
                    coalesce(sum(case when bulan = '3' then ".$column." end), 0) as B_03,
                    coalesce(sum(case when bulan = '4' then ".$column." end), 0) as B_04,
                    coalesce(sum(case when bulan = '5' then ".$column." end), 0) as B_05,
                    coalesce(sum(case when bulan = '6' then ".$column." end), 0) as B_06,
                    coalesce(sum(case when bulan = '7' then ".$column." end), 0) as B_07,
                    coalesce(sum(case when bulan = '8' then ".$column." end), 0) as B_08,
                    coalesce(sum(case when bulan = '9' then ".$column." end), 0) as B_09,
                    coalesce(sum(case when bulan = '10' then ".$column." end), 0) as B_10,
                    coalesce(sum(case when bulan = '11' then ".$column." end), 0) as B_11,
                    coalesce(sum(case when bulan = '12' then ".$column." end), 0) as B_12,
                    account_name,
                    coa,
                    gwlsbi,
                    glwnco",
                'where_in' => ['glwnco' => $arr_coa],
                'group_by' => 'glwnco',
            ])->result_array();
        endif;
    }
    return $data_core;
}

function filter_cabang_admin($access_additional,$cabang,$dt=[]){
	$item = '';

	if(!$access_additional):
		$item .= '<label class="">'.lang('cabang').'  &nbsp</label>';
		$item .= '<select class="select2 custom-select" id="filter_cabang">';
		foreach($cabang as $b){
			$selected = '';
			if($b['kode_cabang'] == user('kode_cabang')) $selected = ' selected';
			$item .= '<option value="'.$b['kode_cabang'].'"'.$selected.'>'.$b['nama_cabang'].'</option>';
		}
		$item .= '</select>';
	else:
		$cab_induk = get_data('tbl_m_cabang',[
			'select' 	=> 'id,kode_cabang,nama_cabang',
			'where' 	=> "kode_cabang like 'G%' and kode_cabang != 'G001' and is_active = '1'",
			'order_by' 	=> 'kode_cabang'
		])->result_array();
		$item .= '<label class="">Cabang Induk  &nbsp</label>';
		$item .= '<select class="select2 custom-select" id="filter_cabang_induk">';
		foreach($cab_induk as $b){
			$selected = '';
			if($b['kode_cabang'] == user('kode_cabang')) $selected = ' selected';
			$nama_cabang = str_replace('GAB', '', $b['nama_cabang']);
			$item .= '<option value="'.$b['id'].'"'.$selected.'>'.$nama_cabang.'</option>';
		}
		$item .= '</select>';

		$item .= '<label class="">&nbsp '.lang('cabang').'  &nbsp</label>';
		$item .= '<select class="select2 custom-select" id="filter_cabang">';
		$item .= '</select>&nbsp';

		if(!isset($dt['no-align'])):
			$item .= '<style>';
			$item .= '.content-header{ height: auto !important; }';
			$item .= '.content-header .float-right{ margin-top: 1rem !important; }';
			$item .= '.content-header .header-info{ position: relative !important; }';
			$item .= '.mt-6{ margin-top: 4em;}';
			$item .= '</style>';
		endif;

	endif;
	return $item;
}

function get_currency($currency){
	$dt_currency = get_data('tbl_m_currency','id',$currency)->row_array();
	$nama  = "Rupiah";
	$nilai = 1;
	if($dt_currency):
		$nama 	= $dt_currency['nama'];
		$nilai	= (float) $dt_currency['nilai'];
	endif;
	return [
		'nama' 	=> $nama,
		'nilai'	=> $nilai,
	];
}

function hitung_rekap_rasio($cabang,$kode,$anggaran){
	$arr_coa 		= [];
	$arr_kode 		= [];
	$status_get 	= false;
	$status_pembagi	= false;
	$status_tambah	= false;
	$status_kurang	= false;
	$s_setahun 		= false;
	$s_no_data 		= false;
	$s_avg 			= false;
	$arr_tambah 	= [];
	$arr_kurang 	= [];
	$arr_bagi 		= [];
	$coa = '';

	if($kode == 'A1'):
		$arr_coa = ['602','5130000']; $status_pembagi = true; $s_setahun = true; $arr_tambah = ['5130000']; $arr_bagi = ['602'];
	elseif($kode == 'A2'):
		$coa = '5130000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A3'):
		$coa = '602'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A4'):
		$arr_coa = ['4150000','1450000']; $status_pembagi = true; $s_setahun = true; $arr_tambah = ['4150000']; $arr_bagi = ['1450000'];
	elseif($kode == 'A5'):
		$coa = '4150000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A6'):
		$coa = '1450000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A7'):
		$s_no_data = true;
	elseif($kode == 'A8'):
		$arr_coa = ['122502','122501']; $status_pembagi = true; $arr_tambah = ['122502']; $arr_bagi = ['122501'];
	elseif($kode == 'A9'):
		$arr_coa = ['122506','122501']; $status_pembagi = true; $arr_tambah = ['122506']; $arr_bagi = ['122501'];
	elseif($kode == 'A10'):
		$arr_kode = ['A15','A16','A17','A22','A23','A24'];
		$arr_coa  = ['122502','122506'];
		$status_pembagi = true; $arr_tambah = $arr_kode; $arr_bagi = $arr_coa;
	elseif($kode == 'A11'):
		$arr_kode = ['A15','A16','A17'];
		$arr_coa  = ['122502'];
		$status_pembagi = true; $arr_tambah = $arr_kode; $arr_bagi = $arr_coa;
	elseif($kode == 'A12'):
		$coa = '122502'; $arr_coa = [$coa];$status_get = true;
	elseif(in_array($kode, ['A13','A14','A15','A16','A17','A20','A21','A22','A23','A24'])):
		$coa = $kode; $arr_kode = [$coa];$status_get = true;
	elseif($kode == 'A18'):
		$arr_kode = ['A22','A23','A24'];
		$arr_coa  = ['122506'];
		$status_pembagi = true; $arr_tambah = $arr_kode; $arr_bagi = $arr_coa;
	elseif($kode == 'A19'):
		$coa = '122506'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A25'):
		$arr_coa = ['602','1450000']; $status_pembagi = true; $arr_tambah = ['1450000']; $arr_bagi = ['602'];
	elseif($kode == 'A26'):
		$arr_coa = ['5100000','5500000','4100000']; $status_pembagi = true; $arr_tambah = ['5100000','5500000']; $arr_bagi = ['5100000','4100000'];
	elseif($kode == 'A27'):
		$arr_coa = ['5100000','5500000']; $status_tambah = true; $arr_tambah = ['5100000','5500000'];
	elseif($kode == 'A28'):
		$arr_coa = ['5100000','4100000']; $status_tambah = true; $arr_tambah = ['5100000','4100000'];
	elseif($kode == 'A29'):
		$arr_coa = ['1000000','59999']; $status_pembagi = true; $s_setahun = true; $s_avg = true; $arr_tambah = ['59999']; $arr_bagi = ['1000000'];
	elseif($kode == 'A30'):
		$coa = '59999'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A31'):
		$coa = '1000000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A32'):
		$arr_coa = ['2100000','2120011','602']; $status_pembagi = true; $arr_tambah = ['2100000','2120011']; $arr_bagi = ['602'];
	elseif($kode == 'A32_1'):
		$arr_coa = ['2100000','2120011']; $status_tambah = true; $arr_tambah = ['2100000','2120011'];
	elseif($kode == 'A32_2'):
		$arr_coa = ['602']; $status_tambah = true; $arr_tambah = ['602'];
	elseif($kode == 'A33'):
		$arr_coa = ['4100000','5100000','1200000','1220000','1250000','1300000','1400000','1450000']; 
		$status_pembagi = true; $s_setahun = true; $s_avg = true;
		$arr_kurang = ['4100000','5100000']; $arr_bagi = ['1200000','1220000','1250000','1300000','1400000','1450000'];
	elseif($kode == 'A33_1'):
		$arr_coa = ['4100000','5100000']; $status_kurang = true; $arr_kurang = ['4100000','5100000'];
	elseif($kode == 'A33_2'):
		$arr_coa = ['1200000','1220000','1250000','1300000','1400000','1450000']; $status_tambah = true; $arr_tambah = $arr_coa;
	elseif($kode == 'A34'):
		$arr_coa = ['4590000','4100000','4500000']; $status_pembagi = true; $arr_tambah = ['4590000']; $arr_bagi = ['4100000','4500000'];
	elseif($kode == 'A34_1'):
		$coa = '4590000'; $arr_coa = [$coa];$status_get = true;
	elseif($kode == 'A34_2'):
		$arr_coa = ['4100000','4500000']; $status_tambah = true; $arr_tambah = ['4100000','4500000'];
	endif;

	$data = [];
	if(count($arr_coa)>0):
		$dt_budget  = get_data('tbl_budget_nett',[
            'where' => [
                'kode_anggaran' => $anggaran->kode_anggaran,
                'kode_cabang'   => $cabang,
                'coa'           => $arr_coa
            ],
        ])->result_array();
        foreach ($arr_coa as $v) {
            $key = array_search($v, array_column($dt_budget, 'coa'));
            if(strlen($key)>0):
                $data[$v] = $dt_budget[$key];
            else:
                $data[$v] = [
                    'B_01' => 0,
                    'B_02' => 0,
                    'B_03' => 0,
                    'B_04' => 0,
                    'B_05' => 0,
                    'B_06' => 0,
                    'B_07' => 0,
                    'B_08' => 0,
                    'B_09' => 0,
                    'B_10' => 0,
                    'B_11' => 0,
                    'B_12' => 0,
                ];
            endif;
        }
	endif;

	if(count($arr_kode)>0):
		$dt_budget_rekaprasio  = get_data('tbl_budget_nett_rekaprasio',[
            'where' => [
                'kode_anggaran' => $anggaran->kode_anggaran,
                'kode_cabang'   => $cabang,
                'kode'          => $arr_kode
            ],
        ])->result_array();
        foreach ($arr_kode as $v) {
            $key = array_search($v, array_column($dt_budget_rekaprasio, 'kode'));
            if(strlen($key)>0):
                $data[$v] = $dt_budget_rekaprasio[$key];
            else:
                $data[$v] = [
                    'B_01' => 0,
                    'B_02' => 0,
                    'B_03' => 0,
                    'B_04' => 0,
                    'B_05' => 0,
                    'B_06' => 0,
                    'B_07' => 0,
                    'B_08' => 0,
                    'B_09' => 0,
                    'B_10' => 0,
                    'B_11' => 0,
                    'B_12' => 0,
                ];
            endif;
        }
	endif;

	$res = [];
	$total = 0;
	for($i=1;$i<=12;$i++){
		$bulan = 'B_'.sprintf("%02d",$i);
		if($status_pembagi):
			$pembagi = 0; foreach ($arr_bagi as $v) {
				$pembagi += $data[$v][$bulan];
			}
			$total += $pembagi;
			if($s_avg) $pembagi = $total / $i; 
			if(!$pembagi) $pembagi = 1;

			$tambah = 0; foreach ($arr_tambah as $v) {
				$tambah += $data[$v][$bulan];
			}
			foreach ($arr_kurang as $k => $v) {
				if($k == 0):
					$tambah = $data[$v][$bulan];
				else:
					$tambah -= $data[$v][$bulan];
				endif;
			}


			if($s_setahun) $tambah = ($tambah/$i) * 12;

       		$A1 = ( $tambah/ $pembagi) * 100;
       		$res[$bulan] = custom_format($A1,false,2);
       	elseif($status_tambah):
			$tambah = 0; foreach ($arr_tambah as $v) {
				$tambah += $data[$v][$bulan];
			}

			if($s_setahun) $tambah = ($tambah/$i) * 12;

       		$res[$bulan] = custom_format(view_report($tambah));
       	elseif($status_kurang):
			$tambah = 0; foreach ($arr_kurang as $k => $v) {
				if($k == 0):
					$tambah = $data[$v][$bulan];
				else:
					$tambah -= $data[$v][$bulan];
				endif;
			}

			if($s_setahun) $tambah = ($tambah/$i) * 12;

       		$res[$bulan] = custom_format(view_report($tambah));
       	elseif($status_get):
       		$res[$bulan] = custom_format(view_report($data[$coa][$bulan]));
       	elseif($s_no_data):
       		$res[$bulan] = '';
		endif;
	}
	return $res;
}

function cabang_not_show(){
	return ['G001'];
}

function checkFormulaAkt($where,$data,$bulan){
	$key = multidimensional_search($data, $where);
	$res = ['status' => false];
	if(strlen($key)>0):
		$changed = json_decode($data[$key]['changed']);
		if(in_array($bulan, $changed)):
			$res['status'] 	= true;
			$res['data']	= $data[$key];
		endif;
	endif;
	return $res;
}

function checkFormulaAkt2($where,$data){
	$key = multidimensional_search($data, $where);
	$res = ['status' => false];
	if(strlen($key)>0):
		$res['status'] 	= true;
		$res['data']	= $data[$key];
	endif;
	return $res;
}

function checkSavedFormulaAkt($data,$anggaran){
	foreach ($data as $k => $v) {
		$dt 		= explode('-', $k);
		$coa 		= $dt[0];
		$tahun_core = $dt[1];
		$cabang 	= $dt[2];

		$record = insert_view_report_arr($v);
		
		$ck = get_data('tbl_formula_akt',[
			'select' => 'id',
			'where'	 => "glwnco = '$coa' and kode_anggaran = '$anggaran->kode_anggaran' and tahun_core = '$tahun_core' and kode_cabang = '$cabang'"
		])->row_array();
		if($ck):
			$ID = $ck['id'];
			update_data('tbl_formula_akt',$record,['id' => $ID]);
		else:
			$record['kode_cabang'] 		= $cabang;
			$record['kode_anggaran'] 	= $anggaran->kode_anggaran;
			$record['tahun_core']	 	= $tahun_core;
			$record['glwnco']			= $coa;
			if($tahun_core != $anggaran->tahun_anggaran):
				$record['parent_id'] = $cabang;
			else:
				$record['parent_id'] = "0";
			endif;
			insert_data('tbl_formula_akt',$record);
		endif;
	}
}

function checkFomulaAktSewa($data,$bulan,$tahun){
	$res = 0;
	foreach ($data as $k => $v) {
		if($v['tahun'] == $tahun && $v['bulan'] == $bulan){
			$res += $v['harga'];
		}
	}
	return $res;
}

function searchPersentase($where,$data){
	$key = multidimensional_search($data, $where);
	$res = 0;
	if(strlen($key)>0):
		$dt = $data[$key];
		$val = (float) $dt['persen'];
		$res = $val/100;
	endif;
	return $res;
}

function cabang_divisi($access=""){
	 $segment = $cur_segment = uri_segment(2) ? uri_segment(2) : uri_segment(1);
    if($access) {
        $cur_segment        = $access;
    }
    $dt_access    = get_access($cur_segment);
    $cabang_user  = get_data('tbl_user',[
        'where' => [
            'is_active' => 1,
            'id_group'  => id_group_access($cur_segment),
            ''
        ]
    ])->result();

    $kode_cabang          = [];
    foreach($cabang_user as $c) $kode_cabang[] = $c->kode_cabang;

    $id = user('kode_cabang');
    $cab = get_data('tbl_m_cabang','kode_cabang',$id)->row_array();

    $x = '';
    if(isset($cab['id'])){ 
        for ($i = 1; $i <= 4; $i++) { 
            $field = 'level' . $i ;

            if($cab['id'] == $cab[$field]) {
                $x = $field ; 
            }    
        }    
    }
    $query = [
	    'select'    => 'distinct a.id,a.kode_cabang,a.nama_cabang',
	    'where'     => [
	        'a.is_active' => 1,
	        'a.'.$x => $cab['id'],
	        'a.kode_cabang' => $kode_cabang,
	        'a.kode_cabang != ' => 'G001'
    	]
    ];
    $data['status_group'] 		= $cab['status_group'];
    $data['access_additional']  = $dt_access['access_additional'];
    if($dt_access['access_additional']):
    	unset($query['where']['a.'.$x]);
    	$data['status_group'] 		= 1;
    endif;
    $data['cabang']            	= get_data('tbl_m_cabang a',$query)->result_array();


    if($data['status_group'] == 1):
    	$option_induk = '<label class="">Cabang Induk &nbsp</label>';
    	$option_induk .= '<select class="select2 custom-select" id="filter_cabang_induk" data-type="divisi">';
		foreach($data['cabang'] as $b){
			$selected = '';
			if($b['kode_cabang'] == user('kode_cabang')) $selected = ' selected';
			$nama_cabang 	= $b['nama_cabang'];
			$option_induk 	.= '<option value="'.$b['id'].'"'.$selected.'>'.$nama_cabang.'</option>';
		}
		$option_induk .= '</select>';

		$option_induk .= '<label class="">&nbsp '.lang('cabang').'  &nbsp</label>';
		$option_induk .= '<select class="select2 custom-select" id="filter_cabang">';
		$option_induk .= '</select>&nbsp';

		$option_induk .= '<style>';
		$option_induk .= '.content-header{ height: auto !important; }';
		$option_induk .= '.content-header .float-right{ margin-top: 1rem !important; }';
		$option_induk .= '.content-header .header-info{ position: relative !important; }';
		$option_induk .= '.mt-6{ margin-top: 4em;}';
		$option_induk .= '</style>';
		$data['option'] = $option_induk;
    else:
    	$item = '<label class="">'.lang('cabang').'  &nbsp</label>';
		$item .= '<select class="select2 custom-select" id="filter_cabang">';
		foreach($data['cabang'] as $b){
			$selected = '';
			if($b['kode_cabang'] == user('kode_cabang')) $selected = ' selected';
			$item .= '<option value="'.$b['kode_cabang'].'"'.$selected.'>'.$b['nama_cabang'].'</option>';
		}
		$item .= '</select>';
		$data['option'] = $item;
    endif;

    $data['tahun'] = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->result();
    return $data;
}

// clone table rate dan prosentase dpk
function clone_rate($kode_anggaran,$table){
	$anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$kode_anggaran)->row();
	$last_anggaran = get_data('tbl_tahun_anggaran',[
		'select' 		=> 'kode_anggaran',
		'where' 		=> "kode_anggaran != '$kode_anggaran' and is_active = '1' ",
		'order_by' 		=> 'id',
		'sort'			=> 'DESC',
	])->row();

	if($last_anggaran):
		$rate = get_data($table,[
			'where' => [
				'kode_anggaran' => $last_anggaran->kode_anggaran,
				'is_active'		=> 1,
			]
		])->result();
		foreach ($rate as $k => $v) {
			unset($v->id);
			$v->kode_anggaran 		= $anggaran->kode_anggaran;
			$v->id_anggaran 		= $anggaran->id;
			$v->keterangan_anggaran = $anggaran->keterangan;
			$v->create_by 			= user('username');
			$v->create_at 			= date("Y-m-d H:i:s");
		}
		if(count($rate)>0):
			insert_batch($table,$rate);
		endif;
	endif;
}

function clone_value_table($table,$last_anggaran,$anggaran,$additional = array()){
	if($last_anggaran):
		$d = get_data($table,[
			'where' => [
				'kode_anggaran' => $last_anggaran->kode_anggaran,
				'is_active'		=> 1,
			]
		])->result_array();
		$data = [];
		foreach ($d as $k => $v) {
			unset($v['id']);
			$v['kode_anggaran'] 		= $anggaran->kode_anggaran;
			foreach ($additional as $k2 => $v2) {
				if(isset($v[$k2])):
					$v[$k2] = $v2;
				endif;
			}
			$data[] = $v;
		}
		if(count($data)>0):
			// render($data,'json');
			insert_batch($table,$data);
		endif;
	endif;
}

function clone_table($table,$table_last_anggaran){
	$CI         			= get_instance();
    $status 				= $CI->db->table_exists($table);
    $status_last_anggaran 	= $CI->db->table_exists($table_last_anggaran);
    if(!$status && $status_last_anggaran):
    	$CI->db->query("CREATE TABLE ".$table." AS SELECT * FROM ".$table_last_anggaran);
    endif;
}

function coa_neraca($coa){
	$data = [];
    foreach ($coa as $k => $v) {
        
        // center
        if(!$v->level0 && !$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa'][] = $h;
        endif;

        // level 0
        if($v->level0 && !$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa0'][$v->level0][] = $h;
        endif;

        // level 1
        if(!$v->level0 && $v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa1'][$v->level1][] = $h;
        endif;

        // level 2
        if(!$v->level0 && !$v->level1 && $v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa2'][$v->level2][] = $h;
        endif;

        // level 3
        if(!$v->level0 && !$v->level1 && !$v->level2 && $v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa3'][$v->level3][] = $h;
        endif;

        // level 4
        if(!$v->level0 && !$v->level1 && !$v->level2 && !$v->level3 && $v->level4 && !$v->level5):
            $h = $v;
            $data['coa4'][$v->level4][] = $h;
        endif;

        // level 5
        if(!$v->level0 && !$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && $v->level5):
            $h = $v;
            $data['coa5'][$v->level5][] = $h;
        endif;
    }

    if(!isset($data['coa'])):
    	$data['coa'] = [];
    endif;

    return $data;
}

function coa_labarugi($coa){
    $data = [];
    foreach ($coa as $k => $v) {

        // center
        if(!$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa'][] = $h;
        endif;

        // level 1
        if($v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa0'][$v->level1][] = $h;
        endif;

        // level 2
        if(!$v->level1 && $v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa1'][$v->level2][] = $h;
        endif;

        // level 3
        if(!$v->level1 && !$v->level2 && $v->level3 && !$v->level4 && !$v->level5):
            $h = $v;
            $data['coa2'][$v->level3][] = $h;
        endif;

        // level 4
        if(!$v->level1 && !$v->level2 && !$v->level3 && $v->level4 && !$v->level5):
            $h = $v;
            $data['coa3'][$v->level4][] = $h;
        endif;

        // level 5
        if(!$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && $v->level5):
            $h = $v;
            $data['coa4'][$v->level5][] = $h;
        endif;
    }
    if(!isset($data['coa'])):
    	$data['coa'] = [];
    endif;
    return $data;
}

function create_autorun($kode_anggaran,$kode_cabang,$page){
	$where = [
		'kode_anggaran' => $kode_anggaran,
		'kode_cabang'	=> $kode_cabang,
		'page'			=> $page,
		'status'		=> 1,
	];
	$ck = get_data('tbl_autorun',['select' => 'id','where' => $where])->result_array();
	if(count($ck)<=0):
		save_data('tbl_autorun',$where);
	endif;
}
function call_autorun($kode_anggaran,$kode_cabang,$page){
	$where = [
		'kode_anggaran' => $kode_anggaran,
		'kode_cabang'	=> $kode_cabang,
		'page'			=> $page,
		'status'		=> 1,
	];
	$ck = get_data('tbl_autorun',['select' => 'id','where' => $where])->result_array();
	$count = count($ck);
	foreach ($ck as $k => $v) {
		$data['id'] 	= $v['id'];
		$data['status']	= 0;
		save_data('tbl_autorun',$data);
	}
	return $count;
}