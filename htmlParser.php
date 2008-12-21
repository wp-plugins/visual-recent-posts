<?php
/*
////////////////////////////////////////////////////////////////////////////////
// Jazarsoft HTML Parser                                                      //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
// VERSION      : 1.0                                                         //
// AUTHOR       : James Azarja                                                //
// CREATED      : 2 May 2001                                                  //
// WEBSITE      : http://www.jazarsoft.com/                                   //
// SUPPORT      : support@jazarsoft.com                                       //
// BUG-REPORT   : bugreport@jazarsoft.com                                     //
// COMMENT      : comment@jazarsoft.com                                       //
// LEGAL        : Copyright (C) 2001 Jazarsoft.                               //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
// This code may be used and modified by anyone so long as  this header and   //
// copyright  information remains intact.                                     //
//                                                                            //
// The code is provided "as-is" and without warranty of any kind,             //
// expressed, implied or otherwise, including and without limitation, any     //
// warranty of merchantability or fitness for a  particular purpose.&#9552;         //
//                                                                            //
// In no event shall the author be liable for any special, incidental,        //
// indirect or consequential damages whatsoever (including, without           //
// limitation, damages for loss of profits, business interruption, loss       //
// of information, or any other loss), whether or not advised of the          //
// possibility of damage, and on any theory of liability, arising out of      //
// or in connection with the use or inability to use this software.&#9552;&#9552;         //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
// HISTORY :                                                                  //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
// 1.0, May 2001                                                              //
//      - Initial Development (Convert from Pascal/Delphi)                    //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
*/

class htmlparser_class
{
      var $html="";
      var $ontagfound="";
      var $ontextfound="";
      var $elements=array();

      function InsertHTML($htmlcode)
      {
               $this->html = "";
               $this->html=$htmlcode;
               return true;
      }

      function LoadHTML($filename)
      {
               $this->html = "";
               if (!file_exists ($filename))
               {
                  //return false;
               }
							 //$filename="http://www.dynamick.it";
							 //echo $filename."<hr>";
               $fh = @fopen (trim($filename), "r");
               if ($fh!=false)
               {
                  //flock($fh,2);
                  while (!feof ($fh))
                  {
                        $buffer = fgets($fh, 10240);
                        if ($buffer!="")
                        {
                               $this->html.=trim($buffer);
                        }
                  }
                  //flock($fh,3);
                  fclose($fh);
                  return true;
               }
               else return false;
      }

      function GetElements(&$result)
      {
               if (count($this->elements)==0) { return false; $result=array();  }
               $result=$this->elements;
               return true;
      }

      function Parse()
      {
               $ignorechar = false;
               $intag = false;
               $tagdepth = 0;
               $line="";
               $text="";
               $tag="";
               if ($this->html=="")
               { return false;}

               $raw = split ("\r\n", $this->html);

               while (list($key, $line) = each ($raw))
               {
                     $htmlline = htmlentities($line);

                     if ($line=="") { continue; }

                     $line = trim($line);
                     for ($charsindex=0;$charsindex<=strlen($line);$charsindex++)
                     {
                         if ($ignorechar==true) { $ignorechar=false;}

                         if (($line[$charsindex]=="<") && (!$intag))
                         {
                            if ($text!="")
                            {
                               /* Found Text */
                               $this->elements[]=$text;
                               $text="";
                            }
                            $intag = true;
                         } else
                         
                         if (($line[$charsindex]==">") && ($intag))
                         {
                            $tag .=">";
                            /* Tag Found */
                            $this->elements[]=$tag;
                            $ignorechar = true;
                            $intag=false;
                            $tag="";
                         }
                         
                         if ((!$ignorechar) && (!$intag))
                         {
                             $text .= $line[$charsindex];
                         } else
                         if ((!$ignorechar) && ($intag))
                         {

                             $tag .= $line[$charsindex];
                         }

                     }
               }
               return true;
      }

  function download($file_source, $file_target) {
    $rh = @fopen($file_source, 'rb');
    $wh = fopen($file_target, 'wb');
    if ($rh===false || $wh===false) { return true; }
    while (!feof($rh)) {
      if (fwrite($wh, fread($rh, 1024)) === FALSE) {
        echo 'Download error: Cannot write to file ('.$file_target.')';
        return true;
      }
    }
    fclose($rh);
    fclose($wh);
    return false;
  }
  
  
  function getAttributes($html) {
    //preg_match_all('/(\w+\s*=\s*"[^"]*")*|(\w+\s*=\s*\'[^\']*\')*/',$html,$attr);
    $attrWithDblQuote='((\w+)\s*=\s*"([^"]*)")*';
    $attrWithQuote='((\w+)\s*=\s*\'([^\']*)\')*';
    $attrWithoutQuote='((\w+)\s*=(\w))*';
    preg_match_all('/'.$attrWithDblQuote.'|'.$attrWithQuote.'|'.$attrWithoutQuote.'/',$html,$attr);
    //echo "#<pre>";print_r($attr);echo "</pre>";
    if (is_array($attr))
    foreach ($attr as $count=>$attrArr) {
      if (is_array($attrArr))
      foreach ($attrArr as $i=>$a) {
        if ($a!="" and $count==2) $res[$a]=$attr[3][$i];
        if ($a!="" and $count==5) $res[$a]=$attr[6][$i];
        if ($a!="" and $count==8) $res[$a]=$attr[9][$i];
      } 
    }
    return $res;
  }   
     
  function linkAnalyzer($url) {
    $regexp = "(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?";
    $regexp = "(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?";
    eregi($regexp,$url,$ret);
    $r["url"]=$url;  
    $r["scheme"]=$ret[2];
    $r["authority"]=$ret[4];
    $r["path"]=$ret[5];
    $r["query"]=$ret[7];
    $r["fragment"]=$ret[9];  
    return $r;  
  }

  function getTagResource($tag="a") {
    global $elements;
    $tag="<".$tag;
    while (list($key, $code) = each ($this->elements)){
      if (strtolower(substr($code,0,strlen($tag)))==$tag) {
        $attribArr[]=$this->getAttributes($code);
      }
    }
    return $attribArr;
  }

  
  function includeImportCss($html,$path="",$level=1) {
  	global $urlToGrabArr;
    
    preg_match_all('/@import\s[\"]*((http:\/\/[^\/]*){0,1}(.*?))[\"]{0,1};/i', $html,$result);
    //echo "<h1>#".dirname($path)."#</h1><pre>";print_r($result);echo "</pre>";die;
    if (is_array($result[3])) 
    foreach ($result[3] as $k=>$v) {
    	$url=dirname($path)."/".$result[3][$k];
			//echo "url ricavato: $url<br>";

      if ($this->url_exists($url)) {
        $css=@file_get_contents($url); 
        //die ($css);
        $html=preg_replace('/@import\s[\"]*((http:\/\/[^\/]*){0,1}(.*?))[\"]{0,1};/i', $css, $html);
        //if (strstr($html,"@import")) $html=importCss($html,dirname($result[3][$k]),$level++);
			}
    }
    return $html;
  }

  
  function url_exists($url)
  {
   $handle = @fopen($url, "r");
   if ($handle === false)
    return false;
   fclose($handle);
   return true;
  }      
  
}
?>
