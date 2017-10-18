<?php
require_once("HTML/QuickForm/text.php");
class HTML_QuickForm_CustomText extends HTML_QuickForm_text{
    function HTML_QuickForm_CustomText($elementName=null, $elementLabel=null, $attributes=null)
    {
        if($attributes)
        {
            if(is_string($attributes))
            {
                // TODO preg_replace
            } 
            elseif(is_array($attributes)) 
            {
                if(isset($attributes['maxlength']))
                {
                    unset($attributes['maxlength']);
                }
            }
        }
        $this->HTML_QuickForm_text($elementName, $elementLabel, $attributes);
    }
    
    function HTML_QuickForm_Text($elementName=null, $elementLabel=null, $attributes=null)
    {
        if($attributes)
        {
            if(is_string($attributes))
            {
                // TODO preg_replace
            } 
            elseif(is_array($attributes)) 
            {
                if(isset($attributes['maxlength']))
                {
                    unset($attributes['maxlength']);
                }
            }
        }
        parent::__construct($elementName, $elementLabel, $attributes);
    }
    
    function setMaxlength($maxlength)
    {
        return;
    }
}