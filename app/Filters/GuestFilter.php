<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestFilter implements FilterInterface
{
    /**
     * 로그인된 사용자가 접근하면 안 되는 페이지에서 실행
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // 이미 로그인된 사용자인 경우
        if ($session->get('isLoggedIn')) {
            // AJAX 요청인 경우 JSON 응답
            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'status' => 'error',
                    'message' => '이미 로그인되어 있습니다.',
                    'redirect' => base_url('/dashboard')
                ])->setStatusCode(400);
            }
            
            // 일반 요청인 경우 대시보드로 리다이렉트
            return redirect()->to('/dashboard');
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
