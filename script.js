const form = document.getElementById('emailForm');
const previewBox = document.getElementById('preview');
const previewText = document.getElementById('previewText');
const sendBtn = document.getElementById('sendBtn');
const message = document.getElementById('message');
let formData = {};

// Basic client-side validation helper
function validateForm(name, email, position) {
  if (!name.trim() || !email.trim() || !position.trim()) return false;
  // Basic email pattern (simple, for client-side only)
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
}

form.addEventListener('submit', function (e) {
  e.preventDefault();

  const name = document.getElementById('name').value;
  const email = document.getElementById('email').value;
  const position = document.getElementById('position').value;
  const statusEl = document.querySelector('input[name="status"]:checked');

  // Validate on submit
  if (!statusEl) {
    showMessage('Please select a status (Selected or Rejected).', 'error');
    return;
  }

  if (!validateForm(name, email, position)) {
    showMessage('Please provide a valid name, email, and position.', 'error');
    return;
  }

  const status = statusEl.value;

  // Build template using selected status
  let template = '';

  if (status === 'selected') {
    template = `Dear ${name},\n\nWe are pleased to inform you that you have been selected for the position of ${position}.\n\nPlease reply to this email to confirm your acceptance.\n\nBest regards,\nHR Team`;
  } else {
    template = `Dear ${name},\n\nThank you for applying for the position of ${position}.\n\nWe regret to inform you that we have decided to move forward with other candidates.\n\nBest regards,\nHR Team`;
  }

  previewText.textContent = template;
  previewBox.classList.remove('hidden');
  showMessage('', '');

  formData = { name, email, position, status };
});

// Show a message to the user (error / success)
function showMessage(text, type = 'success') {
  message.textContent = text;
  message.classList.remove('success', 'error');
  if (type) message.classList.add(type);
}

// Send the email via backend when user clicks Send
sendBtn.addEventListener('click', async () => {
  // Double-check data
  if (!validateForm(formData.name || '', formData.email || '', formData.position || '')) {
    showMessage('Invalid data. Please re-check the form and preview again.', 'error');
    return;
  }

  // Disable send button while processing
  sendBtn.disabled = true;
  sendBtn.textContent = 'Sending...';

  try {
    const response = await fetch('send_email.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(formData)
    });

    const json = await response.json();
    if (json.success) {
      showMessage(json.message, 'success');
      previewBox.classList.add('hidden');
      form.reset();
      formData = {};
    } else {
      showMessage(json.message || 'Failed to send email.', 'error');
    }
  } catch (err) {
    showMessage('Network or server error: ' + err.message, 'error');
  } finally {
    sendBtn.disabled = false;
    sendBtn.textContent = 'Send Email';
  }
});
