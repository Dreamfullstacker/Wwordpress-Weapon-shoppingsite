jQuery(document).ready(function($){


// Lightbox
 function prettyPhotoLoad() {
    $("a.zoom").prettyPhoto({
        hook: 'data-rel',
        social_tools: false,
        theme: 'pp_woocommerce',
        horizontal_padding: 20,
        opacity: 0.8,
        deeplinking: false
    });
    $("a[data-rel^='prettyPhoto']").prettyPhoto({
    	show_title: xoo_qv_localize.prettyPhoto_title,
        hook: 'data-rel',
        social_tools: false,
        theme: 'pp_woocommerce',
        horizontal_padding: 20,
        opacity: 0.8,
        deeplinking: false,
    });

}

/** Quick View Modal Animation **/
	//Animate Type (anim_class = Bounce-in,linear CSS)
function xoo_qv_animate_1(direction,anim_class){

		var height = $(document).height()+'px';
		var width = $(document).width()+'px';

		if(direction == 'top'){
			$(".xoo-qv-inner-modal").css('transform','translate(0,-'+height+')').addClass(anim_class);
		}
		else if(direction == 'next'){
			$(".xoo-qv-inner-modal").css('transform','translate(-'+width+',0)').addClass(anim_class);
		}
		else if(direction == 'prev'){
			$(".xoo-qv-inner-modal").css('transform','translate('+width+',0)').addClass(anim_class);
		}	
}
	//Animate Type (anim_class = Fade-In)
function xoo_qv_animate_2(direction,anim_class){
		$(".xoo-qv-inner-modal").css('opacity','0').addClass(anim_class);
}
	//Animate type(none)
function xoo_qv_animate_3(){}

	//Check User settings
function xoo_qv_animation_func(ajax_data,direction){
	
	if(xoo_qv_localize.modal_anim == 'linear'){
		xoo_qv_ajax(ajax_data,xoo_qv_animate_1,direction,'xoo-qv-animation-linear');
	}
	else if(xoo_qv_localize.modal_anim == 'fade-in'){
		xoo_qv_ajax(ajax_data,xoo_qv_animate_2,null,'xoo-qv-animation-fadein');
	}
	else {
		xoo_qv_ajax(ajax_data,xoo_qv_animate_3);
	}
}

//CLose Popup
function xoo_qv_close_popup(e){
	$.each(e.target.classList,function(key,value){
		if(value == 'xoo-qv-close' || value == 'xoo-qv-inner-modal'){
			$('.xoo-qv-opac').hide();
			$('.xoo-qv-panel').removeClass('xoo-qv-panel-active');
			$('.xoo-qv-modal').html('');
		}
	})
}

$('.xoo-qv-panel').on('click','.xoo-qv-close',xoo_qv_close_popup);
$('body').on('click','.xoo-qv-inner-modal',xoo_qv_close_popup);

$(document).keyup(function(e) {
  if (e.keyCode === 27){
  	$('.xoo-qv-close').trigger('click');
  } 
 })
/*****    Ajax call on button click      *****/	
function xoo_qv_ajax(ajax_data,anim_type,direction,anim_class){
		ajax_data['action'] = 'xoo_qv_ajax';
		$.ajax({
		url: xoo_qv_localize.adminurl,
		type: 'POST',
		data: ajax_data,
		success: function(response){
			$('.xoo-qv-modal').html(response);
			anim_type(direction,anim_class);
			$('.xoo-qv-pl-active').removeClass('xoo-qv-pl-active');
			 prettyPhotoLoad();
		 	$('.xoo-qv-panel').find('.variations_form').wc_variation_form();
		 	$('.xoo-qv-panel .variations_form select').change();
			 
		},
	})
}

// Main Quickview Button
$('body').on('click','.xoo-qv-button',function(){
	$('.xoo-qv-opac').show();
	var xoo_qv_panel = $('.xoo-qv-panel');
	xoo_qv_panel.addClass('xoo-qv-panel-active');
	xoo_qv_panel.find('.xoo-qv-opl').addClass('xoo-qv-pl-active');
	var p_id	  = $(this).attr('qv-id');
	var ajax_data = find_nav_ids(p_id);
	xoo_qv_animation_func(ajax_data,'top');
})

var qv_length = $('.xoo-qv-button').length;

function find_nav_ids(p_id){
	var curr_index = $("[qv-id="+p_id+"]").index('.xoo-qv-button');
	var curr_length = curr_index + 1;
	var next_index,prev_index;
	var qv_btn = $('.xoo-qv-button');
	//Find next button
	if(curr_length == qv_length){
		next_index = 0;
		 
	}
	else{
		next_index = curr_index + 1;
	}

	//Find prev button
	if(curr_length == 1){
		prev_index = qv_length - 1;
	}
	else{
		prev_index = curr_index - 1;
	}

	var qv_next = qv_btn.eq(next_index).attr('qv-id');
	var qv_prev = qv_btn.eq(prev_index).attr('qv-id');
	return {'product_id': p_id , 'qv_next': qv_next , 'qv_prev': qv_prev};

}

// Next Product
$('.xoo-qv-panel').on('click','.xoo-qv-nxt',function(){
	$('.xoo-qv-mpl').addClass('xoo-qv-pl-active');
	var next_id = $(this).attr('qv-nxt-id');
	var ajax_data = find_nav_ids(next_id);
	xoo_qv_animation_func(ajax_data,'next');
})

//Previous Product
$('.xoo-qv-panel').on('click','.xoo-qv-prev',function(){
	$('.xoo-qv-mpl').addClass('xoo-qv-pl-active');
	var prev_id = $(this).attr('qv-prev-id');
	var ajax_data = find_nav_ids(prev_id);
	xoo_qv_animation_func(ajax_data,'prev');
})

})
