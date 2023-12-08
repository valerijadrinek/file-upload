Dropzone.autoDiscover = false;

$(document).ready(function() {
    initializeDropzone();

   
}
);

function initializeDropzone() {
    var formElement = document.querySelector('.js-reference-dropzone');
    if (!formElement) {
        return;
    }
    var dropzone = new Dropzone(formElement, {
        paramName: 'reference',
        init: function() {
            this.on('error', function(file, data) {
                if (data.detail) {
                    this.emit('error', file, data.detail);
                }
            });
        }
    });
    
}