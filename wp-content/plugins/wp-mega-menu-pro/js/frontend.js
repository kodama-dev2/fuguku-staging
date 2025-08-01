/**
 * WP Mega Menu Pro Version 2.1.1 jQuery Plugin
 */
 jQuery(function ($) {
  if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
   var is_mobile = true;
 }else{
   var is_mobile = false;
 }
  var mobile_toggle_option = wp_megamenu_params.wpmm_mobile_toggle_option; //toggle_standard or toggle_accordion
  var event_behavior = wp_megamenu_params.wpmm_event_behavior;
  var ajaxurl = wp_megamenu_params.wpmm_ajaxurl;
  var ajax_nonce = wp_megamenu_params.wpmm_ajax_nonce;
  var check_woocommerce_enabled = wp_megamenu_params.check_woocommerce_enabled;
  var enable_mobile = wp_megamenu_params.enable_mobile;
  var enable_rtl = wp_megamenu_params.wpmm_enable_rtl;

  $('body').addClass('wpmm_megamenu');

  if(enable_rtl == 1){
    $('body').addClass('wpmm_enable_rtl');
  }

    /*
    * Search box Integration
    */
    var submitIcon = $('.wpmm-search-inline');
    var inputBox = $('.wpmm-search-icon .search-field');
    var isOpen = false;
    submitIcon.click(function(e){
      e.preventDefault();
      if($(this).next().find('.wpmm-search-icon').hasClass('inline-search')){
        if(isOpen == false){
         $(this).next().find('.inline-search').addClass('searchbox-open').removeClass('searchbox-closed');
         inputBox.focus();
         isOpen = true;
       } else {
         $(this).next().find('.inline-search').removeClass('searchbox-open').addClass('searchbox-closed');
         inputBox.focusout();
         isOpen = false;
       }
     }
   });


    var isOpen2 = false;
   /*
    * Search Box In Popup Integration
    */
    $('.wpmm-search-popup').click(function(e){
      e.preventDefault();
      if($(this).parent().find('.wpmm-search-icon').hasClass('popup-search-form')){
       if(isOpen2 == false){
         $(this).parent().find('.popup-search-form').addClass('popup-searchbox-open').removeClass('popup-searchbox-closed');
         inputBox.focus();
         isOpen2 = true;
       } else {
         $(this).parent().find('.popup-search-form').removeClass('popup-searchbox-open').addClass('popup-searchbox-closed');
         inputBox.focusout();
         isOpen2 = false;
       }
     }
   });

    /*
    * Search Box In Popup Close Integration
    */
    $('.closepopup,.wpmm-search-overlay').click(function(e){
      $(this).parent().removeClass('popup-searchbox-open').addClass('popup-searchbox-closed');
    });


     // $(".wpmm-search-popup").colorbox({inline:true, width:"50%",transition:"fade"});
   /*
    * Add Woocommerce Class
    */
     if(check_woocommerce_enabled == "true"){
       $('.wp-megamenu-main-wrapper').each(function(){
         $(this).addClass('woocommerce');
       });
     }

    var submenu_open = event_behavior; // check event behavior as follow link on second click or toggle menu on second click
    /* searchtype onclick function */
    $('.wpmm-onclick .wpmega-searchdown').click(function(e){
      e.preventDefault();
      if($(this).closest('.wp-megamenu-main-wrapper').hasClass('wpmm-fade')){
              //fade
              if($(this).parent().find('.wpmm-sub-menu-wrap').hasClass('wpmm-open-fade')){
                $(this).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmm-open-fade');
              }else{
                $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrap').removeClass('wpmm-open-fade');
                $(this).parent().find('.wpmm-sub-menu-wrap').addClass('wpmm-open-fade');
              }
            }else{

                //slide
                if($(this).parent().find('.wpmm-sub-menu-wrap').hasClass('wpmm-mega-slidedown')){
                  $(this).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown').addClass('wpmm-mega-slideup');
                  $(this).parent().find('.wpmm-mega-slideup').slideUp('slow');
                }else{
                  $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown');
                  $(this).parent().find('.wpmm-sub-menu-wrap').removeClass("wpmm-mega-slideup").addClass("wpmm-mega-slidedown");
                  $(this).parent().find('.wpmm-mega-slidedown').slideDown('slow');

                }
          }
    });

    $('.wp-mega-menu-link').click(function(){
     var parentclass = $(this).parent().attr('class');
     if(parentclass == "wpmmreadmorelink"){
       var linkk = $(this).attr('href');
       window.open(linkk,"_self");
     }else if(parentclass == "wpmm-custom-postimage"){
       var linkk = $(this).attr('href');
       window.open(linkk,"_self");
     }
   });

    $( 'body' ).on( "mouseenter",'.wpmm-onhover .wp-mega-menu-link',function() {
               // "mouse enter"
               if($(this).parent().find('.wpmm-sub-menu-wrap').length > 0 || $(this).parent().find('.wpmm-sub-menu-wrapper').length > 0){
                 if($(this).closest('.wp-megamenu-main-wrapper').hasClass('wpmm-slide-right')){
                   $(this).parent().find('.wpmm-sub-menu-wrap').addClass('wpmmenu-slideRight');
                 }
               }
             }).on( "mouseleave", '.wpmm-onhover .wp-mega-menu-link',function() {
               //  "mouse leave"
               if($(this).parent().find('.wpmm-sub-menu-wrap').length > 0 || $(this).parent().find('.wpmm-sub-menu-wrapper').length > 0){
                 if($(this).closest('.wp-megamenu-main-wrapper').hasClass('wpmm-slide-right')){
                   $(this).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmmenu-slideRight');
                 }
               }
      });

      $('body').on( "click",'.wpmm-onclick .wp-mega-menu-link', function(e) {
              e.preventDefault();
              var link = $(this).attr('href');
              var parent_class = $(this).parent().attr('class');
              if(!$(this).parent().hasClass('wpmm-tabs-section')){
              if($(this).parent().find('.wpmm-sub-menu-wrap').length > 0 || $(this).parent().find('.wpmm-sub-menu-wrapper').length > 0){
                if(submenu_open == "follow_link"){
                      //Open submenu on first click and follow link on second click.
                      if(!$(this).hasClass('clicked')){
                       if($(this).closest('.wp-megamenu-main-wrapper').hasClass('wpmm-fade')){
                                //effect as fade
                                if($(this).parent().hasClass('wpmega-menu-megamenu')){
                                 //megamenu
                                 if($(this).parent().find('.wpmm-sub-menu-wrap').hasClass('wpmm-open-fade')){

                                  $(this).closest('.wpmm-mega-wrapper').find('.wp-mega-menu-link').removeClass('clicked');
                                  $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrap').removeClass('wpmm-open-fade');
                                  $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrapper').removeClass('wpmm-open-fade');
                                  $(this).closest('.wpmm-mega-wrapper').find('li').removeClass('active-show');

                                }else{

                                 $('.wpmm-sub-menu-wrap').removeClass('wpmm-open-fade');
                                 $('.wp-mega-menu-link').removeClass('clicked');
                                 $('.wpmm-sub-menu-wrapper').removeClass('wpmm-open-fade');
                                 $('.wpmm-mega-wrapper').find('li').removeClass('active-show');

                                 $(this).parent().find('.wpmm-sub-menu-wrap').addClass('wpmm-open-fade');
                                 $(this).parent().find('.wpmm-sub-menu-wrapper').addClass('wpmm-open-fade');
                                 $(this).parent().find('.wp-mega-menu-link').addClass('clicked');
                                 $(this).parent().addClass('active-show');

                               }

                             }else{
                                  //flyout
                                  $(this).siblings('.wpmm-sub-menu-wrapper').toggleClass('wpmm-open-fade');
                                  $(this).parent().addClass('active-show');
                                  if(!$(this).siblings('.wpmm-sub-menu-wrapper').hasClass('wpmm-open-fade')){
                                   $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrap').removeClass('wpmm-open-fade');
                                   $(this).closest('.wpmm-mega-wrapper').find('.wp-mega-menu-link').not($(this)).removeClass('clicked');
                                   $(this).closest('.wpmm-mega-wrapper').find('.wp-mega-menu-link').removeClass('clicked');
                                   $(this).parent().removeClass('active-show');
                                 }
                               }
                             }else{

                                //slide
                                if($(this).parent().hasClass('wpmega-menu-megamenu')){
                                  if($(this).parent().find('.wpmm-sub-menu-wrap').hasClass('wpmm-mega-slidedown')){

                                   $(this).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown').addClass('wpmm-mega-slideup');
                                   $(this).parent().removeClass('active-show');
                                 }else{

                                  $(this).closest('.wpmm-mega-wrapper').find('.wp-mega-menu-link').removeClass('clicked');
                                  $(this).closest('.wpmm-mega-wrapper').find('li').removeClass('active-show');
                                  $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown');
                                  $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slidedown');
                                  $(this).parent().find('.wpmm-sub-menu-wrap').removeClass("wpmm-mega-slideup").addClass("wpmm-mega-slidedown");
                                  $(this).parent().addClass('active-show');
                                }
                              }else{
                                    //flyout
                                    if($(this).parent().find('.wpmm-sub-menu-wrapper').hasClass('wpmm-mega-slidedown')){
                                     $(this).siblings('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slidedown');
                                     $(this).closest('.wpmm-mega-wrapper').find('li').removeClass('active-show');
                                   }else{

                                    $(this).siblings('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slideup').addClass("wpmm-mega-slidedown");
                                    $(this).parent().addClass('active-show');

                                  }
                                }
                              }
                              $(this).addClass('clicked');
                            }else{
                                //has been clicked once.
                                     if(!link || link == '#') {
                                       $(this).removeClass('clicked');

                                       if($(this).parent().hasClass('wpmega-menu-megamenu')){
                                        if($('.wp-megamenu-main-wrapper').hasClass('wpmm-fade')){
                                          $( this ).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmm-open-fade');
                                          $(this).parent().removeClass('active-show');
                                        }else{

                                         if($(this).parent().find('.wpmm-sub-menu-wrap').hasClass('wpmm-mega-slidedown')){
                                           $(this).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown').addClass('wpmm-mega-slideup');
                                           $(this).parent().removeClass('active-show');
                                         }else{
                                          $(this).closest('.wpmm-mega-wrapper').find('.wp-mega-menu-link').removeClass('clicked');
                                          $(this).closest('.wpmm-mega-wrapper').find('li').removeClass('active-show');
                                          $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown');
                                          $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slidedown');
                                          $(this).parent().find('.wpmm-sub-menu-wrap').removeClass("wpmm-mega-slideup").addClass("wpmm-mega-slidedown");
                                          $(this).parent().addClass('active-show');
                                        }

                                        // $(this).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown').addClass('wpmm-mega-slideup');
                                         // $(this).parent().addClass('active-show');
                                          // $(this).closest('.wpmm-mega-wrapper').find('li').removeClass('active-show');
                                        }
                                        }else{
                                        //flyout
                                        if($('.wp-megamenu-main-wrapper').hasClass('wpmm-fade')){
                                          //fade open
                                          $(this).siblings('.wpmm-sub-menu-wrapper').removeClass('wpmm-open-fade');
                                          $(this).parent().removeClass('active-show');
                                        }else{
                                          //slide
                                          if($(this).parent().find('.wpmm-sub-menu-wrapper').hasClass('wpmm-mega-slidedown')){
                                           $(this).siblings('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slidedown');
                                           $(this).closest('.wpmm-mega-wrapper').find('li').removeClass('active-show');
                                         }else{

                                          $(this).siblings('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slideup').addClass("wpmm-mega-slidedown");
                                          $(this).parent().addClass('active-show');

                                        }

                                            // $(this).siblings('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slidedown');
                                            // $(this).parent().addClass('active-show');
                                          }

                                        }
                                        return false;
                                      }else{

                                        if($(this).hasClass('clicked')){
                                          var target = $(this).attr('target');
                                              //  alert(target);
                                              if(target == "_blank"){
                                                window.open(link,target);
                                              }else{
                                               window.location= link;
                                             }

                                           }else{
                                            $(this).closest('.wpmm-mega-wrapper').find('.wp-mega-menu-link').removeClass('clicked');
                                            $(this).addClass('clicked');
                                          }

                                    }
                        }
                      }else{
                      //submenu_click
                        //Open Submenu on first click and close on second click.
                        $(this).removeClass('clicked');

                        if($(this).closest('.wp-megamenu-main-wrapper').hasClass('wpmm-fade')){
                              //fade effect
                              if($(this).parent().hasClass('wpmega-menu-megamenu')){
                               // alert('megamenu');
                                //megamennu
                                if($(this).parent().find('.wpmm-sub-menu-wrap').hasClass('wpmm-open-fade')){
                                  $(this).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmm-open-fade');
                                  $(this).parent().removeClass('active-show');
                                }else{
                                  $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrap').removeClass('wpmm-open-fade');
                                  $(this).closest('.wpmm-mega-wrapper').find('li').removeClass('active-show');
                                  $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrapper').removeClass('wpmm-open-fade');
                                  $(this).parent().find('.wpmm-sub-menu-wrap').addClass('wpmm-open-fade');
                                  $(this).parent().addClass('active-show');
                                }
                              }else{
                                  //flyout

                                  if($(this).siblings('.wpmm-sub-menu-wrapper').hasClass('wpmm-open-fade')){
                                    $(this).siblings('.wpmm-sub-menu-wrapper').removeClass('wpmm-open-fade');
                                    $(this).parent().removeClass('active-show');
                                  }else{
                                   $(this).siblings('.wpmm-sub-menu-wrapper').addClass('wpmm-open-fade');
                                   $(this).parent().addClass('active-show');
                                 }

                               }


                             }else{
                               // alert('yes_slide');
                            //slide effect
                            if($(this).parent().hasClass('wpmega-menu-megamenu')){
                              if($(this).parent().find('.wpmm-sub-menu-wrap').hasClass('wpmm-mega-slidedown')){
                               $(this).parent().find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown');
                               $(this).parent().removeClass('active-show');
                             }else{
                               $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrap').removeClass('wpmm-mega-slidedown');
                               $(this).closest('.wpmm-mega-wrapper').find('li').removeClass('active-show');
                               $(this).closest('.wpmm-mega-wrapper').find('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slidedown');
                               $(this).parent().find('.wpmm-sub-menu-wrap').removeClass("wpmm-mega-slideup").addClass("wpmm-mega-slidedown");
                               $(this).parent().addClass('active-show');
                             }
                           }else{
                                    //flyout
                                    if($(this).siblings('.wpmm-sub-menu-wrapper').hasClass('wpmm-mega-slidedown')){
                                      $(this).siblings('.wpmm-sub-menu-wrapper').removeClass('wpmm-mega-slidedown');
                                      $(this).parent().removeClass('active-show');
                                    }else{
                                      $(this).siblings('.wpmm-sub-menu-wrapper').removeClass("wpmm-mega-slideup").addClass("wpmm-mega-slidedown");
                                      $(this).parent().addClass('active-show');
                                    }


                                  }
                                }
                              }
                            }else{
                              var target = $(this).attr('target');
                              if(target== ""){
                               target = "_self";
                             }
                              //  alert(target);
                              if(target == "_blank"){
                                window.open(link,target);
                              }else{
                               window.location= link;
                             }
                   }
                e.stopImmediatePropagation();
               //  e.stopPropagation();
                return false;
              }
         });
        $(document).on('click', function (e) {
          if ($(e.target).closest(".wp-megamenu-main-wrapper").length === 0) {
            $(".wp-megamenu-main-wrapper .wpmm-sub-menu-wrap").removeClass('wpmm-open-fade');
            $(".wp-megamenu-main-wrapper li").removeClass('active-show');
            $(".wp-megamenu-main-wrapper .wpmm-search-form .wpmm-search-icon").addClass('searchbox-closed');
            $(".wp-megamenu-main-wrapper .wpmm-sub-menu-wrapper").removeClass('wpmm-open-fade');
            $(".wp-megamenu-main-wrapper .wp-mega-menu-link").removeClass('clicked');
          }
        });


 /* Responsive Settings Toggle Bar*/
/*  $('.wpmega-closeblock').click(function() {
   $(this).parent().parent().parent().find('.wpmm-mega-wrapper').slideToggle(1000,function(){
     $(this).parent().parent().parent().find('.wpmm-mega-wrapper').addClass('hide-menu');
   });
   $(this).parent().parent().parent().find('.wpmega-openblock').show();
   $(this).hide();
   $(this).closest('.wp-megamenu-main-wrapper').find('.wpmega-responsive-closebtn').hide();
  });

  $('.wpmega-openblock').click(function() {
    $(this).parent().parent().parent().find('.wpmm-mega-wrapper').slideToggle(1000,function(){
      $(this).parent().parent().parent().find('.wpmm-mega-wrapper').removeClass('hide-menu');
    });
    $(this).parent().parent().parent().find('.wpmega-closeblock').show();
    $(this).closest('.wp-megamenu-main-wrapper').find('.wpmega-responsive-closebtn').show();
    $(this).hide();
  });
*/

/*
* Mobile - Set to clickable menu always
*/
if(enable_mobile == 1){
  var winwidth = '';
  $(window).on("resize", function(event){
    winwidth = $(this).width();
     $('.wp-megamenu-main-wrapper').each(function(){
        var trigger_effect = $(this).find('.wpmm-mega-wrapper').attr('data-trigger-effect');//wpmm-onhover
        var responsive_breakingpoint = $(this).find('.wpmegamenu-toggle').attr('data-responsive-breakpoint');
        responsive_breakingpoint = responsive_breakingpoint.replace('px', '');
        if(responsive_breakingpoint == ''){
          responsive_breakingpoint = "910";
        }
        if(winwidth <= responsive_breakingpoint){
              if($(this).hasClass('wpmm-onhover')){
               $(this).removeClass('wpmm-onhover');
               $(this).addClass('wpmm-onclick');
             }
             $('.wpmega-tabs').each(function(){
              if($(this).hasClass('wpmm-tabbed-onhover')){
               $(this).removeClass('wpmm-tabbed-onhover');
               $(this).addClass('wpmm-tabbed-onclick');
             }
           });
        }else{
            if(trigger_effect == 'wpmm-onhover'){
              $(this).removeClass('wpmm-onclick');
              $(this).addClass('wpmm-onhover');
            }
        }

     });

  }).resize();

     $('.wp-megamenu-main-wrapper').on('click','.wpmega-openblock',function(){
        var $selector = $(this);
        $selector.closest('.wp-megamenu-main-wrapper').find('.wpmm-mega-wrapper').toggleClass('wpmm-show-menu');
       // $selector.closest('.wp-megamenu-main-wrapper').find('.wpmm-mega-wrapper').slideDown('slow');
        $selector.hide();
        $selector.closest('.wp-megamenu-main-wrapper').find('.wpmegamenu-toggle .wpmega-closeblock').show();
      });

     $('.wp-megamenu-main-wrapper').on('click','.wpmega-closeblock',function(){
        var $selector1 = $(this);
        $selector1.closest('.wp-megamenu-main-wrapper').find('.wpmm-mega-wrapper').toggleClass('wpmm-show-menu');
        //$selector1.closest('.wp-megamenu-main-wrapper').find('.wpmm-mega-wrapper').slideUp('slow');
        $selector1.hide();
        $selector1.closest('.wp-megamenu-main-wrapper').find('.wpmegamenu-toggle .wpmega-openblock').show();
      });

}






var win_width = $(window).width();
if(!is_mobile){
  if( win_width > 980){
    $('.wpmm-orientation-vertical > .wpmm-mega-wrapper > li > .wpmm-sub-menu-wrap > .wpmm-sub-menu-wrapper').each(function(){
      var height1 = $(this).outerHeight();
      var height5 = $(this).prev().outerHeight();
      var height4 = $(this).next().outerHeight();
      var height2 = $(this).prev().prev().outerHeight();
      var height3 = $(this).next().next().outerHeight();
      var height = parseInt(height1) + parseInt(height2) + parseInt(height3) + parseInt(height5) + parseInt(height4);
      $(this).parent('.wpmm-sub-menu-wrap').height(height);
    });
  }

  $( window ).resize(function() {
   $('.wpmm-orientation-vertical').each(function(){

    if(win_width < 1200){
      var menu_width = $(this).width();
                  //var total_width = parseInt(win_width) - parseInt(menu_width)- 70;
                  var total_width = parseInt(win_width) - parseInt(menu_width);
                  $(this).find('.wpmm-sub-menu-wrap').width(total_width);

                }

              });

 }).resize();
}


 /*  if(!is_mobile){

       $('.wpmm-orientation-vertical > .wpmm-mega-wrapper > li > .wpmm-sub-menu-wrap > .wpmm-sub-menu-wrapper').each(function(){
         var height1 = $(this).outerHeight();
         var height2 = $(this).prev().prev().outerHeight();
         var height3 = $(this).next().next().outerHeight();
         var height = parseInt(height1) + parseInt(height2) + parseInt(height3);
         // var height = parseInt(Total) + parseInt(height1);
         $(this).parent('.wpmm-sub-menu-wrap').height(height);


    });

   /* $('.wpmm-orientation-vertical > .wpmm-mega-wrapper > li > .wpmm-sub-menu-wrap > .wpmm-sub-menu-wrapper').each(function(){
         var height1 = $(this).outerHeight();


         // var height2 = $(this).prev().prev().outerHeight();
         // var height3 = $(this).next().next().outerHeight();
         // var height = parseInt(height1) + parseInt(height2) + parseInt(height3);

        var h1 = $(this).prev().outerHeight();
        var h2 = $(this).prev().prev().outerHeight();
        var h3 = $(this).next().outerHeight();
        var h4 = $(this).next().next().outerHeight();
       // if(h1 != "undefined" && h2 != "undefined"){
          var t1 = parseInt(h1) + parseInt(h2);
       // }else if(h1 != "undefined" && h2 == "undefined"){
         //  var t1 = parseInt(h1);
       // }else if(h1 == "undefined" && h2 != "undefined"){
         // var t1 = parseInt(h2);
       // }else{
         // var t1 = 0;
       // }

         //if(h3 != "undefined" && h4 != "undefined"){
          var t2 = parseInt(h3) + parseInt(h4);
       // }else if(h3 != "undefined" && h4 == "undefined"){
           //var t2 = parseInt(h3);
        //}else if(h3 == "undefined" && h4 != "undefined"){
         // var t2 = parseInt(h4);
      //  }else{
        //  var t2 = 0;
       // }


         var Total = parseInt(t1) +  parseInt(t2);
         var height = parseInt(Total) + parseInt(height1);

         $(this).parent('.wpmm-sub-menu-wrap').height(height);


    });

     $( window ).resize(function() {
       $('.wpmm-orientation-vertical').each(function(){
                var win_width = $(window).width();
                if(win_width < 1200){
                  var menu_width = $(this).width();
                  var total_width = parseInt(win_width) - parseInt(menu_width)- 70;

                  $(this).find('.wpmm-sub-menu-wrap').width(total_width);

                }

       });

      }).resize();
   }

   */
   /* Tabs Javascript */

   $('.wpmega-tabs').find('.wp-mega-sub-menu li').addClass('wpmm-tabs-section');
    // Example Hints: http://jsfiddle.net/uyvUZ/2/
    $('.wpmega-tabs > .wpmm-sub-menu-wrapper ul.wp-mega-sub-menu').each(function(i) {
      $(this).addClass('wpmm-tab-groups');
    });
    $('.wpmega-tabs > .wpmm-sub-menu-wrapper ul.wp-mega-sub-menu > li .wp-mega-sub-menu').each(function(i) {
     $(this).addClass('wpmm-tab-groups-panel');
     $(this).removeClass('wpmm-tab-groups');
   });
    $(".wpmm-tabs-section").removeClass('show_tab');
    $(".wpmega-tabs .wpmm-sub-menu-wrapper ul.wp-mega-sub-menu.wpmm-tab-groups").each(function() {
      $(this).find('li:first').addClass("show_tab");
      $(this).nextAll('.wp-mega-sub-menu:first').find('.wpmm-tabs-section:first').addClass('show_tab');
    });


//    $(".wpmega-tabs.wpmm-tabbed-onhover .wpmm-sub-menu-wrapper ul.wp-mega-sub-menu.wpmm-tab-groups > li > a").on('hover',function() {
//      var cTab = $(this).closest('li');
//      var animated = cTab.closest('.wpmega-tabs').attr('data-animation');
//      var vcontent_height =  $(this).parent().find('.wpmm-tab-groups-panel').actual( 'outerHeight' );
//      var totaltabheight = $(this).parents('.wpmm-tab-groups').actual('outerHeight');
//      var hTabTitleHeight = parseInt($(this).parent().height()) + 15;
//      var target = $(this).attr('target');
//      var link = $(this).attr('href');
//
//      if(totaltabheight > 0){
//        /* Open hide tab content start */
//        cTab.siblings('li').removeClass('show_tab');
//        $('.wpmm-tab-groups-panel').removeClass(animated);
//        cTab.addClass('show_tab');
//        cTab.find('.wpmm-tab-groups-panel').addClass('animated '+animated);
//        cTab.closest('ul.wp-mega-sub-menu').nextAll('.wp-mega-sub-menu:first').find('.wpmm-tabs-section').removeClass('show_tab');
//        /* Open hide tab content end */
//        if($(this).closest('.wpmega-tabs').hasClass('wpmega-vertical-tabs')){
//          if(totaltabheight > vcontent_height){
//            $(this).parents('.wpmm-tab-groups').css('min-height',totaltabheight);
//          }else{
//            $(this).parents('.wpmm-tab-groups').css('min-height',vcontent_height);
//          }
//        }else{
//          var hcontent_height =  $(this).parent().find('.wpmm-tab-groups-panel').actual( 'outerHeight' ) + 10;
//          var total_height = parseInt(hcontent_height) + parseInt(hTabTitleHeight);
//          $(this).parents('.wpmm-tab-groups').css('min-height',total_height);
//        }
//      }
//
//      return false;
//    });
$(".wpmega-tabs.wpmm-tabbed-onhover .wpmm-sub-menu-wrapper").on({
    mouseenter: function () {
       var cTab = $(this).closest('li');
      var animated = cTab.closest('.wpmega-tabs').attr('data-animation');
      var vcontent_height =  $(this).parent().find('.wpmm-tab-groups-panel').actual( 'outerHeight' );
      var totaltabheight = $(this).parents('.wpmm-tab-groups').actual('outerHeight');
      var hTabTitleHeight = parseInt($(this).parent().height()) + 15;
      var target = $(this).attr('target');
      var link = $(this).attr('href');

      if(totaltabheight > 0){
        /* Open hide tab content start */
        cTab.siblings('li').removeClass('show_tab');
        $('.wpmm-tab-groups-panel').removeClass(animated);
        cTab.addClass('show_tab');
        cTab.find('.wpmm-tab-groups-panel').addClass('animated '+animated);
        cTab.closest('ul.wp-mega-sub-menu').nextAll('.wp-mega-sub-menu:first').find('.wpmm-tabs-section').removeClass('show_tab');
        /* Open hide tab content end */
        if($(this).closest('.wpmega-tabs').hasClass('wpmega-vertical-tabs')){
          if(totaltabheight > vcontent_height){
            $(this).parents('.wpmm-tab-groups').css('min-height',totaltabheight);
          }else{
            $(this).parents('.wpmm-tab-groups').css('min-height',vcontent_height);
          }
        }else{
          var hcontent_height =  $(this).parent().find('.wpmm-tab-groups-panel').actual( 'outerHeight' ) + 10;
          var total_height = parseInt(hcontent_height) + parseInt(hTabTitleHeight);
          $(this).parents('.wpmm-tab-groups').css('min-height',total_height);
        }
      }

      return false;
    },
    mouseleave:function () {
//        $(this).removeClass("hover");
    }
},'ul.wp-mega-sub-menu.wpmm-tab-groups > li > a');
    $( window ).resize(function() {
      if($(window).width() <= 910){
         $('.wpmega-tabs.wpmm-tabbed-onclick .wpmm-sub-menu-wrapper ul.wp-mega-sub-menu.wpmm-tab-groups > li > a').on('click',function(e) {
              e.preventDefault();
              var cTab = $(this).closest('li');
              var animated = cTab.closest('.wpmega-tabs').attr('data-animation');
              var vcontent_height =  $(this).parent().find('.wpmm-tab-groups-panel').actual( 'outerHeight' );
              var totaltabheight = $(this).parents('.wpmm-tab-groups').actual('outerHeight');
              var hTabTitleHeight = parseInt($(this).parent().height()) + 15;
              var target = $(this).attr('target');
              var link = $(this).attr('href');
              if(link == '' || link == '#'){
                if(totaltabheight > 0){
                  /* Open hide tab content start */
                /* if(cTab.hasClass('show_tab')){
                  cTab.removeClass('show_tab');
                 }else{
                  cTab.siblings('li').removeClass('show_tab');
                  cTab.closest('ul.wp-mega-sub-menu').nextAll('.wp-mega-sub-menu:first').find('.wpmm-tabs-section').removeClass('show_tab');
                  cTab.addClass('show_tab');
                 }*/
                  //cTab.siblings('li').removeClass('show_tab');
                //  cTab.closest('ul.wp-mega-sub-menu').nextAll('.wp-mega-sub-menu:first').find('.wpmm-tabs-section').removeClass('show_tab');
                  cTab.closest('ul.wpmm-tab-groups').find('li').removeClass('show_tab');
                  cTab.addClass('show_tab');

                  /* Open hide tab content end */
                  $('.wpmm-tab-groups-panel').removeClass(animated);
                  cTab.find('.wpmm-tab-groups-panel').addClass('animated '+animated);
                  if($(this).closest('.wpmega-tabs').hasClass('wpmega-vertical-tabs')){
                    if(totaltabheight > vcontent_height){
                      $(this).parents('.wpmm-tab-groups').css('min-height',totaltabheight);
                    }else{
                      $(this).parents('.wpmm-tab-groups').css('min-height',vcontent_height);
                    }
                  }else{
                    var hcontent_height =  $(this).parent().find('.wpmm-tab-groups-panel').actual( 'outerHeight' ) + 10;
                    var total_height = parseInt(hcontent_height) + parseInt(hTabTitleHeight);
                    if(cTab.hasClass('show_tab')){
                        $(this).parents('.wpmm-tab-groups').css('min-height',total_height);
                    }else{
                        $(this).parents('.wpmm-tab-groups').css('min-height',0);
                    }

                  }

                }
              }else{
                if(target == "_blank"){
                  window.open(link,target);
                }else{
                 window.location = link;
               }
             }
          });
      }else{
            $('.wpmega-tabs.wpmm-tabbed-onclick .wpmm-sub-menu-wrapper').on('click','ul.wp-mega-sub-menu.wpmm-tab-groups > li > a',function(e) {
              e.preventDefault();
              var cTab = $(this).closest('li');
              var animated = cTab.closest('.wpmega-tabs').attr('data-animation');
              var vcontent_height =  $(this).parent().find('.wpmm-tab-groups-panel').actual( 'outerHeight' );
              var totaltabheight = $(this).parents('.wpmm-tab-groups').actual('outerHeight');
              var hTabTitleHeight = parseInt($(this).parent().height()) + 15;
              var target = $(this).attr('target');
              var link = $(this).attr('href');

              if(link == '' || link == '#'){
                if(totaltabheight > 0){
                  /* Open hide tab content start */
                  cTab.siblings('li').removeClass('show_tab');
                  $('.wpmm-tab-groups-panel').removeClass(animated);
                  // cTab.addClass('show_tab');
                  cTab.toggleClass('show_tab');
                  cTab.find('.wpmm-tab-groups-panel').addClass('animated '+animated);
                  cTab.closest('ul.wp-mega-sub-menu').nextAll('.wp-mega-sub-menu:first').find('.wpmm-tabs-section').removeClass('show_tab');
                  /* Open hide tab content end */
                  if($(this).closest('.wpmega-tabs').hasClass('wpmega-vertical-tabs')){
                    if(totaltabheight > vcontent_height){
                      $(this).parents('.wpmm-tab-groups').css('min-height',totaltabheight);
                    }else{
                      $(this).parents('.wpmm-tab-groups').css('min-height',vcontent_height);
                    }
                  }else{
                    var hcontent_height =  $(this).parent().find('.wpmm-tab-groups-panel').actual( 'outerHeight' ) + 10;
                    var total_height = parseInt(hcontent_height) + parseInt(hTabTitleHeight);
                    $(this).parents('.wpmm-tab-groups').css('min-height',total_height);
                  }
                }
              }else{
                if(target == "_blank"){
                  window.open(link,target);
                }else{
                 window.location = link;
               }
             }
        });

      }
 }).resize();




    setTimeout(function(){
      $('.wpmega-tabs > .wpmm-sub-menu-wrapper ul.wpmm-tab-groups').each(function(){
        var height = $(this).actual( 'outerHeight' );
        var content_height = $(this).children('li').first().find('.wpmm-tab-groups-panel').actual( 'outerHeight' );
        var horizontalTabTitleHeight = parseInt($(this).children('li').first().actual( 'outerHeight' )) + 15;
        if($(this).closest('.wpmega-tabs').hasClass('wpmega-vertical-tabs')){
          if(height > content_height){
            $(this).css('min-height',height);
          }else{
            $(this).css('min-height',content_height);
          }
        }else{
          var total_height = parseInt(content_height) + parseInt(horizontalTabTitleHeight);
          $(this).css('min-height',total_height);
        }
      });
    },2000);
    /* tabs end */

  //sticky menu
  var sticky_opacity = wp_megamenu_params.wpmm_sticky_opacity;
  var sticky_offset = wp_megamenu_params.wpmm_sticky_offset;

  if(sticky_offset == ''){
    sticky_offset = '0px';
  }else{
    sticky_offset = parseInt(sticky_offset) + 'px';
  }

  var sticky_zindex = wp_megamenu_params.wpmm_sticky_zindex;

  if($('.wp-megamenu-main-wrapper').hasClass( "wpmm-pro-sticky" )){

    $this = $('.wpmm-pro-sticky');
            // Check the initial Poistion of the Sticky Header
            var stickyHeaderTop = $('.wpmm-pro-sticky').offset().top;
            $(window).scroll(function(){
              if( $(window).scrollTop() > stickyHeaderTop ) {

                $this.addClass('navbar-fixed-top');
                $('.navbar-fixed-top').css({'top': sticky_offset, 'z-index' : sticky_zindex});
                $('.navbar-fixed-top').css('opacity', sticky_opacity);
              } else {
               $this.removeClass('navbar-fixed-top');
             }
           });
          }
   // sticky menu end

/*   $('.wp-megamenu-main-wrapper .wpmm-sub-menu-wrap').each(function(){
    var nextimage = $(this).attr('data-nextimage');
    var nextimage1 = $(this).attr('data-nextimage1');
    var repeat = $(this).attr('data-repeat');
    var position = $(this).attr('data-position');
    if(nextimage != '' && nextimage != undefined){

      $(this).hover(function(){
        $(this).addClass("wpmm-fadeOut");
        $(this).css("background-image", "url("+nextimage+")");
        $(this).css("background-repeat",repeat);
        $(this).css("background-position",position);
        $(this).css("background-size",'cover');
      //$(this).css("transition",'2.5s');

    }).mouseleave(function(){
      $(this).removeClass("wpmm-fadeOut");
      $(this).css("background-image", "url("+nextimage1+")");
      $(this).css("background-repeat",repeat);
      $(this).css("background-position",position);
      $(this).css("background-size",'cover');
       //$(this).css("transition",'0s');
     });

  }

});*/


   $('.wpmm-original-submenus').each(function(){
    var id = $(this).find('li').attr('id');
    var appended_html = $(this).html();
    $(this).parent().find('.wpmm-group1').find('.wpmm-clone-submenus').find('li#'+id).parent().replaceWith(appended_html);
    $(this).remove();
  });


   /* widget posts slider*/
   $('.wpmega-posts-slider').each(function(){
    var selector = $(this);
    var speed = $(this).data('speed');
    var auto = $(this).data('auto-slide');
    var slider_duration = $(this).data('duration');
    var controls = $(this).data('controls');
    var id = $(this).data('id');
    var mode = $(this).data('mode');
    $(this).bxSlider({
      speed: speed,
      pause: slider_duration,
      auto: auto,
      pager: false,
      mode: mode,
      controls: controls,
      infiniteLoop: false,
      adaptiveHeight:true
    });

  });

  /*
  * Double animation background image
  */
  $('.wpmm-sub-menu-wrap').each(function(){
    if($(this).hasClass('wpmm-double-image-animation')){
      var topimage = $(this).find('.animation-double-bgimage').data('top-image');
      var bottomimage = $(this).find('.animation-double-bgimage').data('second-image');
     $(this).hover(function(){
        $(this).css("background-image","url("+bottomimage+")");
       }).mouseleave(function(){
        $(this).css("background-image", "url("+topimage+")");
      });
    }
  });

  /*
  * Fusion Avada Theme with its Search icon inside menu compatibiltiy
  */
 $('.wp-megamenu-main-wrapper').each(function(){
   var liclass = $(this).find('li.fusion-custom-menu-item');
   if(liclass.hasClass('fusion-main-menu-search')){
    //has search icon
    $(this).find('li.fusion-custom-menu-item.fusion-main-menu-search .fusion-custom-menu-item-contents').addClass('wpmm-sub-menu-wrap');
   }
  });

 });
