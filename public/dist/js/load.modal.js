+function($) {

    const ModalOptions = function(selector, options) {
        this.title = options !== undefined && options.title !== undefined ? options.title : undefined;
        if(this.title === undefined && selector.hasModalData('title'))
            this.title = selector.modalData('title');

        this.url = options !== undefined && options.url !== undefined ? options.url : undefined;
        if(this.url === undefined && selector.hasModalData('url'))
            this.url = selector.modalData('url');

        this.method = options !== undefined && options.method !== undefined ? options.method : undefined;
        if(this.method === undefined && selector.hasModalData('method'))
            this.method = selector.modalData('method');

        this.data = options !== undefined ? options.data : {};
        this.dataType = options !== undefined && options.dataType !== undefined ? options.dataType : 'json';

        this.animateClass = options !== undefined && options.animateClass !== undefined ? options.animateClass : 'fade';
        if(this.animateClass === undefined && selector.hasModalData('animateClass'))
            this.animateClass = selector.modalData('animateClass');

        this.dialogClass = options !== undefined && options.dialogClass !== undefined ? options.dialogClass : null;
        if(this.dialogClass === undefined && selector.hasModalData('dialogClass'))
            this.dialogClass = selector.modalData('dialogClass');

        this.modalSize = options !== undefined && options.modalSize !== undefined ? options.modalSize : null;
        if(this.modalSize === undefined && selector.hasModalData('modalSize'))
            this.modalSize = selector.modalData('modalSize');

        this.sizeClass = options !== undefined && options.sizeClass !== undefined ? options.sizeClass : undefined;
        if(this.sizeClass === undefined && selector.hasModalData('sizeClass'))
            this.sizeClass = selector.modalData('sizeClass');

        this.backdrop = options !== undefined && options.backdrop !== undefined ? options.backdrop : true;
        if(selector.hasModalData('backdrop'))
            this.backdrop = selector.modalData('backdrop');

        this.keyboard = options !== undefined && options.keyboard !== undefined ? options.keyboard : true;
        if(selector.hasModalData('keyboard'))
            this.keyboard = selector.modalData('keyboard');

        this.show = options !== undefined && options.show !== undefined ? options.show : false;
        if(selector.hasModalData('show'))
            this.show = selector.modalData('show');

        this.closeButton = options !== undefined && options.closeButton !== undefined ? options.closeButton : true;
        if(selector.hasModalData('closeButton'))
            this.closeButton = selector.modalData('closeButton');

        this.onClose = options !== undefined && options.onClose !== undefined ? options.onClose : undefined;
        this.onShown = options !== undefined && options.onShown !== undefined ? options.onShown : undefined;
        this.onLoadComplete = options !== undefined && options.onLoadComplete !== undefined ? options.onLoadComplete : undefined;
        this.onLoadError = options !== undefined && options.onLoadError !== undefined ? options.onLoadError : undefined;
    };

    const FormSubmitOptions = function(selector, options) {
        this.method = selector.attr('method');
        if(options !== undefined && options.method !== undefined)
            this.method = options.method;

        this.url = selector.attr('action');
        if(options !== undefined && options.url !== undefined)
            this.url = options.url;

        this.data = options !== undefined && options.data !== undefined ? options.data : {};
        this.headers = options !== undefined && options.headers !== undefined ? options.headers : {};
        this.dataType = options !== undefined && options.dataType !== undefined ? options.dataType : 'json';
        this.contentType = options !== undefined && options.contentType ? options.contentType : false;
        this.processData = options !== undefined && options.processData ? options.processData : false;
        this.beforeSubmit = options !== undefined && options.beforeSubmit ? options.beforeSubmit : null;
        this.successCallback = options !== undefined && options.successCallback ? options.successCallback : () => {};
        this.errorCallback = options !== undefined && options.errorCallback ? options.errorCallback : () => {};
    };

    const ConfirmOptions = function(options) {
        this.title = options !== undefined && options.title !== undefined ? options.title : 'Konfirmasi';
        this.message = options !== undefined && options.message !== undefined ? options.message : 'Apakah anda yakin?';
        this.closeButton = options !== undefined && options.closeButton !== undefined ? options.closeButton : true;

        this.animateClass = options !== undefined && options.animateClass !== undefined ? options.animateClass : 'fade';
        this.dialogClass = options !== undefined && options.dialogClass !== undefined ? options.dialogClass : 'modal-dialog-centered modal-dialog-confirm';
        this.modalSize = options !== undefined && options.modalSize !== undefined ? options.modalSize : 'modal-sm';

        this.classPositiveButton = options !== undefined && options.classPositiveButton !== undefined ? options.classPositiveButton : 'btn btn-primary btn-xs';
        this.classNegativeButton = options !== undefined && options.classNegativeButton !== undefined ? options.classNegativeButton : 'btn btn-danger btn-xs';

        this.onClickPositiveButton = options !== undefined && options.onClickPositiveButton !== undefined ? options.onClickPositiveButton : null;
        this.onClickNegativeButton = options !== undefined && options.onClickNegativeButton !== undefined ? options.onClickNegativeButton : null;

        this.positiveButton = options !== undefined && options.positiveButton !== undefined ? options.positiveButton : 'Yakin';
        this.negativeButton = options !== undefined && options.negativeButton !== undefined ? options.negativeButton : 'Tidak';

        this.onShow = options !== undefined && options.onShow !== undefined ? options.onShow : null;
        this.onClose = options !== undefined && options.onClose !== undefined ? options.onClose : null;

        this.onChange = options !== undefined && options.onChange !== undefined ? options.onChange : null;
    };

    const LoadingOptions = function(options) {
        this.animateClass = options !== undefined && options.animateClass !== undefined ? options.animateClass : 'fade';
        this.dialogClass = options !== undefined && options.dialogClass !== undefined ? options.dialogClass : 'modal-dialog-centered modal-dialog-confirm';
        this.modalSize = options !== undefined && options.modalSize !== undefined ? options.modalSize : 'modal-sm';

        this.onShow = options !== undefined && options.onShow !== undefined ? options.onShow : null;
        this.onClose = options !== undefined && options.onClose !== undefined ? options.onClose : null;

        this.message = options !== undefined && options.message !== undefined ? options.message : 'Memproses ...';
    };

    /**
     * @param selector jQuery
     * @param options ModalOptions|ConfirmOptions
     * */
    const ModalContext = function(selector, options) {
        this.$ = selector;
        this.options = options;

        this.classes = {
            modal: 'modal',
            dialog: 'modal-dialog',
            content: 'modal-content',
            header: 'modal-header',
            headerTitle: 'card-title',
            body: 'modal-body',
            footer: 'modal-footer',
            buttonClose: 'close',
        };

        this.selector = {
            modal: `[class=${this.classes.modal}]`,
            dialog: `[class=${this.classes.dialog}]`,
            content: `[class=${this.classes.content}]`,
            header: `[class=${this.classes.header}]`,
            body: `[class=${this.classes.body}]`,
            footer: `[class=${this.classes.footer}]`,
            actions: {
                dismiss: '[data-dismiss=modal]'
            }
        };
    };

    ModalContext.prototype.build = function() {
        this.modal = this.$;
        if(!this.modal.hasClass(this.classes.modal)) {
            this.id = `__${this.$.find(this.selector.modal).length}_modal__`;

            this.$.append($('<div>', {class: this.classes.modal, id: this.id})
                .addClass(this.options.animateClass)
            );
            this.modal = this.$.find(`#${this.id}`);
        }

        if(this.modal.find(this.selector.dialog).length === 0)
            this.modal.append($('<div>', {class: this.classes.dialog}));

        this.dialog = this.modal.find(this.selector.dialog);
        this.dialog.addClass(this.options.modalSize);
        this.dialog.addClass(this.options.dialogClass);

        if(this.dialog.find(this.selector.content).length === 0)
            this.dialog.append($('<div>', {class: this.classes.content}));

        this.content = this.dialog.find(this.selector.content);
    };

    ModalContext.prototype.buildContentHeader = function(title) {
        if(this.content.find(this.selector.header).length === 0)
            this.content.append($('<div>', {class: this.classes.header}));

        this.header = this.content.find(this.selector.header);

        if(title !== undefined)
            this.header.append($('<h3>', {class: 'card-title'}).html(title));

        if(this.options.closeButton)
            this.header.append($('<span>', {class: this.classes.buttonClose, 'data-dismiss': 'modal'}).html('&times;'));

        return this;
    };

    ModalContext.prototype.buildContentBody = function(element) {
        if(this.content.find(this.selector.body).length === 0)
            this.content.append($('<div>', {class: this.classes.body}));

        this.body = this.content.find(this.selector.body);

        if(element !== undefined)
            this.body.append(element);

        return this;
    };

    ModalContext.prototype.buildContentFooter = function() {
        if(this.content.find(this.selector.footer).length === 0)
            this.content.append($('<div>', {class: this.classes.footer}));

        this.footer = this.content.find(this.selector.footer);

        return this;
    };

    ModalContext.prototype.buildFromResponse = function(res) {
        if(!res.hasOwnProperty('content')) {
            this.buildContentHeader();
            this.buildContentBody();

            this.header.append($('<h3>', {class: this.classes.headerTitle}).text(res.title));

            if(this.options.closeButton)
                this.header.append($('<span>', {class: this.classes.buttonClose, 'data-dismiss': 'modal'}).html('&times;'));

            this.body.html(res.body);
        }

        else {
            this.content.html(res.content);
        }
    };

    ModalContext.prototype.buildConfirm = function() {

        this.buildContentHeader();
        this.buildContentBody();
        this.buildContentFooter();

        this.header.append($('<h3>', {class: this.classes.headerTitle}).text(this.options.title));

        if(this.options.closeButton)
            this.header.append($('<span>', {class: this.classes.buttonClose, 'data-dismiss': 'modal'}).html('&times;'));

        this.body.html(this.options.message);

        this.positiveButton = $('<button>', {
            type: 'button',
            class: this.options.classPositiveButton,
        }).html(this.options.positiveButton);

        this.negativeButton = $('<button>', {
            type: 'button',
            class: this.options.classNegativeButton,
        }).html(this.options.negativeButton);

        this.footer.append(this.negativeButton, this.positiveButton);
    };

    ModalContext.prototype.buildLoading = function() {
        this.buildContentBody();

        this.body.html($('<div>', {class: 'text-center'}).html(this.options.message));
    };

    ModalContext.prototype.onCloseListener = function() {
        this.modal.on('hidden.bs.modal', () => {
            if(this.options.onClose !== undefined)
                this.options.onClose();

            this.modal.data('loaded', false);
            this.modal.remove();
        });
    };

    ModalContext.prototype.onShownListener = function() {
        this.modal.on('shown.bs.modal', () => {
            if(this.options.onShown !== undefined)
                this.options.onShown();
        });
    };

    const FormSubmit = function(selector) {
        this.$ = $(selector);
    };

    FormSubmit.prototype.find = function(selector, check) {
        return this.$.find(selector, check);
    }

    FormSubmit.prototype.reset = function() {
        if(this.$.get(0) !== undefined)
            this.$.get(0).reset();
    };

    FormSubmit.prototype.setDisabled = function(disabled) {
        this.$.data('stateDisabled', disabled);
        this.$.find('input, select, textarea, button').each((i, item) => {
            if(disabled)
                $(item).attr('disabled', 'disabled');

            else $(item).removeAttr('disabled');
        });
    };

    FormSubmit.prototype.isDisabled = function() {
        return this.$.data('stateDisabled') !== undefined ? this.$.data('stateDisabled') : false;
    };

    FormSubmit.prototype.submit = function(options) {
        let processing = false;

        return this.$.submit((e) => {
            e.preventDefault();
            e.stopPropagation();

            const submitOpt = new FormSubmitOptions(this.$, options);
            const disabled = $(this).data('stateDisabled');

            if(!disabled && !processing) {
                processing = true;
                setTimeout(() => processing = false, 1000);

                let formData = new FormData(this.$.get(0));
                if(typeof submitOpt.data === 'object') {
                    Object.keys(submitOpt.data).forEach((key) => {
                        formData.append(key, submitOpt.data[key]);
                    });
                }

                else if(typeof  submitOpt.data === 'function') {
                    const resultData = submitOpt.data({});
                    Object.keys(resultData).forEach((key) => {
                        formData.append(key, resultData[key]);
                    });
                }

                if(!submitOpt.headers.hasOwnProperty('X-CSRF-TOKEN'))
                    submitOpt.headers['X-CSRF-TOKEN'] = $('meta[name=csrf-token]').attr('content');

                let next = true;
                if(submitOpt.beforeSubmit !== null)
                    next = submitOpt.beforeSubmit(this);

                if(next === undefined || next) {
                    return $.ajax({
                        url: submitOpt.url,
                        type: submitOpt.method,
                        data: formData,
                        dataType: submitOpt.dataType,
                        headers: submitOpt.headers,
                        contentType: submitOpt.contentType,
                        processData: submitOpt.processData,
                        success: (res) => submitOpt.successCallback(res, this),
                        error: (xhr) => submitOpt.errorCallback(xhr, this),
                    });
                }

                return null;
            }
        });
    };

    FormSubmit.prototype.action = function(url) {
        if(url !== undefined) {
            this.$.attr('action', url);
            return this;
        }

        return this.$.attr('action');
    };

    FormSubmit.prototype.method = function(method) {
        if(method !== undefined) {
            this.$.attr('method', method);
            return this;
        }

        return this.$.attr('method');
    };

    FormSubmit.prototype.put = function(action) {
        this.action(action);
        this.method('put');

        return this;
    };

    FormSubmit.prototype.post = function(action) {
        this.action(action);
        this.method('post');

        return this;
    };

    const LoadModal = function (selector, options) {

        /**
         * @var jQuery
         * */
        this.$ = selector;
        this.options = new ModalOptions(selector, options);
        this.context = new ModalContext(selector, this.options);

        if(this.options.show)
            this.open();

        this.find = function(selector, check) {
            return this.context.modal.find(selector, check);
        };

        this.form = function(options)  {
            return new FormSubmit(this.find('form'), options);
        };
    };

    LoadModal.prototype.getContext = function() {
        this.context.build();

        this.context.modal.modal('show');

        return this.context;
    }

    LoadModal.prototype.open = function(options) {
        this.context.build();

        this.context.modal.modal('show');

        if((this.context.modal.data('loaded') === undefined
                || this.context.modal.data('loaded') === false)
            && this.options.url !== undefined)
            this.load(options);

        this.context.onCloseListener();
        this.context.onShownListener();
    };

    LoadModal.prototype.close = function() {
        this.context.modal.modal('hide');
        this.context.onCloseListener();
    };

    LoadModal.prototype.load = function(options) {
        $.ajax({
            url: this.options.url,
            type: this.options.method,
            data: this.options.data,
            dataType: this.options.dataType,
            success: (res, type, xhr) => {
                if(xhr.status === 200) {
                    if(this.options.dataType === 'json')
                        this.context.buildFromResponse(res);

                    else this.context.content.html(res);

                    this.$.data('loaded', true);
                    if(this.options.onLoadComplete !== undefined)
                        this.options.onLoadComplete(res, this);

                    if(options !== undefined && options.success !== undefined)
                        options.success(res, this, type, xhr);
                }

                else {
                    if(this.options.onLoadError !== undefined)
                        this.options.onLoadError(res, this);

                    setTimeout(() => {
                        this.close();

                        AlertNotif.adminlte.error(DBMessage.ERROR_SYSTEM_MESSAGE, {
                            title: DBMessage.ERROR_SYSTEM_TITLE,
                            autohide: false,
                            delay: null
                        });
                        console.error(`Failed to request modal form: ${res.message}`);

                        this.$.data('loaded', false);
                    }, 400);
                }
            },
            error: (xhr, type, message) => {
                setTimeout(() => {
                    this.close();

                    AlertNotif.adminlte.error(DBMessage.ERROR_SYSTEM_MESSAGE, {
                        title: DBMessage.ERROR_SYSTEM_TITLE,
                        autohide: false,
                        delay: null
                    });
                    console.error(`Failed to request modal form: ${message}`);

                    this.$.data('loaded', false);
                }, 400);
            }
        });

        return this;
    };

    const ConfirmModal = function(selector, options) {
        /**
         * @var jQuery
         * */
        this.$ = selector;
        this.options = new ConfirmOptions(options);
        this.context = new ModalContext(selector, this.options);

        this.build();
    };

    ConfirmModal.prototype.build = function() {
        this.context.build();
        this.context.buildConfirm();

        this.context.positiveButton.click((e) => {
            e.stopPropagation();
            e.preventDefault();

            if(this.options.onChange !== null)
                this.options.onChange(true, this);

            if(this.options.onClickPositiveButton !== null)
                this.options.onClickPositiveButton(this);
        });

        this.context.negativeButton.click((e) => {
            e.stopPropagation();
            e.preventDefault();

            if(this.options.onChange !== null)
                this.options.onChange(false, this);

            if(this.options.onClickNegativeButton !== null)
                this.options.onClickNegativeButton(this);

            if(this.options.onClickNegativeButton === null && this.options.onChange === null) {
                this.close();
            }
        });
    };

    ConfirmModal.prototype.disabled = function(disabled) {
        if(disabled) {
            this.context.positiveButton.attr('disabled', 'disabled');
            this.context.negativeButton.attr('disabled', 'disabled');
        }

        else {
            this.context.positiveButton.removeAttr('disabled');
            this.context.negativeButton.removeAttr('disabled');
        }
    };

    ConfirmModal.prototype.show = function() {
        this.context.modal.modal('show');
        if(this.options.onShow !== null)
            this.options.onShow();
    };

    ConfirmModal.prototype.close = function() {
        this.context.modal.modal('hide');
        if(this.options.onClose !== null)
            this.options.onClose();
    };

    const LoadingModal = function(selector, options) {
        /**
         * @var jQuery
         * */
        this.$ = selector;
        this.options = new LoadingOptions(options);
        this.context = new ModalContext(selector, this.options);

        this.build();
    };

    LoadingModal.prototype.build = function() {
        this.context.build();
        this.context.buildLoading();
    };

    LoadingModal.prototype.show = function() {
        this.context.modal.modal('show');
        if(this.options.onShow !== null)
            this.options.onShow();
    };

    LoadingModal.prototype.close = function() {
        setTimeout(() => {
            this.context.modal.modal('hide');
            if(this.options.onClose !== null)
                this.options.onClose();
        }, 500);
    };

    $.fn.loadModal = function(options) {
        let $this = $(this);
        let data = new LoadModal($this, options);
        $this.data('app.loadModal', data);

        return data;
    };


    $.createModal = function(options) {
        let $this = $('<div>');
        $('body').append($this);

        let data = new LoadModal($this, options);
        $this.data('app.loadModal', data);

        return data;
    };

    $.createLoading = function(options) {
        let $this = $('<div>');
        $('body').append($this);

        let data = new LoadingModal($this, options);
        $this.data('app.loadingModal');

        return data;
    };

    $.confirmModal = function(options) {
        let $this = $('<div>');
        $('body').append($this);

        let data = new ConfirmModal($this, options);
        $this.data('app.confirmModal');

        return data;
    };

    $.fn.close = function() {
        let data = $(this).data('app.loadModal');
        if(data !== undefined)
            data.close();
    };

    $.fn.open = function() {
        let data = $(this).data('app.loadModal');
        if(data !== undefined)
            data.open();
    };

    $.fn.hasModalData = function(data) {
        let resultData = $(this).data(`modal-${data}`);
        return resultData !== undefined && resultData !== false;
    };

    $.fn.modalData = function(data) {
        return $(this).data(`modal-${data}`);
    };

    $.fn.formSubmit = function(options) {
        let $this = $(this);
        let data = new FormSubmit(this);
        $this.data('app.formSubmit', data);

        return data.submit(options);
    }
}(jQuery);
