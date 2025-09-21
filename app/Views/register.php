<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입 - CI4 Auth</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #555;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 16px 12px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-wrapper .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        .register-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: #363;
            border: 1px solid #cfc;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
        }

        .strength-indicator {
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #ff4757; }
        .strength-medium { background: #ffa502; }
        .strength-strong { background: #2ed573; }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-loading .loading {
            display: inline-block;
        }

        .btn-loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .register-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>회원가입</h1>
            <p>새로운 계정을 생성하여 시작하세요</p>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('validation_errors')): ?>
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach(session()->getFlashdata('validation_errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/register/process" id="registerForm">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="username">사용자명</label>
                <div class="input-wrapper">
                    <div class="icon">👤</div>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="사용자명을 입력하세요 (3-50자)"
                        value="<?= esc(old('username') ?? '') ?>"
                        required
                        minlength="3"
                        maxlength="50"
                        autocomplete="username"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="email">이메일</label>
                <div class="input-wrapper">
                    <div class="icon">📧</div>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="이메일 주소를 입력하세요"
                        value="<?= esc(old('email') ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">비밀번호</label>
                <div class="input-wrapper">
                    <div class="icon">🔒</div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="비밀번호를 입력하세요 (최소 6자)"
                        required
                        minlength="6"
                        autocomplete="new-password"
                    >
                </div>
                <div class="password-strength" id="passwordStrength">
                    비밀번호 강도를 확인하세요
                </div>
                <div class="strength-indicator">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirm">비밀번호 확인</label>
                <div class="input-wrapper">
                    <div class="icon">🔐</div>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="비밀번호를 다시 입력하세요"
                        required
                        autocomplete="new-password"
                    >
                </div>
            </div>

            <button type="submit" class="register-btn" id="registerBtn">
                <div class="loading"></div>
                <span class="btn-text">회원가입</span>
            </button>
        </form>

        <div class="login-link">
            <p>이미 계정이 있으신가요? <a href="/login">로그인</a></p>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.getElementById('registerBtn');
            btn.classList.add('btn-loading');
            btn.querySelector('.btn-text').textContent = '가입 중...';
        });

        // 비밀번호 강도 체크
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthText = document.getElementById('passwordStrength');
            const strengthBar = document.getElementById('strengthBar');
            
            let strength = 0;
            let strengthLabel = '';
            let strengthClass = '';

            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            if (strength < 2) {
                strengthLabel = '약함';
                strengthClass = 'strength-weak';
            } else if (strength < 4) {
                strengthLabel = '보통';
                strengthClass = 'strength-medium';
            } else {
                strengthLabel = '강함';
                strengthClass = 'strength-strong';
            }

            strengthText.textContent = `비밀번호 강도: ${strengthLabel}`;
            strengthBar.className = `strength-bar ${strengthClass}`;
            strengthBar.style.width = `${(strength / 5) * 100}%`;
        });

        // 비밀번호 확인 체크
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#ff4757';
            } else {
                this.style.borderColor = '#e1e5e9';
            }
        });

        // 입력 필드 포커스 효과
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // 엔터키로 폼 제출
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('registerForm').submit();
            }
        });
    </script>
</body>
</html>
