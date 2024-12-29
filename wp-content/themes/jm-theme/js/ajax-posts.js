jQuery(document).ready(function ($) {
    let page = ajax_posts_params.initial_page; // Start with the initial page
    const postsContainer = $('#posts-container');
    const loadMoreButton = $('#load-more-posts');

    // Function to fetch posts
    function fetchPosts() {
        $.ajax({
            url: ajax_posts_params.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_posts',
                page: page,
                security: ajax_posts_params.nonce,
            },
            beforeSend: function () {
                if (page === 1) {
                    postsContainer.html('<p>Loading posts...</p>'); // Show loading message initially
                } else {
                    loadMoreButton.text('Loading...');
                }
            },
            success: function (response) {
                if (response) {
                    if (page === 1) {
                        postsContainer.html(response); // Replace content on initial load
                    } else {
                        postsContainer.append(response); // Append content for subsequent loads
                    }
                    page++;
                    loadMoreButton.text('Load More');
                } else {
                    if (page === 1) {
                        postsContainer.html('<p>No posts found.</p>');
                    } else {
                        loadMoreButton.text('No More Posts').prop('disabled', true);
                    }
                }
            },
            error: function () {
                if (page === 1) {
                    postsContainer.html('<p>Error loading posts.</p>');
                } else {
                    loadMoreButton.text('Error');
                }
            },
        });
    }

    // Initial posts load
    fetchPosts();

    // Load more posts on button click
    loadMoreButton.on('click', fetchPosts);
});
