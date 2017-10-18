<?php
/* No validation :) */
class HTML_QuickForm_Rule_Skip extends HTML_QuickForm_Rule{
    function validate($value, $options = null){
        return true;
    }

    function getValidationScript($options = null){
        return array(null, 'false');
    }
}