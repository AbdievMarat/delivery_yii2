<?php
/** @var $lang backend\widgets\YandexMap */
/** @var $keyAPI backend\widgets\YandexMap */
/** @var $centerLatitude backend\widgets\YandexMap */
/** @var $centerLongitude backend\widgets\YandexMap */
/** @var $zoom backend\widgets\YandexMap */
/** @var $formIdAddress backend\widgets\YandexMap */
/** @var $formIdLatitude backend\widgets\YandexMap */
/** @var $formIdLongitude backend\widgets\YandexMap */
?>

<script src="https://api-maps.yandex.ru/2.1/?lang=<?= $lang ?>&apikey=<?= $keyAPI ?>" type="text/javascript"></script>

<style>
    #notice {
        color: #f33;
        display: none;
    }
    .input_error{
        outline: none!important;
        border: 1px solid #f33!important;
        box-shadow: 0 0 1px 1px #f33!important;
    }
</style>

<div id="map" class="mb-3" style="width:100%; height:300px"></div>

<script>
    function init() {
        let myMap = new ymaps.Map('map', {
            center: [<?= $centerLatitude ?>, <?= $centerLongitude ?>],
            zoom: <?= $zoom ?>
        });
        let myPlacemark;

        <?php if (!empty($placeMark['lat']) || !empty($placeMark['lon'])) { ?>
            myPlacemark = new ymaps.Placemark([<?= $placeMark['lat'] ?>, <?= $placeMark['lon'] ?>], {
                balloonContent: '<?= $placeMark['content'] ?>',
                iconCaption: '<?= $placeMark['name'] ?>',
            },{
                preset: 'islands#bluePersonIcon',
            });

            myMap.setCenter([<?= $placeMark['lat'] ?>, <?= $placeMark['lon'] ?>], <?= $zoom ?>);
            myMap.geoObjects.add(myPlacemark);
        <?php } ?>

        // Слушаем клик на карте.
        myMap.events.add('click', function (e) {
            var coords = e.get('coords');

            // Если метка уже создана – просто передвигаем ее.
            if (myPlacemark) {
                myPlacemark.geometry.setCoordinates(coords);
            }
            // Если нет – создаем.
            else {
                myPlacemark = new ymaps.Placemark(coords, {
                    balloonContent: 'Фактические координаты',
                },
                {
                    preset: 'islands#bluePersonIcon',
                });
                myMap.geoObjects.add(myPlacemark);

                // Слушаем событие окончания перетаскивания на метке.
                myPlacemark.events.add('dragend', function () {
                    getAddress(myPlacemark.geometry.getCoordinates());
                });
            }
            getAddress(coords);
        });

        //Определяем адрес по координатам (обратное геокодирование).
        function getAddress(coords) {
            myPlacemark.properties.set('iconCaption', 'поиск...');
            ymaps.geocode(coords).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0);

                myPlacemark.properties
                    .set({
                        // Формируем строку с данными об объекте.
                        iconCaption: [
                            // Название населенного пункта или вышестоящее административно-территориальное образование.
                            firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                            // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                            firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                        ].filter(Boolean).join(', '),
                        // В качестве контента балуна задаем строку с адресом объекта.
                        balloonContent: firstGeoObject.getAddressLine()
                    });

                $('#<?= $formIdAddress ?>').val(firstGeoObject.getAddressLine());
                $('#<?= $formIdLatitude ?>').val(coords[0]);
                $('#<?= $formIdLongitude ?>').val(coords[1]);
            });
        }

        //поисковая строка
        new ymaps.SuggestView('<?= $formIdAddress ?>');

        $('#<?= $formIdAddress ?>').bind('blur', function () {
            geocode();
        });

        function geocode() {
            setTimeout(function () {
                var request = $('#<?= $formIdAddress ?>').val();

                if(request === ''){
                    $('#<?= $formIdLatitude ?>').val('');
                    $('#<?= $formIdLongitude ?>').val('');
                }
                else{
                    ymaps.geocode(request).then(function (res) {
                        var obj = res.geoObjects.get(0),
                            error, hint;
                        if (obj) {
                            switch (obj.properties.get('metaDataProperty.GeocoderMetaData.precision')) {
                                case 'exact':
                                    break;
                                case 'number':
                                case 'near':
                                case 'range':
                                    error = 'Неточный адрес, требуется уточнение';
                                    hint = 'Уточните номер дома';
                                    break;
                                case 'street':
                                    error = 'Неполный адрес, требуется уточнение';
                                    hint = 'Уточните номер дома';
                                    break;
                                case 'other':
                                default:
                                    error = 'Неточный адрес, требуется уточнение';
                                    hint = 'Уточните адрес';
                            }
                        } else {
                            error = 'Адрес не найден';
                            hint = 'Уточните адрес';
                        }

                        // Если геокодер возвращает пустой массив или неточный результат, то показываем ошибку.
                        if (error) {
                            showError(error, hint);

                            $('#<?= $formIdLatitude ?>').val('');
                            $('#<?= $formIdLongitude ?>').val('');
                        } else {
                            if (myPlacemark) {
                                myPlacemark.geometry.setCoordinates(obj.geometry.getCoordinates());
                            }
                            // Если нет – создаем.
                            else {
                                myPlacemark = new ymaps.Placemark(obj.geometry.getCoordinates(), {
                                        balloonContent: 'Фактические координаты',
                                    },
                                    {
                                        preset: 'islands#bluePersonIcon',
                                    });
                            }
                            myMap.setCenter(obj.geometry.getCoordinates(), <?= $zoom ?>);
                            myMap.geoObjects.add(myPlacemark);

                            $('#notice').css('display', 'none');
                            $('#<?= $formIdAddress ?>').removeClass('input_error');

                            $('#<?= $formIdLatitude ?>').val(obj.geometry.getCoordinates()[0]);
                            $('#<?= $formIdLongitude ?>').val(obj.geometry.getCoordinates()[1]);
                        }
                    }, function (e) {
                        console.log(e);
                    });
                }
            }, 200);
        }

        function showError(message, hint) {
            $('#notice').text(message + ', ' + hint).css('display', 'block');
            $('#<?= $formIdLatitude ?>').addClass('input_error');
        }

        // если на странице редактирования заказа
        if('<?= $formIdAddress ?>' === 'order-address') {
            // заполняет список магазинов страны по умолчанию
            setTimeout(function () {
                $('#order-dynamic-form').find('#order-country_id').attr('initial_load', 'true').trigger('change');
            }, 200);

            // при смене страны
            $('#order-dynamic-form').on('change', '#order-country_id', function () {
                let country_id = $(this).val();
                let initial_load = $(this).attr('initial_load');

                if(initial_load === 'true'){
                    $(this).attr('initial_load', 'false')
                }
                else{
                    $('.container-items').html('');
                }

                $.ajax({
                    type: "GET",
                    url: "/order/get-country-shops",
                    dataType: "json",
                    data: {
                        country_id: country_id
                    },
                    success: function (data) {
                        if (data.success) {
                            // изменить центр карты по координатам страны
                            myMap.setCenter([data.country_latitude, data.country_longitude]);

                            $('#order-shop_id').html('<option value="">Выберите</option>');
                            $.each(data.shops, function (index, element) {
                                // заполняет список магазинов у выбранной страны
                                $('#order-shop_id').append("<option value='" + element.id + "'>" + element.name + "</option>");

                                // вывод магазинов на карте по координатам
                                let myGeoObjects;
                                if (element.latitude !== '' && element.longitude !== '') {
                                    myGeoObjects = new ymaps.Placemark([Number(element.latitude), Number(element.longitude)], {
                                        iconCaption: element.name,
                                        balloonContent: '<h6 class="small">'+element.name+'<br><abbr class="address-line full-width" title="Телефон">Телефон: </abbr><a href="tel:'+element.contact_phone+'">'+element.contact_phone+'</a></h6>',

                                    }, {
                                        preset: 'islands#violetInfoIcon',
                                        shop_id: element.id,
                                        shop_name: element.name,
                                        shop_mobile_backend_id: element.mobile_backend_id
                                    });

                                    myGeoObjects.events.add('click', function(e) {
                                        let thisPlacemark = e.get('target');
                                        let shop_id = thisPlacemark.options._options.shop_id;
                                        let shop_name = thisPlacemark.options._options.shop_name;
                                        let shop_mobile_backend_id = thisPlacemark.options._options.shop_mobile_backend_id;

                                        $('#order-shop_id').val(shop_id);

                                        let products = [], product_code, product_name, amount;
                                        $('.dynamic_form_wrapper .item').each(function(index){
                                            product_code = $('#orderitem-'+index+'-product_code').val();
                                            product_name = $('#orderitem-'+index+'-product_name').val();
                                            amount = $('#orderitem-'+index+'-amount').val();

                                            products.push({product_code:product_code, product_name:product_name, amount:amount});
                                        });

                                        $.ajax({
                                            type: "GET",
                                            url: "/order/get-remains-products",
                                            dataType: "json",
                                            data: {
                                                country_id: $('#order-country_id').val(),
                                                products: products,
                                                shop_mobile_backend_id: shop_mobile_backend_id
                                            },
                                            success: function (data) {
                                                if (data.success) {
                                                    $('#remains_products').addClass('mb-3');
                                                    $('#remains_products').html('<div style="text-align: center; font-weight: bold;" class="mb-3">Доступные товары в '+shop_name+' на '+data.date_withdrawal_remains+':</div><ol class="list-group"></ol>');

                                                    let product_bg_list, product_icon, product_bg_rounded;
                                                    $.each(data.products, function(index, product) {
                                                        if (product.remainder > 0) {
                                                            product_bg_list = 'list-group-item-success';
                                                            product_icon = '<i class="bi bi-check-square"></i>';
                                                            product_bg_rounded = 'bg-success';
                                                        }
                                                        else{
                                                            product_bg_list = 'list-group-item-danger';
                                                            product_icon = '<i class="bi bi-exclamation-square"></i>';
                                                            product_bg_rounded = 'bg-danger';
                                                        }

                                                        $('#remains_products').find('.list-group').append('<li class="list-group-item d-flex justify-content-between align-items-start list-group-item-action '+product_bg_list+'"><div class="ms-2 me-auto">'+product_icon+' '+product.product_name+'</div><span class="badge rounded-pill '+product_bg_rounded+'">'+product.remainder+'</span></li>');
                                                    });
                                                }
                                                else
                                                    $('#remains_products').html('<div class="alert alert-danger" role="alert">Сервис по остаткам недоступен!</div>');
                                            }
                                        });
                                    });

                                    myMap.geoObjects.add(myGeoObjects);
                                }
                            });
                            $('#order-shop_id').val($('#order-shop_id_value').val());// подставляет магазин, если он был выбран
                        }
                    }
                });
            });
        }
    }

    ymaps.ready(init);
</script>