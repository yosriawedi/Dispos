# Design System — Backoffice Dis Pos (EasyAdmin)

Référentiel pour la refonte du backoffice `/admin`. Dérivé de la charte publique (`assets/styles/app.css`), adapté aux contraintes d'EasyAdmin 4.29 (qui expose son thème via des variables CSS personnalisées et un mode sombre natif scopé `.ea-dark-scheme`).

## 1. Tokens couleur

EasyAdmin n'est pas re-thémé en dupliquant ses classes — on **surcharge ses propres variables** avec les valeurs de la charte, dans les deux blocs qu'il définit déjà (clair par défaut, `.ea-dark-scheme` pour le sombre). Ça préserve son système de mode clair/sombre natif plutôt que de le contourner.

| Variable EasyAdmin | Rôle | Valeur clair | Valeur `.ea-dark-scheme` |
|---|---|---|---|
| `--color-primary` | Accent global (liens, boutons primaires, focus) | `--dispos-mint-accent` `#34C7A9` | `--dispos-mint-accent` `#34C7A9` |
| `--sidebar-bg` | Fond de la barre latérale | `--dispos-deep-teal` `#0A2E3D` | `--dispos-panel-dark` `#0d3347` |
| `--sidebar-menu-color` | Texte des items de menu | `#9FC4BE` (mint atténué) | `#9FC4BE` |
| `--sidebar-menu-icon-color` | Icônes du menu | `#9FC4BE` | `#9FC4BE` |
| `--sidebar-menu-active-item-bg` | Item de menu actif | `--dispos-mint-accent` | `--dispos-mint-accent` |
| `--sidebar-menu-active-item-color` | Texte de l'item actif | `--dispos-deep-teal` (contraste sur mint) | `--dispos-deep-teal` |
| `--sidebar-logo-color` | Titre "Dis Pos Admin" | `--dispos-mint-light` `#52DFC0` | `--dispos-mint-light` |
| `--body-bg` / `--content-bg` | Fond de la zone de contenu | blanc cassé (garde les tableaux lisibles) | `--dispos-deep-teal` |

**Pourquoi la sidebar reste toujours teal foncé, y compris en mode clair du contenu :** c'est l'ancre de marque du backoffice — cohérent avec le fait que le header public reste identifiable quel que soit le thème. Le contenu (tableaux, formulaires), lui, suit le mode clair/sombre choisi par l'admin, parce que la lisibilité des données prime sur le contenu.

Aucune nouvelle couleur n'est inventée : tout provient de la palette déjà validée dans `assets/styles/app.css`.

## 2. Typographie & espacement

EasyAdmin utilise déjà une échelle cohérente (`--font-size-xs` à `--font-size-xxxl`, `--border-radius-*`) — on ne la remplace pas, on l'hérite. Seul ajustement : `--font-family-sans-serif` pointe vers `Inter` (déjà chargée dans `base.html.twig` du site public) pour une continuité typographique entre le site et le backoffice, avec le même stack de repli que le CSS public.

## 3. Composant : badge de statut (`StatusField`)

**Problème résolu :** 14 contrôleurs sur 18 exposent `statut` comme champ texte libre — l'action la plus fréquente d'un admin (valider/refuser) devient une saisie manuelle sans filet.

**Solution :** EasyAdmin fournit nativement `ChoiceField::renderAsBadges()` — pas de template custom à écrire.

```php
ChoiceField::new('statut', 'Statut')
    ->setChoices([...])
    ->renderAsBadges([
        'en_attente' => 'secondary',
        'soumise'    => 'secondary',
        'validee'    => 'success',
        'acceptee'   => 'success',
        'refusee'    => 'danger',
        'bloquee'    => 'danger',
        'en_cours'   => 'info',
    ])
```

### Mapping sémantique (à appliquer à tous les statuts du projet)

| Sens métier | Type de badge EasyAdmin | Couleur perçue |
|---|---|---|
| En attente / soumis / non démarré | `secondary` | Neutre gris |
| En cours / en revue | `info` | Bleu-gris informatif |
| Validé / accepté / terminé | `success` | Vert (résonne avec `--dispos-mint-accent`) |
| Refusé / bloqué | `danger` | Rouge |

### Do's and Don'ts

| ✅ Faire | ❌ Ne pas faire |
|---|---|
| Mapper explicitement chaque valeur de statut à un type de badge | Laisser `renderAsBadges(true)` (tout en gris "secondary", perd l'info) |
| Garder le champ éditable en `ChoiceField` sur les pages Edit/New | Rendre le champ en lecture seule — l'admin doit pouvoir changer le statut, c'est l'action principale |
| Réutiliser le même mapping sur toutes les entités de workflow | Inventer un jeu de couleurs différent par entité |

## 4. Hiérarchie des actions

**Problème résolu :** aucun `configureActions()` nulle part — l'admin voit la disposition par défaut d'EasyAdmin (Edit/Delete au même niveau visuel), sans priorité donnée à l'action réellement fréquente (changer un statut).

**Principe :** sur les écrans de workflow (demandes, candidatures, offres), l'action principale est "ouvrir pour traiter" (Edit), pas "supprimer". On ne retire pas Delete (l'admin doit pouvoir nettoyer des données de test), mais on ne le met pas en avant.

```php
public function configureActions(Actions $actions): Actions
{
    return $actions
        ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $a) => $a->setLabel('Traiter')->setIcon('fa fa-arrow-right'))
        ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
}
```

## 5. Tri et filtres par défaut

**Problème résolu :** aucun `configureCrud()`/`configureFilters()` — les listes s'affichent par ID croissant, et rien ne permet d'isoler "ce qui attend un traitement".

```php
public function configureCrud(Crud $crud): Crud
{
    return $crud->setDefaultSort(['createdAt' => 'DESC']);
}

public function configureFilters(Filters $filters): Filters
{
    return $filters->add('statut');
}
```

Tri par défaut = plus récent en premier (pas l'ID, qui n'a aucun sens métier). Filtre par statut disponible partout où un workflow existe, pour que l'admin puisse se concentrer sur la file "en attente".

## 6. Widget de tableau de bord (KPI cards)

Le widget actuel (`templates/admin/dashboard.html.twig`) est un unique bloc décoratif avec une couleur violette résiduelle (`#6c63ff`) et un texte d'accueil obsolète. Remplacé par une grille de cartes KPI réelles (nombre de demandes en attente par type), dans la palette teal/mint, avec un texte qui reflète les 15 entités DisPos réellement présentes.

## 7. Icônes — un seul système

**Constat (cf. audit) :** pas d'icône à connotation IA à retirer — le vrai problème est la **redondance** emoji + Font Awesome sur chaque item de menu.

**Règle adoptée :** un seul système, Font Awesome (déjà chargé par EasyAdmin, cohérent, dispose d'un jeu d'icônes concrètes/humaines pour chaque concept du projet). Les emoji sont retirés des libellés de menu.

## 8. Accessibilité (rappel transverse)

- Contraste : `--dispos-mint-accent` (#34C7A9) sur `--dispos-deep-teal` (#0A2E3D) et sur blanc seront mesurés systématiquement au moment du `accessibility-review` de chaque écran (voir cycles par écran).
- Cibles tactiles : les boutons d'action EasyAdmin par défaut respectent déjà ~40px de hauteur — à vérifier composant par composant, pas supposé acquis.
- Navigation clavier : le menu latéral et les badges de statut (liens, pas de contrôles interactifs custom) n'introduisent pas de piège au clavier par construction — à confirmer à l'usage.
