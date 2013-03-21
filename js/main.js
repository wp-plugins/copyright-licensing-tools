function show_icopyright_form() {
    document.getElementById('icopyright_registration_form').style.display='block';
    document.getElementById('icopyright_option').style.display='none';
    document.getElementById('fname').focus();
}

function hide_icopyright_form() {
    document.getElementById('icopyright_registration_form').style.display='none';
    document.getElementById('icopyright_option').style.display='block';
}

function show_manual_option() {
    jQuery('#M3').show();
}

function hide_manual_option() {
    jQuery('#M3').hide();
}



// Function to update the previews with what the toolbars will look like with these settings
function toolbarTouch() {
    if(jQuery('#pub_id').html() == '') return;
    var theme = jQuery('#icopyright_article_tools_theme').val();
    var background = jQuery('input:radio[name=background]:checked').val();
    var publication = jQuery('#pub_id').html();
    var url_h = jQuery('#icopyright_server').html()+'/publisher/TouchToolbar.act?' +
        jQuery.param({
            theme: theme,
            background: background,
            orientation: 'horz',
            publication: publication});
    jQuery('#horizontal-article-tools-preview').attr('src', url_h);
    var url_v = jQuery('#icopyright_server').html()+'/publisher/TouchToolbar.act?' +
        jQuery.param({
            theme: theme,
            background: background,
            orientation: 'vert',
            publication: publication});
    jQuery('#vertical-article-tools-preview').attr('src', url_v);
    var url_o = jQuery('#icopyright_server').html()+'/publisher/TouchToolbar.act?' +
        jQuery.param({
            theme: theme,
            background: background,
            orientation: 'one-button',
            publication: publication});
    jQuery('#onebutton-article-tools-preview').attr('src', url_o);
    var noticeUrl = jQuery('#icopyright_server').html()+'/publisher/copyright-preview.jsp?' +
        jQuery.param({
            themeName: theme,
            background: background,
            publicationId: publication,
            publicationName: jQuery('#site_name').html()
        });
    jQuery('#copyright-notice-preview').attr('src', noticeUrl);
}

function categoryListDisplay() {

    if(jQuery("input.category-radio:checked").val() == "yes") {
        jQuery("#icopyright-category-list").parents("tr").show();
    } else {
        jQuery("#icopyright-category-list").parents("tr").hide();
    }
}

jQuery(document).ready(function() {
    jQuery("h2#wait").hide();
    jQuery("div#noneedtohide").show();
    jQuery("#toggle_advance_setting").toggle(function(){
            jQuery(this).next().show();
            jQuery("#toggle_advance_setting").val("Hide Advanced Settings");
        },
        function() {
            jQuery(this).next().hide();
            jQuery("#toggle_advance_setting").val("Show Advanced Settings")
        }
    );
    jQuery("#toggle_advance_setting").next().hide();

    categoryListDisplay();
    jQuery("input.category-radio").change(function() {
        categoryListDisplay();
    });

    toolbarTouch();
    jQuery('#icopyright_article_tools_theme').change(function () {
        toolbarTouch();
    });
    jQuery('input:radio[name=icopyright_background]').change(function () {
        toolbarTouch();
    });
});