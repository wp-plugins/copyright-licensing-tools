function icxInitGlobalSettings() {
    jQuery("#icx_global_settings_wrap").click(function (event) {    	
    	  var url = '';
    	  if (jQuery("#icx_global_settings_wrap_inner").is(":visible")) {
      		url = icx_plugin_url.url + '/images/16_arrow_down.ico';
    	  } else {
    		url = icx_plugin_url.url + '/images/16_arrow_up.ico';
    	  }
    	  jQuery("#icx_global_settings_wrap").css("background-image", "url(" + url + ")");
    	  jQuery("#icx_global_settings_wrap_inner").fadeToggle(400);
    	  //jQuery("#icx_global_settings_wrap_inner").slideToggle(300);
      });
      
      jQuery("#icx_global_settings_form").submit(function (event) {
      	  jQuery("#icx_global_settings_overlay").show();
  		  var onFail = function(result, textStatus, errorThrown) {jQuery("#icx_global_settings_overlay").hide();};
  		  var formData = jQuery(this).serialize();
  	      jQuery.ajax({
  	          url : admin_ajax_url.url,
  	          type : "post",
  	          data : {action: "repubhub_update_global_settings", formValues: formData},
  	          error: onFail,
  	          success:function(result){
  	        	  jQuery("#icx_global_settings_overlay").hide();
  	        	  jQuery("#icx_global_settings_message").html(result);
  	        	  icxInitFadeOut();
  	          }
  	      });
  	      event.preventDefault(); 
      });
      
      
      jQuery(document).mouseup(function (e) {
    	  if (jQuery("#icx_global_settings_wrap_inner").is(":visible")) {
  		      var container = jQuery("#icx_global_settings_wrap_inner");
  		      var btn = jQuery("#icx_global_settings_wrap");
  		      
  		      if (!container.is(e.target) && !btn.is(e.target) // if the target of the click isn't the container...
  		          && container.has(e.target).length === 0) // ... nor a descendant of the container
  		      {
  		          btn.trigger("click");
  		      }
    	  }
  	  });
      
      jQuery("h4.icx_global_setting_section_title").click(function (event) {
    	  var obj = jQuery(this);
    	  obj.next("div.icx_global_setting_section_data").slideToggle(200, function(){
    		  var url = '';
        	  if(jQuery(this).is(":visible")) {
        		  obj.css("border-bottom-left-radius", "0");
        		  obj.css("border-bottom-right-radius", "0");
        		  obj.css("background-color", "#fff");
        		  url = icx_plugin_url.url + '/images/bullet_down.ico';
        	  } else {
        		  obj.css("border-bottom-left-radius", "4px");
        		  obj.css("border-bottom-right-radius", "4px");
        		  obj.css("border-bottom", "1px solid #d3d3d3");
        		  obj.css("background-color", "#e6e6e6");
        		  url = icx_plugin_url.url + '/images/bullet_right.ico';
        	  }  
        	  
        	obj.css("background-image", "url(" + url + ")");
    	  });

      });	
}

function icxInitSearchSection() {
	jQuery("#featuredPublicationSelect").select2({
		placeholderOption: "first",
		width: "resolve"
	});

	jQuery("#featuredPublicationSelectEdit").select2({
		placeholderOption: "first",
		width: "resolve"
	});	
	
	jQuery("#featuredPublicationSelect").on("select2-opening", function(event) {
		jQuery("ul.select2-choices").addClass("icx_bottom_border_none");
	});

	jQuery("#featuredPublicationSelect").on("select2-close", function(event) {
		jQuery("ul.select2-choices").removeClass("icx_bottom_border_none");
	});	
	
	jQuery(".icx_republish_advanced_btn").click(function (event) {
	    if (jQuery(".icx_republish_advanced_fields_front_page").is(":visible")) {
	      jQuery(".icx_republish_advanced_fields_front_page").hide();
	      jQuery(".icx_republish_advanced_btn").html("More Options");
	      // clear the form
	      jQuery("#icx_search_form div.icx_republish_advanced_fields_front_page input[type='text']").val('');
	    } else {
	      jQuery(".icx_republish_advanced_fields_front_page").show();
	      jQuery(".icx_republish_advanced_btn").html("Less Options");
	    }
	    event.preventDefault();
	  });	
	
	
	jQuery("#icx_search_form select[name='featuredPublicationString'], #icx_search_form select[name='dateFilter']").change(function(event) {
		jQuery("#icx_search_form input[name='page']").val('1');
		jQuery("#icx_search_form").trigger("submit");
	});
	
	jQuery("#icx_search_form input.icx_add_btn").on("click", function (event) {
		jQuery("#icx_search_form input[name='page']").val('1');	
	});
	
	jQuery("#icx_search_form").on("submit", function (event){
		  jQuery("#icx_topic_search_results_inner").html("");
		  jQuery("#icx_nav_tab_search_results").html("Search Results");
		  jQuery("#icx_nav_tab_search_results").attr("style", "display: inline-block;");
		  jQuery("#icx_nav_tab_search_results").trigger("click");
		  jQuery("#icx_search_results_spinner").show();
		  
		  // Check if andWords is a quoted string.  If so, move it to Exact Phrase
		  var andWords = jQuery("#icx_and_words").val();
		  if (andWords && andWords.length > 0) {
			  var firstChar = andWords.charAt(0);
			  if (firstChar == '"' || firstChar == '&quot;') {
				  var lastChar = andWords.charAt(andWords.length -1);
				  if (lastChar == '"' || lastChar == '&quot;') {
					  jQuery("#icx_exact_words").val(andWords.substring(1, andWords.length - 1));
					  jQuery("#icx_and_words").val('');
					  jQuery(".icx_republish_advanced_fields_front_page").show();
				      jQuery(".icx_republish_advanced_btn").html("Less Options");					  
				  }
			  }
		  }
		  
		  var onFail = function(result, textStatus, errorThrown) {};
		  var formData = jQuery(this).serialize();
	      jQuery.ajax({
	          url : admin_ajax_url.url,
	          type : "post",
	          data : {action: "repubhub_search", formValues: formData},
	          error: onFail,
	          success:function(result){
	        	     jQuery("#icx_search_results_spinner").hide();
	        	     jQuery("#icx_topic_search_results_inner").html(result);
	        	  }
	      });
	      
	      event.preventDefault();
	  });	
}

function icxInitTabsSection() {
  jQuery(".icx_nav_tab").click(function (event) {
	    jQuery(".icx_topic").hide();
	    jQuery("#icx_topic_" + jQuery(this).attr("href")).show();

	    jQuery(".icx_nav_tab").removeClass("nav-tab-active");
	    jQuery("#icx_nav_tab_" + jQuery(this).attr("href")).addClass("nav-tab-active");
	    
	    jQuery(".icx_nav_tab").removeClass("icx-nav-tab-active");
	    jQuery("#icx_nav_tab_" + jQuery(this).attr("href")).addClass("icx-nav-tab-active");

	    icopyright_update_unread_notification(jQuery(this).attr("href"),
	        jQuery("#icx_topic_"+jQuery(this).attr("href")+"_first_clip_id").html());
	    event.preventDefault();
	  });
	  
	  jQuery(".icx_topic_delete_btn").on("click", function (event) {
		  event.stopPropagation();
		  event.preventDefault();	
		  var onFail = function(result, textStatus, errorThrown) {};
		  var topicId = jQuery(this).data("topicid");
		  
      	jQuery("a#icx_nav_tab_"+topicId).animate({padding:0, margin:0, width: 0, height: 0}, 200, function () {  
            if (jQuery(this).hasClass("nav-tab-active")) {
              jQuery(this).removeClass("nav-tab-active");
              jQuery(this).removeClass("icx-nav-tab-active");
              var ele;
              if(jQuery(this).next("a.icx_nav_tab").length) {
                ele = jQuery(this).next("a.icx_nav_tab");
              }
              else {
            	ele = jQuery(this).prev("a.icx_nav_tab");
              }
              
              if (ele) {
                jQuery(ele).addClass("nav-tab-active");
                jQuery(ele).addClass("icx-nav-tab-active");
                jQuery(ele).trigger("click");
              }
            }
            
            jQuery(this).remove(); 
            jQuery("div#icx_topic_"+topicId).remove();
        });		  
		  
		      jQuery.ajax({
		          url : admin_ajax_url.url,
		          type : "post",
		          data : {action: "repubhub_delete_topic", topicId: topicId},
		          error: onFail,
		          success:function(result){
		            var text = '';	  
		            if (result == 'error') {
		              text = '<div class="icx_error fadeout"><p>Unable to delete topic at this time.  Please try again later.</p></div>';
		            } else if (result == 'success') {
		            	text = '<div class="icx_success fadeout"><p>Topic has been deleted.</p></div>';
		            }
		            
		            jQuery("div.icx_wrap").prepend(text);
		            icxInitFadeOut();
		          }
		      });
	      event.preventDefault();		  
	  });	

	  jQuery("div.icx_repubhub_clips").each(function () {
	    topic = jQuery(this).attr("id").replace(/\D/g,'');
	    div = "div#icx_clips_for_topic_" + topic;
	    var onSuccess = (function(divinclosure, topicinclosure) {
	      return function(data){
	        jQuery(divinclosure).html(data);
	        if (divinclosure == "div#icx_clips_for_topic_0")
	        	jQuery("#rh-spinner").hide();
	        if (jQuery("#icx_nav_tab_"+topicinclosure).hasClass('nav-tab-active'))
	            icopyright_update_unread_notification(topicinclosure, jQuery("#icx_topic_"+topicinclosure+"_first_clip_id").html());
	      };
	    })(div, topic);
	    if (topic == 0) {
	        jQuery.ajax({
	            url : admin_ajax_url.url,
	            type : "get",
	            data : {action: "repubhub_recent_headlines"},
	            success: onSuccess
	        });
	    } else {
	        jQuery.ajax({
	          url : admin_ajax_url.url,
	          type : "get",
	          data : {action: "repubhub_clips", loc: jQuery(this).data("loc"), topicid: jQuery(this).data("topicid")},
	          success: onSuccess
	        });
	    }
	  });
	  
	  jQuery(document).one("submit", "#icx_form_save_search", function(event) {
		  jQuery("input[value='Save Search']").hide();
		  jQuery("#icx_search_results_spinner").show();
		  
		  var dateFilterFromSearch = jQuery("#icx_search_form select[name='dateFilter']").val();
		  jQuery("#icx_form_save_search input[name='dateFilter']").val(dateFilterFromSearch);

		  event.preventDefault();
		  var onFail = function(result, textStatus, errorThrown) {};
		  var formData = jQuery(this).serialize();
	      jQuery.ajax({
	          url : admin_ajax_url.url,
	          type : "post",
	          data : {action: "repubhub_add_topic", formValues: formData},
	          error: onFail,
	          success:function(result){
	        	     jQuery("#icx_search_results_spinner").hide();      	     
	        	     jQuery(".icx_wrap").replaceWith(result);
	        	     icxInitTabsSection();   
	        	     icxInitFadeOut();
	        	  }
	      });
	      event.preventDefault();
	  });  	  
}

function icxInitDocumentBindings() {
	  
	  jQuery(document).on("click", ".icx_pager", function(event) {
		  var page = parseInt(jQuery("#icx_search_form input[name='page']").val());
		  if (jQuery(this).attr("href") == "prev")
			  page = page -1;
		  else
			  page = page +1;
		  
		  jQuery("#icx_search_form input[name='page']").val('' + page);
		  jQuery("#icx_search_form").trigger("submit");
	      event.preventDefault();
	  });
	  
	  
	  jQuery(document).on("click", ".icx_pager_rh", function(event) {
		  jQuery("#rh-spinner").show();	  		
		  location.href = "#rh";
		  var page = jQuery(this).attr("href");
	        jQuery.ajax({
	            url : admin_ajax_url.url,
	            type : "get",
	            data : {action: "repubhub_recent_headlines", rhPage: page},
	            success: function(result) {
	            	jQuery("#rh-spinner").hide();
	            	jQuery("div#icx_clips_for_topic_0").html(result);
	            }
	        });
		  
	      event.preventDefault();
	  });	  
	  
	  jQuery(document).on("click", ".icx_clip_author, .icx_clip_publicationId, .icx_clip_publicationName", function(event) {
		  jQuery("#icx_search_form").trigger("reset");
		  
		  var href = jQuery(this).attr("href");
		  var styleClass = jQuery(this).attr("class");
		  if (styleClass == "icx_clip_author")
		    jQuery("#icx_search_form input[name='author']").val(href);
		  else if (styleClass == "icx_clip_publicationId")
			jQuery("#icx_search_form select[name='featuredPublicationString']").val("p-" + href);
		  else if (styleClass == "icx_clip_publicationName")
			jQuery("#icx_search_form input[name='publicationName']").val(href);
		  
		  jQuery("#icx_search_form input[name='page']").val('1');
		  jQuery("#icx_search_form").trigger("submit");
		  jQuery(".icx_republish_advanced_fields_front_page").show();
	      jQuery(".icx_republish_advanced_btn").html("Less Options");		  
	      event.preventDefault();
	  });  
	  

    jQuery(document).on("click", "#icx_dismiss_save_hint_info_box",function (event) {
        jQuery("#icx_save_hint_info_box").hide();
        jQuery.ajax({
          url : admin_ajax_url.url,
          type : "get",
          data : {action: "repubhub_dismiss_save_search_info_box"},
          success: function() {}
        });
        event.preventDefault();
      });		
}

function icxInit() {
	  jQuery("h2#wait").hide();
	  jQuery("div#noneedtohide").show();
	  jQuery("#toggle_advance_setting").toggle(function () {
	      jQuery(this).next().show();
	      jQuery("#toggle_advance_setting").val("Hide Advanced Settings");
	    },
	    function () {
	      jQuery(this).next().hide();
	      jQuery("#toggle_advance_setting").val("Show Advanced Settings");
	    }
	  );
	  jQuery("#toggle_advance_setting").next().hide();

	  jQuery("#toggle_account_setting").toggle(function () {
	      jQuery(this).next().show();
	      jQuery("#toggle_account_setting").val("Hide Address");
	    },
	    function () {
	      jQuery(this).next().hide();
	      jQuery("#toggle_account_setting").val("Show Address");
	    }
	  );
	  jQuery("#toggle_account_setting").next().hide();

	  categoryListDisplay();
	  jQuery("input.category-radio").change(function () {
	    categoryListDisplay();
	  });
	  
	  authorListDisplay();
	  jQuery("input.author-radio").change(function () {
	    authorListDisplay();
	  });  

		jQuery("div.empty_callback").parents("tr").remove();

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
	  });

	  if (jQuery("input[name='icopyright_pub_id']").val() != "") {
	    jQuery("#no_pub_id_message").hide();
	  }
	  
	  jQuery('a#icopyright_wp_settings_video').colorbox({ href: 'http://www.youtube.com/embed/bpYG-Frhh9E?autoplay=1&vq=hd720"', width: '800px', height: '600px', iframe: true });
}

function icxInitFadeOut() {
  jQuery('.fadeout').delay(5000).fadeOut('slow');
}

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
	    jQuery("#icopyright-category-list").show();
	  } else {
	    jQuery("#icopyright-category-list").hide();
	  }
	}

	function authorListDisplay() {

	  if (jQuery("input.author-radio:checked").val() == "yes") {
	    jQuery("#icopyright-author-list").show();
	  } else {
	    jQuery("#icopyright-author-list").hide();
	  }
	}

function icopyright_update_unread_notification(topicId, contentId) {
	if (topicId == 'search_results') {
		return;
	}
	
    var onSuccess = function(data) {
        // Update unread notifications
        icopyright_update_unread_notification_message(jQuery("#icx_nav_tab_"+topicId+ " span.icx_unread_tab_count"), 0, true);
        icopyright_update_unread_notification_message(jQuery("#wp-admin-bar-republish-1 a"), data, false);
        icopyright_update_unread_notification_message(jQuery("#wp-admin-bar-republish-2 a"), data, false);
        icopyright_update_unread_notification_message(jQuery("li.current a.current[href='edit.php?page=repubhub-republish']"), data, true);
    };

    jQuery.ajax({
        url : admin_ajax_url.url,
        type : "get",
        data : {action: "repubhub_clips_read", topicid: topicId, contentid: contentId},
        success: onSuccess
    });
}

function icopyright_update_unread_notification_message(item, value, circle) {
    var title = item.html();
    var newValue = "&nbsp;"+value;
    var lastIndexVal = "&nbsp;";
    if (circle) {
        newValue = '<span class="icx_unread update-plugins count-1"><span class="plugin-count">'+value+'</span></span>';
        lastIndexVal = '<span class="plugin-count';
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


jQuery(document).ready(function () {
    icxInitGlobalSettings();
    icxInitSearchSection();
    icxInitTabsSection();
    icxInitDocumentBindings();
    icxInitFadeOut();
    icxInit();
 });