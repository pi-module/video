/* From : http://talkerscode.com/webtricks/file-upload-progress-bar-using-jquery-and-php.php */
function upload_image() {
    var bar = $('#bar');
    var percent = $('#percent');
    $('#video').ajaxForm({
        beforeSubmit: function () {
            document.getElementById("progress-bar").style.display = "block";
            var percentVal = '0%';
            bar.width(percentVal)
            percent.html(percentVal);
        },

        uploadProgress: function (event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
        },

        success: function () {
            var percentVal = '100%';
            bar.width(percentVal)
            percent.html(percentVal);
        },

        complete: function (xhr) {
            if (xhr.responseText) {
                var result = jQuery.parseJSON(xhr.responseText);
                window.location.replace(result.url);
            }
        }
    });
}