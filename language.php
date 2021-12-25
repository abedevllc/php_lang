<?php

namespace Language;

if(!defined("LanguageFramework")){ die("Access Denied!"); }

if(!defined("DS")){ define("DS", DIRECTORY_SEPARATOR); }

class Language
{
    public $Name;
    public $Icon;
    public $Code;
    private $Default;

    private $Texts;

    public function __construct($Default, $Code = null)
    {
        $this->Default = $Default;

        if($Code != null)
        {
            $this->Code = $Code;
        }
        else
        {
            $this->Code = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        }

        $this->Initialize();
    }

    private function Initialize()
    {
        define("LANGUAGE_ROOT_PATH", dirname(__FILE__));
        define("LANGUAGE_FILE_PATH", LANGUAGE_ROOT_PATH . DS . "languages");
      
        if(!file_exists(LANGUAGE_FILE_PATH))
        {
            mkdir(LANGUAGE_FILE_PATH);
        }
        
        if(file_exists(LANGUAGE_FILE_PATH . DS . $this->Code . DS . $this->Code . ".php"))
        {
            define("LANGUAGE_CURRENT_PATH", LANGUAGE_FILE_PATH . DS . $this->Code);
        }
        else if(file_exists(LANGUAGE_FILE_PATH . DS . $this->Default . DS . $this->Default . ".php"))
        {
            $this->Code = $this->Default;
            define("LANGUAGE_CURRENT_PATH", LANGUAGE_FILE_PATH . DS . $this->Default);
        }
        
        if(defined("LANGUAGE_CURRENT_PATH"))
        { 
            $Lang = array();
            require_once(LANGUAGE_CURRENT_PATH . DS . $this->Code . ".php");
            $this->Texts = $Lang;

            if(isset($Lang["LANGUAGE_NAME"]))
            {
                $this->Name = $Lang["LANGUAGE_NAME"];
            }

            if(file_exists(LANGUAGE_CURRENT_PATH . DS . $this->Code . ".png"))
            {
                $this->Icon = LANGUAGE_CURRENT_PATH . DS . $this->Code . ".png";
            }
        }
    }

    public function Text($Text)
    { 
        if($this->Texts != null && isset($this->Texts[$Text]))
        {
            return $this->Texts[$Text];
        }
        else
        {
            return $Text;
        }
    }

    public function GetTexts()
    {
        return $this->Texts;
    }

    public function GetLanguages()
    {
        $result = null;

        $dirs = glob(LANGUAGE_FILE_PATH . DS . "*" , GLOB_ONLYDIR);

        if($dirs != null && count($dirs) > 0)
        {
            foreach($dirs as $dir)
            {
                $base_name = basename($dir);
              
                if(file_exists($dir . DS . $base_name . ".php"))
                {
                    if($result == null)
                    {
                        $result = array();
                    }

                    $l = new \stdClass();
                    $l->Code = $base_name;
                    $l->Icon = (file_exists($dir . DS . $base_name . ".png") ? $base_name . ".png": null);                         
                    $Lang = array();
                    require($dir . DS . $base_name . ".php");                  
                    $l->Name = (isset($Lang["LANGUAGE_NAME"]) ? $Lang["LANGUAGE_NAME"] : null);
                                       
                    if($l->Name != null)
                    {
                        array_push($result, $l);
                    }

                    unset($Lang);
                }
            }
        }
        
        return $result;
    }
}

?>