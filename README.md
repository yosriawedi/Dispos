# Dis Pos

**Dis Pos** est une plateforme d'incubation académique et professionnelle. Elle connecte étudiants, formateurs et entreprises autour de la révision par matière, de l'encadrement de projets de fin d'études, du troc de compétences et du recrutement — avec, en toile de fond, un premier module marketplace (startups / talents / investisseurs) hérité d'une itération antérieure du produit.

## Sommaire

- [Stack technique](#stack-technique)
- [Modules fonctionnels](#modules-fonctionnels)
- [Rôles utilisateurs](#rôles-utilisateurs)
- [Installation](#installation)
- [Comptes de démonstration](#comptes-de-démonstration)
- [Charte graphique](#charte-graphique)
- [Structure du projet](#structure-du-projet)
- [Limites connues](#limites-connues)

## Stack technique

| | |
|---|---|
| Framework | Symfony 7.4 |
| PHP | ≥ 8.2 (voir [Installation](#installation) pour une note sur cet environnement) |
| ORM | Doctrine ORM + Migrations |
| Base de données | MariaDB / MySQL |
| Templates | Twig |
| Assets | Webpack Encore (CSS/JS compilés, pas de framework front) |
| Administration | EasyAdmin |
| Authentification | Symfony Security (formulaire de connexion, CSRF *stateless* same-origin) |

## Modules fonctionnels

### Volet académique

| Module | Ce qu'il permet |
|---|---|
| **Révision par matière** | Un étudiant consulte les matières disponibles, choisit une session de révision et s'y inscrit. |
| **Demande d'encadrement** | Un étudiant en PFE, PFA ou doctorat soumet un sujet et/ou une stack technique pour un encadrement précoce. |
| **Troc de compétences** | Un étudiant propose un service/livrable en échange d'une réduction sur son incubation. Le barème de conversion n'est **pas** figé côté code : chaque demande reste au statut `en_attente` jusqu'à traitement manuel par l'équipe DisPos. |
| **Projets internes DisPos** | Un étudiant contribue à un projet professionnel interne de DisPos au lieu de payer en argent. |
| **Espace Formateurs** | Un talent externe postule pour devenir formateur (matières, stacks, tarif, disponibilité). |
| **Espace Recrutement** | Une entreprise publie une offre ; n'importe quel utilisateur peut y candidater. |

### Volet professionnel (TPE/PME) — Incubation

Une entreprise (`ROLE_ENTREPRISE`) démarre un dossier d'incubation depuis `/mon-espace/incubation`. À la création, les **7 phases** du parcours sont générées automatiquement (statut initial `non_demarree`) : Diagnostic, Étude de marché, Structuration juridique, Stratégie marketing, Développement, Test & lancement, Suivi post-incubation. L'entreprise suit sa progression sur une timeline verticale et peut demander une consultation à l'équipe DisPos. Un référent (n'importe quel rôle) peut être affecté à chaque phase depuis l'admin. Sécurité : une entreprise ne voit que son propre dossier (`IncubationVoter`, 403 sinon) ; les admins ont accès à tous les dossiers.

### Administration

Toutes les demandes/offres/candidatures ci-dessus sont gérables depuis `/admin` (EasyAdmin) : changement de statut, notes internes, consultation des soumissions.

## Rôles utilisateurs

Définis sur l'entité `User` (`src/Entity/User.php`) :

- `ROLE_ETUDIANT` — réviseur, demandeur d'encadrement, contributeur de compétences
- `ROLE_FORMATEUR` — candidat puis validé
- `ROLE_ENTREPRISE` — recrutement et/ou incubation professionnelle
- `ROLE_ADMIN` — validation des demandes, gestion des sessions et des projets internes
- `ROLE_STARTUP` / `ROLE_TALENT` / `ROLE_INVESTOR` — hérités du module marketplace d'origine, conservés en parallèle le temps d'une migration progressive

## Installation

> ⚠️ Ce projet nécessite **PHP ≥ 8.2**. Si votre `php` par défaut est plus ancien, adaptez les commandes ci-dessous pour pointer vers un binaire compatible (ex. `/chemin/vers/php8.2/php.exe bin/console ...`).

```bash
# 1. Dépendances PHP
composer install

# 2. Dépendances front + build des assets
npm install
npm run dev        # ou "npm run watch" en développement continu

# 3. Base de données — configurez DATABASE_URL dans .env (ou .env.local)
#    Le serveur cible doit être MySQL ou MariaDB ; précisez la bonne version
#    dans serverVersion, ex :
#    DATABASE_URL="mysql://user:pass@127.0.0.1:3306/DisPos?serverVersion=10.4.32-MariaDB&charset=utf8mb4"
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

# 4. Données de démonstration (comptes + matières + sessions + offres...)
php bin/console doctrine:fixtures:load --no-interaction

# 5. Lancer le serveur de développement
symfony server:start
# ou, sans Symfony CLI :
php -S 127.0.0.1:8000 -t public
```

## Comptes de démonstration

Créés par `src/DataFixtures/AppFixtures.php` :

| Email | Mot de passe | Rôle |
|---|---|---|
| `admin@dispos.io` | `admin123` | Admin |
| `etudiant@dispos.io` | `etudiant123` | Étudiant |
| `formateur@dispos.io` | `formateur123` | Formateur |
| `entreprise@dispos.io` | `entreprise123` | Entreprise |
| `talent@dispos.io` / `designer@dispos.io` | `talent123` | Talent (marketplace) |
| `investor@dispos.io` | `investor123` | Investisseur (marketplace) |
| `startup1@dispos.io` / `startup2@dispos.io` | `startup123` | Startup (marketplace) |

## Charte graphique

Variables CSS centralisées dans `assets/styles/app.css` :

| Nom | Hex | Usage |
|---|---|---|
| `--dispos-deep-teal` | `#0A2E3D` | Fonds sombres, headers |
| `--dispos-mint-accent` | `#34C7A9` | Boutons primaires, liens actifs |
| `--dispos-teal-shade` | `#0B3A4A` | Bordures, variantes de fond |
| `--dispos-mint-light` | `#52DFC0` | États hover |
| `--dispos-mint-pale` | `#D0F5ED` | Badges, fonds clairs secondaires |
| `--dispos-panel-dark` | `#0d3347` | Panneaux/cartes sur fond sombre |

Le logo (`assets/images/disposlogo.png`) est intégré dans la navbar et sert de favicon.

### Mode clair / sombre

Bascule disponible dans la navbar (icône 🌙/☀️). La préférence est stockée dans un cookie `dispos_theme` lu côté serveur (`base.html.twig`) : le thème correct est appliqué dès le premier rendu HTML, sans flash. Les deux jeux de variables sont définis via `:root[data-theme="light"]` / `:root[data-theme="dark"]` dans `assets/styles/app.css`.

## Structure du projet

```
src/
├── Controller/          # Contrôleurs publics (un par module)
├── Controller/Admin/     # Dashboard + CrudControllers EasyAdmin
├── Entity/               # Entités Doctrine
├── Form/                 # Formulaires Symfony (un par flux de soumission)
├── Repository/           # Repositories Doctrine
└── DataFixtures/          # Données de démonstration

templates/
├── base.html.twig        # Layout global (navbar, footer, charte graphique)
├── matiere/, encadrement/, competence/, projet_interne/,
│   formateur/, recrutement/  # Un dossier de templates par module
├── security/, dashboard/, admin/
└── startup/, talent/, project/, home/   # Marketplace hérité

migrations/                # Historique des migrations Doctrine
assets/                    # Sources JS/CSS compilées par Webpack Encore
```

## Limites connues

- **Barème compétence → réduction** — volontairement non automatisé (statut `en_attente` extensible) tant que la règle métier n'est pas figée.
- **Réassignation de rôle depuis l'admin** — le champ `roles` est éditable dans le CRUD `User`, mais aucune interface dédiée de changement de rôle n'existe pour les autres processus de validation (ex. affecter un encadreur à une demande) ; cela reste à faire via l'admin CRUD standard.
- **Processus de validation formateurs / offres de recrutement tierces** — le statut existe (`soumise`, `en_review`, `validee`...) mais le workflow métier précis (qui valide, sous quels critères) n'est pas encore défini.
- **Listes fermées non validées côté métier** — la nomenclature des secteurs d'activité (`IncubationCreationType::SECTEURS`) a été définie par défaut, faute de liste fournie par les tickets ; à confirmer avec l'équipe DisPos (même remarque que les gouvernorats dans `AUDIT-FORMULAIRES.md`).
- **Une seule incubation active par entreprise** — enforcée en logique applicative (`IncubationController::creer`), pas par une contrainte de base de données (MySQL/MariaDB ne supporte pas nativement les index uniques partiels).
