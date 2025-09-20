<?php
namespace App\Libraries;

class SessionManager
{
    protected $session;
    protected $sessionTimeout = 1800; // 30분
    protected $warningTime = 300; // 5분 전 경고
    
    public function __construct()
    {
        $this->session = \Config\Services::session();
    }
    
    /**
     * 세션 시작 및 타이머 설정
     */
    public function startSession()
    {
        // 현재 시간을 세션에 저장
        $this->session->set('session_start_time', time());
        $this->session->set('last_activity', time());
    }
    
    /**
     * 세션 만료 체크
     */
    public function checkSessionExpiry()
    {
        $lastActivity = $this->session->get('last_activity');
        
        if (!$lastActivity) {
            return ['status' => 'expired', 'message' => '세션이 존재하지 않습니다.'];
        }
        
        $timeElapsed = time() - $lastActivity;
        $remainingTime = $this->sessionTimeout - $timeElapsed;
        
        // 세션 만료됨
        if ($timeElapsed >= $this->sessionTimeout) {
            $this->destroySession();
            return ['status' => 'expired', 'message' => '세션이 만료되었습니다.'];
        }
        
        // 만료 임박 경고 (5분 전)
        if ($remainingTime <= $this->warningTime) {
            return [
                'status' => 'warning',
                'remaining_time' => $remainingTime,
                'message' => '세션이 곧 만료됩니다. 연장하시겠습니까?'
            ];
        }
        
        // 정상 상태
        return [
            'status' => 'active',
            'remaining_time' => $remainingTime,
            'message' => '세션이 활성 상태입니다.'
        ];
    }
    
    /**
     * 세션 연장
     */
    public function extendSession()
    {
        $this->session->set('last_activity', time());
        return [
            'status' => 'extended',
            'message' => '세션이 30분 연장되었습니다.',
            'new_expiry' => time() + $this->sessionTimeout
        ];
    }
    
    /**
     * 활동 업데이트
     */
    public function updateActivity()
    {
        $this->session->set('last_activity', time());
    }
    
    /**
     * 세션 파괴
     */
    public function destroySession()
    {
        $this->session->destroy();
    }
    
    /**
     * 남은 시간을 분:초 형식으로 반환
     */
    public function getFormattedRemainingTime()
    {
        $status = $this->checkSessionExpiry();
        
        if ($status['status'] === 'expired') {
            return '00:00';
        }
        
        $remainingSeconds = $status['remaining_time'];
        $minutes = floor($remainingSeconds / 60);
        $seconds = $remainingSeconds % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}