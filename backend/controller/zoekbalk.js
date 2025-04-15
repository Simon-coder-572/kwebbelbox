// functie voor de zoekbalk die overal gebruikt kan worden waar div's zijn
function filterDivs() {
    let searchValue = document.getElementById('search').value.toLowerCase();
    let divs = document.querySelectorAll('.content > div');

    divs.forEach(div => {
        if (div.textContent.toLowerCase().includes(searchValue) || searchValue === '') {
            div.style.display = 'block';
        } else {
            div.style.display = 'none';
        }
    });
}

// Zorg ervoor dat de functie beschikbaar is in de globale scope
window.filterDivs = filterDivs;