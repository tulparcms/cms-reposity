<?php
/**
 * bu eklenti CMS için
 */
defined('REPOSITY_FOLDER') or define('REPOSITY_FOLDER', 'tulparcms/cms-reposity-main');
defined('REPOSITY_LANG') or define('REPOSITY_LANG', 'tulparcms/cms-reposity-main::localize.');

if(is_file(__DIR__.'/model/TcmsReposity.php')){
    include_once(__DIR__.'/model/TcmsReposity.php');
}
if(is_file(__DIR__.'/controller/TcmsReposityController.php')){
    include_once(__DIR__.'/controller/TcmsReposityController.php');
}

TCMS()->addAction('routing', 'cms_reposity_routing', 1);
TCMS()->addAction('localize', 'cms_reposity_localize', 1);

function cms_reposity_localize(){
    $path = storage_path('tcms/'.REPOSITY_FOLDER.'/lang');
    \Lang::addNamespace(REPOSITY_FOLDER, $path);
}
function cms_reposity_routing(){
    Route::group([
        'prefix'=>\Tulparstudyo\Cms\CmsLoader::ADMIN,
        'as'=>\Tulparstudyo\Cms\CmsLoader::ADMIN.'.reposity.',
        'middleware' => ['web', \Tulparstudyo\Cms\CmsLoader::AUTH]
    ], function() {
        Route::get('/reposity', ['App\\Http\\Controllers\\TcmsReposityController', 'index'])->name('index');
        Route::get('/reposity/data-table', ['App\\Http\\Controllers\\TcmsReposityController', 'dataTable'])->name('data-table');
        Route::get('/reposity/add-reposity', ['App\\Http\\Controllers\\TcmsReposityController', 'addReposity'])->name('add-reposity');
        Route::post('/reposity/download', ['App\\Http\\Controllers\\TcmsReposityController', 'downloadReposity'])->name('download');
    });
}
TCMS()->addFilter('system_menu', 'cms_reposity_system_menu', 1);
function cms_reposity_system_menu($menu, $data){
    $menu[] = new  \Tulparstudyo\Cms\CmsMenuItem(
        'reposity',
        __('tulparstudyo/cms-reposity::localize.Reposity') ,
        route('admin.reposity.index'),
        'class',
        'icon');
    return $menu;
}
