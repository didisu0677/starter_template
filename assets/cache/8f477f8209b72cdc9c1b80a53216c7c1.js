
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
