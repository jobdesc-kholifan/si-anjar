const FormBankOptions = function(options) {
    this.selectBank = options !== undefined && options.selectBank !== undefined ? options.selectBank : null;
};

const BankData = function(value = {}) {
    this.bank_id = value.bank_id !== undefined ? value.bank_id : 0;
    this.bank = value.bank !== undefined ? value.bank : null;
    this.branch_name = value.branch_name !== undefined ? value.branch_name : null;
    this.no_rekening = value.no_rekening !== undefined ? value.no_rekening : null;
    this.atas_nama = value.atas_nama !== undefined ? value.atas_nama : null;
    this.is_deleted = false;
};

BankData.prototype.toJSON = function() {
    return {
        bank_id: this.bank_id,
        bank: this.bank,
        branch_name: this.branch_name,
        no_rekening: this.no_rekening,
        atas_nama: this.atas_nama,
    };
};

const FormBankItem = function(element, form) {
    this.$ = element;
    this.__form = form;

    this.selectBank = $(this.$.find('[data-action=select-bank]'));
    this.inputBranch = $(this.$.find('[data-action=input-branch]'));
    this.inputNoRek = $(this.$.find('[data-action=input-norek]'));
    this.inputAtasNama = $(this.$.find('[data-action=input-atasnama]'));
    this.buttonAdd = $(this.$.find('[data-action=button-add]'));
    this.buttonDelete = $(this.$.find('[data-action=button-remove]'));
};

FormBankItem.prototype.init = function() {
    FormComponents.select2.init(this.selectBank);

    this.inputBranch.on('keypress', () => this.$.data('data').branch_name = this.inputBranch.val());
    this.inputNoRek.on('keypress', () => this.$.data('data').no_rekening = this.inputNoRek.val());
    this.inputAtasNama.on('keypress', () => this.$.data('data').atas_nama = this.inputAtasNama.val());

    this.selectBank.change(() => {
        this.$.data('data').bank_id = this.selectBank.val();
        this.$.data('data').bank = {
            id: this.selectBank.val(),
            name: this.selectBank.find('option:selected').text(),
        };
    });

    this.buttonAdd.click(() => {
        this.__form.add();
        this.$.css({borderBottom: '1px solid #ccc'});
        this.buttonAdd.addClass('d-none');
    });
    this.buttonDelete.click(() => {
        if(this.__form.banks.length > 1) {
            this.__form.$.children().first().css({border: 'none'});
            this.$.remove();

            const bank = this.__form.banks.get(this.$.data('index'));
            if(bank.bank_id === 0)
                this.__form.banks.splice(this.$.data('index'), 1);

            else this.__form.banks[this.$.data('index')].is_deleted = true;

            this.__form.$.children().last().data('form').buttonAdd.removeClass('d-none');
        } else {
            this.selectBank.val(null).text(null);
            this.inputBranch.val(null);
            this.inputNoRek.val(null);
            this.inputAtasNama.val(null);
        }
    });
};

const FormBank = function(selector, options = {}) {
    this.$ = $(selector);
    this.options = new FormBankOptions(options);

    this.$formBank = $('<div>', {class: 'px-3 pb-3 mb-3'}).append(
        $('<div>', {class: 'form-group'}).append(
            $('<label>', {class: 'required'}).text('Bank'),
            $('<select>', {
                class: 'form-control',
                'data-toggle': 'select2',
                'data-url': this.options.selectBank,
                'data-action': 'select-bank',
            })
        ),
        $('<div>', {class: 'form-group'}).append(
            $('<label>', {class: 'required'}).text('Cabang'),
            $('<input>', {
                type: 'text',
                class: 'form-control',
                placeholder: 'Ketikan cabang disinis ...',
                maxLength: 100,
                'data-action': 'input-branch'
            })
        ),
        $('<div>', {class: 'form-group'}).append(
            $('<label>', {class: 'required'}).text('No. Rekening'),
            $('<input>', {
                type: 'text',
                class: 'form-control',
                placeholder: 'Ketikan no. rekening disinis ...',
                maxLength: 100,
                'data-action': 'input-norek'
            })
        ),
        $('<div>', {class: 'form-group'}).append(
            $('<label>', {class: 'required'}).text('Atas Nama'),
            $('<input>', {
                type: 'text',
                class: 'form-control',
                placeholder: 'Ketikan no. rekening disinis ...',
                maxLength: 100,
                'data-action': 'input-atasnama',
            })
        ),
        $('<div>', {class: 'text-right'}).append(
            $('<button>', {type: 'button', class: 'btn btn-outline-danger btn-sm mr-1', 'data-action': 'button-remove'}).append(
                $('<i>', {class: 'fa fa-trash'}),
                $('<span>', {class: 'ml-2'}).text('Hapus')
            ),
            $('<button>', {type: 'button', class: 'btn btn-outline-primary btn-sm', 'data-action': 'button-add'}).append(
                $('<i>', {class: 'fa fa-plus-circle'}),
                $('<span>', {class: 'ml-2'}).text('Tambah')
            )
        )
    );

    this.banks = [];
};

FormBank.prototype.add = function() {
    const $form = $(this.$formBank.clone());
    $form.data('form', new FormBankItem($form, this));
    $form.data('data', new BankData());
    $form.data('index', this.banks.length);

    this.$.append($form);

    $form.data('form').init();

    this.banks.push($form.data('data'));

    return $form;
};

FormBank.prototype.set = function(values) {
    let bankData = [];
    values.forEach(value => {
        const $form = this.add();
        $form.data('form').selectBank.append($('<option>', {value: value.bank_id}).text(value.bank.bank_name));
        $form.data('form').inputBranch.val(value.branch_name);
        $form.data('form').inputNoRek.val(value.no_rekening);
        $form.data('form').inputAtasNama.val(value.atas_nama);

        bankData.push(new BankData(value));
    });

    this.banks = bankData;
};

FormBank.prototype.toJSON = function() {
    let json = [];
    this.banks.forEach((bank) => json.push(bank.toJSON()));
    return json;
};

FormBank.prototype.toString = function() {

    return JSON.stringify(this.toJSON());
};
