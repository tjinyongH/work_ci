<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = User::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'username',
        'email', 
        'password',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]'
    ];
    protected $validationMessages = [
        'username' => [
            'required' => '사용자명은 필수입니다.',
            'min_length' => '사용자명은 최소 3자 이상이어야 합니다.',
            'max_length' => '사용자명은 최대 50자까지 가능합니다.',
            'is_unique' => '이미 사용중인 사용자명입니다.'
        ],
        'email' => [
            'required' => '이메일은 필수입니다.',
            'valid_email' => '올바른 이메일 형식이 아닙니다.',
            'is_unique' => '이미 사용중인 이메일입니다.'
        ],
        'password' => [
            'required' => '비밀번호는 필수입니다.',
            'min_length' => '비밀번호는 최소 6자 이상이어야 합니다.'
        ]
    ];
    protected $skipValidation = false;
    // Entity에서 비밀번호 해싱을 처리하므로 콜백 제거

    /**
     * 사용자 생성
     */
    public function createUser(string $username, string $email, string $password): bool
    {
        $user = new User();
        $user->fill([
            'username' => $username,
            'email' => $email,
            'is_active' => true
        ]);
        $user->setPassword($password);

        return $this->insert($user) !== false;
    }

    /**
     * 사용자 인증 (이메일 또는 사용자명으로)
     */
    public function authenticateUser(string $identifier, string $password): ?User
    {
        // 이메일 또는 사용자명으로 사용자 찾기
        $user = $this->where('email', $identifier)
                     ->orWhere('username', $identifier)
                     ->first();

        if ($user && $user->verifyPassword($password) && $user->isActive()) {
            // 마지막 로그인 시간 업데이트
            $user->updateLastLogin();
            $this->save($user);
            return $user;
        }

        return null;
    }

    /**
     * 사용자명으로 사용자 찾기
     */
    public function getUserByUsername(string $username): ?User
    {
        return $this->where('username', $username)->first();
    }

    /**
     * 이메일로 사용자 찾기
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->where('email', $email)->first();
    }

    /**
     * 사용자 ID로 사용자 찾기
     */
    public function getUserById(int $id): ?User
    {
        return $this->find($id);
    }

    /**
     * 사용자 정보 업데이트
     */
    public function updateUser(int $id, array $data): bool
    {
        $user = $this->find($id);
        if (!$user) {
            return false;
        }

        // 비밀번호가 포함된 경우 해싱
        if (isset($data['password'])) {
            $user->setPassword($data['password']);
            unset($data['password']);
        }

        // 다른 필드 업데이트
        $user->fill($data);
        
        return $this->save($user) !== false;
    }

    /**
     * 사용자 삭제
     */
    public function deleteUser(int $id): bool
    {
        return $this->delete($id) !== false;
    }

    /**
     * 활성 사용자 목록 가져오기
     */
    public function getActiveUsers(): array
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * 최근 가입한 사용자 목록 가져오기
     */
    public function getRecentUsers(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')->limit($limit)->findAll();
    }

    /**
     * 사용자 통계 정보 가져오기
     */
    public function getUserStats(): array
    {
        $totalUsers = $this->countAll();
        $activeUsers = $this->where('is_active', true)->countAllResults();
        $recentUsers = $this->where('created_at >=', date('Y-m-d', strtotime('-7 days')))->countAllResults();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'recent_users' => $recentUsers,
            'inactive_users' => $totalUsers - $activeUsers
        ];
    }
}
