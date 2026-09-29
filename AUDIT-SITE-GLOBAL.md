# Audit global — état du site Dis Pos

Vérifié directement dans le code (pas de suppositions) le 2026-08-05.

## 1. Volet Académique — fonctionnel, gaps mineurs

| Module | État | Manque |
|---|---|---|
| Révision par matière | ✅ Bout en bout | Pas de recherche/filtre par filière ou niveau sur `/matieres` |
| Demande d'encadrement | ✅ Bout en bout | Pas de bouton "assigner un encadreur" en un clic (admin doit éditer le champ association standard) |
| Troc de compétences | ✅ Bout en bout | Barème volontairement non figé (attendu) |
| Projets internes | ✅ Bout en bout | Pas de vue "mes contributions" pour l'admin par projet (regroupement) |
| Espace Formateurs | ✅ Bout en bout | Process de validation non défini (qui valide, sur quels critères) ; `cvUrl` est un champ texte libre, pas un vrai upload de fichier |
| Espace Recrutement | ✅ Bout en bout | Idem : `cvUrl` texte libre, pas d'upload ; pas de process de validation tiers défini |

## 2. Volet Professionnel (Incubation TPE/PME) — squelette fonctionnel, incomplet

Ce qui existe : dossier + 7 phases auto-générées + timeline + demande de consultation + sécurité (Voter testé).

Ce qui manque pour que ce soit un vrai outil de suivi métier :
- **Aucune page de détail par phase.** L'entreprise voit le statut dans la timeline mais ne peut pas cliquer sur une phase pour voir des livrables, documents ou une description détaillée.
- **Pas d'upload de documents** (études, contrats, maquettes...) associés à une phase ou à l'incubation.
- ~~Pas d'action rapide côté admin/référent...~~ ✅ **Corrigé** — `/mes-affectations/incubation` permet à un référent (n'importe quel rôle) de mettre à jour statut + commentaire, gardé par `EtapeIncubationVoter` (référent ou admin uniquement, testé : 200/403/200).
- **Secteurs d'activité** : liste définie par défaut (`IncubationCreationType::SECTEURS`), jamais validée côté métier.
- **Une seule incubation active par entreprise** : appliqué en code, pas en base (MySQL ne supporte pas les index uniques partiels).

## 3. Marketplace hérité — toujours intégralement présent

Décision prise en cours de session : "le remplacer progressivement". **Rien n'a encore été retiré ni fusionné.** Startups, Talents, Projets, Investisseurs occupent toujours 3 entrées de navbar, une bonne partie du footer, et le rôle `getPrimaryRole()` continue de gérer `ROLE_STARTUP`/`ROLE_TALENT`/`ROLE_INVESTOR` à égalité avec les rôles DisPos.

## 4. Fonctionnalités transverses manquantes (site entier)

- ~~Mot de passe oublié...~~ ✅ **Corrigé** — `/reset-password`, testé de bout en bout. Sans SMTP configuré, le lien s'affiche en mode dev uniquement (flash dédié, cf. commit) plutôt que d'être réellement envoyé par email.
- ~~Édition de profil...~~ ✅ **Corrigé** — `/mon-profil` (infos) + `/mon-profil/mot-de-passe` (changement avec vérification du mot de passe actuel), testé.
- ~~Upload de fichiers...~~ ✅ **Corrigé pour les CV** (formateurs + recrutement) — vrai `FileType`, stockage dans `public/uploads/cv/`, page de revue des candidatures côté entreprise avec lien de téléchargement. Les documents d'incubation (études, contrats) restent hors périmètre.
- ~~Recherche / filtres...~~ ✅ **Corrigé** — filtres par filière/domaine/type de contrat sur les 3 listes DisPos.
- ~~Pagination...~~ ✅ **Corrigé** — `?page=` sur les 3 listes, wrapper autour du Paginator natif de Doctrine ORM.
- **Notifications email** — aucun `Mailer` configuré (`composer.json` ne liste pas `symfony/mailer`). Quand un admin change un statut (accepté/refusé), l'utilisateur ne reçoit rien : il doit se reconnecter et vérifier sa page "mes demandes" pour le savoir.

## 5. Qualité / infrastructure

- ~~Aucun commit depuis le 03/08...~~ ✅ **Corrigé** — tout le travail est maintenant réparti en commits logiques et poussé sur `feature/dispos-foundations`.
- **Fichiers PHPUnit orphelins** : `bin/phpunit` et `phpunit.dist.xml` traînent non suivis depuis la tentative de mise en place de tests (abandonnée sur ta demande). Aucune suite de tests automatisés n'existe — toute vérification faite dans cette session l'a été manuellement (curl + lecture SQL directe), donc aucune protection contre une régression future.
- **Contraste AA (mode clair/sombre)** — les couleurs ont été choisies pour viser AA (texte foncé sur fond blanc, mint accent sur fond sombre) mais **jamais mesurées avec un outil de contraste réel** ; le critère d'acceptation du Ticket 3.1 n'est donc pas formellement vérifié.
- **Responsive mobile** — jamais testé dans cette session (le pane navigateur ne s'affichait pas). Le CSS a un breakpoint `@media (max-width: 768px)` pour la navbar, mais les nouvelles pages (timeline incubation, listes de modules) n'ont pas été vérifiées sur petit écran.

## 6. Points métier encore non validés (déjà signalés, rappel)

- Secteurs d'activité de l'incubation (`IncubationCreationType::SECTEURS`)
- Barème compétence → réduction
- Process de validation des candidatures formateurs
- Process de validation des offres de recrutement tierces
- Portée géographique du recrutement (Tunisie uniquement, 24 gouvernorats)

## Priorisation suggérée — toutes traitées

1. ✅ **Committer le travail existant** — 13 commits sur `feature/dispos-foundations`, poussés.
2. ✅ **Brancher `EtapeIncubationUpdateType`** — `/mes-affectations/incubation`.
3. ✅ **Mot de passe oublié + édition de profil**.
4. ✅ **Upload de fichiers** — CV formateurs/recrutement.
5. ✅ **Pagination/filtres** — matières, projets internes, recrutement.

## Ce qui reste (hors périmètre de cet audit, connu et documenté)

- Aucun test automatisé (décision explicite du projet).
- Marketplace hérité (Startups/Talents/Investisseurs) toujours présent intégralement.
- Documents d'incubation (études, contrats) — pas d'upload dédié.
- Notifications email — aucune, au-delà du lien de reset (lui-même en mode dev-only sans SMTP réel).
- Contraste AA et responsive mobile jamais mesurés formellement.
- Points métier non validés : secteurs d'incubation, barème compétences, process de validation formateurs/recrutement.
