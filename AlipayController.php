<?php
namespace api\controllers\pay;

use Yii;
use yii\web\Controller;
use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;

class AlipayController extends Controller
{
    protected $config = [
        'use_sandbox'               => true,// 是否使用沙盒模式
        'app_id'                    => '2016092100563016',
        'sign_type'                 => 'RSA2',// RSA  RSA2
        // ！！！注意：如果是文件方式，文件中只保留字符串，不要留下 -----BEGIN PUBLIC KEY----- 这种标记
        // 可以填写文件路径，或者密钥字符串  当前字符串是 rsa2 的支付宝公钥(开放平台获取)
        'ali_public_key'            => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvSB2KOYKxzF7gHM3dBaAnclfa5qKURHiLvWOs70qqRZt6D+28fzfai/LPSZOgoX/bfmKKUBjSAQbcBLDPOky5l8s2+/IwfFOe5X8EqwKpJSMlssp7Lq8yaFXnE4xsqdKxptic2VGTWPoetYI9fvmilDWac6/+DJpLfV5D4lWVp70vNePYpx24Ib8vAL71zyjSOZ8smJngjM0mfZH5M8u1rFfJXNMcoabjs3GVpan8+RtOwzLV5OMwvFtqnUrtzLtr+KVSudw50hHF/QtqeQjGlFTfreU6tAPeHMOF51Rd3cEkyZV01FoK3VgmCX1hwMrtuOcULLBVOSJ8k/8eBrZwQIDAQAB',
        // 可以填写文件路径，或者密钥字符串  我的沙箱模式，rsa与rsa2的私钥相同，为了方便测试
        'rsa_private_key'           => 'MIIEowIBAAKCAQEAvSB2KOYKxzF7gHM3dBaAnclfa5qKURHiLvWOs70qqRZt6D+28fzfai/LPSZOgoX/bfmKKUBjSAQbcBLDPOky5l8s2+/IwfFOe5X8EqwKpJSMlssp7Lq8yaFXnE4xsqdKxptic2VGTWPoetYI9fvmilDWac6/+DJpLfV5D4lWVp70vNePYpx24Ib8vAL71zyjSOZ8smJngjM0mfZH5M8u1rFfJXNMcoabjs3GVpan8+RtOwzLV5OMwvFtqnUrtzLtr+KVSudw50hHF/QtqeQjGlFTfreU6tAPeHMOF51Rd3cEkyZV01FoK3VgmCX1hwMrtuOcULLBVOSJ8k/8eBrZwQIDAQABAoIBABC5JRmatctFSdli1r1yG+9X1hMquB5RCYIDfpnQGUyYSiGmOKQPKyY1kbAXiguqk5qC3T+mjODaVB7F2fOx3ylVsx7Dhin4D2uIKKDyC4LgC8ptrohbPsRJevi8RVK5zjffl4YCp4PCWNU7xdESdCE44qy6dmkY9An3UbwY9cR05N8bgwTIwl91bvYrrK2wygtGOV/sibybZpqTW1JfBPxzm12Wx9cccC4kvnZMumFXJ/NSqlD55ggxCoprYm1j9nyKQLFxZYAHypubFWP3NwbHm6gjvr9y3vu8qdETUOWOV0iK2WPDNglRMfYW/nYOZPUPxbp5pUsgFym3Ly/g2RECgYEA3XU81SAZn/ex77YZuziQZZ57qQRVS8We99VrQp853OZH57XzgT9zXQV0D2IxLaNqNZ/7SvYQRZfwNSR2xepvyexQwvxI/L5Z1QFwsTXfy/twjr4gRMEQA81NHCVDCvcpReDiTdadnAN59sZ534xeDh5nIec/CXDv71nvu/Bws5UCgYEA2qA/rNyEu2whxBsht038m9idEgzEuAZkHtFEed1ExXuVm0DuZDrhzGd4hdQ6VPuMI8sxaZoXwNMirIJf5qCGW+OydRCi1GaLSVcRgx8gyj5Y88RIup5kRd2c4vuJDxwwunXqW11J6p1CWQUBuAO1G1KRS1qBBoirvrISMoX8An0CgYEApCa8mrWaiOqPKEHwvMfmsIxS2StvFiU7+jRltxJTT5wZ/HTwNAOWPYNHTgfYsB2LIoidOik+UneXA60tpPJrPl8+VemvKDjoWW0h3Rkz8/1Cp2vie1QnzMmmuJAeK8ic/UD+PfgxB23EP9lKiRuAtDJw+75vCU5/awldHA/TWikCgYBhmi0TNRekvK+OZzZ0KULfKKNmzYB9T40fHPnfFeN/7p5pw2EqKgdoQ8FQreCk0/BKgoo6xOpZ0Winb0T1JzEUkOC41oZ7IJSKV26gBSOUZqU7nqOBfPjzH+7MftM/haxTchUAWPOI5FAcgdm0lyMhYYkwBOWZTyatab4qBBrv2QKBgHUJkpwwTnJJlOkmf4rz7dBq54JBpJB3aVaLlk7yR9pHwN8p3Ie3jz449mKf3HsGYXJUQp8Qc1vZ0eTi9qm+5mEOOTAfXXgep7PzLDWd39O8s+Nv9YKkhA5zTh1NTc9KzDZs5Js8ZunqYdOXh5U+0sBTzcQLOmH2SUlZaM+kUJyi',
        'limit_pay'                 => [
            //'balance',// 余额
            //'moneyFund',// 余额宝
            //'debitCardExpress',// 	借记卡快捷
            //'creditCard',//信用卡
            //'creditCardExpress',// 信用卡快捷
            //'creditCardCartoon',//信用卡卡通
            //'credit_group',// 信用支付类型（包含信用卡卡通、信用卡快捷、花呗、花呗分期）
        ],// 用户不可用指定渠道支付当有多个渠道时用“,”分隔
        // 与业务相关参数
        'notify_url'                => 'http://service.com/api/web/index.php?r=alipay/gateway',
        'return_url'                => 'http://service.com/api/web/index.php?r=pay/alipay/query-order',
        'return_raw'                => false,// 在处理回调时，是否直接返回原始数据，默认为 true
    ];

    public function actionPcpay()
    {
        $orderNo = time() . rand(1000, 9999);
        $payData = [
            'body'    => 'ali web pay',
            'subject'    => '测试支付宝电脑网站支付',
            'order_no'    => $orderNo,
            'timeout_express' => time() + 600,// 表示必须 600s 内付款
            'amount'    => '0.01',// 单位为元 ,最小为0.01
            'return_param' => '123123',
            // 'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
            'goods_type' => '1',// 0—虚拟类商品，1—实物类商品
            'store_id' => '',
            // 说明地址：https://doc.open.alipay.com/doc2/detail.htm?treeId=270&articleId=105901&docType=1
            // 建议什么也不填
            'qr_mod' => '',
        ];
        try {
                $url = Charge::run(Config::ALI_CHANNEL_WEB, $this->config, $payData);
        } catch (PayException $e) {
            echo $e->errorMessage();
            exit;
        }
        header('Location:' . $url);
    }

    public function actionQueryOrder()
    {
        echo 'ddd';
    }
}
