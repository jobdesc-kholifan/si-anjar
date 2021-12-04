+function($) {
    const UploadImageConfig = function(options) {
        this.allowed = options !== undefined && options.allowed !== undefined ? options.allowed : [];
        this.multiple = options !== undefined && options.multiple !== undefined ? options.multiple : false;
        this.name = options !== undefined && options.name !== undefined ? options.name : null;
        this.files = options !== undefined && options.files !== undefined ? options.files : [];

        this.getMimeType = options !== undefined && options.getMimeType !== undefined ? options.getMimeType : (file) => file.mimetype;
        this.getThumbnail = options !== undefined && options.getThumbnail !== undefined ? options.getThumbnail : (file) => file.thumbnail;
        this.getImage = options !== undefined && options.getImage !== undefined ? options.getImage : (file) => file.image;
    };

    const FormUploadImage = function(selector, upload) {
        this.$ = $(selector);
        this.__upload = upload;

        this.button = $(this.$.find('[data-action=button]'));
        this.file = $(this.$.find('[data-action=file]'));
        this.actions = $(this.$.find('[data-action=actions]'));
        this.remove = $(this.$.find('[data-action=remove]'));
        this.preview = $(this.$.find('[data-action=preview]'));

        this.init();
    };

    FormUploadImage.prototype.init = function() {
        this.button.click(() => {
            this.file.click();
        });

        this.file.change(this.onChange.bind(this));
        this.remove.click(() => {
            this.$.remove();

            console.log(this.__upload.$wrapper.children().length);

            if(this.__upload.$wrapper.children().length === 0
                && !this.__upload.options.multiple)
                this.__upload.form();
        });
    };

    FormUploadImage.prototype.onChange = function(e) {
        const files = e.target.files;
        for(let i = 0; i < files.length; i++) {
            this.render(files[i]);
        }

        if(this.__upload.options.multiple)
            this.__upload.form();
    };

    FormUploadImage.prototype.render = function(file) {
        const $preview = $('<div>', {class: 'image-canvas'});
        if(file.type.indexOf('image') !== -1) {
            const imageReader = new FileReader();
            imageReader.readAsDataURL(file);
            imageReader.onloadend = function() {
                $preview.css({backgroundImage: `url(${this.result})`});
            };
        }

        this.$.append($preview);
        this.button.addClass('d-none');
        this.actions.removeClass('d-none');
    };

    const UploadImage = function(selector, options) {
        this.$ = $(selector);

        this.$wrapper = $('<div>', {class: 'wrapper-upload'});
        this.$.append(this.$wrapper);

        this.options = new UploadImageConfig(options);

        this.$form = $('<div>', {class: 'form-upload'}).append(
            $('<input>', {type: 'file', name: this.options.name, class: 'd-none', 'data-action': 'file'}),
            $('<div>', {class: 'image-canvas', 'data-action': 'button'}).append(
                $('<i>', {class: 'fa fa-camera'})
            ),
            $('<div>', {class: 'image-actions d-none', 'data-action': 'actions'}).append(
                $('<a>', {class: 'btn-image preview mr-1', 'data-action': 'preview'}).html(
                    $('<i>', {class: 'fa fa-eye'})
                ),
                $('<button>', {type: 'button', class: 'btn-image remove', 'data-action': 'remove'}).html("Hapus"),
            )
        );

        this.init();
    };

    UploadImage.prototype.form = function() {
        const $form = this.$form.clone();
        $form.data('form', new FormUploadImage($form, this));

        this.$wrapper.append($form);
    };

    UploadImage.prototype.init = function() {
        this.form();
    };

    $.fn.uploadImage = function(options) {
        let $this = $(this);
        let data = new UploadImage($this, options);
        $this.data('imageUpload', data);

        return data;
    };
}(jQuery);
