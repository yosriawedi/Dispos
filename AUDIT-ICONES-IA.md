# Audit des icônes "IA" — Ticket 4.1

## Résultat : aucune iconographie robot/IA/technologie froide trouvée

- `assets/images/` ne contient que `disposlogo.png` (le logo DisPos) — aucune icône robot, circuit, puce ou cyborg.
- Aucun `<svg>` inline dans les templates (`grep -rl "<svg" templates/` → aucun résultat).
- Toute l'iconographie du site utilise des emojis à connotation humaine/chaleureuse, déjà en place dans les modules construits : 📚 (révision), 🎓 (encadrement), 🤝 (compétences), 🛠️ (projets internes), 👨‍🏫 (formateurs), 💼 (recrutement), 🏢 (incubation), 👥 (communauté), 🗺️ (parcours), 📞 (consultation), 💡, 🚀, 💰 (marketplace hérité).

## Mentions de "IA" trouvées (non-visuelles, hors périmètre du ticket)

Trois occurrences du terme "IA"/"intelligence artificielle" existent, mais ce sont des **contenus**, pas des **icônes** :

| Fichier | Contenu | Pourquoi hors périmètre |
|---|---|---|
| `src/DataFixtures/AppFixtures.php:26` | Tag de démonstration `"IA / Machine Learning"` | Secteur d'activité légitime pour une startup de démo — supprimer reviendrait à censurer un domaine technique réel, pas à "humaniser" l'interface. |
| `src/DataFixtures/AppFixtures.php:96` | Description d'une startup fictive "AgroSmart AI" mentionnant l'IA | Contenu narratif de démonstration, pas une icône ni un ton robotique. |
| `src/Form/DemandeEncadrementType.php:30` | Placeholder d'exemple "Plateforme de recommandation IA pour e-commerce" | Simple suggestion de sujet PFE — un étudiant peut légitimement vouloir faire un projet IA. |

## Conclusion

Le Ticket 4.1 part de la prémisse qu'il existe une iconographie robot/IA à corriger. Ce n'est pas le cas ici : le codebase n'a jamais eu ce type de visuel (probablement parce que les modules ont été construits directement avec des emojis chaleureux). **Aucune action de remplacement d'icônes n'est nécessaire.**

Le Ticket 4.2 est donc recentré sur son second objectif : la révision des micro-textes pour un ton plus chaleureux (voir le résumé de session pour le détail des changements appliqués, le cas échéant).
