<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb($title); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container">
        <div class="row">
            <div class="col-sm-3">
                <div class="show-panel mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-header">Tabel</div>
                        <div class="card-body dropdown-menu">
                            <?php foreach($list_table as $l) { ?>
                            <a class="dropdown-item table-item" href="javascript:;" data-content="<?php echo $l; ?>"><i class="fa-table"></i><?php echo $l; ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    Hanya berlaku untuk query <strong>SELECT</strong>. Dan untuk mempercepat proses, data yang ditampilkan maksimal hanya 100 baris saja. Untuk hasil keseluruhannya silahkan gunakan fitur export.
                </div>
                <form id="form-query" action="<?php echo base_url('query/proses'); ?>" method="post">
                    <div class="form-group row">
                        <div class="col-12">
                            <input type="hidden" id="token" value="<?php csrf_token(false); ?>">
                            <textarea class="form-control code" rows="5" name="query" data-validation="required"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Query</button>
                            <button type="button" class="btn btn-success hidden" id="btn-export">Export</button>
                        </div>
                    </div>
                </form>
                <div class="mt-3" id="result"></div>
            </div>
        </div>
	</div>
</div>
<script>
    function db_query(q) {
        cLoader.open('Memuat Data...');
        $.ajax({
            url     : base_url + 'query/proses',
            data    : {query:q},
            type    : 'post',
            success : function(response) {
                cLoader.close();
                $('#result').attr('data-query',q).html(response);
                $('#btn-export').removeClass('hidden');
            }, error : function(jqXHR, exception) {
                cLoader.close();
                $('#btn-export').addClass('hidden');
                $('#result').html('');
                if (jqXHR.status === 500) {
                    var err = jqXHR.responseText.match(/<p>(.*?)<\/p>/g);
                    if(err != null) {
                        if(err.length == 5) {
                            var konten = '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                                + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                                + '<span aria-hidden="true">&times;</span>'
                                + '</button>';
                            konten += err[1].replace('<p>','').replace('</p>','');
                            konten += err[2].replace('<p>','<div class="mt-2"><code class="font-weight-bold">').replace('</p>','</code></div>');
                            konten += '</div>';
                            $('#result').html(konten);
                        }
                    } else {
                        var konten = '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                                + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                                + '<span aria-hidden="true">&times;</span>'
                                + '</button>Query yang anda masukan tidak valid.<div class="mt-2"><code class="font-weight-bold">'+q+'</code></div></div>';
                            $('#result').html(konten);

                    }
                }
            }
        });
    }
    $('#btn-export').click(function(e){
        e.preventDefault();
        if($('#result').find('table').length == 1) {
            var params = {
                'csrf_token' : $('#token').val(),
                'query' : $('#result').attr('data-query')
            };
            var url = base_url + 'query/export';
            $.redirect(url, params, "POST", "_blank"); 
        }
    });
    $('#form-query').submit(function(e){
        e.preventDefault();
        db_query($('[name="query"]').val());
    });
    $('.table-item').click(function(){
        var query = 'SELECT * FROM ' + $(this).attr('data-content');
        db_query(query);
    });
</script>