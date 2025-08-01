// Gutenberg blocks for Fuguku Gift Post Type

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;

// Gift Grid Block
registerBlockType('fuguku-gift/gift-grid', {
    title: __('Gift Grid', 'fuguku-gift'),
    description: __('Display a grid of gifts', 'fuguku-gift'),
    category: 'fuguku-gift',
    icon: 'grid-view',
    keywords: ['gift', 'grid', 'products'],
    attributes: {
        postsPerPage: {
            type: 'number',
            default: 12
        },
        category: {
            type: 'string',
            default: ''
        },
        showPrice: {
            type: 'boolean',
            default: true
        },
        showBrand: {
            type: 'boolean',
            default: true
        }
    },
    edit: function(props) {
        const { attributes, setAttributes } = props;
        
        return wp.element.createElement('div', {
            className: 'gift-grid-block-editor'
        }, [
            wp.element.createElement('h3', {}, __('Gift Grid', 'fuguku-gift')),
            wp.element.createElement('p', {}, __('This block will display a grid of gifts on the frontend.', 'fuguku-gift')),
            wp.element.createElement('div', {
                className: 'gift-grid-preview'
            }, [
                wp.element.createElement('div', { className: 'gift-card-preview' }),
                wp.element.createElement('div', { className: 'gift-card-preview' }),
                wp.element.createElement('div', { className: 'gift-card-preview' }),
                wp.element.createElement('div', { className: 'gift-card-preview' })
            ])
        ]);
    },
    save: function() {
        return null; // Dynamic block
    }
});

// Gift Slider Block
registerBlockType('fuguku-gift/gift-slider', {
    title: __('Gift Slider', 'fuguku-gift'),
    description: __('Display a slider of featured gifts', 'fuguku-gift'),
    category: 'fuguku-gift',
    icon: 'slides',
    keywords: ['gift', 'slider', 'featured'],
    attributes: {
        postsPerPage: {
            type: 'number',
            default: 6
        },
        showPrice: {
            type: 'boolean',
            default: true
        },
        showBrand: {
            type: 'boolean',
            default: true
        }
    },
    edit: function(props) {
        return wp.element.createElement('div', {
            className: 'gift-slider-block-editor'
        }, [
            wp.element.createElement('h3', {}, __('Gift Slider', 'fuguku-gift')),
            wp.element.createElement('p', {}, __('This block will display a slider of featured gifts on the frontend.', 'fuguku-gift')),
            wp.element.createElement('div', {
                className: 'gift-slider-preview'
            }, [
                wp.element.createElement('div', { className: 'gift-slide-preview' }),
                wp.element.createElement('div', { className: 'gift-slide-preview' }),
                wp.element.createElement('div', { className: 'gift-slide-preview' })
            ])
        ]);
    },
    save: function() {
        return null; // Dynamic block
    }
});
