// получение списка товаров
$('.dynamic_form_wrapper').on('keyup', '.product-search', function () {
    $(this).autocomplete({
        source: function (search_product, o) {
            let result = [];
            $.ajax({
                type: "GET",
                url: "/order/product-search",
                dataType: "json",
                data: {
                    country_id: $('#order-country_id').val(),
                    desired_product: search_product.term
                },
                success: function (data) {
                    if (data.success) {
                        $.each(data.data.products, function (index, element) {
                            if (element.art_id.replace(/\s/g, '  ').split("  ").length === 1 && element.price != null)//если в коде товара встречается пробел, не пропускать его и если цена пришла null
                                result.push(element.art_id + '   ' + element.name + '   ' + element.price);
                        });
                        o(result);
                    } else
                        alert('Товар не найден!');
                }
            });
        },
        minLength: 2,
    });
});

// поиск товара, смена количества товара
$('.dynamic_form_wrapper').on('blur', '.product-search, .product-amount', function () {
    calculationOrderPrice();
});

// удаление товара
$('.dynamic_form_wrapper').on("afterDelete", function () {
    calculationOrderPrice();
});

// создание заказа в яндекс доставке
$('#order-dynamic-form').on('click', '.create-order-yandex', function () {
    $.ajax({
        type: "GET",
        url: "/order/create-order-yandex",
        dataType: "json",
        data: {
            order_id: $('#order-id').val(),
            shop_id: $('#order-shop_id').val(),
            client_phone: $('#order-client_phone').val(),
            address: $('#order-address').val(),
            latitude: $('#order-latitude').val(),
            longitude: $('#order-longitude').val(),
            entrance: $('#order-entrance').val(),
            floor: $('#order-floor').val(),
            flat: $('#order-flat').val(),
            comment_for_operator: $('#order-comment_for_operator').val(),
            comment_for_shop_manager: $('#order-comment_for_shop_manager').val(),
            comment_for_driver: $('#order-comment_for_driver').val()
        },
        success: function (data) {
            $('.is-invalid').each(function () {
                $(this).removeClass('is-invalid');
            });
            $('.invalid-feedback').html('');

            if (data.success) {
                $('#order-count_of_deliveries').val(data.count_of_deliveries);
                $('.span_spinner').addClass('spinner-border').addClass('spinner-border-sm');
                $('.create-order-yandex').attr('style', 'pointer-events: none; color: #212529; background-color: #eee; border-color: #bdbdbd; opacity: .65;');
            } else {
                $.each(data.errors, function (index, element) {
                    if (index === 'order_items') {
                        $('.dynamic_form_wrapper').addClass('is-invalid').after('<div class="invalid-feedback">' + element[0] + '</div>');
                    } else if (index === 'order_yandex') {
                        $('.create-order-yandex').addClass('is-invalid').after('<div class="invalid-feedback">' + element[0] + '</div>');
                    } else {
                        $('#order-dynamic-form').yiiActiveForm('updateAttribute', 'order-' + index, [element[0]]);
                    }
                });
            }
        }
    });
});

// подтверждение заказа в яндекс доставке
$('#order-dynamic-form').on('click', '.accept-order-yandex', function () {
    let yandex_id = $(this).data('yandex_id');

    if(confirm('Подтвердить заказ в Яндекс?')) {
        $.ajax({
            type: "GET",
            url: "/order/accept-order-yandex",
            dataType: "json",
            data: {
                order_id: $('#order-id').val(),
                yandex_id: yandex_id,
            },
            success: function (data) {
                if(data.success){
                    alert('Заказ в Yandex был подтверждён!');

                    $.ajax({
                        type: "GET",
                        url: "/order/transfer-order-to-shop",
                        dataType: "json",
                        data: {
                            order_id: $('#order-id').val(),
                            shop_id: $('#order-shop_id').val()
                        },
                        success: function (data_transfer) {
                        }
                    });
                }
                else
                    alert('Заказ в Yandex не был подтверждён!');
            }
        });
    }
});

// отмена заказа в яндекс доставке
$('#order-dynamic-form').on('click', '.cancel-order-yandex', function () {
    let yandex_id = $(this).data('yandex_id');
    let text_state = '';
    let cancel_state = 'free';

    $.ajax({
        type: "GET",
        url: "/order/cancel-order-yandex-info",
        dataType: "json",
        data: {
            order_id: $('#order-id').val(),
            yandex_id: yandex_id,
        },
        success: function (data) {
            if(data.success){
                cancel_state = data.cancel_state;
                if(cancel_state === 'paid')
                    text_state = 'Отмена в Яндекс будет платной, ';
                else if(cancel_state === 'unavailable')
                    text_state = 'Отмена в Яндекс уже недоступна, ';

                if(confirm(text_state+'отменить заказ в Яндекс?')){
                    $.ajax({
                        type: "GET",
                        url: "/order/cancel-order-yandex",
                        dataType: "json",
                        data: {
                            order_id: $('#order-id').val(),
                            yandex_id: yandex_id,
                            cancel_state: cancel_state
                        },
                        success: function (data_cancel) {
                            if(data_cancel.success)
                                alert('Заказ в Yandex был отменен!');
                            else
                                alert('Заказ в Yandex не был отменен!');
                        }
                    });
                }
            }
        }
    });
});

let driver_coordinates_interval;

//если есть заказы, то начинаем проверять статусы
order_delivery_interval = setInterval(function() {
    if($('#order-count_of_deliveries').val() > 0){
        if($('#order-status').val() === '1'){
            get_orders_delivery();
        }
        // else if($('#order-status').val() === '2' || $('#order-status').val() === '3'){
        //     idIntervals2 = setInterval(function() {
        //         get_driver_position();
        //     }, 10000);//каждые 10 секунд запрашивает местоположение курьера
        //     clearInterval(order_delivery_interval);
        // }
        // else
        //     clearInterval(order_delivery_interval);
    }
}, 3000);
get_orders_delivery();

// получение заказов отправленных в яндекс
function get_orders_delivery() {
    $.ajax({
        type: "GET",
        url: "/order/get-orders-delivery-in-yandex",
        dataType: "json",
        data: {
            order_id: $('#order-id').val(),
        },
        success: function (data) {

            $('#order_statuses').html(data.order_statuses_view);


            $('#orders-delivery-in-yandex').html('');
            let count_ready_for_approval_order = 0;

            let offer_price_template, yandex_id_template, yandex_id_bad_offer;
            $.each(data.orders_delivery_in_yandex, function (index, order) {

                let status_yandex = order.status_description;
                let item_class = ' list-group-item-info';

                if (
                    order.status === 'accepted' || order.status === 'estimating' || order.status === 'estimating_failed' || order.status === 'new' || order.status === 'pay_waiting' || order.status === 'performer_draft' ||
                    order.status === 'performer_found' || order.status === 'performer_lookup' || order.status === 'performer_not_found' || order.status === 'pickup_arrived' || order.status === 'ready_for_approval'
                )// статусы при которых можно отменить
                    status_yandex = status_yandex + '<br> <a class="btn btn-danger float-end cancel-order-yandex ms-1" data-yandex_id="' + order.yandex_id + '">Отменить</a>';

                if (order.status === 'cancelled')// отменен
                    item_class = ' list-group-item-danger';
                else if (order.status === 'delivered')// доставлен
                    item_class = ' list-group-item-success';
                else if (order.status === 'estimating_failed') {// ошибка при расчёте стоимости
                    // отмена заказа
                    $.ajax({
                        type: "GET",
                        url: "/order/cancel-order-yandex",
                        dataType: "json",
                        data: {
                            order_id: $('#order-id').val(),
                            yandex_id: order.yandex_id,
                        },
                    });
                } else if (order.status === 'ready_for_approval') {// успешно оценена
                    count_ready_for_approval_order += 1;
                    status_yandex = status_yandex + '<a class="btn btn-info float-end accept-order-yandex" data-yandex_id="' + order.yandex_id + '">Подтвердить</a>';

                    if (parseInt(order.offer_price) > offer_price_template)
                        yandex_id_bad_offer = order.yandex_id;
                    else
                        yandex_id_bad_offer = yandex_id_template;

                    if(yandex_id_bad_offer){
                        // отмена заказа
                        $.ajax({
                            type: "GET",
                            url: "/order/cancel-order-yandex",
                            dataType: "json",
                            data: {
                                order_id: $('#order-id').val(),
                                yandex_id: yandex_id_bad_offer,
                            },
                        });
                    }

                    yandex_id_template = order.yandex_id;
                    offer_price_template = parseInt(order.offer_price);
                }

                let offer_price = '', final_price = '', driver_phone = '';
                if (order.offer_price > 0)
                    offer_price = '<b>Предварительная стоимость:</b> <span class="badge bg-primary rounded-pill fs-6"><i class="bi bi-cash"></i>  ' + order.offer_price + '</span> <br>\n';
                if (order.final_price > 0)
                    final_price = '<b>Итоговая стоимость:</b> <span class="badge bg-success rounded-pill fs-6"><i class="bi bi-check2"></i>  ' + order.final_price + '</span>\n';
                if (order.driver_phone && order.driver_phone.replace(/\s/g, '') !== '')
                    driver_phone = '<b>Телефон:</b> ' + order.driver_phone + ', <b>доб.</b>' + order.driver_phone_ext + '<br>';

                let li = '<li class="list-group-item">\n' +
                    '                    <div>\n' +
                    '                        <span class="list-group-item list-group-item-action ' + item_class + '">\n' +
                    '                            <div class="d-flex w-100 justify-content-between">\n' +
                    '                                <h5 class="mb-1" style="min-width: 170px;"> ' + order.created_at + '</h5>\n' +
                    '                                <div class="text-end" style="font-size: 0.875em;"> ' + status_yandex + '</div>\n' +
                    '                            </div>\n' +
                    '                        </span>\n' +
                    '                        <b>Откуда:</b> ' + order.shop_address + '<br>\n' +
                    '                        <b>Куда:</b> ' + order.client_address + '<br>\n' +
                    '                        <b>ID:</b> ' + order.yandex_id + '<br>\n' +
                    '                        <b>Отправитель:</b> ' + order.user.username + '<br>\n' +
                    '                        <b>Тариф:</b> ' + order.tariff + '<br>\n' +
                    offer_price +
                    final_price +
                    driver_phone +
                    '                    </div>\n' +
                    '                </li>';
                $('#orders-delivery-in-yandex').append(li);
            });

            if($('.span_spinner').hasClass('spinner-border') === true && count_ready_for_approval_order === 1){
                $('.span_spinner').removeClass('spinner-border').removeClass('spinner-border-sm');
                $('.create-order-yandex').attr('style', '');
            }
        }
    });
}

function get_driver_position() {

}

// расчёт итоговой суммы
function calculationOrderPrice() {
    let final_price = 0;
    $('.product-search').each(function (index) {

        let product_data = $(this).val().split('   ');
        let product_name = product_data[1];

        if (product_name) {
            let product_code = product_data[0];
            let product_price = product_data[2];

            $("#orderitem-" + index + "-product_name").val(product_name);
            $("#orderitem-" + index + "-amount").val(1);
            $("#orderitem-" + index + "-product_code").val(product_code);
            $("#orderitem-" + index + "-product_price").val(product_price);
        } else if ($("#orderitem-" + index + "-product_price").val() === '') {
            $("#orderitem-" + index + "-product_name").val('');
            $("#orderitem-" + index + "-amount").val('');
            $("#orderitem-" + index + "-product_code").val('');
            $("#orderitem-" + index + "-product_price").val('');
        }

        let product_amount = $("#orderitem-" + index + "-amount").val();
        let price;
        if (parseInt(product_amount) > 0) {
            price = parseInt($("#orderitem-" + index + "-product_price").val()) * parseInt(product_amount);
            final_price += price;
        }
    });

    $('#order-order_price').val(final_price);
}