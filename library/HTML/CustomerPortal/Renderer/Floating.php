<?php

require_once("HTML/QuickForm2/Renderer/Default.php");

/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 23.09.12
 * Time: 14:25
 */
class HTML_Customerportal_Renderer_Floating extends HTML_QuickForm2_Renderer_Default
{
    private $_elementCounter = 0;

    public $templatesForClass = array(
        'html_quickform2_element_inputhidden' => '<div style="display: none;">{element}</div>',
        'html_quickform2' => '<div class="quickform">{errors}<form{attributes}><div>{hidden}{content}</div></form><qf:reqnote><div class="reqnote">{reqnote}</div></qf:reqnote></div>',
        'html_quickform2_container_fieldset' => '<fieldset{attributes}><qf:label><legend id="{id}-legend">{label}</legend></qf:label>{content}</fieldset>',
        'special:error' => array(
            'prefix'    => '<div class="errors"><qf:message><p>{message}</p></qf:message><ul><li>',
            'separator' => '</li><li>',
            'suffix'    => '</li></ul><qf:message><p>{message}</p></qf:message></div>'
        ),
        'html_quickform2_element' => '<div class="row{odd} {type}"><p class="label"><qf:required><span class="required">*</span></qf:required><qf:label><label for="{id}">{label}</label></qf:label></p><div class="{type} element<qf:error> error</qf:error>"><qf:error><span class="error">{error}<br /></span></qf:error>{element}</div></div>',
        'html_quickform2_container_group' => '<div class="row {class}"><p class="label"><qf:required><span class="required">*</span></qf:required><qf:label><label>{label}</label></qf:label></p><div class="element group<qf:error> error</qf:error>" id="{id}"><qf:error><span class="error">{error}<br /></span></qf:error>{content}</div></div>',
        'html_quickform2_container_repeat' => '<div class="row repeat" id="{id}"><qf:label><p>{label}</p></qf:label>{content}</div>'
    );

    /**
     * Renders a generic element
     *
     * @param HTML_QuickForm2_Node $element Element being rendered
     */
     public function renderElement(HTML_QuickForm2_Node $element)
     {
         $this->_elementCounter++;

         $elTpl = $this->prepareTemplate($this->findTemplate($element), $element);
         $this->html[count($this->html) - 1][] = str_replace(
             array('{element}', '{id}', '{odd}', '{type}'), array($element, $element->getId(), ($this->_elementCounter%2==0?"2":"1"), $element->getType()), $elTpl
         );

         if($element instanceof HTML_QuickForm2_Element_Textarea) {
             $this->_elementCounter++;
         }

     }

    public function finishForm(HTML_QuickForm2_Node $form) {
        $form->addClass("clearfix");

        return parent::finishForm($form);
    }
}