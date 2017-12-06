<?php
	class APP
    {
        public $real_url;
        public $url;
        public $html;

        public function __construct()
        {
            $method = $_SERVER['REQUEST_METHOD'];
            if(method_exists($this, $method)){
                $this->$method();
            }
        }

        private function POST()
        {
            require_once 'parse.php';
            foreach($_POST as $k=>$v){
                if(method_exists($this, $k)){
                    $this->$k($v);
                }
            }
        }

        private function URL_SEARCH($url)
        {			
            if(trim($url) == ''){
                echo '
                <div class="alert alert-dismissible alert-danger">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Ошибка!</strong> Нету URL для парсинга!.
                </div>';
                return;
            }
			$temp = $_POST['template'];
			
            if($temp == 'template'){
                $this->url = $url;
                $data = $this->CurlSait($url);
                $this->real_url = $this->RealURL($data);
            }else{
                $this->real_url = $url;
            }
            $this->html = $this->CurlSait($this->real_url);

            $this->save_sait($this->html);
        }

        private function save_sait($html)
        {
            $parse = new PARSE($html, $this->real_url);
        }

        /*---------------------------------------------------*/
        public function Textarea()
        {
            if(count($this->html) > 0){
                echo '<textarea style="width: 1261px; height: 600px; margin-left: 0px; margin-right: 0px;">';
                Print_R($this->html);
                echo '</textarea>';
            }
        }

        /*------------------------*/
        private function CurlSait($url)
        {
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
            $data = curl_exec($curl);
            curl_close($curl);
            return $data;
        }

        private function RealURL($data)
        {
            $real_url = '';
            $ds = explode('<iframe', $data);
            $ps = explode("</iframe>", $ds[2]);
            $pr = explode('"',$ps[0]);
            foreach($pr as $k=>$v){
                if(substr($v, 0, 4) == 'http'){
                    $real_url = $v;
                }
            }
            return $real_url;
        }
    }
?>
