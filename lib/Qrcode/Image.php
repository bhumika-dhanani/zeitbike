<?php
include_once 'Qrcode/qrlib.php';
class Qrcode_Image
{
    const PNG_TEMP_DIR = 'tmp/';
    const PNG_WEB_DIR = '';
    
    protected $_errorCorrectionLevel = 'L';
    protected $_size = 2;
    protected $_data = null;
    protected $_dir = null;
    protected $_filename = null;
    public function __construct(array $config = array())
    {
        if(isset($config['level']))
        {
            $this->_errorCorrectionLevel = $config['level'];
        }
        
        if(isset($config['size'])){
            $this->_size = $config['size'];
        }   
    }
    
    public function setData($data = null)
    {
        if(!is_null($data)){
          $this->_data = $data;  
        }
    }
    
    public function getData()
    {
        return $this->_data;
    }
    
    public function setBaseDir($dir = null)
    {
        return $this->_dir = $dir;
    }
    
    public function getBaseDir()
    {
        return $this->_dir;
    }
    
    public function setFileName($file)
    {
        $this->_filename = $file;
    }
    
    public function getFileName()
    {
        return $this->_filename;
    }
    
    public function generatePngImage()
    {   
        
        $dir = $this->getBaseDir();
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $data = $this->getData();
        $fileName = $dir.$this->getFileName();
        try{
            QRcode::png($data,$fileName,$this->_errorCorrectionLevel,$this->_size,2,4,true);
        }catch(Exception $e){
            throw $e;
        }
        
    }
}

