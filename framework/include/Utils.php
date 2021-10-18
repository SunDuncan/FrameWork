<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-10-15 18:19:46
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-15 18:22:36
 */
class Utils {
    /**
     *
     * @return mixed
     */
    public static function getAllHeaders()
    {
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * @param int $min
     * @param int $max
     * @return float|int
     */
    public static function randFloat($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * @param $path
     * @param bool $delDir
     * @return bool
     */
    public static function delFolder($path, $delDir = false)
    {
        $handle = @opendir($path);
        if ($handle)
        {
            while (false !== ($item = readdir($handle)))
            {
                if ($item != "." && $item != "..")
                {
                    if (is_dir("$path/$item"))
                    {
                        CommonUtils::delFolder("$path/$item", $delDir);
                    } else
                    {
                        unlink("$path/$item");
                    }
                }
            }
            closedir($handle);
            if ($delDir)
            {
                return rmdir($path);
            }
        } else
        {
            if (file_exists($path))
            {
                return unlink($path);
            } else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $offset
     * @return false|int
     */
    public static function today($offset = 0)
    {
        $date = date('Y-m-d', time() + $offset);
        $ts = strtotime($date);

        return $ts;
    }

    /**
     * @param string $relativeURI
     * @param null $sch
     * @return string
     */
    public static function absoluteURL($relativeURI = '', $sch = null)
    {
        if (is_null($sch))
        {
            if (isset($_SERVER['REQUEST_SCHEME']))
            {
                $scheme = $_SERVER['REQUEST_SCHEME'];
            } else
            {
                $scheme = 'http';
            }
        } else
        {
            $scheme = $sch;
        }

        if ($relativeURI == '/')
        {
            $url = $scheme . '://' . $_SERVER['HTTP_HOST'];
        } else if (strlen($relativeURI) <= 0)
        {
            $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        } else
        {
            $relativeComponents = explode('/', $relativeURI);

            $selfURL = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
            $urlComponents = explode('/', $selfURL);
            array_pop($urlComponents);

            for ($i = 0; $i < sizeof($relativeComponents); $i++)
            {
                $rc = $relativeComponents[$i];
                if ($rc == '..')
                {
                    array_pop($urlComponents);
                } else if (strlen($rc) > 0 && $rc != '.')
                {
                    array_push($urlComponents, $rc);
                }
            }

            $url = implode($urlComponents, '/');
        }

        return $url;
    }

    /**
     * @return string
     */
    public static function ipAddress()
    {
        $cip = "UNKNOWN";
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }

        return $cip;
    }


    /**
     * @param {array} $list
     * @param {string} $filename
     * @param {string} $encoding
     */
    public static function convertToCSV($list, $filename, $encoding = null)
    {
        $lines = array();
        foreach ($list as $line)
        {
            $lineStrings = array();
            foreach ($line as $cell)
            {
                $strCell = "\"" . str_replace("\"", "\"\"", $cell) . "\"";
                array_push($lineStrings, $strCell);
            }
            array_push($lines, implode(",", $lineStrings));
        }

        $strCSV = implode("\r\n", $lines);

        if ($encoding != null)
        {
            $strCSV = mb_convert_encoding($strCSV, $encoding, 'UTF-8');
        }

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $strCSV;
    }

    /**
     * @param string $guid
     * @param string $namespace
     * @return string
     */
    public static function createUid($guid = '', $namespace = '')
    {
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= CommonUtils::ipAddress();

        if (isset($_SERVER['REQUEST_TIME']))
        {
            $data .= $_SERVER['REQUEST_TIME'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $data .= $_SERVER['HTTP_USER_AGENT'];
        }
        if (isset($_SERVER['LOCAL_ADDR']))
        {
            $data .= $_SERVER['LOCAL_ADDR'];
        }
        if (isset($_SERVER['LOCAL_PORT']))
        {
            $data .= $_SERVER['LOCAL_PORT'];
        }
        if (isset($_SERVER['REMOTE_ADDR']))
        {
            $data .= $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SERVER['REMOTE_PORT']))
        {
            $data .= $_SERVER['REMOTE_PORT'];
        }

        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12);

        return $guid;
    }

    /**
     * @param {string} $str
     * @return array|null
     */
    public static function decodeQuery($str)
    {
        $params = null;
        try
        {
            $paramList = explode('&', $str);
            $params = array();
            foreach ($paramList as $param)
            {
                $kvPair = explode('=', $param);
                $key = $kvPair[0];
                $val = $kvPair[1];
                $params[$key] = rawurldecode($val);
            }
        } catch (Exception $e) {}

        return $params;
    }

    /**
     * @param $data
     * @param string $joint
     * @param bool $encode
     * @return string
     */
    public static function sortAndMerge($data, $joint = '',$encode=false)
    {
        $keys = array();
        foreach ($data as $key => $value)
        {
            array_push($keys, $key);
        }
        sort($keys,SORT_STRING);

        $query = array();
        foreach ($keys as $key)
        {
        	if($encode)
	        {
		        $item = $key . '=' . rawurlencode($data[$key]);
	        }else{
		        $item = $key . '=' . $data[$key];
	        }
            array_push($query, $item);
        }

        return implode($joint, $query);
    }

    /**
     * @param $data
     * @return string
     */
    public static function arrayToKeyValPair($data)
    {
        $query = '';
        if (is_null($data) == false)
        {
            $filed = array();
            foreach ($data as $key => $value)
            {
                $item = $key . '=' . rawurlencode($value);
                array_push($filed, $item);
            }

            if (sizeof($filed) > 0)
            {
                $query = implode('&', $filed);
            }
        }

        return $query;
    }

    /**
     * @param {array} $data
     * @return array
     */
    public static function cloneArray($data)
    {
        $clone = array();
        foreach ($data as $key => $item)
        {
            $clone[$key] = $item;
        }

        return $clone;
    }

    /**
     * @param {string} $uri
     * @param {array} $data
     * @return string
     */
    public static function urlJoint($uri, $data)
    {
        $joint = $uri;
        $kv = CommonUtils::arrayToKeyValPair($data);
        if (strlen($kv) > 0)
        {
        	if(sizeof(explode('?',$uri)) > 1)
	        {
		        $joint = $joint . '&' . $kv;
	        }else{
		        $joint = $joint . '?' . $kv;
	        }
        }

        return $joint;
    }

    /**
     * @param $size
     * @return string
     */
    public static function randomKey($size)
    {
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLNOPQRSTUVWXYZ';
        $key = '';
        while (strlen($key) < $size)
        {
            $key = $key . substr($dict, rand(0, strlen($dict) - 1), 1);
        }

        return $key;
    }

	/**
	 * @return float
	 */
	public static function msTime()
	{
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}

    /**
     * @param $uri
     * @param $data
     * @param string $method
     * @param array headers
     * @return mixed|null
     * @throws Exception
     */
    public static function sendRequest($uri, $data = null, $method = 'get', $headers = null)
    {
        $response = null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        if (strlen($uri) > 5 && strtolower(substr($uri, 0, 5)) == "https")
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (is_array($headers))
        {
            $httpHeaders = array();
            foreach ($headers as $key => $value)
            {
                array_push($httpHeaders, $key . ':' . $value);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if (strtolower($method) == 'post')
        {
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_POST, true);

            $sendData = null;
            if (is_array($data))
            {
                $sendData = CommonUtils::arrayToKeyValPair($data);
            } else
            {
                $sendData = $data;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
        } else
        {
            $uri = CommonUtils::urlJoint($uri, $data);
            curl_setopt($ch, CURLOPT_URL, $uri);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response == false)
        {
            throw new Exception("Error occur when send request to \"$uri\": Unknown error.");
        }

        return $response;
    }

	/**
	 * @param $str
	 * @param int $preKeep
	 * @param int $suKeep
	 * @return string
	 */
    public static function hideSome($str,$preKeep = 0,$suKeep = 0)
    {
	    $result = array();
	    for($i = 0;$i < mb_strlen($str);$i++)
	    {
		    if($i < $preKeep || mb_strlen($str) - $i <= $suKeep)
		    {
			    array_push($result,mb_substr($str,$i,1));
		    }else{
			    array_push($result,'*');
		    }
	    }
	    return implode('',$result);
    }
}
