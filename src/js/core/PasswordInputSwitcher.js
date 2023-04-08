class PasswordInputSwitcher {
  constructor() {
    document
      .querySelectorAll(".switch-input-password-text")
      .forEach(function (el) {
        var input = el.parentNode.querySelector("input");

        el.addEventListener("click", function (e) {
          e.preventDefault();
          input.parentNode.querySelector(".bx").classList.toggle("bx-hide");
          input.parentNode.querySelector(".bx").classList.toggle("bx-show");

          if (input.type === "password") {
            input.type = "text";
          } else {
            input.type = "password";
          }
        });
      });
  }
}

export default PasswordInputSwitcher;
