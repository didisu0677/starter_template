<div class="text-center pt-2 pb-2 mb-4">
	<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="<?php setting('title'); ?>" width="200">
</div>
<?php
    form_open(base_url('auth/register/do_reg'),'post','form-reg');
        col_init(3,9);
        ?>
        <div class="form-group row">
            <label class="col-form-label col-sm-3" for="jenis_rekanan"><?php echo lang('jenis_rekanan'); ?></label>
            <div class="col-sm-9">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-light jenis_rekanan active">
                        <input type="radio" name="jenis_rekanan" id="jenis_rekanan" value="1" checked> <?php echo lang('badan_usaha'); ?>
                    </label>
                    <label class="btn btn-light jenis_rekanan_1">
                        <input type="radio" name="jenis_rekanan" id="jenis_rekanan_1" value="2"> <?php echo lang('perorangan'); ?>
                    </label>
                </div>
            </div>
        </div>
        <?php 
        label(strtoupper(lang('informasi_umum')),'mb-2 mt-2');
        input('text',lang('nama_perusahaan'),'nama','required|max-length:100','','maxlength="100" data-alias1="'.lang('nama_perusahaan').'" data-alias2="'.lang('nama_lengkap').'"');
        input('text',lang('npwp_perusahaan'),'npwp','required|max-length:30','','maxlength="50" data-alias1="'.lang('npwp_perusahaan').'" data-alias2="'.lang('npwp').'"');
        select2(lang('kategori_rekanan'),'id_kategori_rekanan[]','required',$kategori_rekanan,'id','kategori','','multiple');
        ?>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 required" for="no_identitas"><?php echo lang('no_identitas'); ?></label>
            <div class="col-sm-5">
                <input type="text" name="no_identitas" id="no_identitas" class="form-control" autocomplete="off" data-validation="required|max-length:50" />
            </div>
            <div class="col-sm-4 mt-2 mt-sm-0">
                <input type="text" name="tanggal_berakhir_identitas" id="tanggal_berakhir_identitas" autocomplete="off" class="form-control dp" data-validation="required" placeholder="<?php echo lang('berlaku_sampai'); ?>">
            </div>
        </div>
        <?php
        select2(lang('bentuk_badan_usaha'),'id_bentuk_badan_usaha','required',$bentuk_badan_usaha,'id','bentuk_badan_usaha');
        select2(lang('status_perusahaan'),'id_status_perusahaan','required',$status_perusahaan,'id','status_perusahaan');
        select2(lang('kualifikasi'),'id_kualifikasi','required',$kualifikasi,'id','kualifikasi');
        select2(lang('asosiasi'),'id_asosiasi','required',$asosiasi,'id','asosiasi');
        select2(lang('mendaftar_di_unit'),'id_unit_daftar','required',$unit,'id','unit');

        label(strtoupper(lang('alamat_perusahaan')),'mb-2 mt-2','id="label-alamat" data-alias1="'.strtoupper(lang('alamat_perusahaan')).'" data-alias2="'.strtoupper(lang('alamat_lengkap')).'"');
        textarea(lang('alamat'),'alamat','required');
        col_init(3,5);
        select2(lang('negara'),'id_negara','required',$negara,'id','nama','101');
        ?>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 required" for="id_provinsi"><?php echo lang('provinsi'); ?></label>
            <div class="col-sm-5">
                <select name="id_provinsi" id="id_provinsi" class="form-control select2" data-validation="required">
                    <option value=""></option>
                    <?php foreach($provinsi as $p) { ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo $p['nama']; ?></option>
                    <?php } ?>
                    <option value="999"><?php echo lang('lainnya'); ?></option>
                </select>
            </div>
            <div class="col-sm-4 mt-2 mt-sm-0 hidden">
                <input type="text" name="nama_provinsi" id="nama_provinsi" autocomplete="off" class="form-control" data-validation="required|max-length:50" value="" placeholder="<?php echo lang('nama_provinsi'); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 required" for="id_kota"><?php echo lang('kota'); ?></label>
            <div class="col-sm-5">
                <select name="id_kota" id="id_kota" class="form-control select2" data-validation="required">
                    <option value=""></option>
                </select>
            </div>
            <div class="col-sm-4 mt-2 mt-sm-0 hidden">
                <input type="text" name="nama_kota" id="nama_kota" autocomplete="off" class="form-control" data-validation="required|max-length:50" value="" placeholder="<?php echo lang('nama_kota'); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 required" for="id_kecamatan"><?php echo lang('kecamatan'); ?></label>
            <div class="col-sm-5">
                <select name="id_kecamatan" id="id_kecamatan" class="form-control select2" data-validation="required">
                    <option value=""></option>
                </select>
            </div>
            <div class="col-sm-4 mt-2 mt-sm-0 hidden">
                <input type="text" name="nama_kecamatan" id="nama_kecamatan" autocomplete="off" class="form-control" data-validation="required|max-length:50" value="" placeholder="<?php echo lang('nama_kecamatan'); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 required" for="id_kelurahan"><?php echo lang('kelurahan'); ?></label>
            <div class="col-sm-5">
                <select name="id_kelurahan" id="id_kelurahan" class="form-control select2" data-validation="required">
                    <option value=""></option>
                </select>
            </div>
            <div class="col-sm-4 mt-2 mt-sm-0 hidden">
                <input type="text" name="nama_kelurahan" id="nama_kelurahan" autocomplete="off" class="form-control" data-validation="required|max-length:50" value="" placeholder="<?php echo lang('nama_kelurahan'); ?>">
            </div>
        </div>
        <?php
        input('text',lang('kode_pos'),'kode_pos','required|length:5|number','','maxlength="5"');
        col_init(3,9);
        input('text',lang('no_telepon'),'no_telepon','required|phone|max-length:20','','maxlength="20"');
        input('text',lang('no_fax'),'no_fax','required|phone|max-length:30','','maxlength="20"');
        input('text',lang('email'),'email','required|email|unique|max-length:50','','maxlength="100"');

        label(strtoupper(lang('kontak_person')),'mb-2 mt-2');
        input('text',lang('nama'),'nama_cp','required|max-length:100');
        input('text',lang('hp'),'hp_cp','required|phone|max-length:30','','maxlength="20"');
        input('text',lang('email'),'email_cp','required|email|unique|max-length:50');
        ?>
        <div class="form-group row">
            <div class="col-sm-9 offset-sm-3">
                <?php echo $captcha; ?>
            </div>
        </div>
        <div class="form-group row mb-3">
            <div class="col-sm-9 offset-sm-3">
                <div class="custom-checkbox custom-control custom-control-inline">
                    <input class="custom-control-input" type="checkbox" id="setuju" name="setuju">
                    <label class="custom-control-label" for="setuju"><?php echo lang('desc_setuju_pendaftaran'); ?></label>
                </div>
            </div>
        </div>
        <?php
        form_button(lang('daftar'),false);
    form_close();
?>
<script>
function badan_usaha() {
    $('#nama').closest('.form-group').children('.col-form-label').text($('#nama').attr('data-alias1'));
    $('#npwp').closest('.form-group').children('.col-form-label').text($('#npwp').attr('data-alias1'));
    $('#no_identitas').closest('.form-group').addClass('hidden');
    $('#no_identitas,#tanggal_berakhir_identitas').val('');
    $('input[name="validation_no_identitas"],input[name="validation_tanggal_berakhir_identitas"]').val('');
    $('input[name="validation_id_bentuk_badan_usaha"],input[name="validation_id_status_perusahaan"]').val('required');
    $('#id_bentuk_badan_usaha,#id_status_perusahaan').closest('.form-group').removeClass('hidden');
    $('#label-alamat').text($('#label-alamat').attr('data-alias1'));
}
function perorangan() {
    $('#nama').closest('.form-group').children('.col-form-label').text($('#nama').attr('data-alias2'));
    $('#npwp').closest('.form-group').children('.col-form-label').text($('#npwp').attr('data-alias2'));
    $('#no_identitas').closest('.form-group').removeClass('hidden');
    $('#id_bentuk_badan_usaha,#id_status_perusahaan').closest('.form-group').addClass('hidden');
    $('#id_bentuk_badan_usaha,#id_status_perusahaan').val('').trigger('change');
    $('input[name="validation_id_bentuk_badan_usaha"],input[name="validation_id_status_perusahaan"]').val('');
    $('input[name="validation_no_identitas"],input[name="validation_tanggal_berakhir_identitas"]').val('required');
    $('#label-alamat').text($('#label-alamat').attr('data-alias2'));
}
function checkbox_setuju() {
    setTimeout(function(){
        if($('#setuju').is(':checked')) {
            $('button[type="submit"]').removeAttr('disabled');
        } else {
            $('button[type="submit"]').attr('disabled',true);
        }
    },100);
}
function toHome() {
    window.location = base_url;
}
$('.select2').each(function(){
    var $t = $(this);
    $t.select2({
        placeholder: ''
    });
});
$('.dp').each(function(){
    var placeholder = typeof $(this).attr('placeholder') != 'undefined' ? $(this).attr('placeholder') : 'dd/mm/yyyy';
    $(this).mask('00/00/0000', {placeholder: placeholder});
});
$('.dp').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 1950,
    maxYear: parseInt(moment().format('YYYY'),10) + 3,
    locale: {
        format: 'DD/MM/YYYY',
        cancelLabel: lang.batal,
        applyLabel: lang.ok,
        daysOfWeek: [lang.sen, lang.sel, lang.rab, lang.kam, lang.jum, lang.sab, lang.min],
        monthNames: [lang.jan, lang.feb, lang.mar, lang.apr, lang.mei, lang.jun, lang.jul, lang.agu, lang.sep, lang.okt, lang.nov, lang.des]
    },
    autoUpdateInput: false
}, function(start, end, label) {
    $(this.element[0]).removeClass('is-invalid');
    $(this.element[0]).parent().find('.error').remove();
}).on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY'));
    var act = window[$(this).attr('id') + '_callback'];
    if(typeof act == 'function') {
        act();
    }
}).on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
    var act = window[$(this).attr('id') + '_callback'];
    if(typeof act == 'function') {
        act();
    }
});
$('#jenis_rekanan, .jenis_rekanan').click(function(){
    badan_usaha();
});
$('#jenis_rekanan_1, .jenis_rekanan_1').click(function(){
    perorangan();
});
$(document).ready(function(){
    badan_usaha();
    $('#form-reg button[type="submit"]').attr('disabled',true).addClass('no-spinner');
});
$('#id_negara').change(function(){
    if($(this).val() != '101') {
        $('#id_provinsi').html('<option value=""></option><option value="999">'+lang.lainnya+'</option>').trigger('change');
    } else {
        $('#id_provinsi').html('<option value="0">'+lang.mohon_tunggu+'</option>').trigger('change');
        readonly_ajax = false;
        $.getJSON(base_url + 'ajax/json/wilayah', function(data){
            var konten = '<option value=""></option>';
            $.each(data,function(d,v){
                konten += '<option value="'+v.id+'">'+v.nama+'</option>';
            });
            konten += '<option value="999">'+lang.lainnya+'</option>';
            $('#id_provinsi').html(konten).trigger('change');
            readonly_ajax = true;
            checkbox_setuju();
        });
    }
});
$('#id_provinsi').change(function(){
    if($(this).val() != '' && $(this).val() != '0') {
        if($(this).val() == '999') {
            $('#nama_provinsi').parent().removeClass('hidden');
            $('#nama_provinsi').val('');
            $('#id_kota').html('<option value=""></option><option value="999">'+lang.lainnya+'</option>').trigger('change');
        } else {
            $('#nama_provinsi').parent().addClass('hidden');
            $('#nama_provinsi').val($(this).find(':selected').text());
            $('#id_kota').html('<option value="0">'+lang.mohon_tunggu+'</option>').trigger('change');
            readonly_ajax = false;
            $.getJSON(base_url + 'ajax/json/wilayah/' + $(this).val(), function(data){
                var konten = '<option value=""></option>';
                $.each(data,function(d,v){
                    konten += '<option value="'+v.id+'">'+v.nama+'</option>';
                });
                konten += '<option value="999">'+lang.lainnya+'</option>';
                $('#id_kota').html(konten).trigger('change');
                readonly_ajax = true;
                checkbox_setuju();
            });
        }
    } else {
        $('#nama_provinsi').parent().addClass('hidden');
        $('#nama_provinsi').val($(this).find(':selected').text());
        $('#id_kota').html('<option value=""></option>').trigger('change');
    }
});
$('#id_kota').change(function(){
    if($(this).val() != '' && $(this).val() != '0') {
        if($(this).val() == '999') {
            $('#nama_kota').parent().removeClass('hidden');
            $('#nama_kota').val('');
            $('#id_kecamatan').html('<option value=""></option><option value="999">'+lang.lainnya+'</option>').trigger('change');
        } else {
            $('#nama_kota').parent().addClass('hidden');
            $('#nama_kota').val($(this).find(':selected').text());
            $('#id_kecamatan').html('<option value="0">'+lang.mohon_tunggu+'</option>').trigger('change');
            readonly_ajax = false;
            $.getJSON(base_url + 'ajax/json/wilayah/' + $(this).val(), function(data){
                var konten = '<option value=""></option>';
                $.each(data,function(d,v){
                    konten += '<option value="'+v.id+'">'+v.nama+'</option>';
                });
                konten += '<option value="999">'+lang.lainnya+'</option>';
                $('#id_kecamatan').html(konten).trigger('change');
                readonly_ajax = true;
                checkbox_setuju();
            });
        }
    } else {
        $('#nama_kota').parent().addClass('hidden');
        $('#nama_kota').val($(this).find(':selected').text());
        $('#id_kecamatan').html('<option value=""></option>').trigger('change');
    }
});
$('#id_kecamatan').change(function(){
    if($(this).val() != '' && $(this).val() != '0') {
        if($(this).val() == '999') {
            $('#nama_kecamatan').parent().removeClass('hidden');
            $('#nama_kecamatan').val('');
            $('#id_kelurahan').html('<option value=""></option><option value="999">'+lang.lainnya+'</option>').trigger('change');
        } else {
            $('#nama_kecamatan').parent().addClass('hidden');
            $('#nama_kecamatan').val($(this).find(':selected').text());
            $('#id_kelurahan').html('<option value="0">'+lang.mohon_tunggu+'</option>').trigger('change');
            readonly_ajax = false;
            $.getJSON(base_url + 'ajax/json/wilayah/' + $(this).val(), function(data){
                var konten = '<option value=""></option>';
                $.each(data,function(d,v){
                    konten += '<option value="'+v.id+'">'+v.nama+'</option>';
                });
                konten += '<option value="999">'+lang.lainnya+'</option>';
                $('#id_kelurahan').html(konten).trigger('change');
                readonly_ajax = true;
                checkbox_setuju();
            });
        }
    } else {
        $('#nama_kecamatan').parent().addClass('hidden');
        $('#nama_kecamatan').val($(this).find(':selected').text());
        $('#id_kelurahan').html('<option value=""></option>').trigger('change');
    }
});
$('#id_kelurahan').change(function(){
    if($(this).val() == '999') {
        $('#nama_kelurahan').parent().removeClass('hidden');
        $('#nama_kelurahan').val('');
    } else {
        $('#nama_kelurahan').parent().addClass('hidden');
        $('#nama_kelurahan').val($(this).find(':selected').text());
    }
});
$('#setuju').click(function(){
    if($(this).is(':checked')) {
        $('button[type="submit"]').removeAttr('disabled');
    } else {
        $('button[type="submit"]').attr('disabled',true);
    }
});
$('#form-reg').submit(function(e){
    e.preventDefault();
    if(validation('form-reg')) {
        $.ajax({
            url : $(this).attr('action'),
            data : $(this).serialize(),
            type : 'post',
            dataType: 'json',
            success : function(response) {
                if(response.status == 'success') {
                    cAlert.open(response.message,response.status,'toHome');
                } else {
                    cAlert.open(response.message,response.status);
                    $('#captcha_refresh').trigger('click');
                    $('#captcha_code').val('');
                }
            }
        });
    }
});
</script>