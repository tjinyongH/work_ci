class SessionManager {
    constructor() {
        this.checkInterval = 60000; // 1분마다 체크
        this.warningShown = false;
        this.sessionTimer = null;
        this.init();
    }
    
    init() {
        // 페이지 로드 시 세션 체크 시작
        this.startSessionCheck();
        
        // 사용자 활동 감지
        this.bindActivityEvents();
        
        // 세션 연장 모달 생성
        this.createExtensionModal();
    }
    
    // 주기적인 세션 체크
    startSessionCheck() {
        this.sessionTimer = setInterval(() => {
            this.checkSessionStatus();
        }, this.checkInterval);
    }
    
    // 세션 상태 확인
    async checkSessionStatus() {
        try {
            const response = await fetch('/session/check', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            // 세션 타이머 업데이트
            this.updateSessionTimer(data.formatted_time);
            
            switch(data.status) {
                case 'expired':
                    this.handleSessionExpired();
                    break;
                    
                case 'warning':
                    if (!this.warningShown) {
                        this.showExtensionWarning(data.remaining_time);
                    }
                    break;
                    
                case 'active':
                    this.warningShown = false;
                    this.hideExtensionModal();
                    break;
            }
        } catch (error) {
            console.error('세션 체크 오류:', error);
        }
    }
    
    // 사용자 활동 이벤트 바인딩
    bindActivityEvents() {
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, this.throttle(() => {
                this.updateActivity();
            }, 30000)); // 30초마다 한 번만 호출
        });
    }
    
    // 활동 업데이트
    async updateActivity() {
        try {
            await fetch('/session/update-activity', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
        } catch (error) {
            console.error('활동 업데이트 오류:', error);
        }
    }
    
    // 세션 연장 경고 표시
    showExtensionWarning(remainingTime) {
        this.warningShown = true;
        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;
        
        const modal = document.getElementById('sessionExtensionModal');
        const message = document.getElementById('sessionWarningMessage');
        
        message.textContent = `세션이 ${minutes}분 ${seconds}초 후에 만료됩니다. 연장하시겠습니까?`;
        modal.style.display = 'block';
        
        // 자동 카운트다운
        this.startCountdown(remainingTime);
    }
    
    // 카운트다운 시작
    startCountdown(remainingTime) {
        const countdownElement = document.getElementById('sessionCountdown');
        let timeLeft = remainingTime;
        
        const countdown = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(countdown);
                this.handleSessionExpired();
            }
            
            timeLeft--;
        }, 1000);
        
        // 모달이 닫히면 카운트다운 중지
        this.currentCountdown = countdown;
    }
    
    // 세션 연장
    async extendSession() {
        try {
            const response = await fetch('/session/extend', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                this.hideExtensionModal();
                this.warningShown = false;
                this.showNotification('세션이 30분 연장되었습니다.', 'success');
            }
        } catch (error) {
            console.error('세션 연장 오류:', error);
            this.showNotification('세션 연장 중 오류가 발생했습니다.', 'error');
        }
    }
    
    // 세션 만료 처리
    handleSessionExpired() {
        clearInterval(this.sessionTimer);
        this.showNotification('세션이 만료되었습니다. 다시 로그인해주세요.', 'error');
        
        setTimeout(() => {
            window.location.href = '/login';
        }, 3000);
    }
    
    // 연장 모달 숨기기
    hideExtensionModal() {
        const modal = document.getElementById('sessionExtensionModal');
        modal.style.display = 'none';
        
        if (this.currentCountdown) {
            clearInterval(this.currentCountdown);
        }
    }
    
    // 세션 타이머 업데이트
    updateSessionTimer(formattedTime) {
        const timerElement = document.getElementById('sessionTimer');
        if (timerElement) {
            timerElement.textContent = `세션 남은 시간: ${formattedTime}`;
        }
    }
    
    // 알림 표시
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
    
    // 연장 모달 생성
    createExtensionModal() {
        const modal = document.createElement('div');
        modal.id = 'sessionExtensionModal';
        modal.className = 'session-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>세션 만료 경고</h3>
                </div>
                <div class="modal-body">
                    <p id="sessionWarningMessage"></p>
                    <p>남은 시간: <span id="sessionCountdown"></span></p>
                </div>
                <div class="modal-footer">
                    <button id="extendSessionBtn" class="btn btn-primary">세션 연장</button>
                    <button id="logoutBtn" class="btn btn-secondary">로그아웃</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // 이벤트 바인딩
        document.getElementById('extendSessionBtn').addEventListener('click', () => {
            this.extendSession();
        });
        
        document.getElementById('logoutBtn').addEventListener('click', () => {
            window.location.href = '/session/logout';
        });
    }
    
    // 쓰로틀링 함수
    throttle(func, delay) {
        let timeoutId;
        let lastExecTime = 0;
        
        return function (...args) {
            const currentTime = Date.now();
            
            if (currentTime - lastExecTime > delay) {
                func.apply(this, args);
                lastExecTime = currentTime;
            } else {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                    lastExecTime = Date.now();
                }, delay - (currentTime - lastExecTime));
            }
        };
    }
}

// 페이지 로드 시 세션 매니저 초기화
document.addEventListener('DOMContentLoaded', () => {
    window.sessionManager = new SessionManager();
});