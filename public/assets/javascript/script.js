// handling the group calculator functionality, allowing users to calculate the distribution of people into groups.
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#formGroupCalculator");
  const result = document.querySelector(".result");
  const resultPlus = document.querySelector(".resultPlus");

  if (!form) {
    return;
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const nbGroup = parseInt(document.querySelector("#nbGroup").value);
    const nbTotalPerson = parseInt(
      document.querySelector("#nbTotalPerson").value
    );

    if (!nbGroup || nbGroup <= 0 || nbGroup > nbTotalPerson) {
      result.textContent = "";
      resultPlus.textContent = "Veuillez entrer un nombre de groupes valide.";
      result.classList.add("d-none");
      resultPlus.classList.remove("d-none");
      return;
    }

    const baseGroupSize = Math.floor(nbTotalPerson / nbGroup);
    const remainder = nbTotalPerson % nbGroup;
    const groupsWithExtra = remainder;
    const groupsWithBase = nbGroup - remainder;

    const toPlural = (count, singular, plural) =>
      count === 1 ? singular : plural;

    // Affichage du groupe standard
    if (groupsWithBase > 0) {
      result.textContent = `${groupsWithBase} ${toPlural(
        groupsWithBase,
        "groupe",
        "groupes"
      )} de ${baseGroupSize} ${toPlural(
        baseGroupSize,
        "personne",
        "personnes"
      )}`;
      result.classList.remove("d-none");
    } else {
      result.classList.add("d-none");
    }

    // Affichage du groupe avec 1 personne en plus
    if (groupsWithExtra > 0) {
      resultPlus.textContent = `${groupsWithExtra} ${toPlural(
        groupsWithExtra,
        "groupe",
        "groupes"
      )} de ${baseGroupSize + 1} ${toPlural(
        baseGroupSize + 1,
        "personne",
        "personnes"
      )}`;
      resultPlus.classList.remove("d-none");
    } else {
      resultPlus.classList.add("d-none");
    }
  });
});

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

// profile picture upload functionality, allowing users to preview their selected image before submitting the form.
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('profilePicture');
    const submitBtn = fileInput?.closest('form')?.querySelector('button[type="submit"]');
    const cancelBtn = document.getElementById('cancelProfilePicture');
    const currentImg = document.getElementById('currentProfilePicture');

    // Create preview image element if not exists
    let previewImg = document.getElementById('profilePicturePreview');
    if (!previewImg && fileInput) {
        previewImg = document.createElement('img');
        previewImg.id = 'profilePicturePreview';
        previewImg.style.display = 'none';
        previewImg.style.maxWidth = '180px';
        previewImg.style.maxHeight = '180px';
        previewImg.style.margin = '1rem auto';
        previewImg.style.borderRadius = '50%';
        const profilePictureDiv = fileInput.closest('.account-profile-picture');
        if (profilePictureDiv) {
            profilePictureDiv.insertBefore(previewImg, profilePictureDiv.firstChild);
        }
    }

    function updateButtonAndPreview() {
        if (!fileInput || !submitBtn) return;
        if (fileInput.files && fileInput.files[0]) {
            submitBtn.disabled = false;
            if (cancelBtn) cancelBtn.style.display = 'inline-block';
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if (currentImg) currentImg.style.display = 'none';
            };
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            submitBtn.disabled = true;
            previewImg.style.display = 'none';
            if (currentImg) currentImg.style.display = 'block';
            if (cancelBtn) cancelBtn.style.display = 'none';
        }
    }

    if (fileInput && submitBtn) {
        submitBtn.disabled = true;
        if (cancelBtn) cancelBtn.style.display = 'none';
        fileInput.addEventListener('change', updateButtonAndPreview);
    }

    if (cancelBtn && fileInput && previewImg && currentImg) {
        cancelBtn.addEventListener('click', function () {
            fileInput.value = '';
            previewImg.style.display = 'none';
            submitBtn.disabled = true;
            currentImg.style.display = 'block';
            cancelBtn.style.display = 'none';
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const simulateBtn = document.getElementById("simulateButton");
    const modal = document.getElementById("groupCalculatorModal");
    const closeModal = document.getElementById("closeGroupCalculatorModal");

    if (simulateBtn && modal && closeModal) {
        simulateBtn.addEventListener("click", function () {
            modal.style.display = "flex";
        });
        closeModal.addEventListener("click", function () {
            modal.style.display = "none";
        });
        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    }
});