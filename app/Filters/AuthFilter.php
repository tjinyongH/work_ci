<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * 로그인 인증이 필요한 페이지에서 실행
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // 로그인 상태 확인
        if (!$session->get('isLoggedIn')) {
            // AJAX 요청인 경우 JSON 응답
            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'status' => 'error',
                    'message' => '로그인이 필요합니다.',
                    'redirect' => base_url('/login')
                ])->setStatusCode(401);
            }
            
            // 일반 요청인 경우 로그인 페이지로 리다이렉트
            return redirect()->to('/login');
        }
    }

    /**
     * 필터 실행 후 (보통 사용하지 않음)
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 필터 실행 후 로직이 필요한 경우 여기에 작성
    }
}
