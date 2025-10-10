import * as yup from "https://esm.sh/yup";

const $form = $("#connexionForm");
const $firstName = $("#firstName");
const $lastName = $("#lastName");
const $email = $("#email");
const $password = $("#password");
const $passwordConfirmation = $("#passwordConfirmation");
const $rgpd = $("#rgpd");

function showFieldError({ $element, message = "Ce champ est requis" }) {
    const $parent = $element.parent();
    let $span = $parent.find('span.error');
    if ($span.length) $span.remove();
    $span = $('<span></span>').addClass('error').text(message);
    $element.css('border-color', 'red');
    $parent.append($span);
}

function clearFieldError($element) {
    $element.css('border-color', '');
    const $parent = $element.parent();
    const $span = $parent.find('span.error');
    if ($span.length) $span.remove();
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

$form.on("submit", async function(event) {
    event.preventDefault();
    [$firstName, $lastName, $email, $password, $passwordConfirmation, $rgpd].forEach(clearFieldError);

    const formData = new FormData(this);
    const values = {
        firstName: formData.get("firstName"),
        lastName: formData.get("lastName"),
        email: formData.get("email"),
        password: formData.get("password"),
        passwordConfirmation: formData.get("passwordConfirmation"),
        rgpd: $rgpd.prop('checked')
    };

    try {
        await schema.validate(values, { abortEarly: false });
        $form.trigger($.Event("form:valid", { bubbles: true }));
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
        $form.trigger($.Event("form:invalid", { bubbles: true }));
        return;
    }
});