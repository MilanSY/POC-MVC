<div class="section">
    <div class="section-header">
        <h2 class="section-title">Collection de Livres</h2>
        <a href="/add-book" class="btn btn-primary">Ajouter un livre</a>
    </div>
    
    <?php if (!empty($books)): ?>
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($books) ?></div>
                <div class="stat-label">Livres Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($books, fn($book) => $book['disponible'] == 1)) ?></div>
                <div class="stat-label">Disponibles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($books, fn($book) => $book['disponible'] == 0)) ?></div>
                <div class="stat-label">Empruntés</div>
            </div>
        </div>

        <div class="media-grid">
            <?php foreach ($books as $index => $book): ?>
                <div class="media-card book">
                    <div class="media-header">
                        <span class="media-type book">Livre</span>
                        <span class="availability <?= $book['disponible'] ? 'available' : 'unavailable' ?>">
                            <?= $book['disponible'] ? 'Disponible' : 'Emprunté' ?>
                        </span>
                    </div>
                    
                    <h3 class="media-title">
                        <a href="/book-details/<?= $book['id'] ?>" class="media-title-link">
                            <?= htmlspecialchars($book['titre']) ?>
                        </a>
                    </h3>
                    <p class="media-author">par <?= htmlspecialchars($book['auteur']) ?></p>
                    
                    <div class="media-details">
                        <div class="detail-item">
                            <span class="detail-label">Statut</span>
                            <span class="detail-value">
                                <?php if ($book['disponible']): ?>
                                    Disponible                                    
                                <?php else: ?>
                                    Emprunté par <strong><?= htmlspecialchars($book['borrowed_by_username'] ?? 'Inconnu') ?></strong>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="media-actions">
                        <a href="/book-details/<?= $book['id'] ?>" class="btn btn-outline btn-small">Détails</a>
                        
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <?php if ($book['disponible']): ?>
                                <form method="POST" action="/books" style="display: inline;">
                                    <input type="hidden" name="action" value="borrow">
                                    <input type="hidden" name="media_id" value="<?= $book['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-small">Emprunter</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="/books" style="display: inline;">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="media_id" value="<?= $book['id'] ?>">
                                    <button type="submit" class="btn btn-secondary btn-small">Rendre</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="auth-required"><a href="/login">Connectez-vous</a> pour emprunter</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon"></div>
            <h3>Aucun livre disponible</h3>
            <p>La collection de livres est actuellement vide.</p>
            <a href="index.php?page=home" class="btn btn-primary">← Retour à l'accueil</a>
        </div>
    <?php endif; ?>
</div>