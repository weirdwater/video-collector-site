// Globals
var categories = [];

// Load initial data
updateLatestVideos();
getVideosByQuery('Gaming');
getCategories();


$('#search-button').on('click', searchButtonHandler);
$('.video-grid').on('click',  videoItemHandler);
$('#add-video-button').on('click', addVideoToCategory);

function addVideoToCategory()
{
    var video = $('#enlarged-video').prop('data-youtube-id');
    var category = $('#add-video-dropdown option:selected').prop('id');

    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=insert&type=relation&ytId=' + video + '&catId=' + category
    }).done( function() {
        $('#add-video-button').prop('value', 'Added');
        updateLatestVideos();
    });
}

function updateLatestVideos()
{
    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=video&id=latest'
    }).done(function(response) {
        $('.latest-videos .video-grid').empty();
        for (i = 0; i < response.data.length; i++)
        {
            var video = response.data[i];
            $('<section>', { class: 'video-grid-item', 'data-youtube-id': video.youtubeId})
                .append($('<img>', { src: video.thumbnail, alt: video.title }))
                .append($('<h1>', { text: video.title.substring(0,24) + '...' }))
                .appendTo($('.latest-videos .video-grid'));
        }
    });
}

function getCategories()
{
    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=category&category=list'
    }).done(function(response) {
        categories = response.data;
        for (i = 0; i < categories.length; i++)
        {
            $('#add-video-dropdown').append($('<option>', {
                text: categories[i].name,
                id: categories[i].id
            }));
        }
    });

}

function videoItemHandler(e)
{
    if (e.target.tagName == 'IMG' || e.target.tagName == 'H1')
        viewVideo(e.target.parentNode.getAttribute('data-youtube-id'));
    if (e.target.id == 'load-more-button')
        loadMoreVideos(e);
}

function viewVideo(youtubeId)
{
    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=video&id=' + youtubeId
    }).done( function(response) {
        var video = response.data[0];
        var enlargedVideo = $('#enlarged-video');

        enlargedVideo.prop('data-youtube-id', youtubeId);
        enlargedVideo.find('h1').html(video.title);
        // enlargedVideo.find('p').html(video.description);
        $('#enlarged-video-iframe')
            .empty()
            .append('<iframe width="560" height="315" src="' + video.embedUrl + '" allowfullscreen frameborder="0">');
    });
}

function searchButtonHandler()
{
    var query = $('#search-text').prop('value');
    getVideosByQuery(query);
}

function getVideoById(youtubeId)
{
    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=video&id=' + youtubeId
    }).done( function(response) {
        return response.data[0];
    });
}

function getVideosByQuery(query)
{
    $('#searched-query').html(query);
    query.replace(' ', '+');

    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=search&q=' + query
    }).done( function(response) {
        updateSearchResults(response, true);
    });
}

function loadMoreVideos(e)
{
    e.target.setAttribute('disabled', 'disabled');

    var nextPage = e.target.dataset.token;
    var query = $('#searched-query').text();
    query.replace(' ', '+');

    $.ajax({
        datatype: 'json',
        url: 'api/v1?action=search&q=' + query + '&np=' + nextPage
    }).done( function(response) {
        $('#' + e.target.parentNode.getAttribute('id')).remove();
        updateSearchResults(response, false);
    });
}

/**
 * Takes a JSON decoded object from /api/v1/ and displays it as thumbnails
 * @param response
 * @param reset
 */
function updateSearchResults(response, reset)
{
    var resultsBox = $('#search-results-videos');
    if (reset)
        resultsBox.empty();

    var i = 0;
    var rows = [];
    while (response.data.length)
    {
        var video = response.data[0];
        if (!i && reset)
            viewVideo(video.id);

        if (i%5 == 0)
        {
            rows[rows.length] = $('<div>', { class: 'video-grid-row' });
        }

        $('<section>', { class: 'video-grid-item', 'data-youtube-id': video.id})
            .append($('<img>', { src: video.thumbnail, alt: video.title }))
            .append($('<h1>', { text: video.title.substring(0,24) + '...' }))
            .appendTo(rows[rows.length - 1]);

        i++;
        response.data.splice(0, 1);
    }

    for (i = 0; i < rows.length; i++)
    {
        resultsBox.append(rows[i]);
    }
    $('<div>', { id: 'video-grid-more-row' })
        .append($('<input>', {
            type: 'button',
            value: 'Load More',
            id:'load-more-button',
            'data-token': response.nextPage }))
        .appendTo(resultsBox);
}
