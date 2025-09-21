<?php 
namespace App\Controllers;

use App\Services\Authentication;
use App\Libraries\SessionManager;

class Login extends BaseController
{
    protected $authService;
    protected $sessionManager;

    public function __construct()
    {
        $this->authService = new Authentication();
        $this->sessionManager = new SessionManager();
    }

    /**
     * 로그인 페이지 표시
     */
    public function index()
    {
        // 이미 로그인된 사용자는 대시보드로 리다이렉트
        if ($this->authService->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }
        
        return view('login');
    }

    /**
     * 사용자 인증 처리
     */
    public function authenticate()
    {
        $identifier = $this->request->getPost('identifier'); // 이메일 또는 사용자명
        $password = $this->request->getPost('password');

        // CSRF 토큰 검증 (안전한 방법)
        $csrfToken = $this->request->getPost(csrf_token());
        $csrfHash = csrf_hash();
        
        if (empty($csrfToken) || !hash_equals($csrfHash, $csrfToken)) {
            session()->setFlashdata('error', '보안 토큰이 유효하지 않습니다.');
            return redirect()->to('/login');
        }

        // 로그인 시도
        $result = $this->authService->login($identifier, $password);

        if ($result['success']) {
            // 세션 시작
            $this->sessionManager->startSession();
            
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/dashboard');
        } else {
            // 로그인 실패 로깅
            log_message('warning', 'Login failed for identifier: ' . $identifier . ' from IP: ' . $this->request->getIPAddress());
            
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/login');
        }
    }

    /**
     * 로그인 성공 후 대시보드
     */
    public function dashboard()
    {
        if (!$this->authService->isLoggedIn()) {
            session()->setFlashdata('error', '로그인이 필요합니다.');
            return redirect()->to('/login');
        }

        $sessionStatus = $this->sessionManager->checkSessionExpiry();
        $data = [
            'user' => $this->authService->getCurrentUserProfile(),
            'session_status' => $sessionStatus,
            'remaining_time' => $sessionStatus['remaining_time'] ?? 1800
        ];
        
        return view('dashboard', $data);
    }

    /**
     * 로그아웃 처리
     */
    public function logout()
    {
        $user = $this->authService->getCurrentUser();
        
        if ($user) {
            log_message('info', 'User logged out: ' . $user['username']);
        }

        // 인증 서비스와 세션 매니저를 통한 로그아웃
        $this->authService->logout();
        $this->sessionManager->destroySession();

        session()->setFlashdata('success', '로그아웃되었습니다.');
        return redirect()->to('/login');
    }

    /**
     * 회원가입 페이지
     */
    public function register()
    {
        // 이미 로그인된 사용자는 대시보드로 리다이렉트
        if ($this->authService->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }
        
        return view('register');
    }

    /**
     * 회원가입 처리
     */
    public function processRegister()
    {
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'password_confirm' => $this->request->getPost('password_confirm')
        ];

        // CSRF 토큰 검증 (안전한 방법)
        $csrfToken = $this->request->getPost(csrf_token());
        $csrfHash = csrf_hash();
        
        if (empty($csrfToken) || !hash_equals($csrfHash, $csrfToken)) {
            session()->setFlashdata('error', '보안 토큰이 유효하지 않습니다.');
            return redirect()->to('/register');
        }

        // 비밀번호 확인 검증
        if ($userData['password'] !== $userData['password_confirm']) {
            session()->setFlashdata('error', '비밀번호가 일치하지 않습니다.');
            return redirect()->to('/register');
        }

        // 회원가입 처리
        $result = $this->authService->registerNewUser($userData);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/login');
        } else {
            session()->setFlashdata('error', $result['message']);
            if (isset($result['errors'])) {
                session()->setFlashdata('validation_errors', $result['errors']);
            }
            return redirect()->to('/register');
        }
    }

    /**
     * AJAX 세션 상태 확인
     */
    public function checkSession()
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'not_logged_in',
                'message' => '로그인이 필요합니다.'
            ]);
        }

        $status = $this->sessionManager->checkSessionExpiry();
        
        return $this->response->setJSON([
            'status' => $status['status'],
            'message' => $status['message'],
            'remaining_time' => $status['remaining_time'] ?? 0,
            'formatted_time' => $this->sessionManager->getFormattedRemainingTime()
        ]);
    }

    /**
     * AJAX 세션 연장
     */
    public function extendSession()
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '로그인이 필요합니다.'
            ]);
        }

        $result = $this->sessionManager->extendSession();
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $result['message'],
            'new_expiry' => $result['new_expiry']
        ]);
    }
}