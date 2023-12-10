Dropzone.autoDiscover = false;

$(document).ready(function() {

    var referenceList = new ReferenceList($('.js-reference-list'));

    initializeDropzone(referenceList);

   
}
);

/**
 * @param {ReferenceList} referenceList
 */
function initializeDropzone(referenceList) {
    var formElement = document.querySelector('.js-reference-dropzone');
    if (!formElement) {
        return;
    }
    var dropzone = new Dropzone(formElement, {
        paramName: 'reference',
        init: function() {
            this.on('success', function(file, data) {
                referenceList.addReference(data);
            });
            this.on('error', function(file, data) {
                if (data.detail) {
                    this.emit('error', file, data.detail);
                }
            });
        }
    });
    
}

class ReferenceList
{
    constructor($element) {
        this.$element = $element;
        this.references = [];
        this.render();
        $.ajax({
            url: this.$element.data('url')
        }).then(data => {
            this.references = data;
            this.render();
        })
    }

    addReference(reference) {
        this.references.push(reference);
        this.render();
    }

    render() {
        const itemsHtml = this.references.map(reference => {
            return `
<li class="list-group-item d-flex justify-content-between align-items-center">
    ${reference.originalFilename}
    <span>
        <a href="/article/reference/${reference.id}/download"><span class="fa fa-download"></span></a>
    </span>
</li>
`
        });
        this.$element.html(itemsHtml.join(''));
    }
}