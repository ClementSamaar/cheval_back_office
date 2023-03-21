<?php

class DefaultCtrl
{
    public function defaultAction() : void {
        $A_content = [
            'title' => 'Accueil',
            'bodyView' => 'default/default',
            'bodyContent' => null
        ];

        View::show('common/template', $A_content);
    }
}