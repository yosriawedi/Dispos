# Audit des formulaires — Ticket 2.1

Objectif : identifier tous les champs `RadioType` / `CheckboxType` / champs libres qui devraient devenir des listes déroulantes (`ChoiceType`, `expanded => false`).

Légende de confiance :
- 🔴 **Certain** — correspond littéralement au critère du ticket (Radio/Checkbox existant), conversion sans ambiguïté.
- 🟡 **À valider** — champ texte libre représentant un concept fermé/borné ; conversion recommandée mais implique une décision produit (liste de valeurs à figer).
- ⚪ **Hors périmètre** — champ authentiquement libre (texte narratif, identifiant, URL) ; ne pas convertir.

## 🔴 Certains — Radio/Checkbox existants

| Formulaire | Champ | Actuel | Proposé | Remarque |
|---|---|---|---|---|
| `RegistrationFormType.php:34` | `role` | `ChoiceType`, `expanded: true` (rendu en radio-cards, voir `templates/security/register.html.twig`) | `ChoiceType`, `expanded: false` | Nécessite d'adapter le template (retirer le CSS `.role-card`/`.role-cards`, revenir à un `form_widget` standard). |
| `OffreRecrutementType.php:49` | `teletravail` | `CheckboxType` | `ChoiceType` (`Oui` / `Non`), `expanded: false` | ⚠️ Champ booléen par nature — la checkbox est le widget natif standard pour un booléen. Le convertir en `<select>` Oui/Non est possible et respecte la lettre du ticket, mais c'est un choix UX discutable. À confirmer avant conversion. |

## 🟡 À valider — champs libres représentant un concept fermé

| Formulaire | Champ | Actuel | Proposé | Remarque |
|---|---|---|---|---|
| `DemandeEncadrementType.php:46` | `niveauEtude` | `TextType` (placeholder "Licence 3, Master 2...") | `ChoiceType` : Licence 1/2/3, Master 1/2, Doctorat 1/2/3, Autre | Concept borné en pratique — mais la liste exacte des valeurs doit être validée côté métier. |
| `CandidatureFormateurType.php:20` | `matieres` | `TextareaType` (texte libre "Algo, Maths, Java") | `EntityType` (choix multiple, lié à l'entité `Matiere` existante) | Le plus fort candidat : une entité `Matiere` existe déjà en base, la saisie libre duplique une donnée qui devrait être une référence. Implique un changement de type de donnée stockée (relation ManyToMany plutôt que texte), pas juste un changement de widget — portée plus large que les autres lignes de ce tableau. |
| `OffreRecrutementType.php:45` | `localisation` | `TextType` (texte libre "Tunis") | `ChoiceType` : liste des gouvernorats tunisiens | Raisonnable pour un site de recrutement local, mais fige la couverture géographique à la Tunisie — à confirmer si DisPos prévoit des offres hors Tunisie. |

## ⚪ Hors périmètre — texte réellement libre

| Formulaire | Champs | Pourquoi ne pas convertir |
|---|---|---|
| `RegistrationFormType` | `firstName`, `lastName`, `email` | Identité, valeur unique par nature. |
| `DemandeEncadrementType` | `sujet`, `stackTech`, `description`, `etablissement` | `sujet`/`description` sont narratifs par nature. `stackTech`/`etablissement` référencent des concepts ouverts sans liste fermée existante dans le domaine (pas d'entité "Stack" ou "Établissement" en base). |
| `OffreCompetenceType` | `titre`, `description`, `stack` | Idem : narratif ou stack technique sans liste fermée existante. |
| `DemandeReductionType` | `contextePrestationCiblee` | Le barème compétence → réduction est explicitement non figé (consigne projet) ; ce champ reste volontairement ouvert tant que la logique métier n'est pas validée. |
| `CandidatureFormateurType` | `stacks`, `experience`, `diplomes`, `cvUrl` | Texte narratif ou URL. |
| `CandidatureRecrutementType` | `lettreMotivation`, `cvUrl` | Texte narratif ou URL. |
| `ContributionProjetInterneType` | `message` | Texte narratif. |
| `OffreRecrutementType` | `poste`, `salaire` | `poste` : intitulé libre par nature. `salaire` : plage de texte libre ("1200-1500 TND/mois") — convertir en liste de tranches figées est possible mais change significativement l'UX de saisie ; non recommandé sans confirmation explicite. |
| Tous formulaires | Champs numériques (`heuresEstimees`, `disponibiliteHeures`, `tarifHoraire`) | Ce sont des `IntegerType`/`NumberType`, pas des choix — hors périmètre du ticket (qui vise Radio/Checkbox/texte libre à choix fermé, pas les nombres). |

## Formulaires déjà conformes (aucune action)

- `DemandeEncadrementType.type` — déjà `ChoiceType`, `expanded` non défini (donc `false` par défaut = `<select>`). ✅
- `OffreRecrutementType.typeContrat` — idem. ✅

## Formulaires hors `src/Form/` à vérifier séparément

Le ticket mentionne aussi les formulaires "startup/talent hérités" — **aucun `FormType` dédié n'existe** pour ces entités (`Startup`, `Project`, `Tag` sont uniquement éditables via EasyAdmin, pas de formulaire public de création). Rien à convertir côté public pour ce périmètre.

## Décisions validées (2026-08-03)

1. **`teletravail`** → converti en `ChoiceType` Oui/Non.
2. **`niveauEtude`** → converti en `ChoiceType` : Licence 1, Licence 2, Licence 3, Master 1, Master 2, Doctorat 1, Doctorat 2, Doctorat 3, Autre.
3. **`matieres` (formateur)** → converti en relation `ManyToMany` vers l'entité `Matiere` (multi-sélection), changement de modèle de données inclus.
4. **`localisation`** → converti en `ChoiceType`, liste des 24 gouvernorats de Tunisie.
