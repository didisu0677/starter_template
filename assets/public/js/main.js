$('.btn-daftar').click(function(){
    $('#modal-disclaimer').modal();
});
$('#btn-daftar').click(function(e){
    e.preventDefault();
    $(this).attr('disabled',true);
    $.get(base_url + 'welcome/get_token', function(res){
        window.location = base_url + 'auth/register?token=' + res;
    });
});