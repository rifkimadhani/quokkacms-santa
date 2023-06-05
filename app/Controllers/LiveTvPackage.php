<?php

/**
 * Created by PageBuilder
 * Date: 2023-06-05 10:56:27
 */

namespace App\Controllers;

use App\Models\LiveTVModel;
use App\Models\LiveTvPackageModel;
use App\Models\LiveTvPackageForm;

class LiveTvPackage extends BaseController
{
    public function index()
    {
        $baseUrl = $this->getBaseUrl();

        $mainview = 'livetv_package/index';
        $pageTitle = 'Package';

        $model = new LiveTvPackageModel();
        $fieldList = $model->getFieldList();

        $form = new LiveTvPackageForm();

        return view('layout/template', compact('mainview', 'fieldList', 'pageTitle', 'baseUrl', 'form'));
    }

    public function ssp()
    {
        $model = new LiveTvPackageModel();

        header('Content-Type: application/json');

        $data = $model->getSsp();

        self::sspDataConversion($data);

        echo json_encode($data);
    }

    public function insert(){
        $model = new LiveTvPackageModel();

        $this->normalizeData($_POST, true);

        $r = $model->add($_POST);

        if ($r>0){
            $this->setSuccessMessage('Insert success');
        } else {
            $this->setErrorMessage('Insert fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    /**
     * function ini di call saat user click row pada table
     *
     * @return mixed html dialog
     */
    public function edit($packageId)
    {
        $model = new LiveTvPackageModel();
        $data = $model->get($packageId);


        $form = new LiveTvPackageForm();

        $urlAction = $this->baseUrl . '/update';
        return $form->renderForm('Edit', 'formEdit', $urlAction, $data);
    }

    public function update(){
        $model = new LiveTvPackageModel();

        $this->normalizeData($_POST);

        $r = $model->modify($_POST);

        if ($r>0){
            $this->setSuccessMessage('Update success');
        } else {
            $this->setErrorMessage('Update fail ' . $model->errMessage);
        }

        return redirect()->to($this->baseUrl);
    }

    public function delete($packageId){
        $model = new LiveTvPackageModel();
        $r = $model->remove($packageId);

        if ($r>0){
            $this->setSuccessMessage('Delete success');
        } else {
            $this->setErrorMessage('Delete fail');
        }

        return redirect()->to($this->baseUrl);
    }

    public function assoc($packageId){
        $livetv = new LiveTvPackageModel();

        $data = $livetv->getAllByPackageId($packageId);
        $htmlBody = '';
        foreach ($data as $item){
            $htmlBody .= <<<HTML
<div>
    <input type="checkbox" name="livetv_id[]" value="{$item['livetv_id']}" checked/>
    <label for="{$item['livetv_id']}">{$item['name']}</label>
</div>
HTML;
        }

        $htmlBodyReverse = '';
        $data = $livetv->getAllByPackageIdReverse($packageId);
        foreach ($data as $item){
            $htmlBodyReverse .= <<<HTML
<div>
    <input type="checkbox" name="livetv_id[]" value="{$item['livetv_id']}" />
    <label for="{$item['livetv_id']}">{$item['name']}</label>
</div>
HTML;
        }

        $html = <<<HTML
        <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Edit</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                        <form id="formAssoc" action="{$this->baseUrl}/assoc_update" method="post" enctype="multipart/form-data">
            <div class="block-content">
        <div class="form-group">
            <label class='col-form-label'><b>Package Id</b></label>
            <input name='package_id' id='package_id' type='number' class=form-control placeholder='' value='{$packageId}'   readonly>
        </div>        
            $htmlBody
            $htmlBodyReverse
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-alt-primary">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>
        </form>
            </div>
        </div>
HTML;

        return $html;
    }

    public function assoc_update(){
//        $this->varDump('=================================');

        $packageId = $_POST['package_id'];
        $ar = $_POST['livetv_id'];

        $db = new LiveTvPackageModel();
        $db->updateLiveTv($packageId, $ar);

        return redirect()->to($this->baseUrl);
    }

    /**
     * melaukan proses normalisasi data apabila di butuhkan
     *
     * @param $data array, sbg in dan out
     * @param bool $isInsert
     */
    protected function normalizeData(&$data, $isInsert=false){

    }

    /**
     * melakukan conversi data ke asalnya, misalnya utk url balik dari BASE-HOST -> http://
     * @param $data
     */
    protected function sspDataConversion(&$data){
        return;

        foreach($data['data'] as &$row){

        }
    }
}
