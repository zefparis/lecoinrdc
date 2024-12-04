<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réinitialisation du mot de passe</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="reset-password-container">
    <div class="reset-password-card">
      <h2>Réinitialisation du mot de passe</h2>
      <form id="resetPasswordForm">
        <div class="form-group">
          <label for="newPassword">Nouveau mot de passe</label>
          <input type="password" id="newPassword" name="newPassword" required placeholder="Entrez votre nouveau mot de passe">
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirmer le mot de passe</label>
          <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirmez votre nouveau mot de passe">
        </div>
        <button type="submit" class="submit-button">Réinitialiser le mot de passe</button>
      </form>
    </div>
  </div>

  <script>
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      // Logique pour soumettre le formulaire via AJAX
    });
  </script>
</body>
</html>
