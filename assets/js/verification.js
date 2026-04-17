const API_BASE = "http://localhost/GrameenSetu/api"; // ✅ IMPORTANT FIX

const VerificationPopup = {
    create(userId, email, onSuccess) {

        const existing = document.querySelector('.verification-overlay');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.className = 'verification-overlay';
        overlay.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            z-index: 10000;
        `;

        const modal = document.createElement('div');
        modal.style.cssText = `
            background: white; border-radius: 28px; padding: 28px;
            width: 90%; max-width: 400px; text-align: center;
            box-shadow: 0 20px 35px rgba(0,0,0,0.2);
            font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
        `;

        modal.innerHTML = `
            <div style="font-size: 48px;">📧</div>
            <h3 style="margin: 10px 0 5px; font-size: 24px;">Verify Your Email</h3>
            <p style="color: #555; margin-bottom: 20px;">
                We sent a 6-digit code to <strong>${escapeHtml(email)}</strong>
            </p>

            <div id="codeMessage" style="font-size: 14px; color: #166534; margin-bottom: 10px;"></div>

            <input type="text" id="verificationCode" placeholder="Enter code" maxlength="6"
                style="width: 100%; padding: 12px; font-size: 18px; text-align: center;
                border: 1px solid #ccc; border-radius: 12px; margin-bottom: 16px;">

            <div id="verifyError" style="color: #c62828; font-size: 13px; margin-bottom: 12px;"></div>
            <div id="verifySuccess" style="color: #166534; font-size: 14px; font-weight: bold; margin-bottom: 12px; display: none;"></div>

            <button id="verifyBtn"
                style="background: #166534; color: white; border: none;
                padding: 12px 20px; border-radius: 40px; width: 100%;
                font-weight: bold; cursor: pointer; font-size: 16px;">
                Verify
            </button>

            <div style="margin-top: 15px;">
                <a href="#" id="resendCodeLink" style="color: #2e7d32; font-size: 13px;">Resend code</a>
                <span style="margin: 0 8px">|</span>
                <a href="#" id="closeVerification" style="color: #888; font-size: 13px;">Cancel</a>
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        const codeInput = modal.querySelector('#verificationCode');
        const verifyBtn = modal.querySelector('#verifyBtn');
        const errorDiv = modal.querySelector('#verifyError');
        const successDiv = modal.querySelector('#verifySuccess');
        const resendLink = modal.querySelector('#resendCodeLink');
        const closeBtn = modal.querySelector('#closeVerification');
        const codeMessageDiv = modal.querySelector('#codeMessage');

        // ✅ VERIFY FUNCTION (FIXED)
        const verify = async () => {
            const code = codeInput.value.trim();

            if (!code) {
                errorDiv.textContent = 'Please enter the verification code.';
                return;
            }

            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Verifying...';
            errorDiv.textContent = '';
            successDiv.style.display = 'none';

            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('code', code);

            try {
                const response = await fetch(`${API_BASE}/verify_new.php`, {
                    method: 'POST',
                    body: formData
                });

                console.log("STATUS:", response.status);

                const rawText = await response.text();
                console.log("RAW RESPONSE:", rawText);

                if (!rawText) throw new Error("Empty response from server");

                let data;
                try {
                    data = JSON.parse(rawText);
                } catch {
                    throw new Error("Invalid JSON: " + rawText.substring(0, 100));
                }

                if (data.status === 'success') {

                    successDiv.textContent = data.message || '✅ Verification successful!';
                    successDiv.style.display = 'block';

                    if (data.user) {
                        localStorage.setItem('logged_user', JSON.stringify(data.user));
                    }

                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 2000);

                } else {
                    errorDiv.textContent = data.message;
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';
                }

            } catch (err) {
                console.error("FULL ERROR:", err);
                errorDiv.textContent = "Network error: " + err.message;
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify';
            }
        };

        // ✅ RESEND FUNCTION (FIXED)
        const resendCode = async () => {
            resendLink.textContent = 'Sending...';
            errorDiv.textContent = '';

            try {
                const formData = new FormData();
                formData.append('user_id', userId);

                const response = await fetch(`${API_BASE}/resend_code_new.php`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    codeMessageDiv.innerHTML = "✓ New code sent!";
                } else {
                    errorDiv.textContent = data.message;
                }

            } catch (err) {
                errorDiv.textContent = "Error: " + err.message;
            }

            resendLink.textContent = 'Resend code';
        };

        verifyBtn.onclick = verify;
        resendLink.onclick = (e) => { e.preventDefault(); resendCode(); };
        closeBtn.onclick = (e) => { e.preventDefault(); overlay.remove(); };

        codeInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') verify();
        });

        codeInput.focus();
    }
};

function escapeHtml(str) {
    return str.replace(/[&<>]/g, m => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    }[m]));
}

window.VerificationPopup = VerificationPopup;