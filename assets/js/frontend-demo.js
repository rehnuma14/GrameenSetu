(function () {
  function showMessage(elementId, message, success) {
    const element = document.getElementById(elementId);
    if (!element) return;
    element.textContent = message;
    element.classList.toggle('success', Boolean(success));
  }

  function enterDemo(email, name, role) {
    const user = {
      email: email.toLowerCase(),
      name: name || email.split('@')[0],
      role: role || 'farmer',
      demo: true
    };
    localStorage.setItem('logged_user', JSON.stringify(user));
    window.location.href = 'index.html';
  }

  window.doLogin = function () {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    if (!email || !password) {
      showMessage('loginError', 'Enter an email and password to continue in demo mode.', false);
      return;
    }
    enterDemo(email, email.split('@')[0], 'farmer');
  };

  window.doSignup = function () {
    const name = document.getElementById('signupName').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    const password = document.getElementById('signupPassword').value;
    const confirm = document.getElementById('signupConfirm').value;
    if (!name || !email || !password || !confirm) {
      showMessage('signupError', 'Complete all fields to create a demo profile.', false);
      return;
    }
    if (password !== confirm) {
      showMessage('signupError', 'Passwords do not match.', false);
      return;
    }
    const role = document.getElementById('role-buyer').classList.contains('selected') ? 'buyer' : 'farmer';
    enterDemo(email, name, role);
  };
})();
