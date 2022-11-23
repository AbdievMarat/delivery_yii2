<?php /* @var $modelOrder backend\models\Order */ ?>

<div class="container-fluid pt-5 pb-4">

    <div class="row">
        <div class="col-lg-12">

            <div class="horizontal-timeline">

                <ul class="list-inline items">
                    <li class="list-inline-item items-list">
                        <?php if ($modelOrder->status > 0 && $modelOrder->status != 5): ?>
                            <div class="px-4">
                                <div class="event-date badge bg-warning rounded-pill fs-6"><i class="bi bi-headset"></i>
                                    Оператор
                                </div>
                                <h5 class="pt-2">
                                    <?php
                                    switch ($modelOrder->informationAboutDeadline['status_time_operator']) {
                                        case 1:
                                            echo '<i class="bi bi-hand-thumbs-up-fill"></i>';
                                            break;
                                        case 2:
                                            echo '<i class="bi bi-hand-thumbs-down-fill"></i>';
                                            break;
                                        default:
                                            echo '<i class="bi bi-clock-history"></i>';
                                            break;
                                    }
                                    ?>
                                    <?= $modelOrder->informationAboutDeadline['factually_time_spent_by_operator'] ?>
                                </h5>
                                <p class="text-muted">
                                    <?php
                                    switch ($modelOrder->informationAboutDeadline['status_time_operator']) {
                                        case 1:
                                        case 2:
                                            echo $modelOrder->operatorUser->username;
                                            break;
                                        default:
                                            echo 'В обработке';
                                            break;
                                    }
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </li>
                    <li class="list-inline-item items-list">
                        <?php if ($modelOrder->status > 1 && $modelOrder->status != 5): ?>
                            <div class="px-4">
                                <div class="event-date badge bg-info rounded-pill fs-6"><i class="bi bi-shop"></i> Магазин</div>
                                <h5 class="pt-2">
                                    <?php
                                    switch ($modelOrder->informationAboutDeadline['status_time_shop']) {
                                        case 1:
                                            echo '<i class="bi bi-hand-thumbs-up-fill"></i>'; break;
                                        case 2:
                                            echo '<i class="bi bi-hand-thumbs-down-fill"></i>'; break;
                                        default:
                                            echo '<i class="bi bi-clock-history"></i>'; break;
                                    }
                                    ?>
                                    <?= $modelOrder->informationAboutDeadline['factually_time_spent_by_shop'] ?>
                                </h5>
                                <p class="text-muted"> <?= $modelOrder->shop->name ?></p>
                            </div>
                        <?php endif; ?>
                    </li>
                    <li class="list-inline-item items-list">
                        <?php if ($modelOrder->status > 2 && $modelOrder->status != 5): ?>
                            <div class="px-4">
                                <div class="event-date badge bg-primary rounded-pill fs-6"><i class="bi bi-car-front"></i> Курьер</div>
                                <h5 class="pt-2">
                                    <?php
                                    switch ($modelOrder->informationAboutDeadline['status_time_driver']) {
                                        case 1:
                                            echo '<i class="bi bi-hand-thumbs-up-fill"></i>'; break;
                                        case 2:
                                            echo '<i class="bi bi-hand-thumbs-down-fill"></i>'; break;
                                        default:
                                            echo '<i class="bi bi-clock-history"></i>'; break;
                                    }
                                    ?>
                                    <?= $modelOrder->informationAboutDeadline['factually_time_spent_by_driver'] ?>
                                </h5>
                                <p class="text-muted">Yandex</p>
                            </div>
                        <?php endif; ?>
                    </li>
                    <li class="list-inline-item items-list">
                        <?php if ($modelOrder->status > 3 && $modelOrder->status != 5): ?>
                            <div class="px-4">
                                <div class="event-date badge bg-success rounded-pill fs-6"><i class="bi bi-flag"></i> Доставлен</div>
                                <h5 class="pt-2">
                                    <i class="bi bi-clock-fill"></i> <?= $modelOrder->informationAboutDeadline['total_time_spent'] ?>
                                </h5>
                                <p class="text-muted">Общее время</p>
                            </div>
                        <?php endif; ?>
                    </li>
                    <!--<li class="list-inline-item items-list">
                        <div class="px-4">
                            <div class="event-date badge bg-danger rounded-pill fs-6"><i class="bi bi-x-circle"></i> Отменен</div>
                            <h5 class="pt-2">16-09-2022 03:44</h5>
                            <p class="text-muted">Захарова Анастасия</p>
                        </div>
                    </li>-->
                </ul>

            </div>

        </div>
    </div>

</div>