;if (typeof(jQueryIWD) == "undefined") {if (typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}} $ji = jQueryIWD;

$ji(document).ready(function ($) {
    var html = $ji('#sales-edit-user-form').html();
    $ji('<tr><td colspan="2">' + html + '</td></tr>').insertBefore('#sales_order_view_tabs_order_info_content tr:first');
    $ji('#sales-edit-user-form').remove();

    /** product **/
    var rateType = $ji('#salerep_rate_type').val();
    if (rateType == 1) {
        $ji('#salerep_fixed_rate').closest('tr').hide();
    }
    if (rateType == 2) {
        $ji('#salerep_percent_rate').closest('tr').hide();
    }

    $ji('#salerep_rate_type').change(function () {
        var rateType = $ji(this).val();
        if (rateType == 1) {
            $ji('#salerep_fixed_rate').closest('tr').hide();
            $ji('#salerep_percent_rate').closest('tr').show();
        }
        if (rateType == 2) {
            $ji('#salerep_percent_rate').closest('tr').hide();
            $ji('#salerep_fixed_rate').closest('tr').show();
        }
    });

    $ji(document).on('click', '.update-product-rate', function (e) {
        e.preventDefault();
        $ji('#iwd-modal-salesrep').modaliwd('show');
        $ji('#iwd-modal-salesrep .sr-modal-body #product-rate-form').empty();
        var form = $ji('.salesrep-edit-form').serializeArray();

        IWD.Salesrep.user = $ji(this).data('user');
        IWD.Salesrep.product = $ji(this).data('product');
        form.push({"name": "user", "value": IWD.Salesrep.user});
        form.push({"name": "product", "value": IWD.Salesrep.product});
        $.post(detailsRateUrl, form, function (response) {
            if (typeof(response.content)) {
                $ji('#iwd-modal-salesrep .sr-modal-body #product-rate-form').html(response.content);
                IWD.Salesrep.initEditRate();
            }
        });
    });

    /** order **/
    rateType = $ji('#salerep_rate_type_order').val();
    if (rateType == 1) {
        $ji('#salerep_fixed_rate_order').closest('tr').hide();
    }
    if (rateType == 2) {
        $ji('#salerep_percent_rate_order').closest('tr').hide();
    }

    $ji('#salerep_rate_type_order').change(function () {
        var rateType = $ji(this).val();
        if (rateType == 1) {
            $ji('#salerep_fixed_rate_order').closest('tr').hide();
            $ji('#salerep_percent_rate_order').closest('tr').show();
        }
        if (rateType == 2) {
            $ji('#salerep_percent_rate_order').closest('tr').hide();
            $ji('#salerep_fixed_rate_order').closest('tr').show();
        }
    });

    IWD.Salesrep.editProduct();
    IWD.Salesrep.editCustomer();
    IWD.Salesrep.initSaveProductRate();
    IWD.Salesrep.initPicker();
    IWD.Salesrep.initSystemField();
    IWD.Salesrep.checkCreateOrder();
    IWD.Salesrep.checkOrderStatus();
});

window.hasOwnProperty = function (obj) {return (this[obj]) ? true : false;};
if (!window.hasOwnProperty('IWD')) {IWD = {};}
IWD = IWD||{};

IWD.Salesrep = {
    user: null,
    product: null,
    type: null,
    timeout: null,

    /** init apply/remove related product **/
    editProduct: function () {
        $ji(document).on('change', '.salesrep-edit-form #related_product_grid_table .checkbox', function () {
            var id = $ji(this).val();
            $ji(this).wrap('<i class="fa fa-circle-o-notch fa-spin" id="loader-' + id + '"></i>');
            var form = $ji('.salesrep-edit-form').serializeArray();
            var checked = $ji(this).prop('checked');

            if (checked == true) {checked = 1;} else {checked = 0;}

            form.push({"name": "product", "value": id});
            form.push({"name": "checked", "value": checked});

            $ji.post(saveAjaxUrlProduct, form, function (response) {
                var id = response.id;
                $ji(document).find('#loader-' + id + ' input').each(function () {
                    $ji(this).unwrap();
                });

                if (typeof(response.error) != "undefined") {
                    $ji('messages').innerHTML = '<ul class="messages"><li class="error-msg"><ul><li>' + response.error + '</li></ul></li></ul>';
                }

                if (typeof(response.cell)) {
                    IWD.Salesrep.pushCellAftersave(response.cell, id);
                }

            }, 'json');
        });
    },

    /** init apply/remove related product **/
    editCustomer: function () {
        $ji(document).on('change', '.salesrep-edit-form #related_customers_grid_table .checkbox', function () {
            var id = $ji(this).val();
            $ji(this).wrap('<i class="fa fa-circle-o-notch fa-spin" id="loader-' + id + '"></i>');
            var form = $ji('.salesrep-edit-form').serializeArray();

            var checked = $ji(this).prop('checked');
            if (checked == true) {checked = 1;} else {checked = 0;}

            form.push({"name": "customer", "value": id});
            form.push({"name": "checked", "value": checked});

            $ji.post(saveAjaxUrlCustomer, form, function (response) {
                var id = response.id;
                $ji(document).find('#loader-' + id + ' input').each(function () {
                    $ji(this).unwrap();
                });

                if (typeof(response.error) != "undefined") {
                    $ji('messages').innerHTML = '<ul class="messages"><li class="error-msg"><ul><li>' + response.error + '</li></ul></li></ul>';
                }

                if (typeof(response.cell)) {
                    IWD.Salesrep.pushCellAftersave(response.cell, id);
                }

            }, 'json');
        });
    },


    initEditRate: function () {
        var rateType = $ji('#salerep_product_rate_type').val();

        if (rateType == 1) {
            $ji('#salerep_product_fixed_rate').closest('tr').hide();
        }
        if (rateType == 2) {
            $ji('#salerep_product_percent_rate').closest('tr').hide();
        }

        $ji('#salerep_product_rate_type').change(function () {
            var rateType = $ji(this).val();
            if (rateType == 1) {
                $ji('#salerep_product_fixed_rate').closest('tr').hide();
                $ji('#salerep_product_percent_rate').closest('tr').show();
            }
            if (rateType == 2) {
                $ji('#salerep_product_percent_rate').closest('tr').hide();
                $ji('#salerep_product_fixed_rate').closest('tr').show();
            }
        })
    },

    initSaveProductRate: function () {
        $ji('#save-product-rate').click(function () {
            var form = $ji('#product-rate-form').serializeArray();
            form.push({"name": "user_id", "value": IWD.Salesrep.user});
            form.push({"name": "linked_product_id", "value": IWD.Salesrep.product});
            form.push({"name": "form_key", "value": $ji('#product_edit_form input[name="form_key"]').val()});
            $ji.post(saveRetailsRateUrl, form, function (response) {
                if (typeof(response.cell)) {
                    IWD.Salesrep.pushCell(response.cell);
                }
            });
        });
    },

    pushCell: function (cell) {
        $ji('.update-product-rate').each(function () {
            var p = $ji(this).data('product'),
                u = $ji(this).data('user');
            if (p == IWD.Salesrep.product && u == IWD.Salesrep.user) {
                $ji(this).closest('td.last').html(cell);
            }
        });
    },

    pushCellAftersave: function (cell, id) {
        $ji('#related_product_grid tr input').each(function () {
            var p = $ji(this).val();
            if (p == id) {
                var $tr = $ji(this).closest('tr');
                $tr.find('td.last').html(cell);
            }
        });
    },

    initPicker: function () {
        var color = $ji('#salerep_iwd_color').val();
        $ji('#salerep_iwd_color').wrap('<div class="colorpicker-colorswatch" data-color="' + color + '"></div>');
        $ji(".colorpicker-colorswatch").css('background-color', '#' + color);
        $ji(".colorpicker-colorswatch").each(function () {

            var color = $ji(this).data('color');

            $ji(this).colpick({
                colorScheme: 'dark',
                layout: 'rgb',
                color: color,
                onSubmit: function (hsb, hex, rgb, el) {
                    $ji(el).css('background-color', '#' + hex);
                    $ji(el).find('input:first').val(hex);
                    $ji(el).colpickHide();
                }
            })
        });
    },

    initSystemField: function () {
        var val = $ji('#salesrep_create_order_allow').val();
        if (val == 0) {
            $ji('#salesrep_create_order_type').closest('tr').hide();
            $ji('#salesrep_create_order_allowperproduct').closest('tr').hide();
        }

        $ji('#salesrep_create_order_allow').change(function () {
            var val = $ji('#salesrep_create_order_allow').val();
            if (val == 0) {
                $ji('#salesrep_create_order_type').closest('tr').hide();
                $ji('#salesrep_create_order_allowperproduct').closest('tr').hide();
            }
            if (val == 1) {
                $ji('#salesrep_create_order_type').closest('tr').show();
                $ji('#salesrep_create_order_allowperproduct').closest('tr').show();
            }
        });


        /** auto set to special **/
        var valProduct = $ji('#salesrep_create_order_front_autoassign').val();
        var valCustomer = $ji('#salesrep_create_order_front_autoassign_customer').val();
        if (valProduct == 1 || valCustomer == 1) {
            $ji('#salesrep_create_order_front_autoassign_special').prop('disabled', true).val('');
        }
    },

    checkCreateOrder: function () {
        setTimeout(function () {
            if (!$ji('#order-data').length || !$ji('#order-data').is(':visible')) {
                IWD.Salesrep.checkCreateOrder();
            } else {
                IWD.Salesrep.initCreateOrder();
            }
        }, 600);
    },

    initCreateOrder: function () {
        if (IWD.Salesrep.type == 'create') {
            //setup hidden field to check if new or exist customer
            $div = $ji('<div />').addClass('hidden').attr('id', 'salesrep_hidden').insertBefore('#order-data');
            $div.hide();
            $input = $ji('<input />').prop('type', 'text').prop('name', 'order[salesrep_customer]').prop('id', 'salesrep_customer');

            $input.appendTo($div);
            $input.val(order.customerId);

            var html = $ji('#sales-create-user-form').html();
            $ji(html).insertBefore('#order-data');
            $ji('#sales-create-user-form').remove();
        }
    },

    checkOrderStatus: function () {
        if ($ji('#salesrep_order_disable_edit').val() != 2) {
            $ji('#row_salesrep_order_order_status').hide();
        } else {
            $ji('#row_salesrep_order_order_status').show();
        }

        $ji('#salesrep_order_disable_edit').change(function () {
            if ($ji(this).val() != 2) {
                $ji('#row_salesrep_order_order_status').hide();
            } else {
                $ji('#row_salesrep_order_order_status').show();
            }
        });
    }
};