<?php
namespace Colyii\LaravelSms;
use Toplan\PhpSms\Agent;

class OasmsAgent extends Agent {
	//override
	//发送短信一级入口
	public function sendSms($to, $content, $tempId, array $data) {
		//在这个方法中调用二级入口
		$content .= $this->sign_name; //补充签名
		$this->sendContentSms($to, $content);
	}

	//override
	//发送短信二级入口：发送内容短信
	public function sendContentSms($to, $content) {
		$url = $this->url;

		//接口参数
		$params = array(
			'corpAccount' => $this->corpAccount,
			'userAccount' => $this->userAccount,
			'pwd' => $this->pwd,
			'mobile' => $to,
			'content' => $content,
			'needstatus' => true,
			'product' => '',
			'extno' => '',
		);

		//可用方法:
		$result = $this->curlPost($url, $params);
		$result = explode("#", $result);

		//更新发送结果
		if (1 == $result[0]) {
			$this->result('success', true); //发送成功
		} else {
			$this->result('success', false); //发送失败
		}
		$this->result('code', $result[0]); //发送结果代码
		switch ($result[0]) {
		case '1':
			$this->result('info', '发送成功'); //发送结果信息说明
			break;
		case '102': //无该企业或者密码错误
			$this->result('info', '无该企业或者密码错误,或者企业，密码为空'); //发送结果信息说明
			break;
		case '103': //企业被禁用
			$this->result('info', '企业被禁用'); //发送结果信息说明
			break;
		case '104': //业务错误
			$this->result('info', '业务错误'); //发送结果信息说明
			break;
		case '105': //余额不足
			$this->result('info', '余额不足'); //发送结果信息说明
			break;
		case '106': //IP限制
			$this->result('info', 'IP限制'); //发送结果信息说明
			break;
		case '107': //系统繁忙
			$this->result('info', '系统繁忙'); //发送结果信息说明
			break;
		case '108': //非法词组
			$this->result('info', '非法词组'); //发送结果信息说明
			break;
		case '109': //手机黑名单
			$this->result('info', '手机黑名单'); //发送结果信息说明
			break;
		case '999': //发送失败
			$this->result('info', '发送失败'); //发送结果信息说明
			break;
		case '111': //手机格式错误
			$this->result('info', '手机格式错误'); //发送结果信息说明
			break;
		case '112': //手机号码为空
			$this->result('info', '手机号码为空'); //发送结果信息说明
			break;
		case '113': //发送内容为空
			$this->result('info', '发送内容为空'); //发送结果信息说明
			break;
		case '114': //发送内容太长
			$this->result('info', '发送内容太长'); //发送结果信息说明
			break;
		case '115': //Msg_id太长。
			$this->result('info', 'Msg_id太长。'); //发送结果信息说明
			break;
		case '116': //手机号码超过最大值。
			$this->result('info', '手机号码超过最大值。'); //发送结果信息说明
			break;
		case '400': //网络错误
			$this->result('info', '网络错误'); //发送结果信息说明
			break;
		default:
			$this->result('info', '短信发送失败'); //发送结果信息说明
			break;
		}
	}

	//override
	//发送短信二级入口：发送模板短信
	public function sendTemplateSms($tempId, $to, array $tempData) {
		//同上...
	}

	//override
	//发送语音验证码入口
	public function voiceVerify($to, $code, $tempId, array $tempData) {
		//同上...
	}

	/**
	 * 查询额度
	 */
	public function queryBalance() {
		//查询参数
		$postArr = array(
			'account' => $this->apiAccount,
			'pswd' => $this->apiPassword,
		);
		return $this->curlPost($this->apiBalanceQueryUrl, $postArr);
	}

	private function curlPost($url, $postFields) {
		$postFields = http_build_query($postFields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		$result = curl_exec($ch);
		return $result;
	}

	/**
	 * 处理返回值
	 */
	public function execResult($result) {
		return preg_split("/[,\r\n]/", $result);
	}

}
