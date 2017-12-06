<?php
    require_once 'unit/simple_html_dom.php';
	class PARSE
    {
        private $html;
        private $path;
        private $url;
        private $console;
        private $css_failes = array();

        public function __construct($html, $url)
        {
            $url = str_replace('index.html', '', $url);            
            $this->url = $url;
            $this->html = $html;
            $this->save();
            echo $this->path;
            echo $this->console;
        }

        private function save()
        {

            //Создаем папку
            $this->title();
            //Сохраняем HTML код    
            $this->create_html();
            //Сохраняем логотип
            $this->logo();
            //Сохраняем все CSS
            $this->css();
            //Сохраняем Script
            $this->script();
            //Сохраняем все изображения
            $this->img();
            //Сохраняем картинки в DIV и в selection
            $this->images_from_dic_styles();
            //Выводим список всех фалов которые необходимо скачать в css файлах
            $this->images_from_css();
            //Скачиваем все файлы по ссылкам
            $this->all_href_files();
            //print_r($this->console);
            //Парсинг всех страниц по ссылке <a href и так же их парсинг
            //$this->background_img();            
        }

        private function title()
        {            
            $t = explode('<title>', $this->html);
            $ts = explode('</title>', $t[1]);
            $title = md5($ts[0].date('dmY'));
            $this->path = $title;
            $this->create_path($title);
            echo "<pre>Создали папку '$title'</pre>";
            echo '<pre>Открыть сайт для просмотра <a href="'.$title.'" target="_blank">Открыть</a></pre>';
        }

        private function create_html()
        {
            $myfile = fopen($this->path."/index.html", "w") or die("Unable to open file!");
            $txt = $this->html;
            fwrite($myfile, $txt);
            fclose($myfile);
            echo "<pre>Создали файл index.html Путь: $this->path/index.html </pre>";
        }

        private function logo()
        {
            $website = str_get_html($this->html);
            $link = $website->find('link[rel="icon"]');
            if(count($link <= 0)){
                return;
            }
            
            foreach($website->find('link[rel="icon"]') as $s)
            {
                $b = true;
                $ico = $s->href;
                if(substr($ico, 0, 2) == '//'){$b = false;}
                if(substr($ico, 0, 2) == 'ht'){$b = false;}
                if($b){
                  $ps = explode("/", $ico);
                  $path = $this->path.'/';
                  for($i=0;$i<count($ps)-1;$i++){
                      $path .= $ps[$i].'/';
                  }
                  $this->create_path($path);
                  $p = get_headers($this->url.$ico);
                  print_r($p);
                  return $p;
                  //$this->downloadFile($this->url.$ico, $this->path.'/'.$ico);
                }
            }
            echo "<pre>Сохранили логотип файла Путь: $this->path/$ico </pre>";            
        }

        private function css()
        {
            echo "<pre>Сохраняем стили:<br />";
            $website = str_get_html($this->html);
            if($website == false){
                exit;
            }
            foreach ($website->find('link[rel="stylesheet"]') as $stylesheet)
            {
                $b = true;
                $stylesheet_url = $stylesheet->href;
                if(substr($stylesheet_url, 0, 2) == '//'){$b = false;}
                if(substr($stylesheet_url, 0, 2) == 'ht'){$b = false;}
                if($b){
                  $ps = explode("/", $stylesheet_url);
                  $path = $this->path.'/';
                  for($i=0;$i<count($ps)-1;$i++){
                      $path .= $ps[$i].'/';
                  }
                  $this->create_path($path);
                  if($this->downloadFile($this->url.$stylesheet_url, $this->path.'/'.$stylesheet_url)){
                    echo $this->path.'/'.$stylesheet_url."<br>";
                  }
                  $this->css_failes[] = $this->path.'/'.$stylesheet_url;
                }
            }
            echo "</pre>";
        }

        private function script()
        {
            echo "<pre>Сохраняем JS scripts:<br />";
            $website = str_get_html($this->html);
            foreach ($website->find('script') as $script)
            {
                $b = true;
                $script_url = $script->src;
                if(substr($script_url, 0, 2) == '//'){$b = false;}
                if(substr($script_url, 0, 2) == 'ht'){$b = false;}
                if(trim($script_url) == ''){$b = false;}
                if($b){
                      $ps = explode("/", $script_url);
                      $path = $this->path.'/';
                      for($i=0;$i<count($ps)-1;$i++){
                          $path .= $ps[$i].'/';
                      }
                      $this->create_path($path);
                      $this->downloadFile($this->url.$script_url, $this->path.'/'.$script_url);
                      echo $this->path.'/'.$script_url."<br>";
                }
            }
            echo "</pre>";
        }

        private function img()
        {
            echo "<pre>Сохраняем картинки:";
            $website = str_get_html($this->html);
            foreach ($website->find('img') as $img)
            {
                $img_url = $img->src;
                $img_url = str_replace("'", "", $img_url);
                $img_url = str_replace('"', "", $img_url);

                if(trim($img_url) !== ''){
                    $ps = explode("/", $img_url);
                    $path = $this->path.'/';
                    for($i=0;$i<count($ps)-1;$i++){
                        $path .= $ps[$i].'/';
                    }
                    $this->create_path($path);
                    $this->downloadFile($this->url.$img_url, $this->path.'/'.$img_url);
                    echo $this->path.'/'.$img_url."<br>";
                }
            }
            echo "</pre>";
        }

        private function images_from_dic_styles()
        {
            $website = str_get_html($this->html);
            echo "<pre>Скачивание картинок со стилей в DIV: <br />";
            $dsp = array();
            foreach($website->find('div') as $div){ $dsp[] = $div;}
            foreach($website->find('section') as $div){ $dsp[] = $div;}

            foreach($dsp as $div){
               $st = $div->style;
               if(trim($st) !== ''){
                 $t = explode("url(", $st);
                 $s = explode(')', $t[1]);
                 $img_url = $s[0];
                 $img_url = str_replace("'", '', $img_url);
                 $img_url = str_replace('"', '', $img_url);
                 $img_url = str_replace('&quot;', '', $img_url);

                 $ps = explode("/", $img_url);
                 $path = $this->path.'/';
                 for($i=0;$i<count($ps)-1;$i++){
                     $path .= $ps[$i].'/';
                 }
                 $this->create_path($path);
                 $this->downloadFile($this->url.$img_url, $this->path.'/'.$img_url);
                 echo $this->path.'/'.$img_url."<br>";
               }
            }
            echo "</pre>";
        }

        private function images_from_css()
        {
            echo "<pre><b>Необходимо скачать файлы для полноценной работы в ручном режиме:</b> <br />";
            $fail = $this->css_failes;
            foreach($fail as $fail_css){
               $css_text = file_get_contents($fail_css);
               $pst = explode("url(", $css_text);
               $img = array();
               foreach($pst as $k=>$v){
                  $t = explode(")", $v);
                  if(strlen($t[0]) < 150){
                    $img[] = str_replace('"', '', $t[0]);
                  }
               }

               foreach($img as $i=>$c){
                 $css_file = str_replace($this->path."/", '', $fail_css);
                 $path = '';
                 $ps = explode("/", $css_file);
                 $is = count($ps)-1;
                 unset($ps[$is]);
                 foreach($ps as $p=>$v){$path .= $v."/";}
                 $url_file = str_replace("'", '', $this->url.$path.$c);
                 echo  '<a href="'.$url_file.'">'.$url_file.'</a><br />';
               }
            }
            echo  "</pre>";
        }

        private function all_href_files()
        {
           $website = str_get_html($this->html);
           $a = array();
           foreach($website->find('a') as $d){
             $href = $d->href;
             $b = true;
             if($href == '#'){$b = false;}
             $ps = explode("#", $href);
             if(count($ps) > 1){$b = false;}
             if(trim($href) == ''){$b = false;}
             if(trim($href) == './'){$b = false;}
             if(trim($href) == 'index.html'){$b = false;}

             if($b){$a[] = $href;}
           }
           //Удаляем одинаковые значения
           $a=array_unique($a);
           foreach($a as $href){
              $url = $this->url.$href;

              $curl = curl_init();
              curl_setopt($curl, CURLOPT_FAILONERROR, 1);
              curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
              curl_setopt($curl, CURLOPT_TIMEOUT, 10); // times out after 4s
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // return into a variable
              curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
              curl_setopt($curl, CURLOPT_URL, $url);
              curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6");
              curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
              $html = curl_exec($curl);
              curl_close($curl);

              $myfile = fopen($this->path."/".$href, "w") or die("Unable to open file!");
              fwrite($myfile, $html);
              fclose($myfile);

              $this->html = $html;
              echo  "<pre>Создали файл $href Путь: $this->path/$href </pre>";

              $this->img();
              //Сохраняем картинки в DIV и в selection
              $this->images_from_dic_styles();
              $this->css();
                //Сохраняем Script
              $this->script();
                //Выводим список всех фалов которые необходимо скачать в css файлах
              $this->images_from_css();              
           }
        }

        private function create_path($path)
        {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }

        private function downloadFile($url, $path)
        {
            $newfname = $path;
            $file = fopen ($url, 'rb');
            if(!$file){
              echo  "<pre>ошибка скачивания файла: $url</pre>";
              return false;
            }

            if ($file) {
                $newf = fopen ($newfname, 'wb');
                if ($newf) {
                    while(!feof($file)) {
                        fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                    }
                }
            }
            if ($file) {
                fclose($file);
            }
            if ($newf) {
                fclose($newf);
            }
        }
	}
