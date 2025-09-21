<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * 사용자 통계 대시보드
     */
    public function dashboard()
    {
        // 로그인 확인
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => '관리자 대시보드',
            'stats' => $this->userModel->getUserStats(),
            'recent_users' => $this->userModel->getRecentUsers(5),
            'active_users' => $this->userModel->getActiveUsers()
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * 사용자 목록
     */
    public function users()
    {
        // 로그인 확인
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => '사용자 관리',
            'users' => $this->userModel->findAll()
        ];

        return view('admin/users', $data);
    }
}
