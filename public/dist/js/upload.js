+function($) {
    const UploadConfig = function(options) {
        this.allowed = options !== undefined && options.allowed !== undefined ? options.allowed : [];
        this.multiple = options !== undefined && options.multiple !== undefined ? options.multiple : false;
        this.showFileName = options !== undefined && options.showFileName !== undefined ? options.showFileName : false;
        this.withDescription = options !== undefined && options.withDescription !== undefined ? options.withDescription : false;
        this.name = options !== undefined && options.name !== undefined ? options.name : null;
        this.files = options !== undefined && options.files !== undefined ? options.files : [];

        this.getMimeType = options !== undefined && options.getMimeType !== undefined ? options.getMimeType : (file) => file.mimetype;
        this.getThumbnail = options !== undefined && options.getThumbnail !== undefined ? options.getThumbnail : (file) => file.thumbnail;
        this.getPreview = options !== undefined && options.getPreview !== undefined ? options.getPreview : (file) => file.image;
        this.getDesc = options !== undefined && options.getDesc !== undefined ? options.getDesc : (file) => file.image;
    };

    const FormUpload = function(selector, upload) {
        this.$ = $(selector);
        this.__upload = upload;

        this.$form = $(this.$.find('.form-upload'));

        this.id = $(this.$.find('[data-action=id]'));
        this.deletedId = $(this.$.find('[data-action=deleted-id]'));
        this.button = $(this.$.find('[data-action=button]'));
        this.file = $(this.$.find('[data-action=file]'));
        this.actions = $(this.$.find('[data-action=actions]'));
        this.remove = $(this.$.find('[data-action=remove]'));
        this.preview = $(this.$.find('[data-action=preview]'));
        this.filename = $(this.$.find('[data-action=file-name]'));
        this.description = $(this.$.find('[data-action=description]'));
    };

    FormUpload.prototype.init = function() {
        this.id.val(0);
        this.button.click(() => {
            this.file.click();
        });

        this.file.attr('accept', this.__upload.options.allowed.join(","));
        this.file.change(this.onChange.bind(this));
        this.remove.click(() => {
            if(this.id.val() > 0) {
                this.$.addClass('d-none');
                this.deletedId.val(this.id.val())
            }

            else this.$.remove();

            if(this.__upload.$wrapper.children().length === 0
                && !this.__upload.options.multiple)
                this.__upload.add();
        });
    };

    FormUpload.prototype.onChange = function(e) {
        const files = e.target.files;
        for(let i = 0; i < files.length; i++) {
            this.render(files[i]);

            if(this.__upload.options.withDescription) {
                const $description = $(this.__upload.$description.clone());
                $description.attr('name', `${this.__upload.options.name.replace('[]', '')}_desc_create[]`);
                this.$.append($description);
            }
        }

        if(this.__upload.options.multiple)
            this.__upload.add();
    };

    FormUpload.prototype.render = function(file) {
        const $preview = $('<div>', {class: 'image-canvas'});
        if(file.type.indexOf('image') !== -1) {
            const imageReader = new FileReader();
            imageReader.readAsDataURL(file);
            imageReader.onloadend = function() {
                $preview.css({backgroundImage: `url(${this.result})`});
            };
        }

        this.$form.append($preview);
        this.button.addClass('d-none');
        this.actions.removeClass('d-none');

        this.filename.val(file.name);
    };

    FormUpload.prototype.renderImage = function(file) {
        const $preview = $('<div>', {class: 'image-canvas'});
        const imageURL = this.__upload.options.getPreview(file);
        $preview.css({backgroundImage: `url(${imageURL})`});

        this.$form.append($preview);

        this.button.addClass('d-none');
        this.actions.removeClass('d-none');
    };

    const Upload = function(selector, options) {
        this.$ = $(selector);

        this.$wrapper = $('<div>', {class: 'wrapper-upload'});
        this.$.append(this.$wrapper);

        this.options = new UploadConfig(options);

        this.$form = $('<div>').append(
            $('<div>', {class: 'mb-1 form-upload'}).append(
                $('<input>', {type: 'file', name: this.options.name, class: 'd-none', 'data-action': 'file'}),
                $('<div>', {class: 'image-canvas', 'data-action': 'button'}).append(
                    $('<i>', {class: 'fa fa-camera'})
                ),
                $('<div>', {class: 'image-actions d-none', 'data-action': 'actions'}).append(
                    $('<a>', {class: 'btn-image preview mr-1', 'data-action': 'preview'}).html(
                        $('<i>', {class: 'fa fa-eye'})
                    ),
                    $('<button>', {type: 'button', class: 'btn-image remove', 'data-action': 'remove'}).html("Hapus"),
                ),
                $('<input>', {type: 'hidden', name: `${this.options.name.replace('[]', '')}_id[]`, 'data-action': 'id'}),
                $('<input>', {type: 'hidden', name: `${this.options.name.replace('[]', '')}_deleted[]`, 'data-action': 'deleted-id'}),
            ),
        );
        this.$form.css({width: 150, marginRight: 10});

        this.$formFileName = $('<input>', {
            type: 'text',
            class: 'form-control mb-1',
            placeholder: this.options.allowed.join(","),
            disabled: true,
            'data-action': 'file-name',
        });

        this.$description = $('<textarea>', {
            class: 'form-control mb-1',
            placeholder: 'Keterangan',
            'data-action': 'description',
        });

        this.add();
    };

    Upload.prototype.add = function() {
        const $form = this.$form.clone();
        if(this.options.showFileName)
            $form.append(this.$formFileName.clone());

        this.$wrapper.append($form);

        const form = new FormUpload($form, this);
        form.init();

        $form.data('form', form);

        return $form;
    };

    Upload.prototype.set = function(document) {
        this.$wrapper.children().each((i, item) => {
            const $item = $(item);
            $item.remove();
        });

        let documents = Array.isArray(document) ? document : [document];
        documents.forEach(document => {
            const $form = this.add();
            $form.data('form').id.val(document.id);

            const mimeType = this.options.getMimeType(document);
            if(mimeType !== undefined) {
                if(mimeType.indexOf('image/') !== -1) {
                    $form.data('form').renderImage(document);
                }
            }

            if(this.options.showFileName)
                $form.data('form').$.append(this.$formFileName.clone());

            const $description = $(this.$description.clone());
            $description.attr('name', `${this.options.name.replace('[]', '')}_desc_update[]`);

            const description = this.options.getDesc(document);
            if(description !== undefined)
                $description.val(description);

            if(this.options.withDescription)
                $form.data('form').$.append($description);
        });

        if(this.options.multiple)
            this.add();
    }

    $.fn.upload = function(options) {
        let $this = $(this);
        let data = new Upload($this, options);
        $this.data('upload', data);

        return data;
    };
}(jQuery);
