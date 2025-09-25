        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2025 Médiathèque Moderne - Développé avec ❤️ et PHP</p>
        </footer>
    </div>

    <!-- Scripts pour les interactions -->
    <script>
        // Gestion des actions d'emprunt/retour
        async function handleMediaAction(mediaId, action) {
            try {
                const response = await fetch('var/actions.php', {
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