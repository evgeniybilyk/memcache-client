<?php

class StreamMemcacheClient
{
    private $resource;
    private $lastResult;

    private $errReplies = [
        "ERROR",
        "CLIENT_ERROR",
        "SERVER_ERROR"
    ];

    public function __construct($host)
    {
        $this->resource = stream_socket_client($host, $errno, $errstr);

        if (!$this->resource) {
            throw new Exception($errstr, $errno);
        }
    }

    /**
     * @return string
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public function writeToStream($str)
    {
        fwrite($this->resource, $str . "\r\n");

        return $this->readLineStream();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $exptime
     * @param int $flags
     *
     * @return string
     */
    public function set($key, $value, $exptime = 0, $flags = 0)
    {
        return $this->writeToStream("set $key $flags $exptime " . strlen($value) . "\r\n" . $value);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function get($key)
    {
        $strResult = $this->writeToStream("get $key");
        $value = "";
        $arrResult = explode(" ", $strResult);
        if ($arrResult[0] === "VALUE") {
            $value = trim(fread($this->resource, $arrResult[3] + 2));
        }
        return $value;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function delete($key)
    {
        return $this->writeToStream("delete $key");
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    private function readLineStream()
    {
        $strResult = trim(fgets($this->resource));
        $this->lastResult = $strResult;

        $arrResult = explode(" ", $strResult);
        if (in_array($arrResult[0], $this->errReplies)) {
            throw new Exception($this->lastResult);
        }

        return $strResult;
    }
}

//try {
//    $client = new StreamMemcacheClient("localhost:11211");
//} catch (Exception $e) {
//    echo "Error init stream: " . $e->getMessage();
//}
//
//try {
//    var_dump($client->set('myKey', 'myVal'));
//    //var_dump($client->get('myKey'));
//    var_dump($client->delete('myKey1'));
//} catch (Exception $e) {
//    echo $e->getMessage();
//}
