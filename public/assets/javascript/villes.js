document.addEventListener("DOMContentLoaded", function () {
  const codePostalInput = document.getElementById("codePostal");
  const villeSelect = document.getElementById("ville");
  const idVilleInput = document.getElementById("idVille");

  // Check if postal code already has 5 digits on page load
  const initialCodePostal = codePostalInput.value.trim();
  if (initialCodePostal.length === 5 && /^\d{5}$/.test(initialCodePostal)) {
    fetchVilles(initialCodePostal);
  }

  codePostalInput.addEventListener("input", function () {
    const codePostal = this.value.trim();

    // Check if we have exactly 5 digits
    if (codePostal.length === 5 && /^\d{5}$/.test(codePostal)) {
      fetchVilles(codePostal);
    } else {
      // Reset the city select if postal code is not valid
      resetVilleSelect();
    }
  });

  villeSelect.addEventListener("change", function () {
    // Update the hidden idVille field when a city is selected
    const selectedOption = this.options[this.selectedIndex];
    idVilleInput.value = selectedOption.value;
  });

  async function fetchVilles(codePostal) {
    // Show loading state
    villeSelect.innerHTML = '<option value="">Chargement...</option>';
    villeSelect.disabled = true;

    try {
      const response = await fetch("http://le-media-voironnais/villes", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          codePostal: codePostal,
        }),
      });

      const data = await response.json();
      populateVilleSelect(data);
    } catch (error) {
      console.error("Error fetching cities:", error);
      villeSelect.innerHTML =
        '<option value="">Erreur lors du chargement</option>';
      villeSelect.disabled = true;
    }
  }

  function populateVilleSelect(response) {
    villeSelect.innerHTML = '<option value="">Sélectionnez une ville</option>';

    // Check if the response has success and data
    if (
      response &&
      response.succes &&
      response.data &&
      response.data.length > 0
    ) {
      response.data.forEach((city) => {
        const option = document.createElement("option");
        option.value = city.idVille;
        option.textContent = city.ville_nom_reel;
        villeSelect.appendChild(option);
      });
      villeSelect.disabled = false;
      villeSelect.style.backgroundColor = "#6ed3cf"; // Green background

      // If there's only one city, select it by default
      if (response.data.length === 1) {
        villeSelect.value = response.data[0].idVille;
        idVilleInput.value = response.data[0].idVille;
      }
    } else {
      villeSelect.innerHTML = '<option value="">Aucune ville trouvée</option>';
      villeSelect.disabled = true;
      villeSelect.style.backgroundColor = "#f5c2c7"; // Red background
    }
  }

  function resetVilleSelect() {
    villeSelect.innerHTML = '<option value="">Sélectionnez une ville</option>';
    villeSelect.disabled = true;
    idVilleInput.value = "";
    villeSelect.style.backgroundColor = ""; // Reset background
  }
});
