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

<div id="map" style="width:100%; height:300px"></div>

<script>
    function init() {
        var myMap = new ymaps.Map('map', {
            center: [<?= $centerLatitude ?>, <?= $centerLonitude ?>],
            zoom: <?= $zoom ?>
        });
        var myPlacemark;

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
                $('#<?= $formIdLatitude?>').val(coords[0]);
                $('#<?= $formIdLonitude ?>').val(coords[1]);
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
                        $('#<?= $formIdLonitude ?>').val('');
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
                        $('#<?= $formIdLonitude ?>').val(obj.geometry.getCoordinates()[1]);
                    }
                }, function (e) {
                    console.log(e);
                });
            }, 200);
        }

        function showError(message, hint) {
            $('#notice').text(message + ', ' + hint).css('display', 'block');
            $('#<?= $formIdLatitude ?>').addClass('input_error');
        }
    }

    ymaps.ready(init);
</script>