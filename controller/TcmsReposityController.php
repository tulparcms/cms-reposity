<?php

namespace App\Http\Controllers;

use App\Models\TcmsHome;
use FastRoute\Route;
use Illuminate\Http\Request;
use Tulparstudyo\Cms\Response;

class TcmsReposityController extends Controller{
    public function index(Request $request)
    {
        $data['dataTable']= $this->dataTableParams();
        return TCMS()->loadAdminTemplate( 'admin.index', $data, REPOSITY_FOLDER);
    }
    public function addReposity(Request $request)
    {
        $response = new \Tulparstudyo\Cms\Response();
        $response->status = 1;
        $response->html = TCMS()->loadAdminTemplate(  'admin.add-reposity', [], REPOSITY_FOLDER)->render();
        return $response->toArray();
    }
    public function downloadReposity(Request $request)
    {
        $response = new \Tulparstudyo\Cms\Response();
        $response->status = 1;
        $process = $request->input('process');
        $reposity = $request->input('reposity');
        if($reposity){
            $urlParams = parse_url($reposity);
            $path = isset($urlParams['path'])?$urlParams['path']:'';
        } else {
            $response->html = 'Geçerli Bir reposity giriniz...';
            return $response->toArray();
        }

        if($process=='download'){
            if($path){
                $composerInfo = $this->composerInfo($path);
                if($composerInfo){
                    $type = isset($composerInfo['type'])?$composerInfo['type']:'Unknow';
                    $author = isset($composerInfo['authors'])?$composerInfo['authors'][0]:['name'=>'Unknow', 'homepage'=>'#'];

                    if($type=='tcms'){
                        $isDownloaded = TCMS()->downloadReposity($path);
                        if($isDownloaded){
                            $response->html = '<p><i class="tf-icons ti ti-check text-success"></i> Eklenti indirildi</p>';;
                        }
                    } else {
                        $response->html = '<p>Not Supported Type '.' <a target="_blank" href="'.$author['homepage'].'">'.$author['name'].'</a></p>';;
                    }
                }
            }
        } else {
            if($path){
                $composerInfo = $this->composerInfo($path);
                if($composerInfo){
                    $type = isset($composerInfo['type'])?$composerInfo['type']:'Unknow';
                    $author = isset($composerInfo['authors'])?$composerInfo['authors'][0]:['name'=>'Unknow', 'homepage'=>'#'];
                    if($type=='tcms'){
                        $response->html = '<p><span class="badge bg-label-success">'.$composerInfo['license'].'</span> '.$composerInfo['description'].' <a target="_blank" href="'.$author['homepage'].'">'.$author['name'].'</a></p>';
                    } else{
                        $response->html = '<p>Not Supported Type '.' <a target="_blank" href="'.$author['homepage'].'">'.$author['name'].'</a></p>';;
                    }
                } else {
                    $response->html = 'Bilgiler alınamadı';
                }
            } else{
                $response->html = 'Bir reposity giriniz...';
            }
        }
        return $response->toArray();
    }
    private function composerInfo($path){
        $composerJson = @file_get_contents('https://raw.githubusercontent.com'.$path.'/main/composer.json?_v='.time());
        if(empty($composerJson)){
            $composerJson = @file_get_contents('https://raw.githubusercontent.com'.$path.'/master/composer.json?_v='.time());
        }
        if($composerJson){
            return json_decode($composerJson, 1);
        }
        return [];
    }
    private function dataTableParams(){
        $dataTable = new \Tulparstudyo\Cms\AjaxDataTable();
        $dataTable->setTableId('order-list');
        $dataTable->setUrl(route('admin.reposity.data-table'));
        $dataTable->setRecordsTotal(100);
        $dataTable->setRecordsFiltered(90);
        $dataTable->setCols([
            'orderNumber'=>['title'=>'Sıra', 'className'=>'', 'orderable'=>''],
            //'status'=>['title'=>'Durumu', 'className'=>'', 'orderable'=>''],
            'name'=>['title'=>'Eklenti', 'className'=>'', 'orderable'=>''],
            'description'=>['title'=>'Açıklama', 'className'=>'', 'orderable'=>''],
            'actions'=>['title'=>'', 'className'=>'', 'orderable'=>''],
        ]);
        return $dataTable;
    }
    public function dataTable(Request $request){
        $providers = TCMS()->getReposityList();
        $dataTable = $this->dataTableParams();

        $response = [
            'totalCount'=>10,
            'FilteredCount'=>0,
        ];
        $dataTable->setRecordsTotal(isset($response['totalCount'])?$response['totalCount']:0);
        $dataTable->setRecordsFiltered(isset($response['FilteredCount'])?$response['FilteredCount']:0);
        $items = [];
        if($providers){
            foreach($providers as $row){
                $item = [];
                foreach($dataTable->cols() as $key=>$col){
                    $method = '_format_'.$key;
                    if(method_exists($this, $method)){
                        $value = $this->$method($row);
                    } else {
                        $value = isset($row[$key])?$row[$key]:'';
                    }
                    $item[$key] = $value;
                }
                if(isset($item['orderNumber'])){
                    $item['orderNumber'] = count($items) + 1;
                }
                $items[] = $item;
            }
        }
        $dataTable->setItems($items);
        return $dataTable->toJson();
    }
    private function _format_description($item){
        return $item['description'];
    }
    private function _format_status($item){
        if($item['status']){
            $status = '<i class="ti ti-ban ti-sm text-danger"></i>';
        } else{
            $status = '<i class="ti ti-check ti-sm text-success"></i>';
        }
        return $status;
    }
    private function _format_name($item){

        $fist = $item['vendor'][0].$item['reposity'][0];
        return '<div class="d-flex justify-content-start align-items-center"><div class="avatar me-2"><span class="avatar-initial rounded-circle bg-label-success">'.$fist.'</span></div><div class="d-flex flex-column"><span class="emp_name text-truncate">'.$item['name'].'<br><small class="badge bg-label-secondary">'.$item['version'].'</small> </span><small class="emp_post text-truncate text-muted">'.$item['author']['name'].'</small></div></div>';
    }
    private function _format_actions($item){

            $status = '<li class="list-inline-item p-0"><label class="switch switch-success">
                            <input type="checkbox" class="switch-input" '.($item['status']?'checked':'').'>
                            <span class="switch-toggle-slider">
                              <span class="switch-on">
                                <i class="ti ti-check"></i>
                              </span>
                              <span class="switch-off">
                                <i class="ti ti-x"></i>
                              </span>
                            </span>
                            <span class="switch-label"></span>
                          </label>
                       </li>';

        return '<ul class="list-inline email-list-item-actions text-nowrap text-end">
                                  <li class="btn list-inline-item email-delete p-0"><i class="ti ti-trash ti-sm"></i></li>
                                  '.$status.'
                                </ul>';
    }
}
