jQuery(document).ready(function ($) {
    function initializeSelect2() {
        let $element = $('#woocommerce_cekongkir_origin_location_destination');

        if ($element.length && !$element.hasClass('select2-hidden-accessible')) {
            $element.select2({
                placeholder: "Search and select an origin...",
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: cekongkir_select2_params.ajax_url,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'search_origin_locations',
                            term: params.term,
                            security: cekongkir_select2_params.nonce,
                            timestamp: new Date().getTime()
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data?.data?.map(item => ({
                                id: item.id,
                                text: item.text
                            })) || []
                        };
                    },
                    cache: false 
                }
            });

            $.ajax({
                url: cekongkir_select2_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_saved_origin_location',
                    security: cekongkir_select2_params.nonce
                },
                success: function (response) {
                    if (response.success && response.data.id) {
                        let option = new Option(response.data.text, response.data.id, true, true);
                        $element.append(option).trigger('change');
                    }
                },
                error: function () {
                }
            });

            // **Handle Save Button Click**
            $('.wc-settings-save, .button-primary').on('click', function (e) {
                let selectedData = $element.select2('data');

                if (!selectedData.length) {
                    return;
                }

                let selectedOption = selectedData[0];

                $.ajax({
                    url: cekongkir_select2_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'save_origin_location',
                        origin_location_id: selectedOption.id,
                        origin_location_label: selectedOption.text,
                        security: cekongkir_select2_params.nonce
                    },
                    success: function (response) {
                        $element.val(null).trigger('change'); 
                        initializeSelect2(); 
                    },
                    error: function (xhr) {
                        console.error('%c Save Failed:', 'color: red; font-weight: bold;', xhr);
                        alert("Error saving selection: " + xhr.responseText);
                    }
                });
            });
        }
    }

    initializeSelect2();

    $(document).on('wc_backbone_modal_loaded woocommerce_settings_saved', function () {
        console.log("WooCommerce settings reloaded, re-initializing Select2...");
        initializeSelect2();
    });

    let observer = new MutationObserver(function (mutations) {
        mutations.forEach(mutation => {
            if ($('#woocommerce_cekongkir_origin_location_destination').length) {
                console.log("Dropdown re-added to DOM, initializing Select2...");
                initializeSelect2();
                observer.disconnect(); 
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});