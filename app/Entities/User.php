<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'last_login'];
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login' => 'datetime'
    ];

    /**
     * 비밀번호를 제외한 사용자 정보만 반환
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        unset($data['password']);
        return $data;
    }

    /**
     * 비밀번호 설정 (해싱 포함)
     */
    public function setPassword(string $password): self
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    /**
     * 비밀번호 검증
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password'] ?? '');
    }

    /**
     * 사용자 활성화 상태 확인
     */
    public function isActive(): bool
    {
        return $this->attributes['is_active'] ?? true;
    }

    /**
     * 마지막 로그인 시간 업데이트
     */
    public function updateLastLogin(): self
    {
        $this->attributes['last_login'] = date('Y-m-d H:i:s');
        return $this;
    }

    /**
     * 사용자명 가져오기
     */
    public function getUsername(): string
    {
        return $this->attributes['username'] ?? '';
    }

    /**
     * 이메일 가져오기
     */
    public function getEmail(): string
    {
        return $this->attributes['email'] ?? '';
    }

    /**
     * 사용자 ID 가져오기
     */
    public function getId(): int
    {
        return $this->attributes['id'] ?? 0;
    }

    /**
     * 계정 생성일 가져오기
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->attributes['created_at'] ?? null;
    }

    /**
     * 마지막 로그인 시간 가져오기
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->attributes['last_login'] ?? null;
    }

    /**
     * 계정 사용 기간 계산 (일 단위)
     */
    public function getAccountAge(): int
    {
        if (!$this->attributes['created_at']) {
            return 0;
        }

        $created = new \DateTime($this->attributes['created_at']);
        $now = new \DateTime();
        return $now->diff($created)->days;
    }

    /**
     * 마스크된 이메일 반환 (보안용)
     */
    public function getMaskedEmail(): string
    {
        $email = $this->getEmail();
        if (empty($email)) {
            return '';
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $username = $parts[0];
        $domain = $parts[1];

        if (strlen($username) <= 2) {
            return $email;
        }

        $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
        return $maskedUsername . '@' . $domain;
    }

    /**
     * 사용자 프로필 정보 반환
     */
    public function getProfile(): array
    {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'masked_email' => $this->getMaskedEmail(),
            'is_active' => $this->isActive(),
            'created_at' => $this->getCreatedAt()?->format('Y-m-d H:i:s'),
            'last_login' => $this->getLastLogin()?->format('Y-m-d H:i:s'),
            'account_age' => $this->getAccountAge()
        ];
    }
}
