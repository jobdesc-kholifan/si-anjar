const FormPICOptions = function(options) {};

const DataPIC = function(value = {}) {
    this.id = value.id !== undefined ? value.id : 0;
    this.pic_name = value.pic_name !== undefined ? value.pic_name : null;
    this.phone_number = value.phone_number !== undefined ? value.phone_number : null;
    this.address = value.address !== undefined ? value.address : null;
    this.deleted = false;
};

const FormPICItem = function(element, form) {
    this.$ = element;
    this.__form = form;

    this.inputName = $(this.$.find('[data-action=pic-name]'));
    this.inputPhone = $(this.$.find('[data-action=phone-number]'));
    this.inputAddress = $(this.$.find('[data-action=address]'));
    this.buttonDelete = $(this.$.find('[data-action=btn-delete]'));
    this.buttonAdd = $(this.$.find('[data-action=btn-add]'));
};

FormPICItem.prototype.init = function() {
    FormComponents.select2.init();

    this.inputName.on('keypress, keydown, keyup', () => this.$.data('data').pic_name = this.inputName.val());
    this.inputPhone.on('keypress, keydown, keyup', () => this.$.data('data').phone_number = this.inputPhone.val());
    this.inputAddress.on('keypress, keydown, keyup', () => this.$.data('data').address = this.inputAddress.val());

    this.buttonDelete.click(() => {
        const children = this.__form.$.children();
        if(children.length > 1) {
            if(this.$.data('data').id !== 0) {
                this.$.addClass('d-none');
                this.$.data('data').deleted = true;
            } else this.$.remove();

            this.buttonAdd.addClass('d-none');
        } else {
            this.inputName.val(null);
            this.inputPhone.val(null);
            this.inputAddress.val(null);

            if(data.id !== 0) this.$.data('data').deleted = true;
        }

        this.__form.$.children().last().data('form').buttonAdd.removeClass('d-none');
    });

    this.buttonAdd.click(() => {
        this.__form.add();
        this.buttonAdd.addClass('d-none');
    });
};

FormPICItem.prototype.initData = function() {
    const data = this.$.data('data');

    this.inputName.val(data.pic_name);
    this.inputPhone.val(data.phone_number);
    this.inputAddress.val(data.address);
};

FormPICItem.prototype.toJSON = function() {
    const data = this.$.data('data');
    return {
        id: data.id,
        pic_name: data.pic_name,
        phone_number: data.phone_number,
        address: data.address,
        deleted: data.deleted,
    };
};

const FormPIC = function(selector, options = {}) {
    this.$ = $(selector);
    this.options = new FormPICOptions(options);

    this.$formPIC = $('<div>', {class: 'pt-3'}).append(
        $('<div>', {class: 'form-group'}).append(
            $('<div>', {class: 'row justify-content-center align-items-center'}).append(
                $('<label>', {class: 'col-12 col-sm-2 text-left text-sm-right required'}).text('Nama PIC'),
                $('<div>', {class: 'col-12 col-sm-10'}).append(
                    $('<input>', {
                        type: 'text',
                        class: 'form-control',
                        placeholder: 'Masukan nama pic disini ...',
                        maxLength: 100,
                        'data-action': 'pic-name'
                    })
                ),
            )
        ),
        $('<div>', {class: 'form-group'}).append(
            $('<div>', {class: 'row justify-content-center align-items-center'}).append(
                $('<label>', {class: 'col-12 col-sm-2 text-left text-sm-right required'}).text('No. HP'),
                $('<div>', {class: 'col-12 col-sm-10'}).append(
                    $('<input>', {
                        type: 'text',
                        class: 'form-control',
                        placeholder: 'Masukan no hp disini ...',
                        maxLength: 15,
                        'data-action': 'phone-number',
                        'onkeydown': 'return Helpers.isNumberKey(event);'
                    })
                ),
            )
        ),
        $('<div>', {class: 'form-group'}).append(
            $('<div>', {class: 'row justify-content-center align-items-start'}).append(
                $('<label>', {class: 'col-12 col-sm-2 text-left text-sm-right mt-2 required'}).text('Alamat'),
                $('<div>', {class: 'col-12 col-sm-10'}).append(
                    $('<textarea>', {
                        class: 'form-control',
                        placeholder: 'Masukan alamat disini ...',
                        rows: 5,
                        'data-action': 'address'
                    })
                )
            )
        ),
        $('<div>', {class: 'form-group pb-3 text-right border-bottom'}).append(
            $('<button>', {type: 'button', class: 'btn btn-outline-danger btn-sm mr-1', 'data-action': 'btn-delete'}).append(
                $('<i>', {class: 'fa fa-trash'}),
                $('<span>', {class: 'ml-2'}).text('Hapus'),
            ),
            $('<button>', {type: 'button', class: 'btn btn-outline-primary btn-sm', 'data-action': 'btn-add'}).append(
                $('<i>', {class: 'fa fa-plus-circle'}),
                $('<span>', {class: 'ml-2'}).text('Tambah')
            )
        )
    );
};

FormPIC.prototype.add = function() {
    const $form = $(this.$formPIC.clone());
    const form = new FormPICItem($form, this);

    $form.data('form', form);
    $form.data('data', new DataPIC());

    form.init();

    this.$.append($form);

    return $form;
};

FormPIC.prototype.set = function(values) {
    values.forEach(value => {
        const $form = this.add();
        $form.data('data', new DataPIC(value));
        $form.data('form').initData();
    });
};

FormPIC.prototype.toJSON = function() {
    const children = this.$.children();

    let json = [];
    children.each((i, item) => {
        const $item = $(item);
        json.push($item.data('form').toJSON());
    });

    return json;
};

FormPIC.prototype.toString = function() {
    return JSON.stringify(this.toJSON());
};
