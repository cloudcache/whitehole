<?
class MACAddress 
{
    private $_macXenPrefix = '02:00:c0:';

    private $_macAddress;

     public function _generateXenMAC()
     {
        $this->_macChars = md5(uniqid(mt_rand(), TRUE));
        
        $this->_macAddress = substr($this->_macChars, 0,2) . ':';
        $this->_macAddress .= substr($this->_macChars, 8,2) . ':';
        $this->_macAddress .= substr($this->_macChars, 12,2);
        return $this->_macXenPrefix.$this->_macAddress;
     }
}
#$obj = new MACAddress;
#echo $obj->_generateXenMAC();
?>
