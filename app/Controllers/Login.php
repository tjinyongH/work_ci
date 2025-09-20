<?php 
namespace App\Controllers;

class Login extends BaseController{
    public function index(){        
        return view('login');
    }

    public function authenticate(){
        $session = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if ($username === 'admin' && $password === '1234') {
            $session->set('isLoggedIn', true);
            return redirect()->to('/my2/login_ok');
        } else {
            $session->setFlashdata('error', '아이디 혹은 비밀번호가 잘못되었습니다.');
            return redirect()->to('/my2/login');
        }
    }

    public function login_ok(){        
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/my2/login');
        }
        
        return view('login_ok');
    }
    public function logout(){
        log_message('error', '로그아웃');
        $session = session();

        // 세션 데이터 삭제
        $session->remove('isLoggedIn');
    
        $session->destroy();
        return redirect()->to('/my2/login');
    }
}