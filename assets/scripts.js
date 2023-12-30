document.addEventListener('DOMContentLoaded', function () {
  handleTestModal();
  handleAuthMode();
  var cookieExists = checkCookie('submit-result');
  if (cookieExists) {
    showToast('Changes Saved!', 'success');
  }
});

const handleTestModal = () => {
  var testButton = document.getElementById('test-button');
  var modal = document.getElementById('test-modal');
  var closeModal = document.querySelector('.close');
  var sendTestEmailButton = document.getElementById('send-test-email');
  var recipientEmailInput = document.getElementById('recipient_email');
  var testResult = document.getElementById('test-result');

  testButton.addEventListener('click', function () {
    modal.style.display = 'flex';
  });

  closeModal.addEventListener('click', function () {
    modal.style.display = 'none';
  });

  window.addEventListener('click', function (event) {
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  });

  sendTestEmailButton.addEventListener('click', function () {
    var recipientEmail = recipientEmailInput.value;
    var data = {
      action: 'send_test_email',
      recipient_email: recipientEmail,
    };
    document
      .querySelector('.loading-container')
      .classList.remove('display-none');
    testResult.innerHTML = null;

    jQuery.post(ajaxurl, data, function (response) {
      testResult.innerHTML = response;
      document
        .querySelector('.loading-container')
        .classList.add('display-none');
    });
  });
};

const handleAuthMode = () => {
  var switchElement = document.getElementById('authentication-switch');

  switchElement.addEventListener('click', function () {
    var checkbox = document.getElementById('authentication_checkbox');
    checkbox.checked = !checkbox.checked;
    handleInputs(checkbox.checked);
  });

  let smtp_username = document.getElementById('smtp_username');
  let smtp_password = document.getElementById('smtp_password');
  let auth_inputs = document.getElementsByClassName('auth-input');

  let handleInputs = (selectedValue) => {
    if (selectedValue) {
      smtp_username.removeAttribute('disabled');
      smtp_password.removeAttribute('disabled');
      for (let i = 0; i < auth_inputs.length; i++) {
        auth_inputs[i].style.display = 'table-row';
      }
    } else {
      smtp_username.setAttribute('disabled', 'disabled');
      smtp_password.value = '';
      smtp_password.setAttribute('disabled', 'disabled');
      smtp_username.value = '';
      for (let i = 0; i < auth_inputs.length; i++) {
        auth_inputs[i].style.display = 'none';
      }
    }
  };

  function getSelectedRadioButtonValue(fieldset) {
    var selectedRadioButton = fieldset.querySelector(
      'input[name="authentication"]:checked'
    );
    return selectedRadioButton ? selectedRadioButton.value : null;
  }

  handleInputs(document.getElementById('authentication_checkbox').checked);
};

function checkCookie(cookieName) {
  var cookies = document.cookie.split(';');

  for (var i = 0; i < cookies.length; i++) {
    var cookie = cookies[i].trim();

    // Check if the cookie starts with the provided name
    if (cookie.indexOf(cookieName + '=') === 0) {
      return true; // Cookie found
    }
  }

  return false; // Cookie not found
}
function showToast(message, type) {
  var toastContainer = document.getElementById('toast-container');
  var toast = document.getElementById('toast');

  // Set the message
  toast.innerText = message;

  // Add or remove success class based on the type
  if (type === 'success') {
    toast.classList.add('success');
  } else {
    toast.classList.remove('success');
  }

  // Show the toast
  toastContainer.style.display = 'flex';

  // Hide the toast after 3 seconds (adjust as needed)
  setTimeout(function () {
    toastContainer.style.display = 'none';
    toast.classList.remove('success');
  }, 3000);
}
