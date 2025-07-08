// Highlight active navigation (if using a nav bar)
document.addEventListener("DOMContentLoaded", function () {
  const navLinks = document.querySelectorAll(".navbar a");
  const currentPath = window.location.pathname;

  navLinks.forEach(link => {
    if (link.href.includes(currentPath)) {
      link.style.color = "#1a73e8";
      link.style.fontWeight = "bold";
    }
  });

  //basic form validation
  const forms = document.querySelectorAll("form");

  forms.forEach(form => {
    form.addEventListener("submit", function (e) {
      const requiredInputs = form.querySelectorAll("[required]");
      let valid = true;

      requiredInputs.forEach(input => {
        if (!input.value.trim()) {
          input.style.borderColor = "red";
          valid = false;
        } else {
          input.style.borderColor = "#a3c3e7";
        }
      });

      if (!valid) {
        e.preventDefault();
        alert("Please fill out all required fields.");
      }
    });
  });
});

//toggle password visibility
function togglePassword(id) {
  const field = document.getElementById(id);
  field.type = (field.type === "password") ? "text" : "password";
}

// Confirm form submission
function confirmSubmit(event, message = "Are you sure?") {
  if (!confirm(message)) {
    event.preventDefault();
  }
}

function saveCredentials() {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  const remember = document.getElementById("remember").checked;

  if (remember) {
    document.cookie = `username=${username}; max-age=604800`; // 7 days
    document.cookie = `password=${password}; max-age=604800`;
  } else {
    document.cookie = "username=; max-age=0";
    document.cookie = "password=; max-age=0";
  }
}

//from register.php
function togglePassword(id) {
        var x = document.getElementById(id);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

//from dashboard.php
function confirmReturn(bookTitle) {
            return confirm("Are you sure you want to return '" + bookTitle + "'?");
        }

//from archive_book.php
function confirmUnarchive(bookTitle) {
            return confirm("Are you sure you want to unarchive '" + bookTitle + "'?");
        }