jQuery(document).on('click', '.cggff_settings_wrap .cggff_container .cggff_features_tabs li', function () {
    const this_btn = jQuery(this);
    const this_id = this_btn.attr('tab_id');
    jQuery('.cggff_settings_wrap .cggff_container .cggff_features_tabs li').removeClass('active');
    this_btn.addClass('active');

    if(this_id == 'all'){
        jQuery('.cggff_settings_wrap .cggff_container .cggff_features_grid .cggff_feature_card').show();
    }else if(this_id == 'active'){
        jQuery('.cggff_settings_wrap .cggff_container .cggff_features_grid .cggff_feature_card').hide();
        jQuery('.cggff_settings_wrap .cggff_container .cggff_features_grid .cggff_feature_card.active').show();
    }else if(this_id == 'inactive'){
        jQuery('.cggff_settings_wrap .cggff_container .cggff_features_grid .cggff_feature_card').hide();
        jQuery('.cggff_settings_wrap .cggff_container .cggff_features_grid .cggff_feature_card:not(.active)').show();
    }
});

function cggff_refine_feature_tabs(){    
    let all_cnt = 0;
    let atv_cnt = 0;
    let intv_cnt = 0;
    
    jQuery('.cggff_settings_wrap .cggff_container .cggff_features_grid .cggff_feature_card input.cggff_toggle_feature').each(function(ind, inp){
        const this_inp = jQuery(inp);
        const all_cnt_elm = jQuery('.cggff_settings_wrap .cggff_container .cggff_features_tabs li[tab_id="all"]');
        const atv_cnt_elm = jQuery('.cggff_settings_wrap .cggff_container .cggff_features_tabs li[tab_id="active"]');
        const intv_cnt_elm = jQuery('.cggff_settings_wrap .cggff_container .cggff_features_tabs li[tab_id="inactive"]');


        if(this_inp.is(':checked')){
            all_cnt++;
            atv_cnt++;
        }else{
            all_cnt++;
            intv_cnt++;
        }

        all_cnt_elm.find('.cggff_count').text(`(${all_cnt})`);
        atv_cnt_elm.find('.cggff_count').text(`(${atv_cnt})`);
        intv_cnt_elm.find('.cggff_count').text(`(${intv_cnt})`);

    });
}

jQuery(document).ready(function(){
    cggff_refine_feature_tabs();
});

jQuery(document).on('change', '.cggff_settings_wrap .cggff_container .cggff_features_grid .cggff_feature_card input.cggff_toggle_feature', function(){
    const this_inp = jQuery(this);
    const this_wrap = this_inp.closest('.cggff_feature_card');
    
    if(this_inp.is(':checked')){
        this_wrap.addClass('active');
    }else{
        this_wrap.removeClass('active');
    }
    cggff_refine_feature_tabs();
})