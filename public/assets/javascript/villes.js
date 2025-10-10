$(document).ready(function() {
  const $codePostalInput = $("#codePostal");
  const $villeSelect = $("#ville");
  const $idVilleInput = $("#idVille");

  // Check if postal code already has 5 digits on page load
  const initialCodePostal = $codePostalInput.val().trim();
  if (initialCodePostal.length === 5 && /^\d{5}$/.test(initialCodePostal)) {
    fetchVilles(initialCodePostal);
  }

  $codePostalInput.on("input", function () {
    const codePostal = $(this).val().trim();

    // Check if we have exactly 5 digits
    if (codePostal.length === 5 && /^\d{5}$/.test(codePostal)) {
      fetchVilles(codePostal);
    } else {
      // Reset the city select if postal code is not valid
      resetVilleSelect();
    }
  });

  $villeSelect.on("change", function () {
    // Update the hidden idVille field when a city is selected
    const selectedOption = $(this).find('option:selected');
    $idVilleInput.val(selectedOption.val());
  });

  async function fetchVilles(codePostal) {
    // Show loading state
    $villeSelect.html('<option value="">Chargement...</option>');
    $villeSelect.prop('disabled', true);

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
      $villeSelect.html('<option value="">Erreur lors du chargement</option>');
      $villeSelect.prop('disabled', true);
    }
  }

  function populateVilleSelect(response) {
    $villeSelect.html('<option value="">Sélectionnez une ville</option>');

    // Check if the response has success and data
    if (
      response &&
      response.succes &&
      response.data &&
      response.data.length > 0
    ) {
      response.data.forEach((city) => {
        const option = $('<option></option>').val(city.idVille).text(city.ville_nom_reel);
        $villeSelect.append(option);
      });
      $villeSelect.prop('disabled', false);
      $villeSelect.css("backgroundColor", "#6ed3cf"); // Green background

      // If there's only one city, select it by default
      if (response.data.length === 1) {
        $villeSelect.val(response.data[0].idVille);
        $idVilleInput.val(response.data[0].idVille);
      }
    } else {
      $villeSelect.html('<option value="">Aucune ville trouvée</option>');
      $villeSelect.prop('disabled', true);
      $villeSelect.css("backgroundColor", "#f5c2c7"); // Red background
    }
  }

  function resetVilleSelect() {
    $villeSelect.html('<option value="">Sélectionnez une ville</option>');
    $villeSelect.prop('disabled', true);
    $idVilleInput.val("");
    $villeSelect.css("backgroundColor", ""); // Reset background
  }
});
