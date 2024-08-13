<?php
require_once ("./core/core.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="scripts/price.css">
    <link rel="stylesheet" href="core/public/css/core.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <title>ToDoForYou</title>
</head>
<body>
<div class="app">

    <? require_once("./core/blocks/navbar.php") ?>

    <div class="plans">
        <div class="planItem__container">
            <div class="planItem planItem--free">

                <div class="card">
                    <div class="card__header">
                        <div class="card__icon symbol symbol--rounded"></div>
                        <h2>Free</h2>
                    </div>
                    <div class="card__desc">Базовый тариф для новичков</div>
                </div>

                <div class="price">0₽<span>/ месяц</span></div>

                <ul class="featureList">
                    <li>15 задач</li>
                    <li>mini AI</li>
                    <li class="disabled">100% рекламы</li>
                    <li class="disabled">team work</li>
                </ul>

                <button class="button">Начать</button>
            </div>

            <div class="planItem planItem--pro">
                <div class="card">
                    <div class="card__header">
                        <div class="card__icon symbol"></div>
                        <h2>Pro</h2>
                        <div class="card__label label">Лучший выбор</div>
                    </div>
                    <div class="card__desc">Подойдет для больших личный проектов</div>
                </div>

                <div class="price">1250₽<span>/ месяц</span></div>

                <ul class="featureList">
                    <li>55 задач</li>
                    <li>middle AI</li>
                    <li>0% рекламы</li>
                    <li class="disabled">team work</li>
                </ul>

                <button class="button button--pink">Начать</button>
            </div>
            <div class="planItem planItem--entp">
                <div class="card">
                    <div class="card__header">
                        <div class="card__icon"></div>
                        <h2>Maximum</h2>
                    </div>
                    <div class="card__desc">Лучший выбор для командной работы</div>
                </div>

                <div class="price">1500₽<span style="color: #000">/ месяц</span></div>

                <ul class="featureList">
                    <li>∞ задач</li>
                    <li>turbo AI</li>
                    <li>0% рекламы</li>
                    <li>team work</li>
                </ul>

                <button class="button button--white">Начать</button>
            </div>
        </div>
    </div>

</div>
</body>
</html>
