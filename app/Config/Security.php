<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    /**
     * CSRF 보호 활성화 여부
     */
    public bool $csrfProtection = true;

    /**
     * CSRF 토큰 이름
     */
    public string $tokenName = 'csrf_test_name';

    /**
     * CSRF 헤더 이름
     */
    public string $headerName = 'X-CSRF-TOKEN';

    /**
     * CSRF 쿠키 이름
     */
    public string $cookieName = 'csrf_cookie_name';

    /**
     * CSRF 토큰 만료 시간 (초)
     */
    public int $expires = 7200;

    /**
     * CSRF 토큰 재생성 여부
     */
    public bool $regenerate = true;

    /**
     * CSRF 토큰 랜덤화 여부
     */
    public bool $tokenRandomize = false;

    /**
     * CSRF 리다이렉트 URL
     */
    public string $redirect = '';

    /**
     * CSRF 쿠키의 SameSite 속성
     */
    public string $sameSite = 'Lax';

    /**
     * 비밀번호 해싱 설정
     */
    public array $passwordHashing = [
        'algorithm' => PASSWORD_DEFAULT,
        'cost' => 12,
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3,
    ];

    /**
     * 로그인 시도 제한 설정
     */
    public array $loginAttempts = [
        'max_attempts' => 5,
        'lockout_duration' => 900, // 15분
        'reset_duration' => 3600,  // 1시간
    ];

    /**
     * 세션 보안 설정
     */
    public array $sessionSecurity = [
        'regenerate_id' => true,
        'regenerate_interval' => 300, // 5분
        'httponly' => true,
        'secure' => false, // HTTPS 환경에서는 true로 설정
        'samesite' => 'Lax',
    ];
}