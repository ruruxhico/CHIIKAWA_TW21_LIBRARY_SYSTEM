// Basic JS for showing/hiding password
function togglePassword(id) {
  const field = document.getElementById(id);
  field.type = (field.type === "password") ? "text" : "password";
}


// Optional: Confirm form submission
function confirmSubmit(event, message = "Are you sure?") {
  if (!confirm(message)) {
    event.preventDefault();
  }
}
