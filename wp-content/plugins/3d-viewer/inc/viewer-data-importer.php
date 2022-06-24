<?php

add_action('init', function(){
   

    $imported = get_option('bp3d_imported', 0);
    
    if($imported < BP3D_IMPORT_VER){
        bp3d_import();
        update_option('bp3d_imported', BP3D_IMPORT_VER);
    }

});

function bp3d_import(){
    $posts = new WP_Query([
        'post_type' => 'bp3d-model-viewer',
        'posts_per_page' => -1
    ]);
    while($posts->have_posts()): $posts->the_post();
    
    $viewers = get_post_meta(get_the_ID(), '_bp3dimages_', true);
    if(!is_array($viewers)){
        $viewers = [];
    }
    $models = $viewers['bp_3d_models'] ?? false;
    
    $viewers['bp_3d_models'] = [];
    
    if(is_array($models)){
        foreach($models as $item){
            if(is_array($item) && isset($item['model_src']) && isset($item['model_src']['url']) && $item['model_src']['url'] != ''){
                $viewers['bp_3d_models'][]['model_link'] = $item['model_src']['url']; 
            }else {
                $viewers['bp_3d_models'][]['model_link'] = $item['model_link'];
            }
        }
    }
    update_post_meta(get_the_ID(), '_bp3dimages_', $viewers);
    endwhile;
}

