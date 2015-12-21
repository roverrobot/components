<?php
/**
 * The syntax plugin to handle <project-file> tags
 *
 */

require_once DOKU_PLUGIN . 'syntax.php';

class syntax_plugin_components_slice extends DokuWiki_Syntax_Plugin {
 
    function getType() { 
        return 'substition';
    }
        
    function getPType() { 
        return 'normal';
    }
        
    function getSort() { 
        return 1; 
    }
    
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<slice\b.*?/>',
            $mode, 'plugin_components_slice'); 
    }

    private function parse($tag) {
        $xml = DOMDocument::loadXML($tag);
        if ($xml == false) return NULL;
        $attributes = array();
        foreach ($xml->firstChild->attributes as $attribute)
            $attributes[$attribute->name] = $attribute->value;
        return $attributes;
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler) {
        switch ($state) {
            case DOKU_LEXER_SPECIAL:
                $attr = $this->parse($match);
                if (!isset($attr['from']) || !$attr['from']) return false;
                if (!isset($attr['to']) || !$attr['to']) return false;
                global $ID;
                $attr['id'] = $ID;
                return $attr;
        }
        return false;
    }
 
    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        if (!$data || $mode != 'xhtml') return;
        $id = $data['id'];
        $from = $data['from'];
        $to = $data['to'];
        $form = new Doku_Form(array('id' => "slice_$from_to", "class" => "wiki_slice_form"));
        $form->addHidden('from', $from);
        $form->addHidden('to', $to);
        $form->addHidden('id', $id);
        $form->addElement(form_makeButton('submit', '', "Slice $from--$to"));
        $renderer->doc .= $form->getForm();
    }
}
