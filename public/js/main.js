$(document).ready(function() {
    $('#upload-btn').on('click', function fileUpload(e) {
        let file     = $('#csv-file').prop('files')[0],
            type     = $('#csv-type').val(),
            data     = new FormData(),
            $message = $('#message');

        $message.html("Uploading...").addClass('active').removeClass('error');

        if (!file) {
            $message.html("Please select a file.").addClass('error');
        } else if (!file.type.match('csv.*')) {
            $message.addClass('error');
            $message.html("Please choose a csv file.");
        } else if (file.size > 1048576) {
            $message.addClass('error');
            $message.html("Sorry, your file is too large (>1 MB).");
        } else {
            data.append('file', file); // Append the file to the data object
            data.append('table', type); // Append the type, (name of table), of import to the data object
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'default/post', true);
            xhr.send(data);
            xhr.onload = function () {
                let response = JSON.parse(xhr.responseText);

                if (!response.message) {
                    response.message = 'There was an error. Please check that your file structure is valid.'
                }

                if (xhr.status === 200 && response.status !== 'ok') {
                    $message.addClass('error');
                    $message.html(response.message);
                } else if (response.status === 'ext_error') {
                    $message.addClass('error');
                    $message.html("Please choose a csv file.");
                } else {
                    $message.removeClass('error');
                    $message.html(response.message);
                }
            };
        }
    });
});