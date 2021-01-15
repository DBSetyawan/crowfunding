<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class AddKonfirmasiAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Konfirmasi';
    }

    public function getIcon()
    {
        // Action icon which display in left of button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'voyager-x':'voyager-external';
        return 'voyager-window-list';
    }

    public function getAttributes()
    {
        // Action button class
        return [
            'class' => "btn btn-sm btn-success pull-right",
            'data-id' => $this->data->{$this->data->getKeyName()},
            'id'      => 'daniel',
        ];
    }

    public function shouldActionDisplayOnDataType()
    {
        // show or hide the action button, in this case will show for posts model
        return $this->dataType->slug == 'donaturs';
    }
    
    public function getDefaultRoute()
    {
        // exit;
        // URL for action button when click
        return route('donaturs.groups.form.confirmation', array("id"=>$this->data->{$this->data->getKeyName()}));
    }
    

}