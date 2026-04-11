<aside class="main-sidebar sidebar-light-primary elevation-2">
    <a href="/" class="brand-link">
        <span class="brand-text ">Inventory Management</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>Products <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/product/index" class="nav-link">
                                <i class="fas fa-list nav-icon"></i>
                                <p>All Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/product/create" class="nav-link">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Create Product</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Purchases <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/purchase/index" class="nav-link">
                                <i class="fas fa-list nav-icon"></i>
                                <p>All Purchases</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/purchase/create" class="nav-link">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Create Purchase</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Sales <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['sales/index']) ?>" class="nav-link">
                                <i class="fas fa-list nav-icon"></i>
                                <p>All Sales</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['sales/create']) ?>" class="nav-link">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Create Sale</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="/payment" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Payments</p>
                    </a>
                   
                </li>

                <li class="nav-divider"></li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>Inventory <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['inventory/index']) ?>" class="nav-link">
                                <i class="fas fa-chart-line nav-icon"></i>
                                <p>Stock Levels</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['inventory/transactions']) ?>" class="nav-link">
                                <i class="fas fa-exchange-alt nav-icon"></i>
                                <p>Transactions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['inventory/adjust']) ?>" class="nav-link">
                                <i class="fas fa-tools nav-icon"></i>
                                <p>Adjust Stock</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Parties <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/party" class="nav-link">
                                <i class="fas fa-users nav-icon"></i>
                                <p>All Parties</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/party/create" class="nav-link">
                                <i class="fas fa-user-plus nav-icon"></i>
                                <p>Add Party</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-ruler-horizontal"></i>
                        <p>Units <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['unit/index']) ?>" class="nav-link">
                                <i class="fas fa-list nav-icon"></i>
                                <p>All Units</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['unit/create']) ?>" class="nav-link">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Create Unit</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-divider"></li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['report/stock']) ?>" class="nav-link">
                                <i class="fas fa-warehouse nav-icon"></i>
                                <p>Stock Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['report/receivables']) ?>" class="nav-link">
                                <i class="fas fa-money-check nav-icon"></i>
                                <p>Receivables</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['report/payables']) ?>" class="nav-link">
                                <i class="fas fa-money-check-alt nav-icon"></i>
                                <p>Payables</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['report/profit-loss']) ?>" class="nav-link">
                                <i class="fas fa-chart-line nav-icon"></i>
                                <p>Profit & Loss</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<?php
$js = <<<JS
$(document).ready(function () {
    var currentUrl = window.location.pathname;

    $('.nav-link').each(function () {
        var link = $(this).attr('href');
        if (link === currentUrl) {
            $(this).addClass('active');
            $(this).closest('.has-treeview').addClass('menu-open');
            $(this).closest('.nav-treeview').css('display', 'block');
            $(this).closest('.has-treeview').children('.nav-link').addClass('active');
            return false;
        }
    });
});
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
?>