<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class AddPrintKwitansiAction extends AbstractAction
{
    public function getTitle()
    {
        return ' kwitansi';
    }

    public function getIcon()
    {
        return 'voyager-list';
    }

    public function getAttributes()
    {
        // if(Auth::user()->role->id == 3) {
        //     return [
        //         'class' => 'btn btn-sm btn-primary pull-right',
        //     ];
        // }
        //     else {

                return [
                    'id' => 'submit-print-kwitansi-cabang',
                    'class' => 'col-md-12 btn btn-sm btn-dark pull-right no-sort no-click bread-actions hidden',
                    'target' => '_blank'
                ];

            // }
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'users';
    }

    public function getDefaultRoute()
    {
        // if(Auth::user()->role->id == 3) {
            // dd($this->data->name);
            // return route('donaturs.print.prcabang', array("id"=>$this->data->{$this->data->getKeyName()}));
            return route('donaturs.print.prcabang', array("cabang"=>$this->data->name));
        // }   
        //     else {
        //         return;
        // }
    }
}