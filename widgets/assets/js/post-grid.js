    jQuery(document).ready(function ($) {


        $('.post-grid-load-more').on('click', function () {
            alert('Post Grid JS Loaded!');
            const button = $(this);
            const wrapper = button.closest('.custom-post-grid-wrapper');
            const settings = wrapper.data('settings');
            const maxPages = button.data('max-pages');

            let page = parseInt(wrapper.attr('data-page')) + 1;

            if (page > maxPages) {
                button.text('No more posts').prop('disabled', true);
                return;
            }

            $.ajax({
                url: custom_post_grid_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'custom_post_grid_load_more',
                    page: page,
                    settings: settings,
                },
                beforeSend: function () {
                    button.text('Loading...');
                },
                success: function (response) {
                    if (response.success && response.data && response.data.html) {
                        wrapper.find('.custom-post-grid').append(response.data.html);
                        wrapper.attr('data-page', page); // Update page attribute
                        button.text('Load More');
                    } else {
                        button.text('No more posts').prop('disabled', true);
                    }
                },
            });
        });
    });
