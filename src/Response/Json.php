<?php
namespace Pyrite\Stack\Response;

use Pyrite\Stack\Response;
use Pyrite\Stack\Layout;
use Pyrite\Stack\View;

class Json implements Response
{

    private $file;

    private $layout;

    public function render(array $data)
    {
        if (trim($this->file) == '') {
            return $this->prettyPrint(json_encode($data));
        }
        
        $view = new View($this->file);
        $view->assignx($data);
        
        return $view->compile();
    }

    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function setViewFile($viewName)
    {
        $this->file = $viewName;
    }
    
    private function prettyPrint( $json )
    {
        $result = '';
        $level = 0;
        $prev_char = '';
        $in_quotes = false;
        $ends_line_level = NULL;
        $json_length = strlen( $json );
    
        for( $i = 0; $i < $json_length; $i++ ) {
            $char = $json[$i];
            $new_line_level = NULL;
            $post = "";
            if( $ends_line_level !== NULL ) {
                $new_line_level = $ends_line_level;
                $ends_line_level = NULL;
            }
            if( $char === '"' && $prev_char != '\\' ) {
                $in_quotes = !$in_quotes;
            } else if( ! $in_quotes ) {
                switch( $char ) {
                	case '}': case ']':
                	    $level--;
                	    $ends_line_level = NULL;
                	    $new_line_level = $level;
                	    break;
    
                	case '{': case '[':
                	    $level++;
                	case ',':
                	    $ends_line_level = $level;
                	    break;
    
                	case ':':
                	    $post = " ";
                	    break;
    
                	case " ": case "\t": case "\n": case "\r":
                	    $char = "";
                	    $ends_line_level = $new_line_level;
                	    $new_line_level = NULL;
                	    break;
                }
            }
            if( $new_line_level !== NULL ) {
                $result .= "\n".str_repeat( "\t", $new_line_level );
            }
            $result .= $char.$post;
            $prev_char = $char;
        }
    
        return $result;
    }
}