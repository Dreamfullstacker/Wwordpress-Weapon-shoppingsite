jQuery(document).ready(function($){

$(function(){
	$('.color-field').wpColorPicker();
})

$('.xoo-qv-tabs li').on('click',function(){
	var tab_class = $(this).attr('class').split(' ')[0];
	$('li').removeClass('active-tab');
	$('.settings-tab').removeClass('settings-tab-active');
	$(this).addClass('active-tab');
	var class_c = $('[tab-class='+tab_class+']').attr('class');
	$('[tab-class='+tab_class+']').attr('class',class_c+' settings-tab-active');
})
	$('select[name=xoo-qv-button-position]').on('change',function(){
		if($(this).val() == 'image_hover'){
			$('.imgh-alert').show();
		}
		else{
			$('.imgh-alert').hide();
		}
	})
	



})