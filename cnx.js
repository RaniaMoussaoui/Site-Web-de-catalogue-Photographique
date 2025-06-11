document.getElementById('inscription-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('connexion-form').style.display = 'none';
    document.getElementById('inscription-form').style.display = 'block';
});

document.getElementById('connexion-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('inscription-form').style.display = 'none';
    document.getElementById('connexion-form').style.display = 'block';
});
