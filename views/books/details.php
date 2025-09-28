<div class="media-details-container">
    <div class="media-details-header">
        <a href="/books" class="back-button">← Retour aux livres</a>
        <div class="media-type-badge book">
            📚 Livre
        </div>
    </div>

    <div class="media-details-content">
        <div class="media-main-info">
            <h1 class="media-title"><?= htmlspecialchars($book['titre']) ?></h1>
            <p class="media-author">par <?= htmlspecialchars($book['auteur']) ?></p>
            
            <div class="availability-status <?= $book['disponible'] ? 'available' : 'unavailable' ?>">
                <?php if ($book['disponible']): ?>
                    <span class="status-icon">✅</span>
                    <span class="status-text">Disponible</span>
                <?php else: ?>
                    <span class="status-icon">📤</span>
                    <span class="status-text">Emprunté par <strong><?= htmlspecialchars($book['borrowed_by_username'] ?? 'Inconnu') ?></strong></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="media-specific-details">
            <div class="detail-card">
                <h3 class="detail-card-title">Informations du livre</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Nombre de pages</span>
                        <span class="detail-value"><?= $book['page_number'] ?? 'N/A' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Type</span>
                        <span class="detail-value">Livre</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions du livre -->
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <div class="media-actions-section">
                <div class="primary-actions">
                    <?php if ($book['disponible']): ?>
                        <form method="POST" action="/book-details/<?= $book['id'] ?>" style="display: inline;">
                            <input type="hidden" name="action" value="borrow">
                            <input type="hidden" name="media_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-large">📥 Emprunter</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/book-details/<?= $book['id'] ?>" style="display: inline;">
                            <input type="hidden" name="action" value="return">
                            <input type="hidden" name="media_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn btn-secondary btn-large">📤 Rendre</button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="secondary-actions">
                    <a href="/edit-book/<?= $book['id'] ?>" class="btn btn-outline">✏️ Modifier</a>
                    <form method="POST" action="/book-details/<?= $book['id'] ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ? Cette action est irréversible.')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="media_id" value="<?= $book['id'] ?>">
                        <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="auth-required-message">
                <p><a href="/login" class="btn btn-primary">Connectez-vous</a> pour emprunter, modifier ou supprimer ce livre</p>
            </div>
        <?php endif; ?>
    </div>
</div>