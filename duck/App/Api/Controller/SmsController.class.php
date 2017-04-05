<?php
/**
SMS���Žӿ���
@author		sms.cn
@link		http://www.sms.cn
*/
namespace Api\Controller;

use Think\Controller;

class SmsController extends Controller {
	/**
	* SMSAPI�����ַ
	*/
	const API_URL = 'http://utf8.sms.webchinese.cn/';

	/**
	* �ӿ��˺�
	* 
	* @var string
	*/
	protected $uid;

	/**
	* �ӿ�����
	* 
	* @var string
	* @link http://sms.sms.cn/ �뵽�˴�����������->�ӿ����룩��ȡ
	*/
	protected $pwd;

	/**
	* sms api�����ַ
	* @var string
	*/
	protected $apiURL;


	/**
	* ���ŷ����������
	* @var string
	*/
	protected $smsParams;

	/**
	* �ӿڷ�����Ϣ
	* @var string
	*/
    protected $resultMsg;

	/**
	* �ӿڷ�����Ϣ��ʽ
	* @var string
	*/
	protected $format;

	/**
	* ���췽��
	* 
	* @param string $uid �ӿ��˺�
	* @param string $pwd �ӿ�����
	*/
    public function __construct($uid = '', $pwd = '')
    {
		//�û��������ֱ��д������
		$def_uid = 'tianchenxue';
		$def_pwd = '608c3096b11003826580';
        $this->uid	= $uid ?: $def_uid;
        $this->pwd	= $pwd ?: $def_pwd;
        $this->apiURL = self::API_URL;
		$this->format = 'json';
    }
	/**
	* SMS��������
	* @return array 
	*/
    protected function publicParams()
    {
        return array(
            'uid'		=> $this->uid,
            'pwd'		=> md5($this->pwd.$this->uid),
            'format'	=> $this->format,
        );
    }
	/**
	* ���ͱ���ģ�����
	*
	* @param string $mobile �ֻ�����
	* @param string $content �������ݲ���
	* @param string $template ����ģ��ID
	* @return array
	*/
	public function send($mobile, $contentParam,$template='') {
		//���ŷ��Ͳ���
		$this->smsParams = array(
			'ac'		=> 'send',
			'mobile'	=> $mobile,
			'content'	=> $this->array_to_json($contentParam),
			'template'	=> $template
		);
		$this->resultMsg = $this->request();
		return $this->json_to_array($this->resultMsg, true);
	}

	/**
	* ����ȫ��ģ�����
	*
	* @param string $mobile �ֻ�����
	* @param string $content ��������
	* @return array
	*/
	public function sendAll($mobile, $content) {
		//���ŷ��Ͳ���
		$this->smsParams = array(
			'ac'		=> 'send',
			'mobile'	=> $mobile,
			'content'	=> $content,
		);
		$this->resultMsg = $this->request();

		return $this->json_to_array($this->resultMsg, true);
	}

	/**
	* ȡʣ���������
	*
	* @return array
	*/
	public function getNumber() {
		//����
		$this->smsParams = array(
			'ac'		=> 'number',
		);
		$this->resultMsg = $this->request();
		return $this->json_to_array($this->resultMsg, true);
	}


	/**
	* ��ȡ����״̬
	*
	* @return array
	*/
	public function getStatus() {
		//����
		$this->smsParams = array(
			'ac'		=> 'status',
		);
		$this->resultMsg = $this->request();
		return $this->json_to_array($this->resultMsg, true);
	}
	/**
	* �������ж��ţ��ظ���
	*
	* @return array
	*/
	public function getReply() {
		//����
		$this->smsParams = array(
			'ac'		=> 'reply',
		);
		$this->resultMsg = $this->request();
		return $this->json_to_array($this->resultMsg, true);
	}
	/**
	* ȡ�ѷ���������
	*
	* @return array
	*/
	public function getSendTotal() {
		//����
		$this->smsParams = array(
			'ac'		=> 'number',
			'cmd'		=> 'send',
		);
		$this->resultMsg = $this->request();
		return $this->json_to_array($this->resultMsg, true);
	}

	/**
	* ȡ���ͼ�¼
	*
	* @return array
	*/
	public function getQuery() {
		//����
		$this->smsParams = array(
			'ac'		=> 'query',
		);
		$this->resultMsg = $this->request();
		return $this->json_to_array($this->resultMsg, true);
	}

	/**
	* ����HTTP����
	* @return string
	*/
	private function request()
	{
		$params = array_merge($this->publicParams(),$this->smsParams);
        $rparams['Uid']=$params['uid'];
        $rparams['Key']=$params['pwd'];
        $rparams['smsMob']=$params['mobile'];
        $rparams['smsText']=$params['content'];
		if( function_exists('curl_init') )
		{   //var_dump($params);echo $this->apiURL;exit;
			return $this->curl_request($this->apiURL,$params);
		}
		else
		{
			return $this->file_get_request($this->apiURL,$params);
		}
	}
	/**
	* ͨ��CURL����HTTP����
	* @param string $url		 //����URL
	* @param array $postFields //������� 
	* @return string
	*/
	private function curl_request($url,$postFields){
		$postFields = http_build_query($postFields);
		//echo $url.'?'.$postFields;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		return $result;
	}
	/**
	* ͨ��file_get_contents����HTTP����
	* @param string $url  //����URL
	* @param array $postFields //������� 
	* @return string
	*/
	private function file_get_request($url,$postFields)
	{
		$post='';
		while (list($k,$v) = each($postFields)) 
		{
			$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//תURL��׼��
		}
		return file_get_contents($url.'?'.$post);
	}
	/**
	* ��ȡ��ǰHTTP�뷵����Ϣ
	* @return string
	*/
	public function getResult()
	{
		$this->resultMsg;
	}
	/**
	* ��ȡ���λ������
	* @param  integer $len ����
	* @return string       
	*/
	public function randNumber($len = 6)
	{
		$chars = str_repeat('0123456789', 10);
		$chars = str_shuffle($chars);
		$str   = substr($chars, 0, $len);
		return $str;
	}

	//������תjson�ַ���
	function array_to_json($p)
	{
		return urldecode(json_encode($this->json_urlencode($p)));
	}
	//urlת��
	function json_urlencode($p)
	{
		if( is_array($p) )
		{
			foreach( $p as $key => $value )$p[$key] = $this->json_urlencode($value);
		}
		else
		{
			$p = urlencode($p);
		}
		return $p;
	}

	//��json�ַ���ת����
	function json_to_array($p)
	{
		if( mb_detect_encoding($p,array('ASCII','UTF-8','GB2312','GBK')) != 'UTF-8' )
		{
			$p = iconv('GBK','UTF-8',$p);
		}
		return json_decode($p, true);
	}
}

?>