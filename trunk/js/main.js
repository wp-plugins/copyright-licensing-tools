function show_manual_option() {
  jQuery('#M3').show();
}

function hide_manual_option() {
  jQuery('#M3').hide();
}


// Function to update the previews with what the toolbars will look like with these settings
function toolbarTouch() {
  if (jQuery('#pub_id').html() == '') return;
  var theme = jQuery('#icopyright_article_tools_theme').val();
  var background = jQuery('input:radio[name=icopyright_background]:checked').val();
  var publication = jQuery('#pub_id').html();
  var url_h = jQuery('#icopyright_server').html() + '/rights/TouchToolbar.act?' +
    jQuery.param({
      theme: theme,
      background: background,
      orientation: 'horz',
      publication: publication});
  jQuery('#horizontal-article-tools-preview').attr('src', url_h);
  var url_v = jQuery('#icopyright_server').html() + '/rights/TouchToolbar.act?' +
    jQuery.param({
      theme: theme,
      background: background,
      orientation: 'vert',
      publication: publication});
  jQuery('#vertical-article-tools-preview').attr('src', url_v);
  var url_o = jQuery('#icopyright_server').html() + '/rights/TouchToolbar.act?' +
    jQuery.param({
      theme: theme,
      background: background,
      orientation: 'one-button',
      publication: publication});
  jQuery('#onebutton-article-tools-preview').attr('src', url_o);
  var noticeUrl = jQuery('#icopyright_server').html() + '/rights/copyright-preview.jsp?' +
    jQuery.param({
      themeName: theme,
      background: background,
      publicationId: publication,
      publicationName: jQuery('#site_name').html()
    });
  jQuery('#copyright-notice-preview').attr('src', noticeUrl);
}

function categoryListDisplay() {

  if (jQuery("input.category-radio:checked").val() == "yes") {
    jQuery("#icopyright-category-list").parents("tr").show();
  } else {
    jQuery("#icopyright-category-list").parents("tr").hide();
  }
}

jQuery(document).ready(function () {
  jQuery('.fadeout').delay(5000).fadeOut('slow');
  jQuery("h2#wait").hide();
  jQuery("div#noneedtohide").show();
  jQuery("#toggle_advance_setting").toggle(function () {
      jQuery(this).next().show();
      jQuery("#toggle_advance_setting").val("Hide Advanced Settings");
    },
    function () {
      jQuery(this).next().hide();
      jQuery("#toggle_advance_setting").val("Show Advanced Settings")
    }
  );
  jQuery("#toggle_advance_setting").next().hide();

  jQuery("#toggle_account_setting").toggle(function () {
      jQuery(this).next().show();
      jQuery("#toggle_account_setting").val("Hide Address");
    },
    function () {
      jQuery(this).next().hide();
      jQuery("#toggle_account_setting").val("Show Address")
    }
  );
  jQuery("#toggle_account_setting").next().hide();

  categoryListDisplay();
  jQuery("input.category-radio").change(function () {
    categoryListDisplay();
  });

  toolbarTouch();
  jQuery('#icopyright_article_tools_theme').change(function () {
    toolbarTouch();
  });
  jQuery('input:radio[name=icopyright_background]').change(function () {
    toolbarTouch();
  });

  jQuery("input[name='icopyright_pub_id']").keyup(function () {
    if (jQuery(this).val() != "") {
      jQuery("#no_pub_id_message").hide();
    } else {
      jQuery("#no_pub_id_message").show();
    }
  })

  if (jQuery("input[name='icopyright_pub_id']").val() != "") {
    jQuery("#no_pub_id_message").hide();
  }

  jQuery(".icx_republish_advanced_btn").click(function (event) {
    if (jQuery(".icx_republish_advanced_fields").is(":visible")) {
      jQuery(".icx_republish_advanced_fields").hide();
      jQuery(".icx_republish_advanced_btn").html("Advanced Search");
      jQuery("#icx_and_words_label").html("");
      jQuery("#icx_and_words_label").hide();
    } else {
      jQuery(".icx_republish_advanced_fields").show();
      jQuery(".icx_republish_advanced_btn").html("Basic Search");
      jQuery("#icx_and_words_label").html("With all the words:");
      jQuery("#icx_and_words_label").show();
    }
    event.preventDefault();
  });

  jQuery(".icx_nav_tab").click(function (event) {
    jQuery(".icx_topic").hide();
    jQuery("#icx_topic_" + jQuery(this).attr("href")).show();

    jQuery(".icx_nav_tab").removeClass("nav-tab-active");
    jQuery("#icx_nav_tab_" + jQuery(this).attr("href")).addClass("nav-tab-active");
    event.preventDefault();
  });

  jQuery("div.icx_repubhub_clips").each(function () {
    topic = jQuery(this).attr("id").replace(/\D/g,'');
    div = "div#icx_clips_for_topic_" + topic;
    var onSuccess = (function(divinclosure) {
      return function(data){
        jQuery(divinclosure).html(data);
      };
    })(div);
    jQuery.ajax({
      url : "/wp-admin/admin-ajax.php",
      type : "get",
      data : {action: "repubhub_clips", loc: jQuery(this).data("loc")},
      success: onSuccess
    });
  });
});
