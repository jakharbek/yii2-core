<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 23.03.2018
 * Time: 13:40
 */
namespace jakharbek\core\components;

use Yii;
use \yii\base\Component;

    class ToolsComponent extends Component{
        public function ucfirst($text) {
            mb_internal_encoding("UTF-8");
            return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
        }
        public function getFirst($text) {
            mb_internal_encoding("UTF-8");
            return mb_strtoupper(mb_substr($text, 0, 1));
        }
        public function wordsCut($str = null,$length_str = 200,$points = "...")
        {
            mb_internal_encoding("UTF-8");
            $str = $str." ";
            $return_str = null;
            if($str == null){return;}
            if(strlen($str) == 0){return;}
            $pattern = '#(.*?)\s#';
            preg_match_all($pattern,$str,$words);
            $words = $words[1];
            foreach ($words as $word)
            {
                if(strlen(trim($return_str." ".$word)) > $length_str){
                    $return_str .= $points;
                    break;
                }
                $return_str .= " ".$word;
            }
            return $return_str;
        }
        function abbrName($name = "title"){
            if($name == null){return;}
            preg_match_all("#[a-zA-ZА-Яа-я\`\'\-]+#u",$name,$data);
            $last_name = $data[0][0];
            $first_name = $data[0][1];
            $father_name = $data[0][2];
            $abbs = [];
            if(@$this->getFirst($first_name)):
                $abbs[] = $this->getFirst($first_name);
            endif;
            if(@$this->getFirst($father_name)):
                $abbs[] = $this->getFirst($father_name);
            endif;
            $abbs[] = $last_name;
            $abbs_string = @implode(".",$abbs);

            return $abbs_string;
        }
        function slug($string, $replace = array(), $delimiter = '-') {
            $string = $this->translit($string);
            if (!extension_loaded('iconv')) {
                throw new Exception('iconv module not loaded');
            }
            $oldLocale = setlocale(LC_ALL, '0');
            setlocale(LC_ALL, 'en_US.UTF-8');
            $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
            if (!empty($replace)) {
                $clean = str_replace((array) $replace, ' ', $clean);
            }
            $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
            $clean = strtolower($clean);
            $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
            $clean = trim($clean, $delimiter);
            setlocale(LC_ALL, $oldLocale);
            return $clean;
        }
        function translit($string){
            $converter = array(
                'а' => 'a',   'б' => 'b',   'в' => 'v',
                'г' => 'g',   'д' => 'd',   'е' => 'e',
                'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
                'и' => 'i',   'й' => 'y',   'к' => 'k',
                'л' => 'l',   'м' => 'm',   'н' => 'n',
                'о' => 'o',   'п' => 'p',   'р' => 'r',
                'с' => 's',   'т' => 't',   'у' => 'u',
                'ф' => 'f',   'х' => 'h',   'ц' => 'c',
                'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
                'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
                'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

                'А' => 'A',   'Б' => 'B',   'В' => 'V',
                'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
                'Ё' => 'E',   'Ж' => 'J',  'З' => 'Z',
                'И' => 'I',   'Й' => 'Y',   'К' => 'K',
                'Л' => 'L',   'М' => 'M',   'Н' => 'N',
                'О' => 'O',   'П' => 'P',   'Р' => 'R',
                'С' => 'S',   'Т' => 'T',   'У' => 'U',
                'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
                'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sh',
                'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
                'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
            );
            return strtr($string, $converter);
        }
        public function viewsUp($id = null,$type = "post",$callback = null){
            if(!is_callable($callback)){return false;}
            if($id == null){return false;}
            if($type == null){return false;}
            $session = Yii::$app->session;
            $session_id = base64_encode($id.$type);
            if(!$session->has($session_id)){

                $callback();
                $session->set($session_id,time());
            }
        }
    }