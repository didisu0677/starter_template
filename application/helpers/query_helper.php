<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function switch_database($db_group='default') {
    $CI     = get_instance();
    $CI->session->set_userdata('db_active',$db_group);
}

function db_name() {
    $CI         = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_active  = $db_active ? $db_active : 'default';
    $db_group   = $CI->load->database($db_active,TRUE);
    return $db_group->database;
}

function db_list_table($table="") {
    $CI         = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_active  = $db_active ? $db_active : 'default';
    $db_group   = $CI->load->database($db_active,TRUE);
    return $db_group->list_tables($table);
}

function list_tables() {
    $CI         = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_active  = $db_active ? $db_active : 'default';
    $db_group   = $CI->load->database($db_active,TRUE);
    return $db_group->list_tables();
}

function table_exists($table="") {
    $CI         = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_active  = $db_active ? $db_active : 'default';
    $db_group   = $CI->load->database($db_active,TRUE);
    return $db_group->table_exists($table);
}

function db_query($query=""){
    $CI     = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_active  = $db_active ? $db_active : 'default';
    $db_group   = $CI->load->database($db_active,TRUE);
    return $db_group->query($query);
}

function get_field($table="",$get="") {
    $CI     = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_active  = $db_active ? $db_active : 'default';
    $db_group   = $CI->load->database($db_active,TRUE);
    if($get) {
        $field  = $db_group->field_data($table);
        $f      = array();
        foreach ($field as $fi) {
            $f[]    = $fi->$get;
        }
        return $f;
    } else
        return $db_group->field_data($table);
}

function last_query(){
    $CI         = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_group   = $db_active ? $CI->load->database($db_active,TRUE) : $CI->db;
    return $db_group->last_query();
}

function get_data($table="",$attr=array(),$column=''){
    $CI         = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_group   = $db_active ? $CI->load->database($db_active,TRUE) : $CI->db;
    if(is_array($attr)) {
        if(isset($attr['select']))
            $db_group->select($attr['select'],false);
        if(isset($attr['select_max']))
            $db_group->select_max($attr['select_max']);
        if(isset($attr['select_min']))
            $db_group->select_min($attr['select_min']);
        if(isset($attr['select_sum']))
            $db_group->select_sum($attr['select_sum']);
        if(isset($attr['array_in']) && count($attr['array_in'])!=0 && isset($attr['field_in']))
            $db_group->where_in($attr['field_in'],$attr['array_in']);
        if(isset($attr['where_in']) && is_array($attr['where_in'])) {
            foreach($attr['where_in'] as $field => $arr) {
                $db_group->where_in($field,$arr);
            }
        }
        if(isset($attr['array_not_in']) && count($attr['array_not_in'])!=0 && isset($attr['field_not_in']))
            $db_group->where_not_in($attr['field_not_in'],$attr['array_not_in']);
        if(isset($attr['where_not_in']) && is_array($attr['where_not_in'])) {
            foreach($attr['where_not_in'] as $field => $arr) {
                $db_group->where_not_in($field,$arr);
            }
        }
        if(isset($attr['sort']) || isset($attr['order']))
            $sort = $attr['sort'];
        else
            $sort = 'ASC';
        if(isset($attr['sort_by']))
            $db_group->order_by($attr['sort_by'],$sort);
        if(isset($attr['sort_array']) && count($attr['sort_array']) > 0){
            foreach($attr['sort_array'] as $k_sa => $sa){
                $db_group->order_by($k_sa,$sa);
            }
        }
        if(isset($attr['order_by']))
            $db_group->order_by($attr['order_by'],$sort);
        if(isset($attr['order_array']) && count($attr['order_array']) > 0){
            foreach($attr['order_array'] as $k_sa => $sa){
                $db_group->order_by($k_sa,$sa);
            }
        }
        if(isset($attr['where_field'])){
            if(is_array($attr['where_field'])){
                foreach($attr['where_field'] as $wf){
                    $db_group->where($wf);
                }
            }else{
                if(isset($attr['where']))
                    $db_group->where($attr['where_field'],$attr['where']);
                else
                    $db_group->where($attr['where_field']);
            }
        }
        if(isset($attr['where'])){
            if(is_array($attr['where'])){
                foreach($attr['where'] as $kw => $vw) {
                    if(is_array($vw)) {
                        if(strpos($kw, '!=') !== false ) {
                            $wh = trim(str_replace('!=', '', $kw));
                            $db_group->where_not_in($wh,$vw);
                        } else {
                            $db_group->where_in($kw,$vw);
                        }
                    } else {
                        if(strtolower(substr($kw, 0 ,3)) == '__m') {
                            if($vw) $db_group->where($vw);
                        } elseif(strtolower(substr($kw, 0 ,5)) == 'like ') {
                            $db_group->like(substr($kw, 5),$vw);
                        } elseif(strtolower(substr($kw, 0 ,3)) == 'or ') {
                            $db_group->or_where(substr($kw, 3),$vw);
                        } else {
                            if(strtolower($vw) == 'null') {
                                if(strpos($kw, '!=') !== false ) {
                                    $wh = trim(str_replace('!=', '', $kw));
                                    $db_group->where($wh.' IS NOT NULL',NULL,FALSE);
                                } else {
                                    $db_group->where($kw.' IS NULL',NULL,FALSE);
                                }
                            } else {
                                $db_group->where($kw,$vw);
                            }
                        }
                    }
                }
            } else {
                $db_group->where($attr['where']);
            }
        }
        if(isset($attr['where_or_field'])){
            if(isset($attr['where_or']))
                $db_group->or_where($attr['where_or_field'],$attr['where_or']);
            else
                $db_group->or_where($attr['where_or_field']);
        }
        if(isset($attr['group_by'])){
            if(is_array($attr['group_by'])){
                foreach($attr['group_by'] as $g){
                    $db_group->group_by($g);
                }
            }else
                $db_group->group_by($attr['group_by']);
        }
        if(isset($attr['limit']) && $attr['limit'] > 0){
            if(isset($attr['offset']))
                $db_group->limit($attr['limit'],$attr['offset']);
            else
                $db_group->limit($attr['limit']);
        }
        if(isset($attr['join']) && isset($attr['join_on'])){
            if(isset($attr['join_type']))
                $db_group->join($attr['join'],$attr['join_on'],$attr['join_type']);
            else
                $db_group->join($attr['join'],$attr['join_on']);
        } elseif(isset($attr['join'])) {
            if(is_array($attr['join'])) {
                foreach($attr['join'] as $kj => $vj) {
                    if(isset($vj['on'])) {
                        if(isset($vj['type'])) {
                            $db_group->join($kj,$vj['on'],$vj['type']);
                        } else {
                            $db_group->join($kj,$vj['on']);
                        }
                    } elseif (!is_array($vj)) {
                        $spl_on     = preg_split("/ on /i",$vj);
                        if(count($spl_on) == 2) {
                            $table_join     = trim($spl_on[0]);
                            $spl_type       = preg_split("/ type /i",$spl_on[1]);
                            $on_join        = trim($spl_type[0]);
                            if(count($spl_type) == 2) {
                                $type_join  = trim($spl_type[1]);
                                $db_group->join($table_join,$on_join,$type_join);
                            } else {
                                $db_group->join($table_join,$on_join);
                            }
                        }        
                    }
                }    
            } else {
                $spl_on     = preg_split("/ on /i",$attr['join']);
                if(count($spl_on) == 2) {
                    $table_join     = trim($spl_on[0]);
                    $spl_type       = preg_split("/ type /i",$spl_on[1]);
                    $on_join        = trim($spl_type[0]);
                    if(count($spl_type) == 2) {
                        $type_join  = trim($spl_type[1]);
                        $db_group->join($table_join,$on_join,$type_join);
                    } else {
                        $db_group->join($table_join,$on_join);
                    }
                }
            }
        }
        if(isset($attr['multi_join'])){
            foreach($attr['multi_join'] as $mj){
                if(isset($mj['type']))
                    $db_group->join($mj['join'],$mj['on'],$mj['type']);
                else
                    $db_group->join($mj['join'],$mj['join_on']);
            }
        }
        if(isset($attr['like']) && is_array($attr['like'])) {
            foreach($attr['like'] as $k_lk => $v_lk) {
                if(is_array($v_lk)) {
                    foreach($v_lk as $k => $vlk) {
                        $db_group->like($k_lk,$vlk);
                    }
                } else {
                    if(strtolower(substr($k_lk, 0 ,3)) == 'or ') {
                        $db_group->or_like(substr($k_lk, 3),$v_lk);
                    } else {
                        $db_group->like($k_lk,$v_lk);
                    }
                }
            }
        }
        if(isset($attr['field_like']) && isset($attr['like']))
            $db_group->like($attr['field_like'],$attr['like']);
        if(isset($attr['field_or_like']) && isset($attr['or_like']))
            $db_group->or_like($attr['field_or_like'],$attr['or_like']);
        if(isset($attr['or_like']) && is_array($attr['or_like'])) {
            foreach($attr['or_like'] as $k_lk => $v_lk) {
                if(is_array($v_lk)) {
                    foreach($v_lk as $k => $vlk) {
                        if($k == 0) {
                            $db_group->like($k_lk,$vlk);
                        } else {
                            $db_group->or_like($k_lk,$vlk);
                        }
                    }
                } else {
                    $db_group->or_like($k_lk,$v_lk);
                }
            }
        }
        if(isset($attr['not_like']) && is_array($attr['not_like'])) {
            foreach($attr['not_like'] as $k_lk => $v_lk) {
                if(is_array($v_lk)) {
                    foreach($v_lk as $k => $vlk) {
                        $db_group->not_like($k_lk,$vlk);
                    }
                } else {
                    $db_group->not_like($k_lk,$v_lk);
                }
            }
        }
        if(isset($attr['or_not_like']) && is_array($attr['or_not_like'])) {
            foreach($attr['or_not_like'] as $k_lk => $v_lk) {
                if(is_array($v_lk)) {
                    foreach($v_lk as $k => $vlk) {
                        if($k == 0) {
                            $db_group->not_like($k_lk,$vlk);
                        } else {
                            $db_group->or_not_like($k_lk,$vlk);
                        }
                    }
                } else {
                    $db_group->or_not_like($k_lk,$v_lk);
                }
            }
        }
        if(isset($attr['where_array']) && count($attr['where_array'])>0) {
            foreach($attr['where_array'] as $kw => $vw) {
                if(is_array($vw)) {
                    if(strpos($kw, '!=') !== false ) {
                        $wh = trim(str_replace('!=', '', $kw));
                        $db_group->where_not_in($wh,$vw);
                    } else {
                        $db_group->where_in($kw,$vw);
                    }
                } else {
                    if(strtolower(substr($kw, 0 ,3)) == 'or ') {
                        $db_group->or_where(substr($kw, 3),$vw);
                    } else {
                        if(strtolower($vw) == 'null') {
                            if(strpos($kw, '!=') !== false ) {
                                $wh = trim(str_replace('!=', '', $kw));
                                $db_group->where($wh.' IS NOT NULL',NULL,FALSE);
                            } else {
                                $db_group->where($kw.' IS NULL',NULL,FALSE);
                            }
                        } else {
                            $db_group->where($kw,$vw);
                        }
                    }
                }
            }
        }
        return $db_group->get($table);
    } else {
        if(is_array($column)) {
            $db_group->where_in($attr,$column);
        } else {
            $x = preg_split( '/(>|<|=)/', $attr, -1, PREG_SPLIT_NO_EMPTY );
            if(count($x) == 1) {
                $db_group->where($attr,$column);
            } else {
                $db_group->where($attr);
            }
        }
        return $db_group->get($table);
    }

}

function get_field_data($field="", $table="",$attr=array(),$column=''){
    $dt     = get_data($table,$attr,$column)->result_array();
    $res    = [];
    foreach($dt as $d) {
        $res[]  = $d[$field];
    }
    return $res;
}

function insert_data($table="",$data=array()){
    $CI     = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_group   = $db_active ? $CI->load->database($db_active,TRUE) : $CI->db;
    $db_group->insert($table,$data);
    $id = $db_group->insert_id();
    if($id)
        return $id;
    else
        return true;
}

function insert_batch($table="",$data=array()){
    $CI     = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_group   = $db_active ? $CI->load->database($db_active,TRUE) : $CI->db;
    return $db_group->insert_batch($table,$data);
}

function update_data($table="",$data=array(),$column="",$where=""){
    $CI     = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_group   = $db_active ? $CI->load->database($db_active,TRUE) : $CI->db;
    if(is_array($column) && count($column) > 0){
        foreach($column as $c => $w){
            if(is_array($w))
                $db_group->where_in($c,$w);
            else
                $db_group->where($c,$w);
        }
    }elseif($column){
        if(is_array($where))
            $db_group->where_in($column,$where);
        else
            $db_group->where($column,$where);
    }
    return $db_group->update($table,$data);
}

function delete_data($table="",$column="",$where=""){
    $CI         = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_group   = $db_active ? $CI->load->database($db_active,TRUE) : $CI->db;
    $tipe       = is_array($column) || (!is_array($column) && $column) ? 'delete' : 'truncate';
    if($tipe == 'delete') {
        if(is_array($column)){
            foreach($column as $col => $val){
                if(is_array($val))
                    $db_group->where_in($col,$val);
                else
                    $db_group->where($col,$val);
            }
        }else{
            if(is_array($where))
                $db_group->where_in($column,$where);
            else
                $db_group->where($column,$where);
        }
        return $db_group->delete($table);
    } else
        return $db_group->query('TRUNCATE `'.$table.'`');
}

function get_master($id_setting=0) {
    $setting    = get_data('tbl_master_setting',array('where_array'=>array('id'=>$id_setting)))->row();
    if(isset($setting->id) && $setting->id_master) {
        $master = get_data('tbl_master',array('where_array'=>array('id'=>$setting->id_master)))->row();
        $arr    = array(
            'where_array' => array(
                'parent_id'     => $setting->id_master,
                'is_active'     => 1
            )
        );
        if($setting->tipe){
            $arr['sort_by'] = 'konten';
            $arr['sort']    = $setting->tipe;
        }
        if(isset($master->id) && $master->tipe == 'Integer') {
            $arr['select']  = '`id`,CONVERT(`konten`,UNSIGNED INTEGER) AS `konten`';
        }
        return get_data('tbl_master',$arr)->result_array();
    } else return array();
}

function get_menu( $tabel="" , $tbl_menu="", $id = 0 , $parent_id = 0 ){
    $CI     = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_group   = $db_active ? $CI->load->database($db_active,TRUE) : $CI->db;
    $query 	    = $db_group->query( 'SELECT id_menu FROM '.$tabel.' WHERE id_group = '.$id.' AND act_view = 1' )->result_array();
    $get_id[] = 0;
    foreach( $query as $q ){
        $get_id[] = $q['id_menu'];
    }
    $db_group->where_in( 'id' , $get_id );
    $db_group->where( 'parent_id' , $parent_id );
    $db_group->where( 'is_active' , 1 );
    $db_group->order_by( 'urutan' , 'ASC' );
    return $db_group->get( $tbl_menu )->result();
}

function id_group_access($target='',$act='view') {
    $CI         = get_instance();
    $db_active  = $CI->session->userdata('db_active');
    $db_group   = $db_active ? $CI->load->database($db_active,TRUE) : $CI->db;
    $db_group->where('target',$target);
    $menu       = $db_group->get('tbl_menu')->row_array();
    $id_menu    = isset($menu['id']) ? $menu['id'] : 0;
    $db_group->where('id_menu',$id_menu);
    $db_group->where('act_'.$act,1);
    $akses      = $db_group->get('tbl_user_akses')->result();
    $id_group   = [0];
    foreach($akses as $a) $id_group[] = $a->id_group;
    return $id_group;
}

function get_code($table="",$prefix="",$suffix="",$jumlah_digit=3,$kode="kode") {
    $left   = strlen($prefix) + $jumlah_digit;
    $right  = strlen($suffix);
    $CI     = get_instance();
    $CI->db->select('MAX(RIGHT(LEFT('.$kode.','.$left.'),'.$jumlah_digit.')) AS k',false);
    $CI->db->like($kode,$prefix,'after');
    $CI->db->like($kode,$suffix,'before');
    return $CI->db->get($table);
}
function row_content($table='',$field='',$where='',$column='') {
    $row = get_data($table,$where,$column)->row_array();
    return isset($row[$field]) ? $row[$field] : '';
}
