        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2025 Médiathèque Moderne - Développé avec ❤️ et PHP</p>
        </footer>
    </div>

    <!-- Scripts pour les interactions -->
    <script>
        // Animation des cartes au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer toutes les cartes de média
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.media-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });
        });

        // Gestion des actions d'emprunt/retour
        async function handleMediaAction(mediaId, action) {
            try {
                const response = await fetch('actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=${action}&media_id=${mediaId}`
                });

                const result = await response.json();
                
                if (result.success) {
                    // Recharger la page pour refléter les changements
                    window.location.reload();
                } else {
                    alert(result.message || 'Une erreur s\'est produite');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de connexion');
            }
        }

        // Ajouter les événements aux boutons
        document.addEventListener('DOMContentLoaded', () => {
            const actionButtons = document.querySelectorAll('[data-action]');
            actionButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const mediaId = button.dataset.mediaId;
                    const action = button.dataset.action;
                    handleMediaAction(mediaId, action);
                });
            });
        });
    </script>
</body>
</html>