// Services
let services = [];
const serviceList = document.getElementById('services-list');
const serviceForm = document.getElementById('add-service-form');

// Afficher les services
function displayServices() {
    serviceList.innerHTML = '';
    services.forEach((service, index) => {
        const li = document.createElement('li');
        li.textContent = `${service.name} - ${service.description}`;
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Supprimer';
        deleteButton.onclick = () => {
            services.splice(index, 1);
            displayServices();
        };
        li.appendChild(deleteButton);
        serviceList.appendChild(li);
    });
}

// Ajouter un service
serviceForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const name = document.getElementById('service-name').value;
    const description = document.getElementById('service-description').value;
    services.push({ name, description });
    displayServices();
    serviceForm.reset();
});

// Réservations (statique pour l'exemple)
const reservationsList = document.getElementById('reservations-list');
const reservations = [
    { name: 'Jean Dupont', date: '2025-01-10', service: 'Shooting Mariage' }
];

function displayReservations() {
    reservationsList.innerHTML = '';
    reservations.forEach((reservation, index) => {
        const li = document.createElement('li');
        li.textContent = `${reservation.name} - ${reservation.date} (${reservation.service})`;
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Supprimer';
        deleteButton.onclick = () => {
            reservations.splice(index, 1);
            displayReservations();
        };
        li.appendChild(deleteButton);
        reservationsList.appendChild(li);
    });
}

displayReservations();

// Galerie
let gallery = [];
const galleryList = document.getElementById('gallery-list');
const galleryForm = document.getElementById('add-image-form');

// Afficher les images
function displayGallery() {
    galleryList.innerHTML = '';
    gallery.forEach((image, index) => {
        const li = document.createElement('li');
        const img = document.createElement('img');
        img.src = image;
        img.style.maxWidth = '200px';
        li.appendChild(img);
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Supprimer';
        deleteButton.onclick = () => {
            gallery.splice(index, 1);
            displayGallery();
        };
        li.appendChild(deleteButton);
        galleryList.appendChild(li);
    });
}

// Ajouter une image
galleryForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const imageUrl = document.getElementById('image-url').value;
    gallery.push(imageUrl);
    displayGallery();
    galleryForm.reset();
});
