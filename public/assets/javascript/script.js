
/**
 * adds a loading overlay to all forms on the page when they are submitted.
 * The overlay contains a loading image and disables the submit button to prevent multiple submissions.
 */
// if form id is not "formGroupCalculator", do not apply the loader

document.querySelectorAll("form").forEach(function (form) {
  form.addEventListener("submit", function (e) {
    if (document.querySelector("#formGroupCalculator")) {
      return;
    }
    if (!document.getElementById("loaderOverlay")) {
      const overlay = document.createElement("div");
      overlay.id = "loaderOverlay";
      Object.assign(overlay.style, {
        position: "fixed",
        top: 0,
        left: 0,
        width: "100vw",
        height: "100vh",
        backgroundColor: "rgba(255, 255, 255, 0.5)",
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
        zIndex: 9999,
      });

      const loaderImage = document.createElement("img");
      loaderImage.src = "assets/images/loader/loader.svg";
      loaderImage.alt = "Chargement...";
      loaderImage.style.width = "150px";
      loaderImage.style.zIndex = 9999;

      overlay.appendChild(loaderImage);
      document.body.appendChild(overlay);
    }

    const submitBtn = form.querySelector('[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Chargement...";
    }
  });
});

// This  is a toggle functionality to password inputs, allowing users to show or hide their passwords with an eye icon.
document.querySelectorAll('input[type="password"]').forEach(function (input) {
  const wrapper = document.createElement("div");
  wrapper.classList.add("password-wrapper");
  input.parentNode.insertBefore(wrapper, input);
  wrapper.appendChild(input);

  const eyeIcon = document.createElement("span");
  eyeIcon.classList.add("toggle-password");
  eyeIcon.textContent = "👁️‍🗨️";
  wrapper.appendChild(eyeIcon);

  eyeIcon.addEventListener("click", function () {
    input.type = input.type === "password" ? "text" : "password";
    eyeIcon.textContent = input.type === "password" ? "👁️‍🗨️" : "🙈";
  });

  const toggleIconVisibility = () => {
    eyeIcon.style.display = input.value ? "inline" : "none";
  };

  toggleIconVisibility();
  input.addEventListener("input", toggleIconVisibility);
});

//  handle the burger menu functionality for mobile navigation.
  document.addEventListener('DOMContentLoaded', function() {
    const burger = document.getElementById('burger-menu');
    const nav = document.getElementById('nav-links');

    burger.addEventListener('click', function(e) {
      e.stopPropagation();
      burger.classList.toggle('active');
      nav.classList.toggle('open');
    });

    nav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        burger.classList.remove('active');
        nav.classList.remove('open');
      });
    });

    document.addEventListener('click', function(e) {
      if (
        nav.classList.contains('open') &&
        !nav.contains(e.target) &&
        !burger.contains(e.target)
      ) {
        burger.classList.remove('active');
        nav.classList.remove('open');
      }
    });
  });

