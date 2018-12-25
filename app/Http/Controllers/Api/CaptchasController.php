<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchaRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-' . str_random(15);
        $phone = $request->phone;

        // controller 中，注入CaptchaBuilder，通过它的 build 方法，创建出来验证码图片
        $captcha = $captchaBuilder->build();
        $expiredAt = now()->addMinutes(2);
        // 使用 getPhrase 方法获取验证码文本，跟手机号一同存入缓存。
        Cache::put($key, ['phone' => $phone, 'code' => $captcha->getPhrase()], $expiredAt);

        // 返回 captcha_key，过期时间以及 inline 方法获取的 base64 图片验证码
        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' =>$captcha->inline(),

//            'code'=> $captcha->getPhrase()  调试获取图片验证码
        ];

        return $this->response->array($result)->setStatusCode(201);
    }
}
