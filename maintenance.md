# Guide de Maintenance (TMA) — CineTech

Ce document définit les procédures d'exploitation, le workflow Git et les règles de traitement des tickets pour assurer la stabilité et l'évolution de l'application **CineTech**.

---

## 1. Stack technique et environnement

* **Framework :** Symfony 7
* **Langage :** PHP 8.4+
* **Base de données :** MySQL / MariaDB via Doctrine ORM
* **Interface :** Twig + Tailwind CSS
* **Outils qualité :** PHPUnit, PHPStan, Composer Audit

---

## 2. Workflow Git et gestion des branches

Le dépôt s'appuie sur une arborescence à trois niveaux :

1. **`main`** : branche de production stable (protégée). Mise à jour uniquement lors d'une release validée depuis `dev`.
2. **`dev`** : branche d'intégration et de pré-production (recette).
3. **`<developpeur>` ou `<type>/<id-issue>-<slug>`** (ex : `sacha`, `fix/04-faille-xss`) : branches de travail créées systématiquement à partir de `dev`.

### Règles de contribution
* Aucun commit direct sur `main` ou `dev`.
* Chaque développement est lié à une Issue GitHub et intégré via une **Pull Request** vers `dev`.
* Les messages de commit suivent la convention *Conventional Commits* (`fix:`, `feat:`, `chore:`, `refactor:`, `docs:`).

---

## 3. Qualification et cycle de vie TMA

Tout retour client ou incident suit le workflow en 5 étapes sur le tableau **GitHub Projects** :

1. **`Backlog`** : ticket brut en attente d'analyse.
2. **`Ready (Lotifié)`** : ticket qualifié (labels + milestone + priorité) prêt à être traité.
3. **`In Progress`** : branche Git créée, développement en cours.
4. **`Review / QA`** : Pull Request ouverte vers `dev`, relecture de code et exécution de la suite de tests.
5. **`Done`** : code mergé, ticket clôturé.

### Nomenclature des labels
* **Nature de la maintenance :**
  * `type:corrective` : correction d'anomalie ou de faille de sécurité.
  * `type:evolutive` : ajout de fonctionnalité métier.
  * `type:adaptive` : mise à jour liée à l'environnement (version PHP, serveur, hébergeur).
  * `type:preventive` : refactoring, ajout de tests, mise à jour préventive de dépendances.
* **Niveau de sévérité (anomalies uniquement) :**
  * `sev:blocker` : faille de sécurité critique (XSS, CSRF, IDOR) ou blocage total — traitement immédiat (`P0`).
  * `sev:major` : crash serveur (HTTP 500) ou dégradation forte sans contournement (`P1`).
  * `sev:minor` : dysfonctionnement partiel avec contournement possible (`P2`).
  * `sev:cosmetic` : défaut visuel mineur ou faute de frappe (`P3`).

### Politique de versionnement (Milestones)
* **`v1.0.1` (Correctifs)** : résolution des bugs critiques/majeurs, failles de sécurité et compatibilité PHP.
* **`v1.1.0` (Évolutions mineures)** : filtres catalogue, liste de favoris et refactoring technique.
* **`v2.0.0` (Version majeure)** : refonte billetterie VIP, paiement Stripe et abonnements.

---

## 4. Contrôle qualité avant Pull Request (Definition of Done)

Avant de passer un ticket en **`Review / QA`**, exécuter les vérifications suivantes en local :

```bash
# 1. Vérifier la cohérence du schéma Doctrine
php bin/console doctrine:schema:validate

# 2. Contrôler les vulnérabilités connues des dépendances
composer audit

# 3. Lancer l'analyse statique
vendor/bin/phpstan analyse

# 4. Exécuter les tests automatisés
php bin/phpunit
```

---

## 5. Procédures d'exploitation courantes

### Déploiement d'une mise à jour
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --env=prod
```

### Gestion d'un incident critique en production (`sev:blocker`)
1. Consulter les journaux d'erreurs dans `var/log/prod.log`.
2. Créer une branche corrective dédiée depuis `dev` (ou un *hotfix* depuis `main` en cas d'urgence absolue).
3. Rédiger un test reproduisant l'incident avant d'appliquer le correctif.
4. Déployer après validation en **`Review / QA`** et synchroniser `dev` et `main`.