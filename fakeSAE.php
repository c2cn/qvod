<?php
class SaeFetchurl{
    private $url    = '';
    private $data   = '';
    private $header = array();
    private $method = 'GET';
    
    public function setHeader($k,$v){
        $this->header[$k] = $v;
    }
    
    public function setMethod($m){
        $this->method = $m;
    }
    
    public function setPostData($d){
        $this->data = $d;
    }
    
    public function fetch($url){
        $ch	= curl_init( $url );
        if( $this->method =='POST' ){
            curl_setopt( $ch, CURLOPT_POST, 1);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->data );
        }
        curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        if( $this->header['User-Agent'] ) curl_setopt( $ch, CURLOPT_USERAGENT, $this->header['User-Agent'] );
        if( $this->header['Referer'] ) curl_setopt( $ch, CURLOPT_REFERER, $this->header['Referer'] );
        $result = curl_exec( $ch );
        return $result;
    }
}

class SaeStorage{
    public function fileExists($dir, $file){
        if( file_exists($file) ){
            $r = true;
        }else{
            $r = false;
        }
        return $r;
    }

    public function read($dir, $file){
        $file = $dir.'/'.$file;
        if( file_exists($file) ){
            $r = file_get_contents($file);
        }else{
            $r = false;
        }
        return $r;
    }
    
    public function write($dir, $file, $str){
        $file = $dir.'/'.$file;
        if( file_exists($file) ){
            $f = fopen($file, 'w');
            fwrite($f, $str);
            fclose($f);
            $r = true;
        }else{
            if( !file_exists($dir) )
                mkdir($dir);
            $f = fopen($file, 'w');
            fwrite($f, $str);
            fclose($f);
            $r = true;
        }
        return $r;
    }
    
    public function delete($dir, $file){
        $file = $dir.'/'.$file;
        if( file_exists($file) ){
            unlink($file);
        }
    }
    
    public function getAttr($dir, $file){
        $r = array(); 
        $file = $dir.'/'.$file;
        if( file_exists($file) ){
            $r['datetime'] = filemtime($file);
        }else{
            $r['datetime'] = '2000-01-01 00:00:00';
        }
        return $r;
    }
    
    public function getList($dir, $prefix='', $limit=10){
        $r = array();
        if( file_exists($dir) ){
            $d = opendir($dir);
            while( $f=readdir($d) ) array_push($r,$f);
            closedir($d);
        }
        return $r;
    }
}
?>