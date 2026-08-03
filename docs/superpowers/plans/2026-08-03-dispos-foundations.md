# DisPos Foundations Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Unblock every DisPos feature module (révision par matière, encadrement, troc de compétences, projets internes, formateurs, recrutement) by fixing the four cross-cutting gaps found in the audit: no test tooling, roles that don't match the domain model, missing database schema for 11 existing entities, and an unapplied charte graphique.

**Architecture:** Symfony 7.4 MVC app (Doctrine ORM, Twig, Webpack Encore, EasyAdmin). No architectural changes — this plan only adds the missing plumbing (test harness, roles, migration, CSS variables) so that later per-module plans can build directly on top of it.

**Tech Stack:** PHP 8.2+ (use `/c/php82/php.exe` explicitly — the `php` on PATH is 8.0.30 and will fail), Symfony 7.4, Doctrine ORM/Migrations, PHPUnit via `symfony/test-pack`, MySQL 8.0 (local, `DisPos` / `DisPos_test` databases), Webpack Encore (plain CSS, no Sass loader enabled).

## Global Constraints

- Always invoke PHP as `/c/php82/php.exe`, never bare `php` or the XAMPP `php.exe` (both resolve to 8.0.30, incompatible with `composer.json`'s `"php": ">=8.2"`).
- Charte graphique colors (exact hex, from the project prompt): Deep Teal `#0A2E3D` (primaire), Mint Accent `#34C7A9`, Teal Shade `#0B3A4A`, Mint Light `#52DFC0`, Mint Pale `#D0F5ED`, Panel Dark `#0d3347`. No color literals outside the central variable block once this plan is done.
- Do not touch `DemandeReduction` / `OffreCompetence` calculation logic — the barème is explicitly TBD; only schema/status plumbing is in scope.
- `.env.*.local` is gitignored — machine-specific DB credentials go there, never in `.env.test` (which would be committed).

---

### Task 1: Test tooling + DB-independent smoke test

**Files:**
- Modify: `composer.json`, `composer.lock` (via `composer require`)
- Create: `tests/bootstrap.php`, `phpunit.xml.dist` (via Flex recipe)
- Create: `tests/Controller/SecurityControllerTest.php`

**Interfaces:**
- Produces: a working `php bin/phpunit` command later tasks' tests rely on.

- [ ] **Step 1: Install the Symfony test pack**

```bash
cd "/c/Users/yosri/Desktop/Dispos l'agence/Dis Pos"
"/c/php82/php.exe" /c/php82/composer.phar require --dev symfony/test-pack --no-interaction
```

If `composer.phar` isn't at that path, run `where composer` first and substitute the real path (or `composer.bat`), but always prefix the call with `"/c/php82/php.exe"` so it resolves dependencies against 8.2, not 8.0.

- [ ] **Step 2: Confirm PHPUnit runs**

Run: `"/c/php82/php.exe" bin/phpunit --version`
Expected: prints a PHPUnit version line (no fatal errors, no "requires PHP" complaints).

- [ ] **Step 3: Write the failing smoke test**

Create `tests/Controller/SecurityControllerTest.php`:

```php
<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }
}
```

This route was picked deliberately: `SecurityController::login()` (`src/Controller/SecurityController.php:18-31`) does not query the database on a GET request, so this test proves the kernel boots and routing/Twig work without needing the DB/migration work done in Task 3.

- [ ] **Step 4: Run it and watch it fail for the right reason (or pass)**

Run: `"/c/php82/php.exe" bin/phpunit tests/Controller/SecurityControllerTest.php`

If it fails, read the error — likely a missing `APP_ENV=test` config or missing `.env.test`. If Flex didn't generate one, create `.env.test`:

```
KERNEL_CLASS='App\Kernel'
APP_SECRET='$ecretf0rtest'
SYMFONY_DEPRECATIONS_HELPER=999999
PANTHER_APP_ENV=panther
```

Re-run until it passes.

- [ ] **Step 5: Confirm it passes**

Run: `"/c/php82/php.exe" bin/phpunit tests/Controller/SecurityControllerTest.php`
Expected: `OK (1 test, 2 assertions)`

- [ ] **Step 6: Commit**

```bash
git add composer.json composer.lock phpunit.xml.dist tests/bootstrap.php tests/Controller/SecurityControllerTest.php .env.test
git commit -m "test: add PHPUnit tooling with a DB-independent smoke test"
```

---

### Task 2: New roles (ROLE_ETUDIANT, ROLE_FORMATEUR, ROLE_ENTREPRISE)

**Files:**
- Modify: `src/Entity/User.php:18-21` (constants), `src/Entity/User.php:101-107` (`getPrimaryRole()`)
- Modify: `src/Form/RegistrationFormType.php:34-44` (choices)
- Test: `tests/Entity/UserTest.php`

**Interfaces:**
- Produces: `User::ROLE_ETUDIANT`, `User::ROLE_FORMATEUR`, `User::ROLE_ENTREPRISE` (string constants, values `'ROLE_ETUDIANT'`, `'ROLE_FORMATEUR'`, `'ROLE_ENTREPRISE'`) — every later module task (encadrement, formateurs, recrutement, troc) assigns these roles to `User` and must use these exact constant names, not raw strings.
- Consumes: nothing new (pure additions to the existing `User` entity from the audit).

Per the earlier decision: existing `ROLE_STARTUP` / `ROLE_TALENT` / `ROLE_INVESTOR` are **kept** for now (marketplace is being replaced progressively, not deleted in this task) — this task only *adds* the three new roles alongside them.

- [ ] **Step 1: Write the failing test**

Create `tests/Entity/UserTest.php`:

```php
<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testPrimaryRoleResolvesEtudiant(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ETUDIANT']);

        $this->assertSame(User::ROLE_ETUDIANT, $user->getPrimaryRole());
    }

    public function testPrimaryRoleResolvesFormateur(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_FORMATEUR']);

        $this->assertSame(User::ROLE_FORMATEUR, $user->getPrimaryRole());
    }

    public function testPrimaryRoleResolvesEntreprise(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ENTREPRISE']);

        $this->assertSame(User::ROLE_ENTREPRISE, $user->getPrimaryRole());
    }

    public function testAdminRoleStillWinsOverEtudiant(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN', 'ROLE_ETUDIANT']);

        $this->assertSame(User::ROLE_ADMIN, $user->getPrimaryRole());
    }
}
```

- [ ] **Step 2: Run it and confirm it fails**

Run: `"/c/php82/php.exe" bin/phpunit tests/Entity/UserTest.php`
Expected: FAIL — `Undefined constant App\Entity\User::ROLE_ETUDIANT` (or similar).

- [ ] **Step 3: Add the constants**

In `src/Entity/User.php`, replace:

```php
    public const ROLE_TALENT = 'ROLE_TALENT';
    public const ROLE_STARTUP = 'ROLE_STARTUP';
    public const ROLE_INVESTOR = 'ROLE_INVESTOR';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
```

with:

```php
    public const ROLE_TALENT = 'ROLE_TALENT';
    public const ROLE_STARTUP = 'ROLE_STARTUP';
    public const ROLE_INVESTOR = 'ROLE_INVESTOR';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_ETUDIANT = 'ROLE_ETUDIANT';
    public const ROLE_FORMATEUR = 'ROLE_FORMATEUR';
    public const ROLE_ENTREPRISE = 'ROLE_ENTREPRISE';
```

- [ ] **Step 4: Update `getPrimaryRole()`**

Replace:

```php
    public function getPrimaryRole(): string
    {
        foreach ([self::ROLE_ADMIN, self::ROLE_INVESTOR, self::ROLE_STARTUP, self::ROLE_TALENT] as $role) {
            if (in_array($role, $this->roles)) return $role;
        }
        return 'ROLE_USER';
    }
```

with:

```php
    public function getPrimaryRole(): string
    {
        foreach ([
            self::ROLE_ADMIN,
            self::ROLE_ENTREPRISE,
            self::ROLE_FORMATEUR,
            self::ROLE_ETUDIANT,
            self::ROLE_INVESTOR,
            self::ROLE_STARTUP,
            self::ROLE_TALENT,
        ] as $role) {
            if (in_array($role, $this->roles)) return $role;
        }
        return 'ROLE_USER';
    }
```

- [ ] **Step 5: Run the test again and confirm it passes**

Run: `"/c/php82/php.exe" bin/phpunit tests/Entity/UserTest.php`
Expected: `OK (4 tests, 4 assertions)`

- [ ] **Step 6: Update the registration form**

In `src/Form/RegistrationFormType.php`, replace the `role` field's `choices`:

```php
                'choices' => [
                    '🚀 Un entrepreneur / Startup founder' => User::ROLE_STARTUP,
                    '💡 Un talent / Développeur / Designer' => User::ROLE_TALENT,
                    '💰 Un investisseur / Business Angel' => User::ROLE_INVESTOR,
                ],
```

with:

```php
                'choices' => [
                    '🎓 Un étudiant (révision, PFE/PFA, encadrement)' => User::ROLE_ETUDIANT,
                    '👨‍🏫 Un formateur' => User::ROLE_FORMATEUR,
                    '🏢 Une entreprise (recrutement / incubation TPE-PME)' => User::ROLE_ENTREPRISE,
                    '🚀 Un entrepreneur / Startup founder' => User::ROLE_STARTUP,
                    '💡 Un talent / Développeur / Designer' => User::ROLE_TALENT,
                    '💰 Un investisseur / Business Angel' => User::ROLE_INVESTOR,
                ],
```

- [ ] **Step 7: Run the full suite**

Run: `"/c/php82/php.exe" bin/phpunit`
Expected: all tests green (5 tests total: 1 from Task 1 + 4 from this task).

- [ ] **Step 8: Commit**

```bash
git add src/Entity/User.php src/Form/RegistrationFormType.php tests/Entity/UserTest.php
git commit -m "feat: add ROLE_ETUDIANT, ROLE_FORMATEUR, ROLE_ENTREPRISE roles"
```

---

### Task 3: Migration + test database for the 11 existing DisPos entities

**Files:**
- Create: `.env.test.local` (gitignored, not committed)
- Create: `migrations/VersionXXXXXXXXXXXXXX.php` (generated by `doctrine:migrations:diff`, exact filename depends on timestamp — do not hand-write it)
- Test: `tests/Entity/MatiereRepositoryTest.php`

**Interfaces:**
- Produces: DB tables for `matiere`, `session_revision`, `inscription_session`, `demande_encadrement`, `offre_competence`, `demande_reduction`, `projet_interne_dispos`, `contribution_projet_interne`, `candidature_formateur`, `offre_recrutement`, `candidature_recrutement` — every module task from here on depends on these tables existing.

- [ ] **Step 1: Fix the metadata storage (found during audit)**

Run: `"/c/php82/php.exe" bin/console doctrine:migrations:sync-metadata-storage`
Expected: `Metadata storage synchronized`

Run: `"/c/php82/php.exe" bin/console doctrine:migrations:status`
Expected: no error, shows `Version20260803162457` as the only executed migration.

- [ ] **Step 2: Create the test database config**

Create `.env.test.local`:

```
DATABASE_URL="mysql://root:@127.0.0.1:3306/DisPos_test?serverVersion=8.0"
```

Create the database itself:

Run: `"/c/php82/php.exe" bin/console doctrine:database:create --env=test --if-not-exists`
Expected: `Created database "DisPos_test"`

- [ ] **Step 3: Generate the migration diff for the 11 unmigrated entities**

Run: `"/c/php82/php.exe" bin/console doctrine:migrations:diff --no-interaction`
Expected: a new file appears under `migrations/`, e.g. `migrations/Version20260803180000.php`, containing `CREATE TABLE` statements for `matiere`, `session_revision`, `inscription_session`, `demande_encadrement`, `offre_competence`, `demande_reduction`, `projet_interne_dispos`, `contribution_projet_interne`, `candidature_formateur`, `offre_recrutement`, `candidature_recrutement`.

Open the generated file and confirm it only adds tables — it must not contain any `DROP TABLE` for `user`, `startup`, `project`, `tag`, `startup_tag`. If it does, stop and re-check that `src/Entity/*` wasn't accidentally modified.

- [ ] **Step 4: Apply the migration to both databases**

Run: `"/c/php82/php.exe" bin/console doctrine:migrations:migrate --no-interaction`
Expected: `Migrated 1 to VersionXXXXXXXXXXXXXX. Migration completed.`

Run: `"/c/php82/php.exe" bin/console doctrine:migrations:migrate --no-interaction --env=test`
Expected: same success line, applied to `DisPos_test`.

- [ ] **Step 5: Validate schema matches entities**

Run: `"/c/php82/php.exe" bin/console doctrine:schema:validate`
Expected: `[OK] The mapping files are correct.` and `[OK] The database schema is in sync with the mapping files.`

- [ ] **Step 6: Write the failing integration test**

Create `tests/Entity/MatiereRepositoryTest.php`:

```php
<?php

namespace App\Tests\Entity;

use App\Entity\Matiere;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MatiereRepositoryTest extends KernelTestCase
{
    public function testPersistAndFindActiveMatiere(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        $matiere = (new Matiere())
            ->setNom('Algorithmique')
            ->setFiliere('Informatique')
            ->setNiveau('Licence')
            ->setActive(true);

        $em->persist($matiere);
        $em->flush();

        /** @var MatiereRepository $repo */
        $repo = $container->get(MatiereRepository::class);
        $found = $repo->find($matiere->getId());

        $this->assertNotNull($found);
        $this->assertSame('Algorithmique', $found->getNom());
        $this->assertTrue($found->isActive());
    }
}
```

- [ ] **Step 7: Run it and confirm it fails before the migration is picked up by test env**

Run: `"/c/php82/php.exe" bin/phpunit tests/Entity/MatiereRepositoryTest.php`

If this fails with a table-not-found error, it means Step 4's `--env=test` migration didn't apply — re-run `"/c/php82/php.exe" bin/console doctrine:migrations:migrate --no-interaction --env=test` and retry. Once the table exists, this test should already pass (there's no new production code to write here — the test exists to prove the schema is real and reachable, not to drive new implementation).

- [ ] **Step 8: Confirm it passes**

Run: `"/c/php82/php.exe" bin/phpunit tests/Entity/MatiereRepositoryTest.php`
Expected: `OK (1 test, 3 assertions)`

- [ ] **Step 9: Run the full suite**

Run: `"/c/php82/php.exe" bin/phpunit`
Expected: all 6 tests green.

- [ ] **Step 10: Commit**

```bash
git add migrations/ tests/Entity/MatiereRepositoryTest.php
git commit -m "feat: add database migration for the 11 DisPos domain entities"
```

Note: `.env.test.local` is gitignored by design — do not force-add it.

---

### Task 4: Charte graphique — centralized CSS variables, logo, favicon

**Files:**
- Modify: `assets/styles/app.css`
- Modify: `templates/base.html.twig`
- Create: `public/favicon.png` (copy of the logo)
- Modify: `tests/Controller/SecurityControllerTest.php`

**Interfaces:**
- Produces: CSS custom properties `--dispos-deep-teal`, `--dispos-mint-accent`, `--dispos-teal-shade`, `--dispos-mint-light`, `--dispos-mint-pale`, `--dispos-panel-dark` on `:root` in `assets/styles/app.css` — every future template/component must reference these instead of hardcoding hex values.

Two things found in the audit that this task fixes together: (1) the charte graphique isn't applied anywhere — `base.html.twig` hardcodes an unrelated purple/cyan palette inline; (2) the Encore-compiled `app.css` is never actually loaded by `base.html.twig` (no `encore_entry_link_tags`), so `assets/styles/app.css` currently has zero effect on the rendered page.

- [ ] **Step 1: Replace the placeholder CSS with the real palette as custom properties**

Replace the entire contents of `assets/styles/app.css`:

```css
:root {
    --dispos-deep-teal: #0A2E3D;
    --dispos-mint-accent: #34C7A9;
    --dispos-teal-shade: #0B3A4A;
    --dispos-mint-light: #52DFC0;
    --dispos-mint-pale: #D0F5ED;
    --dispos-panel-dark: #0d3347;

    --dispos-text: #E8F5F1;
    --dispos-text-muted: #9FC4BE;
    --dispos-gradient: linear-gradient(135deg, var(--dispos-deep-teal) 0%, var(--dispos-teal-shade) 100%);
    --dispos-radius: 16px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
    font-family: 'Inter', sans-serif;
    background: var(--dispos-deep-teal);
    color: var(--dispos-text);
    line-height: 1.6;
}

.navbar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
    background: rgba(10, 46, 61, 0.92);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--dispos-teal-shade);
    padding: 0 2rem;
    height: 70px;
    display: flex; align-items: center; justify-content: space-between;
}
.navbar-brand { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; }
.navbar-brand img { height: 32px; width: auto; }
.navbar-nav { display: flex; align-items: center; gap: 0.25rem; list-style: none; }
.navbar-nav a {
    color: var(--dispos-text-muted); text-decoration: none; padding: 0.5rem 1rem;
    border-radius: 8px; font-size: 0.9rem; font-weight: 500;
}
.navbar-nav a:hover, .navbar-nav a.active { color: var(--dispos-mint-light); }

.btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: none; cursor: pointer; }
.btn-outline { background: transparent; border: 1px solid var(--dispos-teal-shade); color: var(--dispos-text); }
.btn-outline:hover { border-color: var(--dispos-mint-accent); color: var(--dispos-mint-accent); }
.btn-primary { background: var(--dispos-mint-accent); color: var(--dispos-deep-teal); }
.btn-primary:hover { background: var(--dispos-mint-light); }

.card {
    background: var(--dispos-panel-dark); border: 1px solid var(--dispos-teal-shade);
    border-radius: var(--dispos-radius); padding: 1.5rem;
}

.badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
.badge-primary { background: rgba(52, 199, 169, 0.18); color: var(--dispos-mint-accent); border: 1px solid rgba(52, 199, 169, 0.3); }

main { margin-top: 70px; min-height: calc(100vh - 70px); }
.container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

.flash-container { position: fixed; top: 80px; right: 1rem; z-index: 2000; display: flex; flex-direction: column; gap: 0.5rem; }
.flash { padding: 1rem 1.5rem; border-radius: 10px; font-size: 0.9rem; font-weight: 500; }
.flash-success { background: var(--dispos-mint-pale); color: var(--dispos-deep-teal); }
.flash-error { background: rgba(255, 101, 132, 0.15); border: 1px solid rgba(255, 101, 132, 0.3); color: #ff6584; }

.footer { background: var(--dispos-panel-dark); border-top: 1px solid var(--dispos-teal-shade); padding: 3rem 0 1.5rem; margin-top: 5rem; }

.form-group { margin-bottom: 1.25rem; }
.form-label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--dispos-text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
.form-control { width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.04); border: 1px solid var(--dispos-teal-shade); border-radius: 10px; color: var(--dispos-text); font-size: 0.95rem; font-family: inherit; }
.form-control:focus { outline: none; border-color: var(--dispos-mint-accent); box-shadow: 0 0 0 3px rgba(52, 199, 169, 0.2); }
```

This intentionally keeps the same class names already used across `templates/*.html.twig` (`.navbar`, `.btn-primary`, `.card`, `.badge-primary`, `.flash-success`, `.form-control`, etc.) so no template markup needs to change — only the colors do.

- [ ] **Step 2: Wire Encore into the base template and remove the inline palette**

In `templates/base.html.twig`, replace the entire `{% block stylesheets %}...{% endblock %}` block (currently lines 11-137, the `<style>` tag with the hardcoded purple/cyan variables) with:

```twig
    {% block stylesheets %}
        {{ encore_entry_link_tags('app') }}
    {% endblock %}
```

- [ ] **Step 3: Move the inline `<script>` block to Encore too**

Replace the `{% block javascripts %}...{% endblock %}` block (the navbar scroll/hamburger/flash JS) with:

```twig
    {% block javascripts %}
        {{ encore_entry_script_tags('app') }}
    {% endblock %}
```

Move that same inline JS (navbar scroll effect, hamburger toggle, flash auto-dismiss) from the old `<script>` block into `assets/app.js`, appended after the existing `import './styles/app.css';` line:

```js
import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });
    }

    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('open');
        });
    }

    document.querySelectorAll('.flash').forEach((el) => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }, 4000);
    });
});
```

- [ ] **Step 4: Add the logo to the navbar and set the favicon**

In `templates/base.html.twig`, replace:

```twig
        <a href="{{ path('app_home') }}" class="navbar-brand">⚡ Dis Pos</a>
```

with:

```twig
        <a href="{{ path('app_home') }}" class="navbar-brand">
            <img src="{{ asset('images/disposlogo.png') }}" alt="Dis Pos">
            <span>Dis Pos</span>
        </a>
```

Add a favicon link in the `<head>`, right after the `<title>` block:

```twig
    <link rel="icon" type="image/png" href="{{ asset('images/disposlogo.png') }}">
```

Copy the logo so the `asset()` path resolves (Symfony's `asset()` serves from `public/`, not `assets/`):

```bash
cp "assets/images/disposlogo.png" "public/images/disposlogo.png"
```

(`assets/styles/disposlogo.png` is a stray duplicate — leave it for now, don't delete without confirming nothing references it via Sass/CSS `url()`; grep confirmed no references, so it's dead weight from a copy-paste but out of scope to delete in this task.)

- [ ] **Step 5: Build the Encore assets**

Run: `npm install` (only if `node_modules/` doesn't exist yet)
Run: `npm run dev`
Expected: `public/build/app.css` and `public/build/app.js` (or hashed variants) are created, no webpack errors.

- [ ] **Step 6: Extend the smoke test to prove the new template renders**

In `tests/Controller/SecurityControllerTest.php`, add a second test method:

```php
    public function testLoginPageUsesDisposBranding(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('img[alt="Dis Pos"]');
        $this->assertSelectorExists('link[rel="icon"]');
    }
```

- [ ] **Step 7: Run it and confirm it fails, then passes**

Run: `"/c/php82/php.exe" bin/phpunit tests/Controller/SecurityControllerTest.php`

If it fails with an asset-manifest error (`Could not find entry "app"`), Step 5's `npm run dev` didn't run or failed — check its output for errors and re-run.

Once Step 5's build artifacts exist:

Run: `"/c/php82/php.exe" bin/phpunit tests/Controller/SecurityControllerTest.php`
Expected: `OK (2 tests, 5 assertions)`

- [ ] **Step 8: Visually verify in a browser**

Run: `"/c/php82/php.exe" -S 127.0.0.1:8000 -t public`
Open `http://127.0.0.1:8000/login` and confirm: dark teal background (not purple), mint-green primary button, DisPos logo visible in the navbar, browser tab shows the logo as favicon.

- [ ] **Step 9: Run the full suite one last time**

Run: `"/c/php82/php.exe" bin/phpunit`
Expected: all 7 tests green.

- [ ] **Step 10: Commit**

```bash
git add assets/styles/app.css assets/app.js templates/base.html.twig public/images/disposlogo.png tests/Controller/SecurityControllerTest.php
git commit -m "feat: apply DisPos charte graphique and wire Encore assets"
```

---

## Self-Review

**Spec coverage** (against the project prompt, section 5 "Consignes pour l'agent de code"):
- §5.1 "Auditer avant de coder" → done prior to this plan (audit delivered in conversation, informed every task above).
- §5.2 "Respecter la charte graphique... variables CSS centralisées" → Task 4.
- §5.3 "Ne pas figer la logique de réduction par compétences" → explicitly called out in Global Constraints; `DemandeReduction`/`OffreCompetence` are untouched by this plan.
- §5.4 "Poser une question si ambigu" → done in conversation (marketplace fate, roles) before this plan was written.
- §5.5 "Plan d'implémentation par module" → this is the cross-cutting foundations plan; per-module plans (révision par matière first, per the audit ordering) follow separately once this lands.

**Placeholder scan:** no TBD/TODO markers; every step has literal file paths, literal code, and literal shell commands with expected output.

**Type/name consistency:** `User::ROLE_ETUDIANT` / `ROLE_FORMATEUR` / `ROLE_ENTREPRISE` (Task 2) are the exact names later module plans (encadrement, formateurs, recrutement) must reuse. CSS variable names (`--dispos-deep-teal` etc., Task 4) are the exact names later template work must reference — do not invent new variable names for the same colors.
