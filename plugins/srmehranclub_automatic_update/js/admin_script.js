"use strict";

(function ($)
{

	var reloadGutenberg = false;

 
	   
	   
	  $( "#srm_license_page_form" ).validate({
		  rules: {
			srm_license_key: {required: true,minlength:6},
			srm_license_secret_key: {required: true,minlength:6},
		  }
		});
	
	 /**
     * Activation of license
     */
	jQuery(document).ready(function($){
    $(document).on('click', '.activate-license-btn', function(e){
        e.preventDefault(); // prevent default form submission

        var $form = $("#srm_license_page_form");

        if(!$form.valid()) return; // check form validation

        var srm_license_key = $('#srm_license_key').val();

        if(srm_license_key === ''){
            $('.msg').text('*Please enter license');
            return false;
        }

        $form.addClass('loaderdis');
        $('.loader').show();

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: 'activate_license',
                srm_license_key: srm_license_key
            },
            dataType: 'json',
            cache: false,
            success: function(response){
                $form.removeClass('loaderdis');
                $('.loader').hide();

                if(response.error){
                    $('.msg').text(response.msg || 'An error occurred');
                    return;
                }

                if(response.success){
                    window.location.href = "admin.php?page=srm-setup";
                } else {
                    $('.msg').text(response.message || 'Something went wrong');
                }
            },
            error: function(xhr, status, error){
                $form.removeClass('loaderdis');
                $('.loader').hide();
                $('.msg').text('AJAX request failed: ' + error);
            }
        });
    });
});

    /**
     * Deactivating license
     */
    jQuery(document).on('click', '.deactivate-license-btn', function(e){
         var $form = $("#srm_license_page_form");
        
        var license = $('#srm_license').val();
        if(license == ''){
            $('.msg').text('*Please enter license');
            return false;
        }
        $('.loader2').show();
         $form.addClass('loaderdis');
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {license:license,'action':'deactivate_license'},
            cache: false,
            dataType:'json',
            success: function(response){
                 $form.removeClass('loaderdis');
                $('.loader2').hide();
                if (response.error) {
                    $('.msg').text(response.msg);
                    return;
                }
                if (response.success) {
                    $('.msg').text(response.msg);
                    //$('.status').html('Status : Deactivated');
                   window.location.href="admin.php?page=srm-license";
					
                }
            }
        });
    
    });


   

    const showMainLoader = function () {
        const circleProgress = document.querySelector(".circle-loader.active #circleProgress");
        circleProgress.classList.add('start');
        document.querySelector(".circle-loader.active #circleProgressSvg").classList.remove('grey');
        document.querySelector(".circle-loader.active #circleProgressSvg").classList.add('green');

        let counter = 0;
        const myInterval = setInterval(() => {
            document.querySelector(".circle-loader.active #percentageCounter").innerHTML = `${counter++}%`;
           
        }, 50);

        const back_step = document.querySelector(".circle-loader.active .back_step");
        back_step.classList.add("disabled");

        setTimeout(() => {
            clearInterval(myInterval);
            circleProgress.classList.remove('start');
            nextStep();

            back_step.classList.remove('disabled');
            //document.querySelector(".circle-loader #glofdkmgkdfms").style.display = 'block';
        }, 5085);
        
        setTimeout(() => {
            document.querySelector(".circle-loader.active #stp-1").classList.remove('active');
            document.querySelector(".circle-loader.active #stp-2").classList.add('active');
            setTimeout(() => {
                document.querySelector(".circle-loader.active #stp-2").classList.remove('active');
                document.querySelector(".circle-loader.active #stp-3").classList.add('active');
            }, 1700);
        }, 1700);
    }

   
    jQuery(document).on('click', '#nextsetp', function(e){
        jQuery(this).hide();
        jQuery('.pluginAction-serverInfo').hide();
        jQuery('.nextstp').show();
        jQuery('#step2').addClass('SR-step-active-done'); 
        jQuery('#step2').removeClass('SR-step-active');
        jQuery('#step3').removeClass('disabled');
        jQuery('#step3').addClass('SR-step-active');
        setTimeout(function () {
            showMainLoader(1);
        }, 1000);
    });
    

   function nextStep(){
    jQuery('.pluginAction-serverInfo').hide();
    jQuery('.nextstp').hide();
    jQuery('.sucesquestion').show();
    jQuery('#step2').addClass('SR-step-active-done'); 
    jQuery('#step2').removeClass('SR-step-active');
    jQuery('#step3').removeClass('SR-step-active');
    jQuery('#step3').addClass('SR-step-active-done');
    jQuery('#step4').removeClass('disabled');
    jQuery('#step4').addClass('SR-step-active');
    
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {'action':'complete_setup'},
                cache: false,
                dataType:'json',
                success: function(response){
                    window.location.href=response.redirect;
                    
                }
            }); 
    }


    jQuery(document).on('click','.history-remove',function(){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.isConfirmed) {
                var id = jQuery(this).attr('data-id');
                jQuery.post(document.location.href, {'delete_id': id}, function(){
                    document.location.reload();
                });
            }
          })
      
    });

    jQuery(document).on('click','.restore',function(){
        let pluginName=jQuery(this).data('name');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to restore "+pluginName,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, restore it!'
          }).then((result) => {
            if (result.isConfirmed) {
                var id = jQuery(this).attr('data-id');
               
                jQuery.post(document.location.href, {'restore_id': id}, function(){
                    Swal.fire(
                        'Success!',
                        'Restored successfully!',
                        'success'
                      ).then(()=>{
                            document.location.reload();
                      });
                  
                });
            }
          })
      
    });

    
   
	/** /Showing link stats in inbound suggestions **/
})(jQuery);

function download_file( element,  id ) {
    
    var element = '#download_'+id;
    var prev_id = id;
    jQuery(element).html('<img src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" style="width: 25px;">');

    jQuery.ajax({
        url:ajaxurl,
        type : 'post',
        data : {
            action : 'send_request_using_ajax',
            post_id : id
        },
        success : function( response ) {
            
            if (IsJsonString(response)) {
                var obj = JSON.parse(response);
                if ( obj.status == "success" ) {
                    id = obj.id;
                    
                    var fst = true;
                    var tmp = "";
                    jQuery(".download_remains").each(function( index ) {
                        if (fst){
                            tmp = jQuery(this).find('.remains').text();
                            if (obj.prev <= 0) {
                                tmp = tmp - 1;
                            }
                        }
                        if (tmp >= 0) {
                            jQuery(this).find(".remains").text(""+tmp);
                            jQuery(".available_download_count .count").text(""+tmp);
                            jQuery('#mainplanLimit').text(tmp);
                        }
                        fst = false;
                    });
                     Swal.fire(
                    'Success!',
                    'Product Downloaded',
                    'success'
                  ).then(()=>{
                    var version_new = jQuery(element).parent().parent().parent().find('td:nth-child(5)').text();
                    jQuery(element).parent().parent().parent().find('td:nth-child(4)').text(version_new+"");

                    jQuery(element).html('<a class="download" style="cursor:pointer;" onclick="install_file(\''+obj.name+'\','+id+')"><i class="fa fa-play-circle-o"></i></a>');
                    jQuery(element).attr("id","download_"+id);
                  });
                   

                }else{
                    Swal.fire(
                    'Failed!',
                    'Oops something went wrong please try again',
                    'warning'
                  ).then(()=>{
                    jQuery(element).html('<a class="download" onclick="download_file( this,'+id+')" style="cursor:pointer;" title="Download"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>');
                    jQuery(element).attr("id","download_"+id);
                  });
                   
                } 
                // Swal.fire(
                //     'Success!',
                //     'Product Downloaded',
                //     'success'
                //   ).then(()=>{
                //     window.location.reload();
                //   });
                //alert('Product Downloaded');
            }else{
               
                //console.log(response);
                //alert('Sorry! Download Limit Exceeded');
                jQuery(element).html('Limit Exceeded');
            }
        }
    });
}
 

function install_file( id, name ) {

    // Name == id
    // Id == name

    var element = '#download_'+name;
    jQuery(element).html('<img src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" style="width: 25px;">');

    jQuery.ajax({
        url: ajaxurl,
        type : 'post',
        data : {
            action : 'srclubplugins_page_plugins_install_ajax',
            post_id : id
        },
        success : function( response ) {
            if(response=="Error"){
                Swal.fire(
                    'Error!',
                    'Oops something went wrong please try again!',
                    'error'
                  );
                jQuery(element).html('<a class="download" title="Install" style="cursor:pointer;" onclick="install_file(\''+id+'\','+name+');"><i class="fa fa-play-circle-o"></i></a>');
            }else if(response=="Installation failed multiple files detected"){
                Swal.fire(
                    'Installation failed!',
                    response,
                    'info'
                  );
                  jQuery(element).html('<a class="download" title="Install" style="cursor:pointer;" onclick="install_file(\''+id+'\','+name+');"><i class="fa fa-play-circle-o"></i></a>');
            }else{
                Swal.fire(
                    'Success!',
                    response,
                    'success'
                  );
                  var version_new = jQuery(element).parent().parent().parent().find('td:nth-child(4)').text();
                  jQuery(element).parent().parent().parent().find('td:nth-child(3)').text(version_new+"");
                  
                  jQuery(element).html('<a class="download" onclick="install_file(\''+id+'\', '+name+');">Installed </a>');
            }
        }
    });
}

function remove_from_list( id ){
    id = "#product_list_"+id;
    jQuery(id).hide();
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function myFunction() {
    // Declare variables
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
  
    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  jQuery(document).on('click','#getProductbysearch',function(){
    const searchByurl=jQuery('#searchByurl').val();
    if(searchByurl==''){
        alert('Please enter valid url');
        return;
    }
   jQuery('#searchByurl').attr("disabled", true);
   jQuery(this).attr("disabled", true);
   jQuery(this).text('Loading...');
    jQuery.ajax({
        url: ajaxurl,
        type : 'post',
        data : {
            action : 'installbyurl',
            searchByurl : searchByurl
        },
        success : function( response ) {
          
            if ( response.status == "success" ) {
                Swal.fire(
                'Success!',
                'Product has been added to below list please check',
                'success'
              ).then(()=>{
                window.location.reload();
              });
            }else{
                Swal.fire(
                    'Success!',
                    response.msg,
                    'error'
                  ).then(()=>{
                    window.location.reload();
                  });
            }
        }
    });


  });


  function d_loader(PereentDiv=false){
    let loader='';
    if(PereentDiv){
       loader=PereentDiv;
    }else{
       loader='body'; 
    }
    jQuery(loader).after().append('<div class="ssloading">Loading&#8230;</div>');
}

function r_loader(PereentDiv=false){
    let loader='';
        if(PereentDiv){
           loader=PereentDiv;
        }else{
           loader='body'; 
        }
        jQuery(loader).find('.ssloading').remove();
   }


   jQuery(document).on('click','.whiteLbtn',function(){
    jQuery( "#sr_pluginactions" ).validate({
        rules: {
            plugin_name: {required: true},
            plugin_author: {required: true},
            plugin_author_URI: {required: true},
            plugin_description: {required: true},
            enable_white_label:{required: true},
        }
      });
      if(jQuery( "#sr_pluginactions" ).valid()){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes enable white labeling!'
          }).then((result) => {
            if (result.isConfirmed) {
                let plugin_name=jQuery('#plugin_name').val();
                let plugin_author=jQuery('#plugin_author').val();
                let plugin_author_URI=jQuery('#plugin_author_URI').val();
                let enable_white_label=jQuery('#enable_white_label').val();
                let plugin_description=jQuery('#plugin_description').val();
                let plugin_icon_URI=jQuery('#plugin_icon_URI').val();
                let allowed=jQuery('.js-example-basic-multiple').val();
                let soft_whitelebal_activation=jQuery('#soft_whitelebal_activation').val();
                jQuery.ajax({
                    url: ajaxurl,
                    type : 'post',
                    data : {
                        action : 'enablewhitelebel',
                        plugin_name : plugin_name,
                        plugin_author : plugin_author,
                        plugin_author_URI : plugin_author_URI,
                        enable_white_label : enable_white_label,
                        plugin_description : plugin_description,
                        plugin_icon_URI : plugin_icon_URI,
                        allowed : allowed,
                        soft_whitelebal_activation
                    },
                    success : function( response ) {
                        window.location.href="admin.php?page=sr-automatic-upgrade";
                    }
                });
            }
          })
      }
   });



jQuery(document).on('click', '.srm-update-item', function (e) {
    e.preventDefault();
    d_loader();
    let btn = jQuery(this);
    btn.prop('disabled', true).text('Updating...');

    let data = {
        action: 'srm_update_item',
        type: btn.data('type'),
        file: btn.data('file'),
        name: btn.data('name'),
        package: btn.data('package')
    };

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: data,
        success: function (response) {
            if (response.success) {
                btn.text('Updated ✔');
            } else {
                btn.text('Failed ✖');
            }
        },
        error: function () {
            btn.text('Error');
        },
        complete: function () {
            r_loader();
            btn.prop('disabled', false);
        }
    });
});



jQuery(document).on('click', '.srm-checkupdate-item', function (e) {
    e.preventDefault();

    let btn = jQuery(this);
    btn.prop('disabled', true).text('Updating...');

    const row = btn.closest('tr');
    const updateColumn = row.find('.update-column');
    const currentversion = row.find('.srm-currentversion').text();


    updateColumn.html('<span class="update-status checking-badge">Checking for updates...</span>');

    let type = btn.data('type');
    let file = btn.data('file');
    let name = btn.data('name');
    let packageFile = btn.data('package');

    let data = {
        action: 'srm_check_update',
        type: type,
        file: file,
        name: name,
        package: packageFile
    };

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: data,
        success: function (response) {
            btn.prop('disabled', false).text('Check Update');

            if (response.success) {
                if (response.data.has_update) {
                    console.log("currentversion",currentversion,"response.data.new_version",response.data.new_version);
                    if(currentversion==response.data.new_version){
                        updateColumn.html('<span class="update-status up-to-date-badge">Up to date</span>');
                         row.removeClass('update-available');
                         return;
                    }
                    // Update available
                    updateColumn.html('<span class="update-status update-available-badge">Update to ' + response.data.new_version + '</span>');

                    // Actions column
                    const actionsColumn = row.find('.actions-column');
                    if (response.data.package) {
                        actionsColumn.append(
                            '<button class="btn btn-update-now srm-update-item" ' +
                            'data-type="' + type + '" ' +
                            'data-file="' + file + '" ' +
                            'data-name="' + name + '" ' +
                            'data-package="' + response.data.package + '">Update Now</button>'
                        );
                    } else {
                        actionsColumn.append(
                            '<button class="btn btn-update-now srm-update-item" ' +
                            'data-type="' + type + '" ' +
                            'data-file="' + file + '" ' +
                            'data-name="' + name + '" ' +
                            'data-package="">Manual Update</button>'
                        );
                    }

                    // Mark row
                    row.addClass('update-available');
                } else {
                    updateColumn.html('<span class="update-status up-to-date-badge">Up to date</span>');
                    row.removeClass('update-available');
                }
            } else {
                updateColumn.html('<span class="update-status" style="background: #dc3545; color: #fff;">Error: ' + response.data.message + '</span>');
            }
        },
        error: function () {
            btn.text('Error');
        },
        complete: function () {
            r_loader();
            btn.prop('disabled', false);
        }
    });
});


  
