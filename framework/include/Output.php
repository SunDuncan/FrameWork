<?php
/**
 * Created by PhpStorm.
 * User: xuchunning
 * Date: 2/22/18
 * Time: 11:55 AM
 */

class Output
{
	public static $NO_ERROR = 0;
	public static $INVALID_PARAM = 8901;
	public static $INCORRECT_SIGN = 8902;
	public static $REQUEST_TIMEOUT = 8903;
	public static $THIRD_API_ERROR = 8904;
	public static $DB_ERROR = 8905;
	public static $UNKNOWN_ERROR = 8999;

	public static $USER_NOT_EXIST = 8010;
	public static $INVALID_MEMBER_TOKEN = 8011;
	public static $SELF_PAIR = 8012;
	public static $OTHERS_SHOES = 8013;
	public static $PARE_EXIST = 8014;


    /**
     * @var int|null
     */
    public $errId = null;

    /**
     * @var null
     */
    public $data = null;

    /**
     * @var null|string
     */
    public $extraInfo = null;

    /**
     * Output constructor.
     * @param int $errId
     * @param null $data
     */
    public function __construct($errId = 0, $data = null)
    {
        $this->errId = $errId;
        if (is_null($data) == false)
        {
            $this->data = $data;
        }
    }

    public function output()
    {
        switch ($this->errId)
        {
	        case Output::$INVALID_PARAM:
	        	$msg = 'Invalid param.(%info%)';
	        	break;

	        case Output::$REQUEST_TIMEOUT:
	        	$msg = 'Request timeout';
	        	break;

	        case Output::$INCORRECT_SIGN:
		        $msg = 'Sign is not correct.';
		        break;

	        case Output::$THIRD_API_ERROR:
		        $msg = 'Third party API Error. (%info%)';
		        break;

	        case Output::$DB_ERROR:
		        $msg = 'System error(DB).(%info%)';
		        break;

	        case Output::$UNKNOWN_ERROR:
                $msg = 'Unknown error.';
                break;

	        case Output::$NO_ERROR:
	        default:
		        $msg = 'Success';
		        break;

	        case Output::$USER_NOT_EXIST:
		        $msg = 'User does not exist.';
		        break;

	        case Output::$INVALID_MEMBER_TOKEN:
	        	$msg = 'Invalid member token.';
	        	break;

	        case Output::$SELF_PAIR:
		        $msg = 'Can not pair shoes created by self';
		        break;

	        case Output::$OTHERS_SHOES:
		        $msg = 'Shoes does not belongs to current user.';
		        break;

	        case Output::$PARE_EXIST:
		        $msg = 'Pair exist.';
		        break;
        }

        $output = array('errcode' => $this->errId);
        if (strlen($msg) > 0)
        {
            if (is_null($this->extraInfo) == false && strlen($this->extraInfo) > 0)
            {
                $msg = str_replace('%info%', $this->extraInfo, $msg);
            }

            $output['errmsg'] = $msg;
        }

        if (is_null($this->data) == false)
        {
            $output['data'] = $this->data;
        }

        header('Content-type: application/json');
        echo json_encode($output);
    }
}