<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>대시보드 - CI4 Auth</title>
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
            padding: 20px;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .welcome-section h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .welcome-section p {
            color: #666;
            font-size: 14px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }

        .logout-btn {
            background: #ff4757;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background: #ff3742;
            transform: translateY(-2px);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-icon {
            font-size: 24px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .session-status {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .session-timer {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .timer-display {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            font-size: 24px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            min-width: 100px;
            text-align: center;
        }

        .session-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: #2ed573;
            color: white;
        }

        .btn-success:hover {
            background: #26c965;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

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

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .session-timer {
                flex-direction: column;
                text-align: center;
            }

            .session-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <div class="welcome-section">
                <h1>환영합니다! 👋</h1>
                <p><?= $user['username'] ?>님, 대시보드에 오신 것을 환영합니다.</p>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <div>
                    <div style="font-weight: 600; color: #333;"><?= $user['username'] ?></div>
                    <div style="font-size: 12px; color: #666;"><?= $user['email'] ?></div>
                </div>
                <form method="post" action="/logout" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="logout-btn">로그아웃</button>
                </form>
            </div>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-info">
                <?= session()->getFlashdata('success'); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="card">
                <h3><span class="card-icon">👤</span> 계정 정보</h3>
                <div class="info-item">
                    <span class="info-label">사용자명</span>
                    <span class="info-value"><?= $user['username'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">이메일</span>
                    <span class="info-value"><?= $user['email'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">회원가입일</span>
                    <span class="info-value"><?= date('Y-m-d', strtotime($user['created_at'])) ?></span>
                </div>
            </div>

            <div class="card">
                <h3><span class="card-icon">🔒</span> 보안 정보</h3>
                <div class="info-item">
                    <span class="info-label">로그인 시간</span>
                    <span class="info-value"><?= date('Y-m-d H:i:s', session()->get('login_time') ?? time()) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">계정 상태</span>
                    <span class="info-value" style="color: #2ed573;">활성</span>
                </div>
                <div class="info-item">
                    <span class="info-label">보안 등급</span>
                    <span class="info-value">표준</span>
                </div>
            </div>

            <div class="card">
                <h3><span class="card-icon">📊</span> 활동 통계</h3>
                <div class="info-item">
                    <span class="info-label">총 로그인 횟수</span>
                    <span class="info-value">1회</span>
                </div>
                <div class="info-item">
                    <span class="info-label">마지막 활동</span>
                    <span class="info-value">방금 전</span>
                </div>
                <div class="info-item">
                    <span class="info-label">계정 사용 기간</span>
                    <span class="info-value"><?= $user['account_age'] ?? 0 ?>일</span>
                </div>
            </div>
        </div>

        <div class="session-status">
            <h3 style="color: #333; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <span>⏰</span> 세션 관리
            </h3>
            
            <div class="session-timer">
                <div>
                    <div style="color: #666; font-size: 14px; margin-bottom: 5px;">세션 만료까지</div>
                    <div class="timer-display" id="sessionTimer">30:00</div>
                </div>
                <div>
                    <div style="color: #666; font-size: 14px; margin-bottom: 5px;">상태</div>
                    <div id="sessionStatus" style="font-weight: 600; color: #2ed573;">활성</div>
                </div>
            </div>

            <div class="session-actions">
                <button class="btn btn-primary" onclick="extendSession()">세션 연장</button>
                <button class="btn btn-success" onclick="updateActivity()">활동 업데이트</button>
            </div>

            <div id="sessionMessage" style="margin-top: 15px;"></div>
        </div>
    </div>

    <script>
        let sessionTimer;
        let remainingTime = 1800; // 30분

        function updateTimer() {
            const minutes = Math.floor(remainingTime / 60);
            const seconds = remainingTime % 60;
            document.getElementById('sessionTimer').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (remainingTime <= 0) {
                clearInterval(sessionTimer);
                document.getElementById('sessionStatus').textContent = '만료됨';
                document.getElementById('sessionStatus').style.color = '#ff4757';
                showMessage('세션이 만료되었습니다. 다시 로그인해주세요.', 'error');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else if (remainingTime <= 300) { // 5분 남음
                document.getElementById('sessionStatus').textContent = '만료 임박';
                document.getElementById('sessionStatus').style.color = '#ffa502';
                showMessage('세션이 곧 만료됩니다. 연장하시겠습니까?', 'warning');
            }
        }

        function startTimer() {
            updateTimer();
            sessionTimer = setInterval(() => {
                remainingTime--;
                updateTimer();
            }, 1000);
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('sessionMessage');
            messageDiv.innerHTML = `<div class="alert alert-${type === 'error' ? 'warning' : 'info'}">${message}</div>`;
            
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 5000);
        }

        async function extendSession() {
            try {
                const response = await fetch('/session/extend', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    remainingTime = 1800; // 30분으로 리셋
                    document.getElementById('sessionStatus').textContent = '활성';
                    document.getElementById('sessionStatus').style.color = '#2ed573';
                    showMessage(data.message, 'success');
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('세션 연장 중 오류가 발생했습니다.', 'error');
            }
        }

        async function updateActivity() {
            try {
                const response = await fetch('/session/update-activity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    showMessage('활동이 업데이트되었습니다.', 'success');
                }
            } catch (error) {
                showMessage('활동 업데이트 중 오류가 발생했습니다.', 'error');
            }
        }

        // 페이지 로드 시 타이머 시작
        document.addEventListener('DOMContentLoaded', function() {
        // 서버에서 전달된 남은 시간으로 초기화
        <?php if(isset($remaining_time) && is_numeric($remaining_time)): ?>
        remainingTime = <?= (int)$remaining_time ?>;
        <?php endif; ?>
            startTimer();
            
            // 주기적으로 세션 상태 확인
            setInterval(async () => {
                try {
                    const response = await fetch('/session/check', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'expired') {
                        clearInterval(sessionTimer);
                        window.location.href = '/login';
                    }
                } catch (error) {
                    console.error('세션 확인 중 오류:', error);
                }
            }, 60000); // 1분마다 확인
        });
    </script>
</body>
</html>
