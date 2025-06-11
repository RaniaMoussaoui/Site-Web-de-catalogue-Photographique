function addProduct(event) {
    event.preventDefault();

    const nom = document.getElementById("nom").value;
    const description = document.getElementById("description").value;

    fetch("services.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            action: "add",
            nom: nom,
            description: description,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert(data.message);
                const servicesList = document.getElementById("services");
                const newService = document.createElement("li");
                newService.innerHTML = `<strong>${nom}</strong>: ${description} <button onclick="deleteService(${data.id})">Supprimer</button>`;
                servicesList.appendChild(newService);

                // Réinitialiser le formulaire
                document.querySelector("form").reset();
            } else {
                alert(data.message);
            }
        })
        .catch((error) => console.error("Erreur :", error));
}

// Associez la fonction au formulaire
document.querySelector("form").addEventListener("submit", addProduct);
