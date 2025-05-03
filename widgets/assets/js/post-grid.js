jQuery(document).ready(function ($) {
    $('.post-grid-load-more').on('click', function () {
        const button = $(this);
        const wrapper = button.closest('.custom-post-grid-wrapper');
        const settings = wrapper.data('settings');
        let page = parseInt(wrapper.attr('data-page')) + 1;

        $.ajax({
            url: custom_post_grid_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'custom_post_grid_load_more',
                page: page,
                settings: settings,
            },
            beforeSend: function () {
                button.text('Loading...').prop('disabled', true);
            },
            success: function (response) {
                if (response.success && response.data && response.data.html) {
                    wrapper.find('.custom-post-grid').append(response.data.html);
                    wrapper.attr('data-page', page);
                    
           
                    const maxPages = response.data.max_pages || button.data('max-pages');
                    button.data('max-pages', maxPages);
                    
                    if (page >= maxPages) {
                        button.text('No more posts').prop('disabled', true);
                    } else {
                        button.text('Load More').prop('disabled', false);
                    }
                } else {
                    button.text('No more posts').prop('disabled', true);
                }
            },
            error: function () {
                button.text('Error loading posts').prop('disabled', false);
            }
        });
    });
});