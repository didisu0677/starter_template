<?php 
option();
foreach($field as $f) {
	if($f != 'id' && $f != 'is_active' && $f != 'create_by' && $f != 'update_by' && $f != 'create_at' && $f != 'update_at') {
		option($f,$f);
	}
}
?>