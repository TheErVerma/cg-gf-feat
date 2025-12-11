<?php

if (!defined('ABSPATH')) {
    exit();
}


?>
<header class="gform-settings-header ">
    <div class="gform-settings__wrapper">
        <img src="<?php echo home_url(); ?>/wp-content/plugins/gravityforms/images/logos/gravity-logo-dark.svg" alt="Gravity Forms" width="220">
    </div>
</header>
<div class="cggff_settings_wrap">
    <div class="cggff_container">
        <div class="cggff_features_wrap">
            <div class="cggff_features_tabs">
                <ul>
                    <li tab_id="all" class="active"><span class="cggff_count">4 </span>All</li>
                    <li tab_id="active"><span class="cggff_count">2 </span>Active</li>
                    <li tab_id="inactive"><span class="cggff_count">2 </span>Inactive</li>
                </ul>
            </div>
            <div class="cggff_features_grid" data_tab_id="all">
                <div class="cggff_feature_card">
                    <div class="cggff_feature_card__head">
                        <span class="cggff_feat_icon"></span>
                        <h4>Dynamic Populate</h4>
                        <div class="cggff_toggle_action">
                            <label for="cggff_toggle_feature_1">
                                <input type="checkbox" name="" id="cggff_toggle_feature_1" class="cggff_toggle_feature">
                            </label>
                        </div>
                    </div>
                    <div class="cggff_feature_card__body">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa iste, maiores voluptates exercitationem odit adipisci molestiae dolore veritatis sit nisi? Vero harum</p>
                        <button type="button" class="cggff_view_feature_details">View Details</button>
                    </div>
                </div>
            </div>
            <div class="cggff_features_grid" data_tab_id="active">
                <div class="cggff_feature_card">

                </div>
            </div>
            <div class="cggff_features_grid" data_tab_id="inactive">
                <div class="cggff_feature_card">

                </div>
            </div>
        </div>
    </div>
</div>