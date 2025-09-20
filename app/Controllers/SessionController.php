<?php
namespace App\Controllers;

use App\Libraries\SessionManager;

class SessionController extends BaseController
{
    protected $sessionManager;
    
    public function __construct()
    {
        $this->sessionManager = new SessionManager();
    }
    
    /**
     * 세션 상태 체크 (AJAX)
     */
    public function checkSession()
    {
        $status = $this->sessionManager->checkSessionExpiry();
        
        return $this->response->setJSON([
            'status' => $status['status'],
            'message' => $status['message'],
            'remaining_time' => $status['remaining_time'] ?? 0,
            'formatted_time' => $this->sessionManager->getFormattedRemainingTime()
        ]);
    }
    
    /**
     * 세션 연장 (AJAX)
     */
    public function extendSession()
    {
        $result = $this->sessionManager->extendSession();
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $result['message'],
            'new_expiry' => $result['new_expiry']
        ]);
    }
    
    /**
     * 활동 업데이트 (AJAX)
     */
    public function updateActivity()
    {
        $this->sessionManager->updateActivity();
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => '활동이 업데이트되었습니다.'
        ]);
    }
    
    /**
     * 로그아웃
     */
    public function logout()
    {
        $this->sessionManager->destroySession();
        return redirect()->to('/login');
    }
}