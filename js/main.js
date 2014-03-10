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

    icopyright_update_unread_notification(jQuery(this).attr("href"),
        jQuery("#icx_topic_"+jQuery(this).attr("href")+"_first_clip_id").html());
    event.preventDefault();
  });

  jQuery("div.icx_repubhub_clips").each(function () {
    topic = jQuery(this).attr("id").replace(/\D/g,'');
    div = "div#icx_clips_for_topic_" + topic;
    var onSuccess = (function(divinclosure, topicinclosure) {
      return function(data){
        jQuery(divinclosure).html(data);
        if (jQuery("#icx_nav_tab_"+topicinclosure).hasClass('nav-tab-active'))
            icopyright_update_unread_notification(topicinclosure, jQuery("#icx_topic_"+topicinclosure+"_first_clip_id").html());
      };
    })(div, topic);
    if (topic == 0) {
        jQuery.ajax({
            url : "/wp-admin/admin-ajax.php",
            type : "get",
            data : {action: "repubhub_recent_headlines"},
            success: onSuccess
        });
    } else {
        jQuery.ajax({
          url : "/wp-admin/admin-ajax.php",
          type : "get",
          data : {action: "repubhub_clips", loc: jQuery(this).data("loc"), topicid: jQuery(this).data("topicid")},
          success: onSuccess
        });
    }
  });



  jQuery('a#icopyright_wp_settings_video').colorbox({ href: 'http://www.youtube.com/embed/bpYG-Frhh9E?autoplay=1&vq=hd720"', width: '800px', height: '600px', iframe: true });
});


function icopyright_update_unread_notification(topicId, contentId) {

    var onSuccess = function(data) {
        // Update unread notifications
        icopyright_update_unread_notification_message(jQuery("#icx_nav_tab_"+topicId), 0, true);
        icopyright_update_unread_notification_message(jQuery("#wp-admin-bar-republish-1 a"), data, false);
        icopyright_update_unread_notification_message(jQuery("#wp-admin-bar-republish-2 a"), data, false);
        icopyright_update_unread_notification_message(jQuery("li.current a.current[href='edit.php?page=repubhub-republish']"), data, true);
    }

    jQuery.ajax({
        url : "/wp-admin/admin-ajax.php",
        type : "get",
        data : {action: "repubhub_clips_read", topicid: topicId, contentid: contentId},
        success: onSuccess
    });
}

function icopyright_update_unread_notification_message(item, value, circle) {
    var title = item.html();

    var newValue = "&nbsp;"+value;
    var lastIndexVal = "&nbsp;"
    if (circle) {
        newValue = '<span class="icx_unread update-plugins count-1"><span class="plugin-count">'+value+'</span></span>';
        lastIndexVal = '<span class="icx_unread';
    }
    if (title.lastIndexOf(lastIndexVal)>0) {
        if (value > 0)
            item.html(title.substr(0, title.lastIndexOf(lastIndexVal)) + newValue);
        else
            item.html(title.substr(0, title.lastIndexOf(lastIndexVal)));
    } else {
        if (value > 0)
            item.html(title + newValue);
    }
}
