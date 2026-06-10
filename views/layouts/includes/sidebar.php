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

                <li class="nav-item">
                    <a href="/product/index" class="nav-link">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>Products</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Purchases <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['purchase/index']) ?>" class="nav-link">
                                <i class="fas fa-list nav-icon"></i>
                                <p>All Purchases</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['purchase/create']) ?>" class="nav-link">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>New Purchase</p>
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
                                <p>New Sale</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="/payment/create" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Payments</p>
                    </a>

                </li>

                <li class="nav-divider"></li>

                <li class="nav-item">
                    <a href="<?= yii\helpers\Url::to(['inventory/index']) ?>" class="nav-link">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>Inventory</p>
                    </a>

                </li>

                <li class="nav-item">
                    <a href="/party" class="nav-link">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Parties</p>
                    </a>
                </li>

                <li class="nav-divider"></li>

                <li class="nav-item">
                    <a href="<?= yii\helpers\Url::to(['account/index']) ?>" class="nav-link">
                        <i class="nav-icon fas fa-university"></i>
                        <p>Accounts</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= yii\helpers\Url::to(['expense/index']) ?>" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Expense Accounts</p>
                    </a>
                </li>

                <li class="nav-divider"></li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['account/chemical-ledger']) ?>" class="nav-link">
                                <i class="nav-icon fas fa-flask"></i>
                                <p>Chemical Ledger</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= yii\helpers\Url::to(['report/balance-sheet']) ?>" class="nav-link">
                                <i class="fas fa-balance-scale nav-icon"></i>
                                <p>Balance Sheet</p>
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