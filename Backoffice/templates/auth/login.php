<section class="card auth-card">
    <h1>Connexion admin</h1>
    <p class="muted">Accès sécurisé au backoffice.</p>

    <form action="/traitementLogin" method="post" class="form-grid">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

        <label for="username">Nom d'utilisateur</label>
        <input id="username" name="username" type="text" required value="<?= e(old('username')) ?>">

        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" required>

        <button type="submit" class="btn">Se connecter</button>
    </form>
</section>
