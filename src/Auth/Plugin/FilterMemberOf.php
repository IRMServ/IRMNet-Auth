<?php

namespace Auth\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class FilterMemberOf extends AbstractPlugin {

    public function filter($arrayfilter) {
        $return = array();
        foreach($arrayfilter as $item) {
            $slices = explode(',',$item);
            list($g,$group) = explode('=',$slices[0]);
            $return[] = $group;
        }
        return $return;
    }

}

?>
