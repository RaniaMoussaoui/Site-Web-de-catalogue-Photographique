document.addEventListener("DOMContentLoaded", () => {
    // Gestion des boutons de menu
    const menuBtn = document.getElementById("menu-btn");
    const navLinks = document.getElementById("nav-links");
    const menuBtnIcon = menuBtn.querySelector("i");

    menuBtn.addEventListener("click", () => {
        navLinks.classList.toggle("open");
        const isOpen = navLinks.classList.contains("open");
        menuBtnIcon.setAttribute("class", isOpen ? "ri-close-line" : "ri-menu-line");
    });

    navLinks.addEventListener("click", () => {
        navLinks.classList.remove("open");
        menuBtnIcon.setAttribute("class", "ri-menu-line");
    });

    // Fonction pour charger les services
    const fetchServices = () => {
        fetch("services.php")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Erreur lors de la récupération des données");
                }
                return response.json();
            })
            .then((services) => {
                const servicesContainer = document.querySelector(".service__grid");
                servicesContainer.innerHTML = "";

                services.forEach((service) => {
                    const serviceCard = document.createElement("div");
                    serviceCard.classList.add("service__card");

                    serviceCard.innerHTML = `
                        <h4>${service.nom}</h4>
                        <p>${service.description}</p>
                        <p><strong>Prix :</strong> ${service.prix} €</p>
                    `;

                    const reserveButton = document.createElement("button");
                    reserveButton.textContent = "Réserver";
                    reserveButton.classList.add("reserve-btn");
                    reserveButton.setAttribute("data-service-id", service.id);

                    reserveButton.addEventListener("click", async () => {
                        const availableDates = await fetchAvailableDates(service.id);
                        initCalendar(availableDates);
                        openReservationModal(service);
                    });

                    serviceCard.appendChild(reserveButton);
                    servicesContainer.appendChild(serviceCard);
                });
            })
            .catch((error) => {
                console.error("Erreur lors du chargement des services :", error);
                document.querySelector(".service__grid").innerHTML = `
                    <p>Une erreur est survenue lors du chargement des services.</p>
                `;
            });
    };

    // Fonction pour récupérer les dates disponibles pour un service
    const fetchAvailableDates = (serviceId) => {
        return fetch(`available_dates.php?service_id=${serviceId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Erreur lors de la récupération des créneaux disponibles");
                }
                return response.json();
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des créneaux :", error);
                return [];
            });
    };

    // Initialisation du calendrier
    const initCalendar = (availableDates) => {
        const calendarElement = document.getElementById("calendar");
        calendarElement.innerHTML = "";

        if (availableDates.length === 0) {
            calendarElement.innerHTML = "<p>Aucun créneau disponible pour ce service.</p>";
            return;
        }

        const calendarInput = document.createElement("select");
        availableDates.forEach(date => {
            const option = document.createElement("option");
            option.value = date;
            option.textContent = date;
            calendarInput.appendChild(option);
        });

        calendarElement.appendChild(calendarInput);
    };

    // Modal de réservation
    const reservationModal = document.getElementById("reservation-modal");
    const closeBtn = reservationModal.querySelector(".close-btn");
    const serviceName = document.getElementById("selected-service-name");

    const openReservationModal = (service) => {
        serviceName.textContent = service.nom;
        reservationModal.style.display = "block";
    };

    const closeReservationModal = () => {
        reservationModal.style.display = "none";
    };

    closeBtn.addEventListener("click", closeReservationModal);

    window.addEventListener("click", (e) => {
        if (e.target === reservationModal) {
            closeReservationModal();
        }
    });

    // Charger les services au démarrage
    fetchServices();
});

document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.galerie__content .btn');

    buttons.forEach(function(button) {
        button.addEventListener('click', function() {
            const category = button.getAttribute('data-categorie');
            alert('Vous avez sélectionné la galerie : ' + category);
        });
    });
});
