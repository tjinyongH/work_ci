<?php namespace App\Services;

use App\Entities\User;
use App\Models\UserModel;

class Authentication
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * 사용자 등록
     */
    public function registerNewUser(array $userData): array
    {
        // 1. 사용자 정보 유효성 검사
        if (!$this->validateUserData($userData)) {
            return [
                'success' => false,
                'message' => '필수 정보가 누락되었습니다.',
                'errors' => []
            ];
        }

        // 2. 데이터베이스 트랜잭션으로 사용자 생성
        $this->userModel->db->transBegin();
        
        try {
            $result = $this->userModel->createUser(
                $userData['username'], 
                $userData['email'], 
                $userData['password']
            );
            
            if ($result) {
                $this->userModel->db->transCommit();
                return [
                    'success' => true,
                    'message' => '회원가입이 완료되었습니다.',
                    'user' => $this->userModel->getUserByUsername($userData['username'])?->getProfile()
                ];
            } else {
                $this->userModel->db->transRollback();
                return [
                    'success' => false,
                    'message' => '회원가입 중 오류가 발생했습니다.',
                    'errors' => $this->userModel->errors()
                ];
            }
        } catch (\Exception $e) {
            $this->userModel->db->transRollback();
            return [
                'success' => false,
                'message' => '회원가입 중 예외가 발생했습니다: ' . $e->getMessage(),
                'errors' => []
            ];
        }
    }

    /**
     * 사용자 로그인
     */
    public function login(string $identifier, string $password): array
    {
        // 1. 입력값 검증
        if (empty($identifier) || empty($password)) {
            return [
                'success' => false,
                'message' => '이메일/사용자명과 비밀번호를 입력해주세요.'
            ];
        }

        // 2. 사용자 인증
        $user = $this->userModel->authenticateUser($identifier, $password);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => '이메일/사용자명 또는 비밀번호가 잘못되었습니다.'
            ];
        }

        // 3. 세션 설정
        $this->setUserSession($user);
        
        return [
            'success' => true,
            'message' => '로그인되었습니다.',
            'user' => $user->getProfile()
        ];
    }

    /**
     * 사용자 세션 설정
     */
    public function setUserSession(User $user): void
    {
        $this->session->set([
            'isLoggedIn' => true,
            'user_id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'login_time' => time()
        ]);
    }

    /**
     * 로그인 상태 확인
     */
    public function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    /**
     * 현재 로그인된 사용자 정보 가져오기
     */
    public function getCurrentUser(): ?User
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userId = $this->session->get('user_id');
        return $this->userModel->getUserById($userId);
    }

    /**
     * 현재 로그인된 사용자 프로필 정보 가져오기
     */
    public function getCurrentUserProfile(): ?array
    {
        $user = $this->getCurrentUser();
        return $user ? $user->getProfile() : null;
    }

    /**
     * 로그아웃
     */
    public function logout(): void
    {
        $this->session->destroy();
    }

    /**
     * 사용자 정보 유효성 검사
     */
    private function validateUserData(array $data): bool
    {
        return isset($data['username'], $data['email'], $data['password']) &&
               !empty($data['username']) &&
               !empty($data['email']) &&
               !empty($data['password']);
    }

    /**
     * 비밀번호 재설정 (향후 구현)
     */
    public function resetPassword(string $email): array
    {
        // TODO: 비밀번호 재설정 기능 구현
        return [
            'success' => false,
            'message' => '비밀번호 재설정 기능은 아직 구현되지 않았습니다.'
        ];
    }
}
