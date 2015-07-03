// Globals
var categories = [];
var view = 'explore';
switchView();

getCategories();

$('#search-button').on('click', searchButtonHandler);
$('.video-grid').on('click', videoItemHandler);
$('#add-video-button').on('click', addVideoToCategory);
$('#category-list').on('click', 'li', showCategory);
$('#switch').on('click', switchView);

// Hides the appropriate elements when switching views
// Latest video bar slides out.
function switchView()
{
    if (view == 'collection')
    {
        view = 'explore';
        updateLatestVideos();
        getVideosByQuery('gaming');

        // Hide collection view
        $('#collection-header').hide();
        $('.collection-view').hide();

        // Show explore view
        $('#explore-header').show();
        $('.explore-view').show();
        $('.latest-videos').animate({ 'width': '282px' }, 'slow');
    }
    else if (view == 'explore')
    {
        view = 'collection';
        getCategoryVideos('all');

        // Hide explore view
        $('#explore-header').hide();
        $('.explore-view').hide();
        $('.latest-videos').animate({ 'width': '0' }, 'slow');

        // Show collection view
        $('#collection-header').show();
        $('.collection-view').show();
    }
}

// Switches the videogrid to show the selected category's videos
function showCategory(e)
{
    getCategoryVideos(e.target.dataset.category);
}

// Gets the videos of the given category
function getCategoryVideos(categoryId)
{
    // Update title so it displays what category is being shown
    var categoryName = 'all categories';
    for (i = 0; i < categories.length; i++)
    {
        if (categories[i].id == categoryId)
        {
            categoryName = categories[i].name;
        }
    }
    $('#current-category').html(categoryName);

    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=category&category=' + categoryId
    }).done(function(response) {
        updateVideoGrid(response.data, $('#favorited-videos'), null, false);
    });
}

// Adds a video to a category in the database
function addVideoToCategory() {
    var video = $('#enlarged-video').prop('data-youtube-id');
    var category = $('#add-video-dropdown option:selected').prop('id');

    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=insert&type=relation&ytId=' + video + '&catId=' + category
    }).done(function () {
        $('#add-video-button').prop('value', 'Added');
        updateLatestVideos();
    });
}

// Updates the sidebar to show the latest 10 videos
function updateLatestVideos() {
    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=video&id=latest'
    }).done(function (response) {
        $('.latest-videos .video-grid').empty();
        for (i = 0; i < response.data.length; i++) {
            var video = response.data[i];
            $('<section>', {class: 'video-grid-item', 'data-youtube-id': video.youtubeId})
                .append($('<img>', {src: video.thumbnail, alt: video.title}))
                .append($('<h1>', {text: video.title.substring(0, 24) + '...'}))
                .appendTo($('.latest-videos .video-grid'));
        }
    });
}

// Gets a list with all the categories
function getCategories() {
    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=category&category=list'
    }).done(function (response) {
        categories = response.data;
        for (i = 0; i < categories.length; i++) {
            // Add to dropdown in explore view
            $('#add-video-dropdown').append($('<option>', {
                text: categories[i].name,
                id: categories[i].id
            }));

            // Add to listing in Category view
            $('#category-list').append($('<li>', {
                text: categories[i].name,
                'data-category': categories[i].id
            }));
        }
        getCategoryVideos('all');
    });

}

// Places a video in the theater if the source was an image or a heading
// If it was a load more button it will load more videos.
function videoItemHandler(e) {
    if (e.target.tagName == 'IMG' || e.target.tagName == 'H1')
        viewVideo(e.target.parentNode.getAttribute('data-youtube-id'));
    if (e.target.id == 'load-more-button')
        loadMoreVideos(e);
}

// Places a video in the theater
function viewVideo(youtubeId) {
    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=video&id=' + youtubeId
    }).done(function (response) {
        var video = response.data[0];
        var enlargedVideo = $('#enlarged-video');

        enlargedVideo.prop('data-youtube-id', youtubeId);
        enlargedVideo.find('h2').html(video.title);
        // enlargedVideo.find('p').html(video.description);
        $('#enlarged-video-iframe')
            .empty()
            .append('<iframe width="1024" height="576" src="' + video.embedUrl + '" allowfullscreen frameborder="0">');
    });
}

// Is activated when the searchbutton has been pressed
function searchButtonHandler() {
    var query = $('#search-text').prop('value');
    getVideosByQuery(query);
}

// Searches the query and updates the videogrid
function getVideosByQuery(query) {
    $('#searched-query').html(query);
    query.replace(' ', '+');

    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=search&q=' + query
    }).done(function (response) {
        updateVideoGrid(response.data, $('#search-results-videos'), response.nextPage, false);
    });
}

// Loads more videos and updates the videogrid
function loadMoreVideos(e) {
    e.target.setAttribute('disabled', 'disabled');

    var nextPage = e.target.dataset.token;
    var query = $('#searched-query').text();
    query.replace(' ', '+');

    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=search&q=' + query + '&np=' + nextPage
    }).done(function (response) {
        $('#' + e.target.parentNode.getAttribute('id')).remove();
        updateVideoGrid(response.data, $('#search-results-videos'), response.nextPage, false);
    });
}

/**
 * Takes a JSON decoded object from /api/v1/ and displays it as thumbnails
 * @param videolist
 * @param resultsBox
 * @param nextPage
 */
function updateVideoGrid(videolist, resultsBox, nextPage, reset) {
    if (reset === false)
        resultsBox.empty();

    var i = 0;
    var rows = [];
    while (videolist.length) {
        var video = videolist[0];
        if (!i && nextPage === null)
            viewVideo(video.youtubeId);

        if (i % 5 === 0) {
            rows[rows.length] = $('<div>', {class: 'video-grid-row'});
        }

        $('<section>', {class: 'video-grid-item', 'data-youtube-id': video.youtubeId})
            .append($('<img>', {src: video.thumbnail, alt: video.title}))
            .append($('<h1>', {text: video.title.substring(0, 24) + '...'}))
            .appendTo(rows[rows.length - 1]);

        i++;
        videolist.splice(0, 1);
    }

    for (i = 0; i < rows.length; i++) {
        resultsBox.append(rows[i]);
    }

    if (nextPage)
        $('<div>', {id: 'video-grid-more-row'})
            .append($('<input>', {
                type: 'button',
                value: 'Load More',
                id: 'load-more-button',
                'data-token': nextPage
            }))
            .appendTo(resultsBox);
}
