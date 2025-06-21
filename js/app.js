// Hamburger menu toggle
const hamburger = document.getElementById('hamburger');
const mainNav = document.getElementById('main-nav');
hamburger.addEventListener('click', () => {
    mainNav.classList.toggle('active');
});

// Movie Search & Favorites
const searchInput = document.getElementById('movie-search');
const searchBtn = document.getElementById('search-btn');
const searchResults = document.getElementById('search-results');
const favoritesGrid = document.getElementById('favorites-grid');

let favorites = [];

function renderFavorites() {
    favoritesGrid.innerHTML = '';
    favorites.forEach((movie, idx) => {
        const card = document.createElement('div');
        card.className = 'movie-card';
        card.innerHTML = `
            <img src="${movie.image}" alt="${movie.title}">
            <h3>${movie.title}</h3>
            <p>${movie.summary}</p>
            <button class="remove-btn" aria-label="Remove from favorites" data-idx="${idx}">&times;</button>
        `;
        favoritesGrid.appendChild(card);
    });
    // Add remove event listeners
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.onclick = function() {
            const idx = this.getAttribute('data-idx');
            favorites.splice(idx, 1);
            renderFavorites();
        };
    });
}

// Add static placeholders to favorites on load
window.addEventListener('DOMContentLoaded', () => {
    favorites = [
        {
            image: 'assets/images/batman.jpg',
            title: 'Batman Returns',
            summary: 'Lorem ipsum dolor sit amet, consectetur sadipscing elitr...'
        },
        {
            image: 'assets/images/wildwest.jpg',
            title: 'Wild Wild West',
            summary: 'Lorem ipsum dolor sit amet, consectetur sadipscing elitr...'
        },
        {
            image: 'assets/images/spiderman.jpg',
            title: 'The Amazing Spiderman',
            summary: 'Lorem ipsum dolor sit amet, consectetur sadipscing elitr...'
        }
    ];
    renderFavorites();
});

// Movie search
searchBtn.addEventListener('click', searchMovies);
searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchMovies();
    }
});

function searchMovies() {
    const query = searchInput.value.trim();
    if (!query) return;
    searchResults.innerHTML = '<p>Loading...</p>';
    fetch(`https://api.tvmaze.com/search/shows?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            searchResults.innerHTML = '';
            if (!data.length) {
                searchResults.innerHTML = '<p>No results found.</p>';
                return;
            }
            data.forEach(item => {
                const show = item.show;
                const card = document.createElement('div');
                card.className = 'movie-card';
                card.innerHTML = `
                    <img src="${show.image ? show.image.medium : 'assets/images/placeholder.jpg'}" alt="${show.name}">
                    <h3>${show.name}</h3>
                    <p>${show.summary ? show.summary.replace(/<[^>]+>/g, '').slice(0, 100) + '...' : 'No description.'}</p>
                    <button class="add-btn" aria-label="Add to favorites">+</button>
                `;
                card.querySelector('.add-btn').onclick = function() {
                    favorites.push({
                        image: show.image ? show.image.medium : 'assets/images/placeholder.jpg',
                        title: show.name,
                        summary: show.summary ? show.summary.replace(/<[^>]+>/g, '').slice(0, 100) + '...' : 'No description.'
                    });
                    renderFavorites();
                };
                searchResults.appendChild(card);
            });
        })
        .catch(() => {
            searchResults.innerHTML = '<p>Network error. Please try again.</p>';
        });
}
