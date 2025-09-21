<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimitFilter implements FilterInterface
{
    /**
     * 로그인 시도 제한
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $ip = $request->getIPAddress();
        
        // IP별 시도 횟수 체크
        $attempts = $session->get('login_attempts_' . md5($ip)) ?? [];
        $now = time();
        
        // 15분 이전의 시도 기록 제거
        $attempts = array_filter($attempts, function($timestamp) use ($now) {
            return ($now - $timestamp) < 900; // 15분
        });
        
        // 시도 횟수가 5회 이상이면 차단
        if (count($attempts) >= 5) {
            return service('response')->setJSON([
                'success' => false,
                'message' => '너무 많은 로그인 시도가 있었습니다. 15분 후에 다시 시도해주세요.'
            ])->setStatusCode(429);
        }
        
        // 현재 시도 기록 추가
        $attempts[] = $now;
        $session->set('login_attempts_' . md5($ip), $attempts);
    }

    /**
     * 필터 실행 후 (사용하지 않음)
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 로그인 실패 시 시도 횟수 증가
        if ($response->getStatusCode() === 200 && 
            strpos($request->getUri()->getPath(), 'authenticate') !== false) {
            
            $body = $response->getBody();
            if (is_string($body)) {
                $data = json_decode($body, true);
                if (isset($data['success']) && !$data['success']) {
                    // 로그인 실패 - 시도 횟수는 이미 before에서 기록됨
                }
            }
        }
    }
}
