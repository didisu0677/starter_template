
$(document).ready(function(){
	localStorage.clear();
});
$('.hide-password').click(function(){
	if( $('#password').attr('type') == 'text') {
		$('#password').attr('type','password').focus();
		$('.hide-password i').removeClass('fa-eye-slash').addClass('fa-eye');
	} else {
		$('#password').attr('type','text').focus();
		$('.hide-password i').removeClass('fa-eye').addClass('fa-eye-slash');
	}
});
$('#form').submit(function(e){
	e.preventDefault();
	if(validation()){
		$.ajax({
			url : $(this).attr('action'),
			data : $(this).serialize(),
			type : 'POST',
			dataType : 'json',
			success : function(response) {
				if(response.status == 'success') {
					window.location = response.redirect;
				} else {
					cAlert.open(response.message,response.status);
				}
			}
		});
	}
});
