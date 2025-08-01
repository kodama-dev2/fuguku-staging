<div class="import-container">
    <h2>Import Demo Data</h2>
    <div style="background-color: #F5FAFD; margin:10px 0;padding: 5px 10px;color: #0C518F;border: 2px solid #CAE0F3; clear:both; width:90%; line-height:18px;">
        <p class="tie_message_hint">Importing demo data (post, pages, images, theme settings, ...) is the easiest way to setup your theme. It will
            allow you to quickly edit everything instead of creating content from scratch. When you import the data following things will happen:</p>

        <ul style="padding-left: 20px;list-style-position: inside;list-style-type: square;}">
            <li>Before you begin, make sure all the required plugins are activated.</li>
            <li>Please click import only once and wait, it can take a couple of minutes</li>
        </ul>
    </div>

    <div style="background-color: #F5FAFD; margin:10px 0;padding: 5px 10px;color: #0C518F;border: 2px solid #CAE0F3; clear:both; width:90%; line-height:18px; text-align: center;">
        <h2 class="tie_message_hint" style="color: red; font-size: 30px;">All your old data will be lost if you use this function.</h2>
    </div>
    <form method="post" style="background-color: #fff;width: 90%;margin: 10px 0;padding: 5px 10px;">
        <?php

        $layout = isset($_REQUEST['layout']) ? $_REQUEST['layout'] : 'default';

        ?>
        <div id="import-form" style="text-align: center;padding: 10px;">
            <span><?php esc_html_e('Choose layout: ','jas-sample'); ?></span>
            <select name="layout">
                <option value="Elementor" <?php if($layout == 'Elementor'):?> selected = "selected" <?php endif; ?> >Elementor</option>
                <option value="VisualComposer" <?php if($layout == 'VisualComposer'):?> <?php endif; ?> >Visual Composer</option>
            </select>
            <input type="hidden" name="demononce" value="<?php echo wp_create_nonce('jas-demo-code'); ?>" />
            <input name="reset" class="panel-save button-primary radium-import-start" type="button" value="Import" />
            <input type="hidden" name="action" value="demo-data" />
        </div>
        <div id="import-loading" style="display:none; text-align: center;"><img src="<?php echo JAS_SAMPLE_URI.'/import/view/loading.gif';?>"></div>

    </form>
</div>
<script>
    ( function( $ ) {
        "use strict";
        $(document).ready(function($) {
            $('.radium-import-start').click(function(){
                var view = $('select[name="layout"]').val();
                if(confirm("Warning: all your old data will be lost!"))
                {
                    $.ajax({
                        url:'<?php echo admin_url( 'admin-ajax.php' ); ?>',
                        type:'post',
                        dataType: 'json',
                        data:{layout:view,action:'look_import_demo'},
                        beforeSend: function(){
                            $('#import-form').hide();
                            $('#import-loading').show();
                        },
                        success: function(){
                            $('#import-form').show();
                            $('#import-loading').hide();
                            alert('Import Done !!');
                        },
                        error:function()
                        {
                            $('#import-form').show();
                            $('#import-loading').hide();
                            alert('Import Done !!');
                        }
                    });
                }

            });


        });
    } )( jQuery );
</script>