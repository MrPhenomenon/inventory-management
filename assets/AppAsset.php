<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/portal-assets';
    public $baseUrl = '@web/portal-assets';
    public $css = [
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700',
        'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css',
        'adminlte-assets/plugins/fontawesome-free/css/all.min.css',
        'adminlte-assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
        'adminlte-assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        'adminlte-assets/plugins/jqvmap/jqvmap.min.css',
        'adminlte-assets/dist/css/adminlte.min.css',
        'adminlte-assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
        'adminlte-assets/plugins/daterangepicker/daterangepicker.css',
        'adminlte-assets/plugins/summernote/summernote-bs4.css',
        'adminlte-assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css',
        'adminlte-assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        'adminlte-assets/plugins/jquery-ui/jquery-ui.min.css',
        'css/site.css',
    ];
    public $js = [
        // 'adminlte-assets/plugins/jquery/jquery.min.js',
        'adminlte-assets/plugins/jquery-ui/jquery-ui.min.js',
        'adminlte-assets/plugins/bootstrap/js/bootstrap.bundle.min.js',
        'adminlte-assets/plugins/chart.js/Chart.min.js',
        'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js',
        'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js',
        'adminlte-assets/plugins/sparklines/sparkline.js',
        'adminlte-assets/plugins/jqvmap/jquery.vmap.min.js',
        'adminlte-assets/plugins/jqvmap/maps/jquery.vmap.usa.js',
        'adminlte-assets/plugins/jquery-knob/jquery.knob.min.js',
        'adminlte-assets/plugins/moment/moment.min.js',
        'adminlte-assets/plugins/daterangepicker/daterangepicker.js',
        'adminlte-assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
        'adminlte-assets/plugins/summernote/summernote-bs4.min.js',
        'adminlte-assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
        'adminlte-assets/plugins/sweetalert2/sweetalert2.min.js',
        'adminlte-assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
        'adminlte-assets/dist/js/adminlte.js',
        // 'adminlte-assets/dist/js/pages/dashboard.js',
        // 'adminlte-assets/dist/js/demo.js',
        'js/functions.js',
    ];
    public $depends = [
       'yii\bootstrap5\BootstrapAsset',
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}
