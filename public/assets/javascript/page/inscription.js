import * as yup from "https://esm.sh/yup";

const form = document.querySelector("#connexionForm");
const $firstName = document.querySelector("#firstName");
const $lastName = document.querySelector("#lastName");
const $email = document.querySelector("#email");
const $password = document.querySelector("#password");
const $passwordConfirmation = document.querySelector("#passwordConfirmation");
const $rgpd = document.querySelector("#rgpd");

function showFieldError({ $element, message = "Ce champ est requis" }) {
  // Remove previous error if exists
  const parent = $element.parentElement;
  let span = parent.querySelector('span.error');
  if (span) span.remove();
  span = document.createElement("span");
  span.classList.add("error");
  span.textContent = message;
  $element.style.borderColor = "red";
  parent.appendChild(span);
}

function clearFieldError($element) {
  $element.style.borderColor = "";
  const parent = $element.parentElement;
  const span = parent.querySelector('span.error');
  if (span) span.remove();
}

// YUP schema
const schema = yup.object().shape({
  firstName: yup
    .string()
    .min(2, "Le prénom doit contenir au moins 2 caractères")
    .required("Champ obligatoire"),
  lastName: yup
    .string()
    .min(2, "Le nom doit contenir au moins 2 caractères")
    .required("Champ obligatoire"),
  email: yup
    .string()
    .email("Email invalide")
    .required("Champ obligatoire"),
  password: yup
    .string()
    .min(8, "Le mot de passe doit contenir au moins 8 caractères")
    .matches(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/, "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre")
    .required("Champ obligatoire"),
  passwordConfirmation: yup
    .string()
    .oneOf([yup.ref('password'), null], "Les mots de passe ne correspondent pas")
    .required("Champ obligatoire"),
  rgpd: yup
    .boolean()
    .oneOf([true], "Veuillez accepter les conditions d'utilisation")
    .required("Champ obligatoire")
});

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  [$firstName, $lastName, $email, $password, $passwordConfirmation, $rgpd].forEach(clearFieldError);

  const formData = new FormData(form);
  const values = {
    firstName: formData.get("firstName"),
    lastName: formData.get("lastName"),
    email: formData.get("email"),
    password: formData.get("password"),
    passwordConfirmation: formData.get("passwordConfirmation"),
    rgpd: $rgpd.checked
  };

  try {
    await schema.validate(values, { abortEarly: false });
    // Dispatch custom event for valid form
    form.dispatchEvent(new CustomEvent("form:valid", { bubbles: true }));
  } catch (error) {
    if (error.inner) {
      error.inner.forEach((err) => {
        switch (err.path) {
          case "firstName":
            showFieldError({ $element: $firstName, message: err.message });
            break;
          case "lastName":
            showFieldError({ $element: $lastName, message: err.message });
            break;
          case "email":
            showFieldError({ $element: $email, message: err.message });
            break;
          case "password":
            showFieldError({ $element: $password, message: err.message });
            break;
          case "passwordConfirmation":
            showFieldError({ $element: $passwordConfirmation, message: err.message });
            break;
          case "rgpd":
            showFieldError({ $element: $rgpd, message: err.message });
            break;
        }
      });
    }
    // Dispatch custom event for invalid form to remove loader
    form.dispatchEvent(new CustomEvent("form:invalid", { bubbles: true }));
    return; // <--- Add this line to stop further execution
  }
});