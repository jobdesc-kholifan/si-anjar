String.prototype.format = function () {
    let a = this;
    if(typeof arguments[0] !== 'object') {
        Object.keys(arguments).forEach((key) => {
            a = a.replace(new RegExp("\\{" + key + "\\}", 'g'), arguments[key]);
        });
    }

    else {
        Object.keys(arguments[0]).forEach((key) => {
            a = a.replace(new RegExp("\\{" + key + "\\}", 'g'), arguments[0][key]);
        });
    }
    return a
};

String.prototype.route = function () {
    let a = this;
    if(typeof arguments[0] !== 'object') {
        Object.keys(arguments).forEach((key) => {
            a = a.replace(new RegExp("\\__" + key + "\\__", 'g'), arguments[key]);
        });
    }

    else {
        Object.keys(arguments[0]).forEach((key) => {
            a = a.replace(new RegExp("\\__" + key + "\\__", 'g'), arguments[0][key]);
        });
    }
    return a
};

const DBConfig = {
    DATATABLES_DOM: "<'row no-gutters align-items-center'<'col-6 d-none d-sm-block'l><'col-12 col-sm-6'f><'table-responsive't><'col-12 col-sm-6'i><'col-12 col-sm-6'p>>",
};

const DBMessage = {
    ERROR_NETWORK_TITLE: "Gagal menghubungkan ke server",
    ERROR_NETWORK_MESSAGE: "Diperkirakan terdapat kesalahan pada server atau terdapat kendala jaringan. Periksa kembali jaringan anda, pastikan terhubung dengan internet",
    ERROR_SYSTEM_TITLE: "Terdeteksi kejanggalan pada sistem",
    ERROR_SYSTEM_MESSAGE: "Demi keamanan, akan terdapat beberapa fitur yang tidak dapat berjalan dengan benar. Segera hubungi admin untuk informasi lebih lanjut",
    ERROR_PROCESSING_TITLE: "Gagal memproses data",
    LOGIN_SUCCESS_TITLE: "Login telah berhasil",
    LOGIN_FAILED_TITLE: "Gagal memproses login",
    FETCH_FAIL_TITLE: "Gagal memproses data",
    FETCH_FAIL_MESSAGE: "Terjadi kesalahan sistem pada server",
    FETCH_SUCCESS_TITLE: "Process Berhasil",
    CONFIRM_DELETE_TITLE: "Konfirmasi Hapus Data",
    CONFIRM_DELETE_MESSAGE: "Apakah anda yakin untuk menghapus data ini?",
    CONFIRM_CHANGE_TITLE: "Konfirmasi Perubahan Data",
    CONFIRM_CHANGE_MESSAGE: "Terdapat beberapa perubahan data yang belum anda simpan. Apakah anda yakin akan merubah ini?",
    CONFIRM_PROCESSDATA_TITLE: "Konfirmasi Process",
    CONFIRM_PROCESSDATA_MESSAGE: "Apakah anda yakin akan memproses data ini?",
    FORM_VALIDATION_TITLE: "Form tidak valid",
    FORM_VALIDATION_FILTER_VARIANT: "Tidak ditemukan produk varian yang terpilih"
};

const ServiceAjax = {
    post: function(url, options = {}) {
        return $.ajax({
            url: url,
            type: 'post',
            headers: $.extend(options.headers !== undefined ? options.headers : {}, {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            }),
            contentType: options.contentType,
            processData: options.processData,
            data: options.data !== undefined ? options.data : {},
            success: function(res, type, xhr) {
                $('body').removeClass('loading');

                if(options.success !== undefined)
                    options.success(res, type, xhr);
            },
            error: function(res, type, message) {
                $('body').removeClass('loading');
                console.error(`request data failed: ${message}`);

                AlertNotif.adminlte.error(DBMessage.ERROR_NETWORK_MESSAGE, {
                    title: DBMessage.ERROR_NETWORK_TITLE
                });

                if(options.error !== undefined)
                    options.error(res, type, message);
            }
        });
    },
    get: function(url, options = {}) {
        return $.ajax({
            url: url,
            type: 'get',
            data: options.data !== undefined ? options.data : {},
            success: function(res, type, xhr) {
                $('body').removeClass('loading');

                if(options.success !== undefined)
                    options.success(res, type, xhr);
            },
            error: function(res, type, message) {
                $('body').removeClass('loading');
                console.error(`request data failed: ${message}`);

                AlertNotif.adminlte.error(DBMessage.ERROR_NETWORK_MESSAGE, {
                    title: DBMessage.ERROR_NETWORK_TITLE
                });

                if(options.error !== undefined)
                    options.error(res, type, message);
            }
        });
    },
    put: function(url, options = {}) {
        return $.ajax({
            url: url,
            type: 'put',
            headers: $.extend(options.headers !== undefined ? options.headers : {}, {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            }),
            data: options.data !== undefined ? options.data : {},
            success: function(res, type, xhr) {
                $('body').removeClass('loading');

                if(options.success !== undefined)
                    options.success(res, type, xhr);
            },
            error: function(res, type, message) {
                $('body').removeClass('loading');
                console.error(`request data failed: ${message}`);

                AlertNotif.adminlte.error(DBMessage.ERROR_NETWORK_MESSAGE, {
                    title: DBMessage.ERROR_NETWORK_TITLE
                });

                if(options.error !== undefined)
                    options.error(res, type, message);
            }
        });
    },
    delete: function(url, options = {}) {
        return $.ajax({
            url: url,
            type: 'delete',
            headers: $.extend(options.headers !== undefined ? options.headers : {}, {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            }),
            data: options.data !== undefined ? options.data : {},
            success: function(res, type, xhr) {
                $('body').removeClass('loading');
                if(options.success !== undefined)
                    options.success(res, type, xhr);
            },
            error: function(res, type, message) {
                $('body').removeClass('loading');
                console.error(`request data failed: ${message}`);

                AlertNotif.adminlte.error(DBMessage.ERROR_NETWORK_MESSAGE, {
                    title: DBMessage.ERROR_NETWORK_TITLE
                });

                if(options.error !== undefined)
                    options.error(res, type, message);
            }
        });
    }
};

const FormComponents = {
    select2: {
        selector: '[data-toggle=select2]',
        init: function(elements, config = {}) {
            if(elements === undefined || elements === null)
                elements = $(FormComponents.select2.selector);

            elements.each(function(i, elm) {
                const $select2 = $(elm);
                if($select2.data('select2')) {
                    $select2.select2('destroy');
                }

                let url = $select2.data('url');
                let method = $select2.data('method') ? $select2.data('method') : 'get';
                let data = $select2.data('params') ? $select2.data('params') : {};

                if(config !== undefined && config.ajax !== undefined) {
                    if(typeof config.ajax === 'string')
                        url = config.ajax;

                    else if(typeof  config.ajax === 'object') {
                        if(config.ajax.method !== undefined)
                            method = config.ajax.method;

                        if(config.ajax.url !== undefined)
                            url = config.ajax.url;

                        if(config.ajax.data !== undefined)
                            data = config.ajax.data;
                    }
                }

                let placeholder = $select2.data('selectPlaceholder') ? $select2.data('selectPlaceholder') : 'Pilih salah satu';
                if(config !== undefined && config.placeholder !== undefined)
                    placeholder = config.placeholder;

                let width = $select2.data('selectWidth') ? $select2.data('selectWidth') : '100%';
                if(config !== undefined && config.width !== undefined)
                    width = config.width;

                $select2.select2({
                    allowClear: true,
                    placeholder: placeholder,
                    width: width,
                    ajax: {
                        url: url,
                        type: method,
                        dataType: 'json',
                        data : function(params) {
                            if(typeof data === 'object') {
                                Object.keys(data).forEach((key) => {
                                    params[key] = data[key];
                                });
                            }

                            else if(typeof data === 'function') {
                                Object.keys(data({})).forEach((key) => {
                                    params[key] = data({})[key];
                                });
                            }

                            return params;
                        },
                        processResults: function (data) {
                            const res = [];
                            for(let i  = 0 ; i < data.length; i++) {
                                res.push({id:data[i].id, text:data[i].text});
                            }
                            return {
                                results: res
                            }
                        },
                    }
                });
            });
        },
        reset: function(elements) {
            if(elements === undefined || elements === null)
                elements = $(FormComponents.select2.selector);

            elements.each((i, element) => {
                $(element).text(null).val(null);
            });
        }
    },
    daterangepicker: {
        selector: '[data-toggle=daterangepicker]',
        init: function(elements, config) {
            if(elements === undefined || elements === null)
                elements = $(FormComponents.daterangepicker.selector);

            elements.each((i, item) => {
               const $item = $(item);

                let format = $item.data('format');
                if(config !== undefined && config.format !== undefined)
                    format = config.format;

                let singleDatePicker = $item.data('singleDate');
                if(config !== undefined && config.singleDatePicker !== undefined)
                    singleDatePicker = config.singleDatePicker;

                let opens = $item.data('opens');
                if(config !== undefined && config.opens !== undefined)
                    opens = config.opens;

                let autoApply = $item.data('autoApply') === true;
                if(config !== undefined && config.autoApply !== undefined)
                    autoApply = config.autoApply;

                let showDropdowns = $item.data('showDropdowns') === true;
                if(config !== undefined && config.showDropdowns !== undefined)
                    showDropdowns = config.showDropdowns;

                let autoUpdateInput = $item.data('autoUpdateInput') === true;
                if(config !== undefined && config.autoUpdateInput !== undefined)
                    autoUpdateInput = config.autoUpdateInput;

                let timePicker = $item.data('timePicker') === true;
                if(config !== undefined && config.timePicker !== undefined)
                    timePicker = config.timePicker;

                $item.daterangepicker({
                    opens: opens,
                    singleDatePicker: singleDatePicker,
                    locale: {
                        format: format
                    },
                    autoApply: autoApply,
                    showDropdowns: showDropdowns,
                    autoUpdateInput: autoUpdateInput,
                    timePicker: timePicker,
                    timePicker24Hour: timePicker,
                    maxDate: new Date(),
               }, function(start, end, label) {
                    if(!autoUpdateInput) {
                        if(singleDatePicker)
                            $item.val(start.format(format));
                        else $item.val(`${start.format(format)}-${end.format(format)}`);
                    }
                });
            });
        }
    },
    number: {
        selector: '[data-toggle=jquery-number]',
        init: function(elements, options = {}) {
            if(elements === undefined || elements === null)
                elements = $(FormComponents.number.selector);

            elements.each((i, item) => {
                const $item = $(item);

                let number = true;

                if($item.data('number') !== undefined)
                    number = $item.data('number');

                if(options !== undefined && options.number !== undefined)
                    number = options.number;

                let decimal = 0;

                if($item.data('decimal') !== undefined)
                    decimal = parseInt($item.data('decimal'));

                if(options !== undefined && options.decimal !== undefined)
                    decimal = options.decimal;

                let decimalPoint = ",";

                if($item.data('decimalPoint') !== undefined)
                    decimalPoint = $item.data('decimalPoint');

                if(options !== undefined && options.hasOwnProperty('decimalPoint'))
                    decimalPoint = options.decimalPoint;

                let thousandSeparator = ".";

                if($item.data('thousandSeparator') !== undefined)
                    thousandSeparator = $item.data('thousandSeparator');

                if(options !== undefined && options.hasOwnProperty('thousandSeparator'))
                    thousandSeparator = options.thousandSeparator;

                $item.number(number, decimal, decimalPoint, thousandSeparator);
            });
        }
    }
};

const Helpers = {
    isNumberKey: function(evt) {
        const charCode = (evt.which) ? evt.which : evt.keyCode;
        return !(charCode !== 46 && charCode > 31
            && (charCode < 48 || charCode > 57));
    },
};

toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "300",
    "timeOut": "2000",
    "extendedTimeOut": "0",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

const AlertNotif = {
    adminlte: {
        response: function(res) {
            if(res.result)
                AlertNotif.adminlte.success(res.message);

            else AlertNotif.adminlte.error(res.message);
        },
        success: function(message, options = {}) {
            $(document).Toasts('create', {
                class: 'bg-success',
                title: options.title !== undefined ? options.title : 'Berhasil',
                subtitle: null,
                body: message,
                autohide: true,
                delay: 3000,
            });
        },
        error: function(message, options = {}) {
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: options.title !== undefined ? options.title : 'Terjadi Kesalahan',
                subtitle: null,
                body: message,
                autohide: options.autohide !== undefined ? options.autohide : true,
                delay: options.delay !== undefined ? options.delay : 5000,
            });
        },
    },
    toastr: {
        response: function(res) {
            if(res.result)
                AlertNotif.toastr.success(res.message);

            else {
                toastr.options.timeOut = 0;
                AlertNotif.toastr.error(res.message);
            }
        },
        success: function(message, options = {}) {
            Object.keys(options).forEach((key) => {
                toastr.options[key] = options[key];
            });
            toastr.success(message)
        },
        error: function(message, options = {}) {
            Object.keys(options).forEach((key) => {
               toastr.options[key] = options[key];
            });

            toastr.error(message)
        }
    }
};
