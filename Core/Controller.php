<?php

class Controller
{
    private array $_urlArgs;

    public function __construct(?string $ctrl, ?string $action){
        if (!isset($ctrl)){
            $this->_urlArgs['ctrl'] = 'DefaultCtrl';
        }
        else{
            $this->_urlArgs['ctrl'] = ucfirst($ctrl) . 'Ctrl';
        }

        if (!isset($action)){
            $this->_urlArgs['action'] = 'defaultAction';
        }
        else{
            $this->_urlArgs['action'] = $action . 'Action';
        }
    }

    /**
     * @desc Create a controller and calls an action on this controller. Both the ctrl and the action are defined by the URL
     * @return void
     */
    public function callCtrl() : void{
        call_user_func_array(array(new $this->_urlArgs['ctrl'], $this->_urlArgs['action']), array());
    }
}