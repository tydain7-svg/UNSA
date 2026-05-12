// Slide Panel Toggle
const container = document.getElementById('container');
document.getElementById('signUp').addEventListener('click', () => container.classList.add("right-panel-active"));
document.getElementById('signIn').addEventListener('click', () => container.classList.remove("right-panel-active"));

// Spotlight Toggle
const spotlight = document.querySelector('.spotlight');
const modeSwitch = document.getElementById('mode-switch');
modeSwitch.addEventListener('change', () => {
  document.body.classList.toggle('dark');
  spotlight.style.top = '0%';
  spotlight.style.opacity = '1';
  setTimeout(() => {
    spotlight.style.opacity = '0';
    spotlight.style.top = '-100%';
  }, 600);
});



// Show Notification Toast
function showNotification(message) {
  const toast = document.createElement('div');
  toast.className = 'toast-message';
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.classList.add('show'), 100);
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 500);
      window.location.href = "cap.php";



  }, 1500);
}

// Check if toast message is stored (from PHP redirect)
window.onload = function() {
  const msg = localStorage.getItem('toastMessage');
  if (msg) {
    showNotification(msg);
    localStorage.removeItem('toastMessage');
  }
};
