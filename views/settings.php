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
                    <li tab_id="all" class="active">All <span class="cggff_count">(2)</span></li>
                    <li tab_id="active">Active <span class="cggff_count">(1)</span></li>
                    <li tab_id="inactive">Inactive <span class="cggff_count">(1)</span></li>
                </ul>
            </div>
            <div class="cggff_features_grid">
                <div class="cggff_feature_card active">
                    <div class="cggff_feature_card__head">
                        <div class="cggff_fc_hd_start">
                            <span class="cggff_feat_icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 21 21"><path d="m6.5 7.5h4l-6 9v-6.997l-4-.003 6-9z" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" transform="translate(5 2)"/></svg>
                            </span>
                            <h4>Dynamic Populate</h4>
                        </div>
                        <div class="cggff_toggle_action">
                            <label for="cggff_toggle_feature_1">
                                <input type="checkbox" name="" id="cggff_toggle_feature_1" class="cggff_toggle_feature" checked>
                            </label>
                        </div>
                    </div>
                    <div class="cggff_feature_card__body">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa iste, maiores voluptates exercitationem odit adipisci molestiae dolore veritatis sit nisi? Vero harum</p>
                        <button type="button" class="cggff_view_feature_details">View Details</button>
                    </div>
                </div>
                <div class="cggff_feature_card">
                    <div class="cggff_feature_card__head">
                        <div class="cggff_fc_hd_start">
                            <span class="cggff_feat_icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 21 21"><g fill="none" fill-rule="evenodd" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" transform="translate(4 3)"><path d="m3.5 1.5c-.42139382 0-1.08806048 0-2 0-.55228475 0-1 .44771525-1 1v11c0 .5522848.44771525 1 1 1h10c.5522847 0 1-.4477152 1-1v-11c0-.55228475-.4477153-1-1-1-.8888889 0-1.55555556 0-2 0"/><path d="m4.5.5h4c.55228475 0 1 .44771525 1 1s-.44771525 1-1 1h-4c-.55228475 0-1-.44771525-1-1s.44771525-1 1-1z"/><path d="m5.5 5.5h5"/><path d="m5.5 8.5h5"/><path d="m5.5 11.5h5"/><path d="m2.5 5.5h1"/><path d="m2.5 8.5h1"/><path d="m2.5 11.5h1"/></g></svg>
                            </span>
                            <h4>Inventory</h4>
                        </div>
                        <div class="cggff_toggle_action">
                            <label for="cggff_toggle_feature_2">
                                <input type="checkbox" name="" id="cggff_toggle_feature_2" class="cggff_toggle_feature">
                            </label>
                        </div>
                    </div>
                    <div class="cggff_feature_card__body">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa iste, maiores voluptates exercitationem odit adipisci molestiae dolore veritatis sit nisi? Vero harum</p>
                        <button type="button" class="cggff_view_feature_details">View Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>