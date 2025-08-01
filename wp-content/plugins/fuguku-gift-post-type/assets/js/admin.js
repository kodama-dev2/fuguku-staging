jQuery(document).ready(function($) {
    // Gallery functionality
    $('#add-gallery-images').on('click', function(e) {
        e.preventDefault();
        
        var frame = wp.media({
            title: 'Select Gallery Images',
            button: {
                text: 'Add to Gallery'
            },
            multiple: true
        });
        
        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();
            var galleryInput = $('#gift_gallery');
            var preview = $('#gift-gallery-preview');
            var currentImages = galleryInput.val() ? galleryInput.val().split(',') : [];
            
            attachments.forEach(function(attachment) {
                if (currentImages.indexOf(attachment.id.toString()) === -1) {
                    currentImages.push(attachment.id);
                    
                    var imageHtml = '<div class="gallery-image" data-id="' + attachment.id + '">' +
                        '<img src="' + attachment.sizes.thumbnail.url + '" alt="" />' +
                        '<button type="button" class="remove-image">×</button>' +
                        '</div>';
                    
                    preview.append(imageHtml);
                }
            });
            
            galleryInput.val(currentImages.join(','));
        });
        
        frame.open();
    });
    
    // Remove gallery image
    $(document).on('click', '.remove-image', function() {
        var imageDiv = $(this).closest('.gallery-image');
        var imageId = imageDiv.data('id');
        var galleryInput = $('#gift_gallery');
        var currentImages = galleryInput.val() ? galleryInput.val().split(',') : [];
        
        // Remove from array
        var index = currentImages.indexOf(imageId.toString());
        if (index > -1) {
            currentImages.splice(index, 1);
        }
        
        galleryInput.val(currentImages.join(','));
        imageDiv.remove();
    });
    
    // Price formatting
    $('#gift_price').on('blur', function() {
        var price = $(this).val();
        if (price && !price.includes('$')) {
            $(this).val('$' + price);
        }
    });
    
    // Auto-generate SKU
    $('#gift_brand, #title').on('blur', function() {
        var sku = $('#gift_sku').val();
        if (!sku) {
            var brand = $('#gift_brand').val();
            var title = $('#title').val();
            if (brand && title) {
                var autoSku = brand.substring(0, 3).toUpperCase() + '-' + 
                             title.substring(0, 5).toUpperCase().replace(/\s+/g, '');
                $('#gift_sku').val(autoSku);
            }
        }
    });
    
    // Featured gift toggle
    $('#gift_featured').on('change', function() {
        if ($(this).is(':checked')) {
            // Show confirmation
            if (!confirm('Mark this gift as featured? Featured gifts will appear in special sections.')) {
                $(this).prop('checked', false);
            }
        }
    });
    
    // Form validation
    $('form#post').on('submit', function(e) {
        var price = $('#gift_price').val();
        var brand = $('#gift_brand').val();
        
        if (!price) {
            alert('Please enter a price for the gift.');
            $('#gift_price').focus();
            e.preventDefault();
            return false;
        }
        
        if (!brand) {
            alert('Please enter a brand for the gift.');
            $('#gift_brand').focus();
            e.preventDefault();
            return false;
        }
    });
});
